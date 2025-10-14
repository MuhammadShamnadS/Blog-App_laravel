<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserService
{
    public function listUsers()
    {
        $user= User::where('role','!=','admin')->get();

    }

    public function getUsers(Request $request)
    {
        $name = $request->query('role');

        if (!$name) {
            return response()->json(['error' => 'Please provide a role']);
        }
        if ($name == "admin") {
            return response()->json(['error' => 'Please provide a valid role']);
        }
        $user = User::where('role', $name)->paginate(5);
        return response()->json($user);
    }

    public function deleteUsers($id)
    {
        $user = User::findorfail($id);

        if ($user) {

            $user->delete();
            return response()->json(['message' => 'User deleted']);
        }
    }
}

