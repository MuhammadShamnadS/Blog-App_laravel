<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordChangeRequest;
use App\Http\Services\PasswordChangeService;

class PasswordChangeController extends Controller
{
    protected $passwordChangeService;

    public function __construct(PasswordChangeService $passwordChangeService)
    {
        $this->passwordChangeService = $passwordChangeService;
    }

    //  password change after login
    public function Changepassword(PasswordChangeRequest $request)
    {
        return $this->passwordChangeService->handlePasswordChange($request->validated());
    }
}
