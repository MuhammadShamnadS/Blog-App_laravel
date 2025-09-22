<?php

namespace App\Http\Services;

use App\Models\Editor;
use App\Models\EditorReview;

class EditorPostService
{
    public function fetchAssignedPost()
    {
        $editor = Editor::where('user_id', auth()->id())->firstOrFail();

        $reviews = EditorReview::with('post.category', 'post.author', 'post.tags', 'post.media')
            ->where('editor_id', $editor->id)
            ->where('status', 'pending')
            ->whereHas('post', function ($q) {
                $q->where('status', 'under_review');
            })
            ->get();

        return response()->json([
            'editor'  => $editor,
            'reviews' => $reviews
        ]);
    }


    public function fetchReview($reviewId)
    {
        $editor = Editor::where('user_id', auth()->id())->firstOrFail();

        $review = EditorReview::with(['post.category', 'post.author', 'post.media'])
            ->where('id', $reviewId)
            ->where('editor_id', $editor->id)
            ->first();

        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }

        return response()->json($review);
    }


    // Editor reviews the post
    public function reviewPost($reviewId, $validatedData)
    {
        $editor = Editor::where('user_id', auth()->id())->firstOrFail();

        $review = EditorReview::with('post')
            ->where('id', $reviewId)
            ->where('editor_id', $editor->id)
            ->where('status', 'pending')
            ->first();

        if (!$review) {
            return response()->json(['error' => 'Review not found or already processed'], 404);
        }

        $post = $review->post;

        if ($post->status !== 'under_review') {
            return response()->json(['error' => 'Post is not in a reviewable state'], 400);
        }

        // If rejecting, feedback is mandatory
        if ($validatedData['status'] === 'rejected' && empty($validatedData['feedback'])) {
            return response()->json(['error' => 'Feedback is required when rejecting a post'], 400);
        }

        // Update review
        $review->update([
            'status'   => $validatedData['status'],
            'feedback' => $validatedData['feedback'],
        ]);

        // Update post status
        if ($validatedData['status'] === 'approved') {
            $post->status = 'editor_approved';
        } else {
            $post->status = 'editor_rejected';
        }

        $post->save();

        return response()->json([
            'message' => 'Post reviewed successfully',
            'review'  => $review,
            'post'    => $post
        ]);
    }
}
