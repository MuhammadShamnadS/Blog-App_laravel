<?php

namespace App\Http\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

class AdminDashboardStatsService
{
    //  view admin dashboard stats
    public function showStats()
    {
        $usersCount = User::where('role', '!=', 'admin')->get()->count();
        $posts = Post::withoutGlobalScope('parentNotDeleted')->orderBy('created_at', 'desc')->take(5)->get();
        $categoriesCount = Category::count();

        return response()->json([
            'users' => $usersCount,
            'posts' => $posts,
            'categories' => $categoriesCount,
        ]);
    }
}
