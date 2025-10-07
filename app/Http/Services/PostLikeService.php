<?php

namespace App\Http\Services;

use App\Models\Like;
use App\Models\Post;

class PostLikeService
{
public function handleLikeToggle($validatedData)
{
    $user = auth()->user();
    $post = Post::withoutGlobalScope('parentNotDeleted')->findOrFail($validatedData['post_id']);

    if ($post->deleted_at != null) {
        return response()->json([
            'error' => 'Cannot like a deleted post.'
        ], 403);
    }

    $like = Like::withoutGlobalScope('parentNotDeleted')->where('post_id', $post->id)
        ->where('user_id', $user->id)
        ->first();

    if ($like) {
        $like->delete();

        return response()->json([
            'message' => 'Post unliked successfully.',
            'liked' => false,
        ]);
    }

    Like::create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    return response()->json([
        'message' => 'Post liked successfully.',
        'liked' => true,
    ]);
}


    public function fetchPostLIke($id)
    {
        $post = Post::withoutGlobalScope('parentNotDeleted')->findorfail($id);
if($post->deleted_at != null)
{
    return response()->json([
            'error' => 'Cannot get likes of  a deleted post.'
        ], 403);
}
$likeCount = Like::withoutGlobalScope('parentNotDeleted')->where('post_id', $id)
    ->whereHas('user', function($q) {
        $q->whereNull('deleted_at');
    })
    ->count();

return response()->json([
    'post_id' => $id,
    'likes'   => $likeCount
]);

    }
}
