<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class GuestPostController extends Controller
{
    public function index()
    {
        if(auth()->user()->role !== "guest"){
            return response()->json(['error'=> 'You are not a guest']);
        }
        $post = Post::with('media','author')->where('status', 'published')->get();
        return response()->json($post);
    }


   public function singlePost($id)
    {


 if (auth()->user()->role !== "guest") {
return response()->json(['error' => 'You are not a guest'], 403);
}
    
    $is_liked = Like::where('post_id', $id)
                    ->where('user_id', auth()->id())
                    ->exists();

        $post = Post::with(['category','tags', 'media', 'author'])->findOrFail($id);
        if($post->status != "published") return;
                if(!$post){
            return response()->json(['error' => 'This post is unavailable'], 404);
        }
        
        return response()->json([$post,'is_liked' => $is_liked]);
    }

    public function like(Request $request)
    {

    }
}
