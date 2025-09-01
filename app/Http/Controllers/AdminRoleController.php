<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RoleRequest;
use App\Models\User;

class AdminRoleController extends Controller
{
   public function decision(Request $request, $id)
{
    $validator=Validator::make($request->all(),[
        'action' => 'required|in:approve,reject'
    ]);
    if($validator->fails())
    {
        return response()->json($validator->errors(),422);
    }

    $roleRequest = RoleRequest::findOrFail($id);

    if ($roleRequest->status !== 'pending') {
        return response()->json(['message' => 'Request already processed'], 422);
    }

    if ($request->action === 'approve') {
        $roleRequest->status = 'approved';
        $roleRequest->save();

        // Update user role
        $roleRequest->user->update(['role' => $roleRequest->requested_role]);

        return response()->json(['message' => 'Role approved and updated']);
    }

    if ($request->action === 'reject') {
        $roleRequest->status = 'rejected';
        $roleRequest->save();

        return response()->json(['message' => 'Role request rejected']);
    }
}

public function viewrolerequest()
{
    $requests = RoleRequest::with('user')->get();
    return response()->json($requests);
}

}
