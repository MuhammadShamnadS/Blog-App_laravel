<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Services\RegisterUserService;

class RegisterController extends Controller
{
  protected $registerUserService;

    public function __construct(RegisterUserService $registerUserService)
    {
        $this->registerUserService = $registerUserService;
    }
    
    //  register a user 
    public function register(RegisterRequest $request)
    {

        return $this->registerUserService->registerUser($request->validated());
    }

}
