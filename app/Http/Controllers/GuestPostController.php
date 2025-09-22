<?php

namespace App\Http\Controllers;

use App\Http\Services\GuestPostService;

class GuestPostController extends Controller
{
    protected $guestPost;
    public function __construct(GuestPostService $guestPost)
    {
        $this->guestPost = $guestPost;
    }
    
    //  view post by guest
    public function index()
    {
        return $this->guestPost->viewPost();
    }

    //  view single post by guest
    public function singlePost($id)
    {
        return $this->guestPost->viewSinglePost($id);
    }
}
