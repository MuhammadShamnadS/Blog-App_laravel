<?php

namespace App\Http\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthService
{
    public function login(array $credentials)

    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $user = auth()->user();
            if ($user->is_blocked == 1) {
                return response()->json(['error' => 'You are blocked by system admin, Please contact admin'], 401);
            }

            return response()->json([
                'token' => $token,
                'user'=> [
                'user_id'  => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role
                ]
            ], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    public function me()
    {
        $user = auth()->user();
        return response()->json($user);
    }


    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout Successfully']);
    }
}
