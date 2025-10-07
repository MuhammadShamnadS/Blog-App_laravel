<?php

namespace App\Http\Services;

use App\Models\RoleRequest;

class RoleService
{
    public function handleRole($validatedData)
    {
        if (RoleRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists()
        ) {
            return response()->json(['message' => 'You already have a pending request'], 422);
        }

        $user = auth()->user();
        if ($user->role === $validatedData['requested_role']) {
            return response()->json(['error' => "You are already an {$user->role}"], 422);
        }

        if ($user->role !== "guest") {
            return response()->json(['error' => "You are already assigned as {$user->role}"], 422);
        }
        $roleRequest = RoleRequest::create($validatedData  + ['user_id' => auth()->id()]);
        return response()->json([
            'message' => 'Role request submitted',
            'data'    => $roleRequest
        ], 201);
    }

    public function handleStatus()
    {
        $userId = auth()->id();

        $response = RoleRequest::where('user_id', $userId)
            ->latest()
            ->first();

        return response()->json($response);
    }
}
