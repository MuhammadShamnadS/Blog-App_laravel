<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    // Toggle like/unlike
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
        ]);

        $user = $request->user();

        $like = Like::where('post_id', $request->post_id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            // If already liked → unlike
            $like->delete();

            return response()->json([
                'message' => 'Post unliked successfully.',
                'liked' => false,
            ]);
        }

        // Create new like
        Like::create([
            'post_id' => $request->post_id,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Post liked successfully.',
            'liked' => true,
        ]);
    }

    public function postLike($id)
    {
        $post = Post::findorfail($id);

        if (!$post) {
            return response()->json(['error' => 'Post unavailable']);
        }

        $likeCount = Like::where('post_id', $id)->count();
        return response()->json([
            'post_id' => $id,
            'likes' => $likeCount
        ]);
    }
}
