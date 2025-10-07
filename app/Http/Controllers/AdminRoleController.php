<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRoleActionRequest;
use App\Http\Services\AdminRoleActionService;

class AdminRoleController extends Controller
{

    protected $adminRoleAction;

    public function __construct(AdminRoleActionService $adminRoleAction)
    {
        $this->adminRoleAction = $adminRoleAction;
    }
    //  take decision on role request from guest by admin
    public function decision(AdminRoleActionRequest $request, $id)
    {
        return $this->adminRoleAction->roleDecision($request->validated(), $id);
    }

    //  list pending role request by admin
    public function viewrolerequest()
    {
        return $this->adminRoleAction->viewRoleRequests();
    }

    // list history or role requests by admin
        public function viewRoleRequestsHistory()
    {
        return $this->adminRoleAction->fetchRoleRequestHistory();
    }
    
}
