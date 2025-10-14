<?php

namespace App\Http\Services;

use App\Models\Category;
use App\Models\Post;

class AdminCategoryService
{   
    //  add new category by admin
    public function addCategories($validatedData)
    {
        $newCategory = Category::create($validatedData);
        return response()->json($newCategory);
    }

    //  edit category by admin
    public function editCategories($id, $validatedData)
    {

        $existing = Category::findOrFail($id);
        $existing->name = $validatedData['name'];
        $existing->save();

        return response()->json($existing);
    }

    // delete category by admin
    public function deleteCategories($id)
    {
        $category = Category::findorfail($id);
        $category->delete();
        return response()->json(['message' => 'Succesffully removed category']);
    }
}
