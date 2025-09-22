<?php
namespace App\Http\Services;

use App\Models\User;

class AdminUserService
{
    public function listUsers()
    {
        $user= User::where('role','!=','admin')->get();
        return response()->json($user);
    }

    public function deleteUsers($id)
    {
        $user = User::findorfail($id);
        if($user)
        {
            $user->delete();
            return response()->json(['message' => 'User deleted']);
        }
    }
}