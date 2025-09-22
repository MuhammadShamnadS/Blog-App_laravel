<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordChangeService
{
    public function handlePasswordChange($validatedData)
    {
        $user = auth()->user();
        if ($user->is_manual == 1) {
            return response()->json(['error' => 'Method not allowed']);
        }
        if (Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['error' => 'Please choose a different password']);
        }
        $user->password = bcrypt($validatedData['password']);
        $user->save();
        return response()->json(['message' => 'Password changed successfully']);
    }
}
