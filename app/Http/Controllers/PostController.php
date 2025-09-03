<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Media;
use App\Models\Tag;
use App\Models\EditorReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{

    
    // List posts
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = Post::with(['category', 'media'])
            ->where('author_id', $userId);

        // filter by type
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'approved':
                    $query->whereIn('status', ['approved']);
                    break;
                case 'rejected':
                    $query->where('status', 'editor_rejected');
                    break;
                default:
                    break;
            }
        }

        return response()->json($query->get());
    }


    // Show single post
    public function show($id)
    {
        $post = Post::with(['category','tags', 'media', 'author','editorReview'])->where('author_id',auth()->id())->findOrFail($id);
        return response()->json($post);
    }

    // Create post (author only, default draft)
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category'    => 'required|string|max:100',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string|max:50',
            'media.*'     => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,pdf,docx|max:20480',
        ]);

        $user = Auth::user();

        if ($user->role !== 'author') {
            return response()->json(['error' => 'Only authors can create posts.'], 403);
        }

        $category = Category::firstOrCreate(['name' => $request->category]);

        $post = Post::create([
            'title'       => $request->title,
            'content'     => $request->content,
            'category_id' => $category->id,
            'status'      => 'draft',
            'author_id'     => $user->id,
        ]);

        // Attach tags if provided
    if ($request->has('tags')) 
        { 
            $tagIds = []; 
            foreach ($request->tags as $tagName) 
                { 
                    $tag = Tag::firstOrCreate(['name' => $tagName]); 
                    $tagIds[] = $tag->id; 
                } 
                $post->tags()->sync($tagIds); 
        }

        // Save media files
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');

                $type = $this->getMediaType($file->getClientMimeType());

                Media::create([
                    'post_id' => $post->id,
                    'url'     => $path,
                    'type'    => $type,
                ]);
            }
        }

        return response()->json(['message' => 'Post created successfully', 'post' => $post->load('media', 'tags', 'category')]);
    }

    // Update post
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->author_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category'    => 'required|string|max:100',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string|max:50',
            'status'      => 'required|in:draft,submitted',
            'media.*'     => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,pdf,docx|max:20480',
        ]);

        $category = Category::firstOrCreate(['name' => $request->category]);

   if (in_array($post->status, ["submitted", "editor_approved","under_review"])) 
        {
            return response()->json(['error' , 'You cannot do any actions on a submitted post , wait until editors responnse']);
        }


        $post->update([
            'title'       => $request->title,
            'content'     => $request->content,
            'category_id' => $category->id,
            'status' =>$request->status,
        ]);
        
    if ($request->has('tags')) 
        { 
            $tagIds = []; 
            foreach ($request->tags as $tagName) 
                { 
                    $tag = Tag::firstOrCreate(['name' => $tagName]); 
                    $tagIds[] = $tag->id; 
                } 
                $post->tags()->sync($tagIds); 
        }

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');

                $type = $this->getMediaType($file->getClientMimeType());

                Media::create([
                    'post_id' => $post->id,
                    'url'     => $path,
                    'type'    => $type,
                ]);
            }
        }

        return response()->json(['message' => 'Post updated successfully', 'post' => $post->load('media', 'tags', 'category')]);
    }

    // Delete post
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->author_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete media files from storage
        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->url);
            $media->delete();
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    private function getMediaType($mime)
    {
        if (str_contains($mime, 'image')) return 'image';
        if (str_contains($mime, 'video')) return 'video';
        if (str_contains($mime, 'audio')) return 'audio';
        return 'file';
    }
    public function category()
    {
        $category=Category::all();
        return response()->json($category);
    }

    public function tags()
    {
        $tags=Tag::all();
        return response()->json($tags);
    }

  public function deleteMedia($id)
{
    $userId = auth()->id();
    $media = Media::findOrFail($id);
    $post = Post::findOrFail($media->post_id);

    if ($userId !== $post->author_id) {
        return response()->json(['error' => 'You are not allowed to delete this'], 403);
    }

    // Delete file from storage
    Storage::disk('public')->delete($media->url);

    // Delete media record
    $media->delete();

    return response()->json(['message' => 'Media deleted successfully']);
}

public function resubmitPost($id)
{
    $post = Post::findOrFail($id);

    // only allow resubmit if post is draft or rejected
    if ($post->status !== 'editor_rejected') {
        return response()->json(['message' => 'This post cannot be resubmitted.'], 400);
    }
      

    $review = EditorReview::where('post_id', $post->id)
        ->latest()
        ->first();

        if ($review && $review->editor_id) {
    
            $post->status = 'under_review';
            $post->save();

            $review->status = 'pending';
            $review->feedback=null;
            $review->save();
        return response()->json([
            'message' => 'Post resubmitted to the same editor.',
            'post' => $post->load('editorReview', 'author', 'category', 'tags', 'media')
        ]);
    }

    // if no review exists, fallback to admin for assignment
    $post->status = 'submitted';
    $post->save();

    return response()->json([
        'message' => 'Post resubmitted for admin assignment.',
        'post' => $post->load('author', 'category', 'tags', 'media')
    ]);
}


}
