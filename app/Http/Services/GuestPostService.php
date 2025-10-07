<?php

namespace App\Http\Services;

use App\Models\Category;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;

class GuestPostService
{
    public function viewPost()
    {
        $user = auth()->user();

        if ($user->role !== "guest") {
            return response()->json(['error' => 'You are not a guest'], 403);
        }

        $followedAuthorIds = Follow::where('guest_id', $user->id)
            ->pluck('author_id')
            ->toArray();

        $posts = Post::with('media', 'author')
            ->where('status', 'published')
            ->whereIn('author_id', $followedAuthorIds)
            ->inRandomOrder()
            ->get();

        return response()->json($posts);
        if (!$post) {
            return response()->json(['error' => 'This post is unavailable'], 404);
        }
    }

    public function viewSinglePost($id)
    {
        if (auth()->user()->role !== "guest") {
            return response()->json(['error' => 'You are not a guest'], 403);
        }

        $is_liked = Like::where('post_id', $id)
            ->where('user_id', auth()->id())
            ->exists();

        $post = Post::withoutGlobalScope('parentNotDeleted')->with(['category', 'tags', 'media', 'author'])->findOrFail($id);
        if ($post->status != "published") return;
        if (!$post) {
            return response()->json(['error' => 'This post is unavailable'], 404);
        }

        return response()->json([$post, 'is_liked' => $is_liked]);
    }

        public function fetchCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function fetchTags($id)
{
    $tags = Tag::where('category_id',$id)->get();
    return response()->json($tags);
}
}
