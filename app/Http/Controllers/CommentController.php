<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Services\CommentService;


class CommentController extends Controller
{

    protected $comment;

    public function __construct(CommentService $comment)
    {
        $this->comment = $comment;
    }
    // Create a comment
    public function store(CommentRequest $request)
    {
        return $this->comment->createComment($request->validated());
    }

    // List comments for a post 
    public function index($postId)
    {
        return $this->comment->listComments($postId);
    }

    // Delete a comment
    public function destroy($commentId)
    {
        return $this->comment->deleteComment($commentId);
    }

    public function reportSpam($id)
    {
        return $this->comment->reportComment($id);
    }
        public function listSpam($id)
    {
        return $this->comment->listSpamComments($id);
    }

    
}
