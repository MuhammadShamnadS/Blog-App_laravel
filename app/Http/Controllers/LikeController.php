<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeToggleRequest;
use App\Http\Services\PostLikeService;

class LikeController extends Controller
{
    protected $postLikeService;
    public function __construct(PostLikeService $postLikeService)
    {
        $this->postLikeService = $postLikeService;
    }
    //  toggle like/unlike by guest
    public function toggle(LikeToggleRequest $request)
    {
        return $this->postLikeService->handleLikeToggle($request->validated());
    }
    
    //  get like countt of a post
    public function postLike($id)
    {
        return $this->postLikeService->fetchPostLike($id);
    }
}
