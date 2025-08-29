<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    // List all posts of logged-in author
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'author') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $posts = Post::with(['category', 'tags'])
            ->where('author_id', $user->id)
            ->latest()
            ->get();

        return response()->json($posts);
    }

    // Create a new post
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'author') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'status'      => 'required|in:draft,published,archived',
            'category'    => 'required|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ensure category exists (create if not)
        $category = Category::firstOrCreate(['name' => $request->category]);

        // Create post
        $post = Post::create([
            'title'       => $request->title,
            'content'     => $request->content,
            'author_id'   => $user->id,
            'status'      => $request->status,
            'category_id' => $category->id,
        ]);

        // Handle tags
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return response()->json($post, 201);
    }

    // Show single post
    public function show(Post $post)
    {
        $user = Auth::user();

        if ($user->role !== 'author' || $post->author_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($post->load(['category', 'tags']));
    }

    // Update a post
    public function update(Request $request, Post $post)
    {
        $user = Auth::user();

        if ($user->role !== 'author' || $post->author_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|required|string|max:255',
            'content'     => 'sometimes|required|string',
            'status'      => 'sometimes|required|in:draft,published,archived',
            'category'    => 'sometimes|required|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('category')) {
            $category = Category::firstOrCreate(['name' => $request->category]);
            $post->category_id = $category->id;
        }

        $post->update($request->only(['title', 'content', 'status']));

        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return response()->json($post->load(['category', 'tags']));
    }

    // Delete a post
    public function destroy(Post $post)
    {
        $user = Auth::user();

        if ($user->role !== 'author' || $post->author_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->tags()->detach();
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
