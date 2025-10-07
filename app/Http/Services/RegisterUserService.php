<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterUserService
{
    public function registerUser(array $validatedData)
    {
        $user = User::create($validatedData + ['role' => 'guest']);
        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ], 201);
    }
}
