<?php

namespace App\Http\Services;

use App\Models\Follow;
use App\Models\User;

class GuestFollowAuthorService
{
    public function listAuthors()

    {
        $user = auth()->user();

        if ($user->role != "guest") {
            return response()->json(['error' => 'Not a valid guest'], 403);
        }

        $authors = User::where('role', 'author')->get();

        $authors->transform(function ($author) use ($user) {
            $author->is_followed = Follow::where('guest_id', $user->id)
                ->where('author_id', $author->id)
                ->exists();
            return $author;
        });

        return response()->json($authors);
    }


    public function follow($validatedData)
    {
        $user = auth()->user();

        if ($user->role != "guest") {
            return response()->json(['error' => 'You are not a guest'], 403);
        }
        $author = User::where('id', $validatedData['author_id'])
            ->where('role', 'author')
            ->first();

        if (!$author) {
            return response()->json(['error' => 'Invalid author'], 400);
        }

        $follow = Follow::where('author_id', $validatedData['author_id'])
            ->where('guest_id', $user->id)
            ->first();

        if ($follow) {
            $follow->delete();
            return response()->json(['success' => true, 'action' => 'unfollowed'], 200);
        }

        Follow::create([
            'guest_id'  => $user->id,
            'author_id' => $validatedData['author_id'],
            'subscribed' => '0',
        ]);

        return response()->json(['success' => true, 'action' => 'followed'], 200);
    }
    public function subscribe($validatedData)
    {
        $user = auth()->user();

        if ($user->role != "guest") {
            return response()->json(['error' => 'You are not a guest'], 403);
        }
        $author = User::findorfail($validatedData['author_id']);
        if ($author->role != 'author') {
            return response()->json(['error' => 'Invalid author'], 400);
        }

        $follow = Follow::where('author_id', $validatedData['author_id'])
            ->where('guest_id', $user->id)
            ->first();

        if (!$follow) {
            return response()->json(['error' => 'You must follow the author first'], 400);
        }
        if ($follow->subscribed == 1) {
            $follow->subscribed = 0;
            $follow->save();
            return response()->json(['success' => true, 'action' => 'unsubscribed'], 200);
        } else {
            $follow->subscribed = 1;
            $follow->save();
            return response()->json(['success' => true, 'action' => 'subscribed'], 200);
        }



        return response()->json(['success' => true, 'action' => 'followed'], 200);
    }

    public function viewAuthorProfile($id)
    {
        $author = User::findorfail($id);

        if ($author->role != "author") {
            return response()->json(["error" => "Not a valid author"], 400);
        }
        $profile = User::with([
            'posts' => function ($query) {
                $query->where('status', 'published')->with('media');
            }
        ])->find($id);


        return response()->json($profile);
    }

    public function fetchSingleAuthorFollow($id)
    {
        $user = auth()->user();

        if ($user->role != "guest") {
            return response()->json(['error' => 'You are not a guest'], 403);
        }

        $author = User::where('id', $id)
            ->where('role', 'author')
            ->first();

        if (!$author) {
            return response()->json(['error' => 'Invalid author'], 400);
        }

        $follow = Follow::where('author_id', $id)
            ->where('guest_id', $user->id)
            ->first();
        return response()->json([
            'is_followed'   => $follow ? true : false,
            'is_subscribed' => $follow ? (bool) $follow->subscribed : false,
        ]);
    }
}
