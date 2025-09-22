<?php

namespace App\Http\Controllers;

use App\Http\Services\RoleService;
use App\Http\Requests\RoleChangeRequest;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    //  request for role change by guest
    public function requestrole(RoleChangeRequest $request)
    {
        return $this->roleService->handleRole($request->validated());
    }

    //  view the status of role change request
    public function viewstatus()
    {    
        return $this->roleService->handleStatus();
    }
}
