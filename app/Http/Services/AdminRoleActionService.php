<?php

namespace App\Http\Services;

use App\Models\Editor;
use App\Models\RoleRequest;

class AdminRoleActionService
{
    //  take decision on role request from guests by admin
    public function roleDecision($validatedData, $id)
    {
        $roleRequest = RoleRequest::findOrFail($id);

        if ($roleRequest->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 422);
        }

        if ($validatedData['action'] === 'approve') {
            $roleRequest->status = 'approved';
            $roleRequest->save();


            $roleRequest->user->update(['role' => $roleRequest->requested_role]);

            if ($roleRequest->requested_role === 'editor') {
                if (empty($validatedData['category_id'])) {
                    return response()->json(['message' => 'Category ID is required for editor role'], 422);
                }

                Editor::updateOrCreate(
                    ['user_id' => $roleRequest->user_id],
                    ['category_id' => $validatedData['category_id']]
                );
            }

            return response()->json(['message' => 'Role approved and updated']);
        }

        if ($validatedData['action'] === 'reject') {
            $roleRequest->status = 'rejected';
            $roleRequest->save();

            return response()->json(['message' => 'Role request rejected']);
        }
        return response()->json(['message' => 'Invalid action'], 400);
    }

    // list pending role requests by admin
    public function viewRoleRequests()
    {
        $requests = RoleRequest::with('user')->where('status', 'pending')->get();
        return response()->json($requests);
    }

    // list role request histories by admin
    public function fetchRoleRequestHistory()
    {
        $requests = RoleRequest::with('user')->where('status', ['approved', 'rejected'])->get();
        return response()->json($requests);
    }
}
