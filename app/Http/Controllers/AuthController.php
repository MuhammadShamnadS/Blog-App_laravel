<?php

namespace App\Http\Controllers;
use App\Http\Requests\LoginRequest;
use App\Http\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService=$authService;
    }

    //  login
    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    //  get details of logged in user
    public function me()
    {
        return $this->authService->me();
    }

    //  logout
    public function logout(AuthService $authService)
    {
        return $authService->logout();
    }

}
