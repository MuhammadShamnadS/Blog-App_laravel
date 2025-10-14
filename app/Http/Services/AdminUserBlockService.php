<?php
namespace App\Http\Services;

use App\Models\User;

class AdminUserBlockService
{
    public function userBlock($id)
    {
        $user = User::findorfail($id);
        $user->is_blocked = true;
        $user->save();

        return response() -> json(['message' => 'User blocked successfully',200]);
    }

    public function UserUnblock($id)
    {
        $user = User::findorfail($id);
        $user->is_blocked =false;
        $user->save();

        return response()->json(['message' => 'User activated successfully']);
    }
}