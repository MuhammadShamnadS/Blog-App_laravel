<?php

namespace App\Http\Controllers;

use App\Models\Editor;
use App\Models\Category;
use App\Models\EditorReview;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    // Get posts that authors submitted
    public function submittedPosts()
    {
        $posts = Post::with(['author', 'category'])
            ->where('status', 'submitted')
            ->get();

        return response()->json($posts);
    }

    // Assign editor to a submitted or resubmitted post
    public function assignEditor(Request $request, $postId)
    {
        $request->validate([
            'editor_id' => 'required|exists:editors,id'
        ]);

        $post = Post::findOrFail($postId);


        // if($post->status === 'under_review')
        // {
        //         return response()->json([
        //             'error' => 'Post already assigned to an editor'
        //         ], 400);
        // }
        // Only "submitted" posts can be assigned
        if ($post->status !== 'submitted') {
            return response()->json([
                'error' => 'Only submitted posts can be assigned to editors'
            ], 400);
        }

        $editor = Editor::findOrFail($request->editor_id);

        // Check category compatibility
        if ($editor->category_id !== $post->category_id) {
            return response()->json([
                'error' => 'Editor category does not match the post category'
            ], 400);
        }

        // Prevent assigning if it's already under active review
        $review = EditorReview::where('post_id', $post->id)
            ->where('editor_id', $editor->id)
            ->first();

        if ($review) {
            if ($review->status === 'rejected') {
                // Resubmit case -> reset to pending
                $review->status = 'pending';
                $review->feedback = null; // clear old feedback
                $review->save();
            } else {
                return response()->json([
                    'error' => 'This post is already assigned to this editor and not rejected'
                ], 400);
            }
        } else {
            // First time assignment
            $review = EditorReview::create([
                'editor_id' => $editor->id,
                'post_id'   => $post->id,
                'status'    => 'pending',
            ]);
    }

    // Update post status
    $post->update(['status' => 'under_review']);

    return response()->json([
        'message' => 'Editor assigned successfully',
        'review'  => $review,
    ]);
}

    // Get editors for a given category
    public function editors(Category $category)
    {
        return response()->json([
            'category' => [
                'id'   => $category->id,
                'name' => $category->name,
            ],
            'editors' => $category->editors()->with('user')->get(),
        ]);
    }

    public function getEditorApprovedPosts()
{
    $posts = Post::where('status', 'editor_approved')
        ->with(['author', 'category', 'tags', 'media', 'editorReview'])
        ->get();

    return response()->json($posts);
}

public function schedulePost(Request $request, $id)
{
    $post = Post::findOrFail($id);

    if ($post->status !== 'editor_approved') {
        return response()->json(['message' => 'Only editor-approved posts can be scheduled.'], 400);
    }

    $request->validate([
        'schedule_at' => 'required|date|after:now',
    ]);

    $post->schedule_at = $request->schedule_at;
    $post->status = 'scheduled';
    $post->save();

    return response()->json([
        'message' => 'Post scheduled successfully.',
        'post' => $post
    ]);
}
public function show($id)
    {
        $post = Post::with(['category','tags', 'media', 'author','editorReview'])->findOrFail($id);
        return response()->json($post);
    }

public function index()
    {
        $post=Post::with(['category','tags', 'media', 'author','editorReview'])->get();

        return response()->json($post);
    }

public function toggleFeatured(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Only allow editor-approved or published posts to be featured
        if (!in_array($post->status, ['editor_approved', 'published'])) {
            return response()->json([
                'message' => 'Only editor-approved or published posts can be featured.'
            ], 400);
        }

        $request->validate([
            'featured' => 'required|boolean',
        ]);

        $post->featured = $request->featured;
        $post->save();

        return response()->json([
            'message' => $post->featured ? 'Post marked as featured.' : 'Post unfeatured.',
            'post' => $post
        ]);
    }

public function publish($id)
{
    $post = Post::findOrFail($id);

    if (!in_array($post->status, ['editor_approved', 'scheduled', 'archived'])) {
        return response()->json(['message' => 'Only approved or scheduled posts can be published.'], 400);
    }

    $post->status = 'published';
    $post->schedule_at = null; 
    $post->save();

    return response()->json([
        'message' => 'Post published successfully.',
        'post' => $post
    ]);
}

public function destroy($id)
{
    $post = Post::findOrFail($id);
    $post->delete();

    return response()->json(['message' => 'Post deleted successfully.']);
}


// Archive a post
public function archive($id)
{
    $post = Post::findOrFail($id);

    // Only allow published posts to be archived
    if ($post->status !== 'published') {
        return response()->json(['message' => 'Only published posts can be archived.'], 400);
    }

    $post->status = 'archived';
    $post->save();

    return response()->json(['message' => 'Post archived successfully.', 'post' => $post]);
}

// Unarchive a post
public function unarchive($id)
{
    $post = Post::findOrFail($id);

    // Only allow archived posts to be unarchived
    if ($post->status !== 'archived') {
        return response()->json(['message' => 'Only archived posts can be unarchived.'], 400);
    }

    $post->status = 'published'; 
    $post->save();

    return response()->json(['message' => 'Post unarchived successfully.', 'post' => $post]);
}

public function category()
{
    $categories = Category::all();
    return response()->json($categories);
}

}