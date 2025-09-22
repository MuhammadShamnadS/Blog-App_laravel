<?php

namespace App\Http\Services;

use App\Models\Category;
use App\Models\EditorReview;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function viewPost()
    {
        $userId = auth()->id();
        $post = Post::with(['category', 'media'])
            ->where('author_id', $userId)->get();

        return response()->json($post);
    }

    public function viewSinglePost($id)
    {
        $post = Post::with(['category', 'tags', 'media', 'author', 'editorReview'])->where('author_id', auth()->id())->findOrFail($id);
        return response()->json($post);
    }
    public function createPost(array $validatedData)
    {
        $user = auth()->user();

        if ($user->role !== 'author') {
            return response()->json(['error' => 'Only authors can create posts.'], 403);
        }

        $category = Category::where('name', $validatedData['category'])->firstOrFail();

        $post = Post::create([
            'title'       => $validatedData['title'],
            'content'     => $validatedData['content'],
            'author_id'   => $user->id,
            'category_id' => $category->id,
            'status'      => 'draft',
        ]);

        if (!empty($validatedData['tags'])) {
            $tagIds = [];
            foreach ($validatedData['tags'] as $tagName) {
                $tag = Tag::where('name', $tagName)
                    ->where('category_id', $category->id)
                    ->first();

                if (!$tag) {
                    return response()->json(['error' => "Invalid tag: {$tagName}"], 422);
                }

                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        if (!empty($validatedData['media'])) {
            foreach ($validatedData['media'] as $file) {
                $path = $file->store('posts', 'public');
                $type = $this->getMediaType($file->getClientMimeType());

                Media::create([
                    'post_id' => $post->id,
                    'url'     => $path,
                    'type'    => $type,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post'    => $post->load('media', 'tags', 'category')
        ]);
    }

    public function updatePost($validatedData, $id)
    {
        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->author_id) {
            return response()->json(['error' => 'You are not the owner of this post'], 403);
        }

        if (in_array($post->status, ["submitted", "editor_approved", "under_review"])) {
            return response()->json(['error' => 'You cannot do any actions on a submitted post, wait until editor response'], 403);
        }
        $category = Category::where('name', $validatedData['category'])->firstOrFail();

        $post->update([
            'title'   => $validatedData['title'],
            'content' => $validatedData['content'],
            'status'  => $validatedData['status'],
            'category_id' => $category->id,
        ]);

        if (!empty($validatedData['tags'])) {
            $tagIds = [];
            foreach ($validatedData['tags'] as $tagName) {
                $tag = Tag::where('name', $tagName)
                    ->where('category_id', $category->id)
                    ->first();

                if (!$tag) {
                    return response()->json(['error' => "Invalid tag: {$tagName}"], 422);
                }

                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        if (!empty($validatedData['media'])) {
            foreach ($validatedData['media'] as $file) {
                $path = $file->store('posts', 'public');
                $type = $this->getMediaType($file->getClientMimeType());

                Media::create([
                    'post_id' => $post->id,
                    'url'     => $path,
                    'type'    => $type,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'post'    => $post->load('media', 'tags', 'category')
        ]);
    }

    private function getMediaType($mime)
    {
        if (str_contains($mime, 'image')) return 'image';
        if (str_contains($mime, 'video')) return 'video';
        if (str_contains($mime, 'audio')) return 'audio';
        return 'file';
    }
    public function deletePost($id)
    {
        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->author_id) {
            return response()->json(['error' => 'You are not allowed to delete this post']);
        }
            foreach ($post->media as $media) {
                Storage::disk('public')->delete($media->url);
                $media->delete();
            }

            $post->delete();

            return response()->json(['message' => 'Post deleted successfully']);
        }
    

    

    public function categoryList()
    {
        $category = Category::all();
        return response()->json($category);
    }

    public function fetchTags($id)
{
    $tags = Tag::where('category_id',$id)->get();
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

        Storage::disk('public')->delete($media->url);


        $media->delete();

        return response()->json(['message' => 'Media deleted successfully']);
    }
    public function handleResubmitPost($id)
    {

        $user = auth::id();
        $post = Post::findOrFail($id);
        if ($post->author_id != $user) {
            return response()->json(['error' => 'You are not allowed to do any actions on others post']);
        }
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
            $review->feedback = null;
            $review->save();
            return response()->json([
                'message' => 'Post resubmitted to the same editor.',
                'post' => $post->load('editorReview', 'author', 'category', 'tags', 'media')
            ]);
        }
        $post->status = 'submitted';
        $post->save();

        return response()->json([
            'message' => 'Post resubmitted for admin assignment.',
            'post' => $post->load('author', 'category', 'tags', 'media')
        ]);
    }
}
