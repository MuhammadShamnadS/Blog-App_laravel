<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    // Create a comment
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'post_id'   => 'required|integer|exists:posts,id',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'content'   => 'required|string|max:2000',
            'status'    => 'sometimes|in:0,1',
        ]);

        $user = $request->user();

        $comment = Comment::create([
            'post_id'   => $request->post_id,
            'user_id'   => $user->id,
            'parent_id' => $request->parent_id,
            'content'   => $request->content,
            'status'    => $request->status ?? '0',
        ]);

        return response()->json([
            'message' => 'Comment created successfully.',
            'comment' => $comment,
        ], 201);
    }

    // List comments for a post 
public function index(Request $request, int $postId): JsonResponse
{
    $comments = Comment::where('post_id', $postId)
        ->whereNull('parent_id')
        ->with(['user', 'replies.user'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'comments' => $comments,
    ]);
}


    // Delete a comment
    public function destroy(Request $request, int $commentId): JsonResponse
    {
        $comment = Comment::find($commentId);

        if (!$comment) {
            return response()->json([
                'error' => 'Comment not found.',
            ], 404);
        }

        // Only comment owner or admin can delete
        if ($comment->user_id !== $request->user()->id && !$request->user()->role != "admin") {
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
