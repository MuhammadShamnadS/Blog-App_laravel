<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Throwable $e) {
            return redirect("http://localhost:5173/login?error=GoogleAuthFailed");
        }

        $user = User::firstOrCreate(


            ['email' => $googleUser->email],
            [
                'name' => $googleUser->getName(),
                'provider'    => 'google',
                'provider_id' => $googleUser->getId(),
                'role' => 'guest',
                'is_manual' => '1',
            ]
        );
        
        $token = JWTAuth::fromUser($user);

        return redirect("http://localhost:5173/google-success?token=$token");
    }
}
