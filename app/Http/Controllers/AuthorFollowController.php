<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowToggleRequest;
use App\Http\Requests\SubscribeAuthorsRequest;
use App\Http\Services\GuestFollowAuthorService;


class AuthorFollowController extends Controller
{
    protected $guestFollowAuthor;


    public function __construct(GuestFollowAuthorService $guestFollowAuthor)
    {
        $this->guestFollowAuthor = $guestFollowAuthor;
    }
    //list authors by guest
    public function authors()
    {
        return $this->guestFollowAuthor->listAuthors();
    }
    //follow author by guest
    public function followToggle(FollowToggleRequest $request)
    {
        return $this->guestFollowAuthor->follow($request->validated());
    }
    //view each author profile by guest
    public function authorProfile($id)
    {
        return $this->guestFollowAuthor->viewAuthorProfile($id);
    }
    //show single author follow by guest
    public function singleAuthorFollow($id)
    {
        return $this->guestFollowAuthor->fetchSingleAuthorFollow($id);
    }

    public function subscribeToggle(SubscribeAuthorsRequest $request)
    {
        return $this->guestFollowAuthor->subscribe($request->validated());
    }
}
