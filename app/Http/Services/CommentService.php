<?php

namespace App\Http\Services;

use App\Models\Comment;
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
        $comments = Comment::withoutGlobalScope('parentNotDeleted')->where('post_id', $postId)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();

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
        if ($comment->user_id !== $user->id && $user->role != "admin") {
            return response()->json([
                'error' => 'Unauthorized to delete this comment.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }
}
