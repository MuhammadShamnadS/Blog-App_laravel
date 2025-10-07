<?php

namespace App\Http\Services;

use App\Models\Category;
use App\Models\Tag;

class AdminTagService
{
public function addTags($validatedData, $id)
{
    $user = auth()->user();

    if ($user->role != 'admin') {
        return response()->json(['error' => 'You are not allowed to add tags'], 403);
    }

    $category = Category::findOrFail($id);

    $existingTag = Tag::where('category_id', $id)
        ->where('name', $validatedData['name'])
        ->first();

    if ($existingTag) {
        return response()->json(['error' => 'Tag already exists for this category'], 422);
    }

    $tag = Tag::create([
        'name' => $validatedData['name'],
        'category_id' => $category->id,
    ]);

    return response()->json($tag, 201);
}

public function fetchTags($id)
{
    $tags = Tag::where('category_id',$id)->get();
    return response()->json($tags);
}

    public function editTags($validatedData, $id)
    {
        $user = auth()->user();
        if ($user->role != 'admin') {
            return response()->json(['error' => 'You are not allowed to edit tags'], 403);
        }

        $tag = Tag::findOrFail($id);

        $existingTag = Tag::where('category_id', $tag->category_id)
            ->where('name', $validatedData['name'])
            ->where('id', '!=', $id)
            ->first();

        if ($existingTag) {
            return response()->json(['error' => 'Tag already exists for this category'], 422);
        }

        $tag->name = $validatedData['name'];
        $tag->save();

        return response()->json($tag);
    }

    public function deleteTag($id)
    {
        $user = auth()->user();
        if ($user->role != 'admin') {
            return response()->json(['error' => 'You are not allowed to delete tags'], 403);
        }

        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully'], 200);
    }

}