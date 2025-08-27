<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
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

        // Find or create user
        $user = User::updateOrCreate(
            ['email' => $googleUser->email], 
            [
                'username' => explode('@', $googleUser->getEmail())[0],
                'name' => $googleUser->getName(),
                'provider'    => 'google',
                'provider_id' => $googleUser->getId(),
                // 'password' => bcrypt(Str::random(16)), 
                'role' => 'guest',
            ]
        );

        // Create JWT token
            $token = JWTAuth::fromUser($user);

           return redirect("http://localhost:5173/google-success?token=$token");

    } 
}
