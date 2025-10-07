<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitReviewRequest;
use App\Http\Services\EditorPostService;


class EditorPostController extends Controller
{
    protected $editorPost;
    public function __construct(EditorPostService $editorPost)
    {
        $this->editorPost = $editorPost;
    }

    //  get posts assigned and that are still pending review by editor
    public function myAssignedPosts()
    {
        return $this->editorPost->fetchAssignedPost();
    }

    //  get review of a post by  editor
    public function viewReview($reviewId)
    {
        return $this->editorPost->fetchReview($reviewId);
    }

    //  review the post by editor
    public function reviewPost(SubmitReviewRequest $request, $reviewId)
    {
        return $this->editorPost->reviewPost($reviewId, $request->validated());
    }
}
