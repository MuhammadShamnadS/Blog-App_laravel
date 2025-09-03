<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RoleRequest;

class RoleController extends Controller
{
    public function requestrole(Request $request)
    {
     $rules = [
        'requested_role' => 'required|in:author,editor',
    ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Prevent duplicate pending requests
        if (RoleRequest::where('user_id', auth()->id())
                       ->where('status', 'pending')
                       ->exists()) {
            return response()->json(['message' => 'You already have a pending request'], 422);
        }
        $user=auth()->user();
        if ($user->role === $request->requested_role) {
            return response()->json(['error' => "You are already an {$user->role}"], 422);
        }

        if ($user->role !== "guest") {
            return response()->json(['error' => "You are already assigned as {$user->role} by admin"],422);
    }
        
        // Create a new request
        $roleRequest = RoleRequest::create([
            'user_id'       => auth()->id(),
            'requested_role'=> $request->requested_role,
        ]);

        return response()->json([
            'message' => 'Role request submitted',
            'data'    => $roleRequest
        ], 201);
    }

public function viewstatus()
{
    $userId = auth()->id();

    $response = RoleRequest::where('user_id', $userId)
                           ->latest()
                           ->first();

    return response()->json($response);
}

}
