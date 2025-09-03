<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RoleRequest;
use App\Models\User;
use App\Models\Editor;

class AdminRoleController extends Controller
{
    public function decision(Request $request, $id)
    {
        $roleRequest = RoleRequest::findOrFail($id);

        if ($roleRequest->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 422);
        }

    // base rules
    $rules = [
        'action' => 'required|in:approve,reject',
    ];

    // only if approving AND requested role is editor
    if ($request->action === 'approve' && $roleRequest->requested_role === 'editor') {
        $rules['category_id'] = 'required|exists:categories,id';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

        if ($request->action === 'approve') {
            $roleRequest->status = 'approved';
            $roleRequest->save();

            // Update user role
            $roleRequest->user->update(['role' => $roleRequest->requested_role]);

            // If editor → assign category
            if ($roleRequest->requested_role === 'editor') {
                Editor::updateOrCreate(
                    ['user_id' => $roleRequest->user_id],
                    ['category_id' => $request->category_id]
                );
            }

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
