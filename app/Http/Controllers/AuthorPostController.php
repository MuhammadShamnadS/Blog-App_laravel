<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorPostCreateRequest;
use App\Http\Requests\AuthorPostUpdateRequest;
use App\Http\Services\PostService;



class AuthorPostController extends Controller
{
    protected $postService;
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    // List posts
    public function viewPost()
    {
        return $this->postService->viewPost();
    }


    // Show single post
    public function viewSinglePost($id)
    {
        return $this->postService->viewSinglePost($id);
    }

        public function deleteSinglePost($id)
    {
        return $this->postService->deletePost($id);
    }

    // Create post
    public function createPost(AuthorPostCreateRequest $request)
    {
        return $this->postService->createPost($request->validated());
    }
        public function listTags($id)
    {
        return $this->postService->fetchTags($id);              
    }

    // Update post
    public function editPost(AuthorPostUpdateRequest $request, $id)
    {
        return $this->postService->updatePost($request->validated(), $id);
    }

    //list category
    public function category()
    {
        return $this->postService->categoryList();
    }

    //media delete
    public function handleMediaDelete($id)
    {
        return $this->postService->deleteMedia($id);
    }

    //resubmit post
    public function resubmitPost($id)
    {
        return $this->postService->handleResubmitPost($id);
    }
}
