<?php

namespace App\Http\Services;

use App\Models\Category;
use App\Models\Editor;
use App\Models\EditorReview;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use App\Notifications\AuthorPublishedPost;

class AdminPostService
{
    // view author submitted posts by admin
    public function viewSubmittedPost()
    {
        $posts = Post::with(['author', 'category'])
            ->where('status', 'submitted')
            ->get();

        return response()->json($posts);
    }

    //  get editors under a category by admin
    public function getEditors($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return response()->json([
            'category' => [
                'id'   => $categoryId,
                'name' => $category->name,
            ],
            'editors' => $category->editors()->with('user')->get(),
        ]);
    }

    //  assign editor to a post by admin
    public function handleAssignEditor($postId, $validatedData)
    {
        $post = Post::findOrFail($postId);
        if ($post->status !== 'submitted') {
            return response()->json([
                'error' => 'Only submitted posts can be assigned to editors'
            ], 400);
        }


        $editor = Editor::findOrFail($validatedData['editor_id']);

        if ($editor->category_id !== $post->category_id) {
            return response()->json([
                'error' => 'Editor category does not match the post category'
            ], 400);
        }

        $review = EditorReview::where('post_id', $post->id)
            ->where('editor_id', $editor->id)
            ->first();

        if ($review) {
            if ($review->status === 'rejected') {
                $review->status = 'pending';
                $review->feedback = null;
                $post->status = 'under_review';
                $review->save();
                $post->save();
            } else {
                return response()->json([
                    'error' => 'This post is already assigned to this editor and not rejected'
                ], 400);
            }
        } else {
            $review = EditorReview::create([
                'editor_id' => $editor->id,
                'post_id'   => $post->id,
                'status'    => 'pending',
            ]);
            $post->status = 'under_review';
            $post->save();
        }
    }

    //  list editor approved posts by admin
    public function listEditorApprovedPosts()
    {
        $posts = Post::where('status', 'editor_approved')
            ->with(['author', 'category', 'tags', 'media', 'editorReview'])
            ->get();

        return response()->json($posts);
    }

    //  list pending posts by admin
    public function listPendingPost()
    {
        $posts = Post::where('status', 'editor_approved')
            ->with(['author', 'category', 'tags', 'media', 'editorReview'])
            ->get();

        return response()->json($posts);
    }

    // schedule a post by admin
    public function handleSchedule($id, $validatedData)
    {
        $post = Post::findorFail($id);
        if ($post->status !== 'editor_approved' && $post->status !== 'scheduled') {
            return response()->json(['message' => 'Only editor-approved posts can be scheduled.'], 400);
        }
        $post->schedule_at = $validatedData['schedule_at'];
        $post->status = 'scheduled';
        $post->save();

        return response()->json([
            'message' => 'Post scheduled successfully.',
            'post' => $post
        ]);
    }

    // view single post by admin
    public function showSinglePost($id)
    {
        $post = Post::with(['category', 'tags', 'media', 'author', 'editorReview'])->findorFail($id);
        return response()->json($post);
    }

    // view published posts by admin
    public function listPublishedPost()
    {
        $post = Post::with(['category', 'tags', 'media', 'author', 'editorReview'])->where('status', 'published')->get();

        return response()->json($post);
    }

    // view scheduled posts by admin
    public function listScheduledPost()
    {
        $post = Post::with(['category', 'tags', 'media', 'author', 'editorReview'])->where('status', 'scheduled')->get();

        return response()->json($post);
    }

    //  view archieved post by admin
    public function listArchievedPost()
    {
        $post = Post::with(['category', 'tags', 'media', 'author', 'editorReview'])->where('status', 'archived')->get();

        return response()->json($post);
    }

    //  view featured posts by admin
    public function listFeaturedPosts()
    {
        $post = Post::with(['category', 'tags', 'media', 'author', 'editorReview'])->where('status', 'published')->where('featured', '1')->get();

        return response()->json($post);
    }

    //  feature/unfeature a post by admin
    public function featurePost($id, $validatedData)
    {
        $post = Post::findORFail($id);

        if (!in_array($post->status, ['published'])) {
            return response()->json([
                'message' => 'Only published posts can be featured.'
            ], 400);
        }

        $post->featured = $validatedData['featured'];
        $post->save();

        return response()->json([
            'message' => $post->featured ? 'Post marked as featured.' : 'Post unfeatured.',
            'post' => $post
        ]);
    }

    //  publish/unpublish a post by admin
    public function handlePublish($id)
    {
        $post = Post::findorFail($id);

        if (!in_array($post->status, ['editor_approved', 'scheduled', 'archived'])) {
            return response()->json(['message' => 'Only approved or scheduled posts can be published.'], 400);
        }

        $post->status = 'published';
        $post->schedule_at = null;
        $post->save();
        // notify all followers of the author
$followers = Follow::where('author_id', $post->author_id)->get();

foreach ($followers as $follower) {
    $guest = User::find($follower->guest_id); // adjust model if your guest is another entity
    if ($guest) {
        $guest->notify(new AuthorPublishedPost($post->author, $post));
    }
}


        return response()->json([
            'message' => 'Post published successfully.',
            'post' => $post
        ]);
    }

    //  delete a post by admin
    public function handleDelete($id)
    {
        $post = Post::findorFail($id);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }

    // archieve a post by admin
    public function handleArchieve($id)
    {
        $post = Post::findorFail($id);

        if ($post->status !== 'published') {
            return response()->json(['message' => 'Only published posts can be archived.'], 400);
        }

        $post->status = 'archived';
        $post->featured = '0';
        $post->save();

        return response()->json(['message' => 'Post archived successfully.', 'post' => $post]);
    }

    //  unarchieve a post by admin
    public function handleUnarchieve($id)
    {
        $post = Post::findorFail($id);

        if ($post->status !== 'archived') {
            return response()->json(['message' => 'Only archived posts can be unarchived.'], 400);
        }

        $post->status = 'published';
        $post->save();

        return response()->json(['message' => 'Post unarchived successfully.', 'post' => $post]);
    }

    //  list categories by admin
    public function fetchCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
}
