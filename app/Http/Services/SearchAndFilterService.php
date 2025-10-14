<?php
namespace App\Http\Services;

use App\Models\Post;
use Illuminate\Http\Request;

class SearchAndFilterService
{
public function search(Request $request)
{
    $search = $request->input('search');

    $posts = Post::query()
        ->where('title', 'like', "%{$search}%")
        ->where('status', 'published')
        ->with(['author', 'category', 'tags'])
        ->inRandomOrder()
        ->paginate(5);

    return response()->json($posts);
}       

public function filterByName(Request $request)
{
    $categoryName = $request->input('category');
    $tagName = $request->input('tag');
    $authorName = $request->input('author');           

    $postsQuery = Post::query()->with(['author', 'category', 'tags', 'media'])->where('status', 'published');

    if ($categoryName) {
        $postsQuery->whereHas('category', function($q) use ($categoryName) {
            $q->where('name', $categoryName);
        });
    }

    if ($tagName) {
        $postsQuery->whereHas('tags', function($q) use ($tagName) {
            $q->where('name', $tagName);
        });
    }
    if ($authorName) {
        $postsQuery->whereHas('author', function ($q) use ($authorName) {
            $q->where('name', $authorName);
    
    });
    }
    $posts = $postsQuery->paginate(5)->appends($request->query());

    return response()->json($posts);
}




}