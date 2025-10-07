<?php

namespace App\Http\Services;

use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Post;

class CommentService
{
public function createComment($validatedData)
{
    $user = auth()->user();

    if ($user->role != 'guest') {
        return response()->json(['error' => 'Only guest can comment'], 403);
    }

    $post = Post::withoutGlobalScope('parentNotDeleted')->findOrFail($validatedData['post_id']);

    if ($post->deleted_at != null) {
        return response()->json(['error' => 'Cannot comment on a deleted post.'], 403);
    }

    $comment = Comment::create([
        'post_id'   => $post->id,
        'user_id'   => $user->id,
        'parent_id' => $validatedData['parent_id'] ?? null,
        'content'   => $validatedData['content'],
        'status'    => $validatedData['status'] ?? '0',
    ]);

    return response()->json([
        'message' => 'Comment created successfully.',
        'comment' => $comment,
    ], 201);
}


public function listComments($postId)
{
    $user = auth()->user();

    $comments = Comment::withoutGlobalScope('parentNotDeleted')
        ->where('post_id', $postId)
        ->whereNull('parent_id')
        ->with([
            'user',
            'replies.user',
            'reports'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

    $comments = $comments->map(function ($comment) use ($user) {
        $comment->total_reports = $comment->reports()->count();
        $comment->is_reported_by_user = $comment->reports->contains('user_id', $user->id);

        $comment->replies->transform(function ($reply) use ($user) {
            $reply->total_reports = $reply->reports()->count();
            $reply->is_reported_by_user = $reply->reports->contains('user_id', $user->id);
            return $reply;
        });

        return $comment;
    });

    return response()->json([
        'comments' => $comments,
    ]);
}


    public function deleteComment($commentId)
    {
        $comment = Comment::find($commentId);

        if (!$comment) {
            return response()->json([
                'error' => 'Comment not found.',
            ], 404);
        }
        $user = auth()->user();
        if ($user->role !== "admin" && $comment->user_id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized to delete this comment.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }


  public function reportComment($id)
{
    $user = auth()->user();

    if ($user->role != 'guest') {
        return response()->json(['error' => 'Not a valid guest'], 403);
    }

    $comment = Comment::withoutGlobalScope('parentNotDeleted')->find($id);

    if (!$comment) {
        return response()->json(['error' => 'Comment not found'], 404);
    }

    $existingReport = CommentReport::where('comment_id', $id)
        ->where('user_id', $user->id)
        ->first();

    if ($existingReport) {
        $existingReport->delete();
        $reported = false;
    } else {
        CommentReport::create([
            'comment_id' => $id,
            'user_id' => $user->id,
        ]);
        $reported = true;
    }

    $comment->is_spam = $comment->reports()->count();
    $comment->save();

    return response()->json([
        'message' => $reported ? 'Comment reported' : 'Comment unreported',
        'is_reported_by_user' => $reported,
        'total_reports' => $comment->reports()->count(),
    ]);
}

}


