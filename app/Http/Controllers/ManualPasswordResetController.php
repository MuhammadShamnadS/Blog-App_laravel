<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\OtpVerificationRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Services\ForgotPasswordService;

class ManualPasswordResetController extends Controller
{

    protected $forgotPassword;

    public function __construct(ForgotPasswordService $forgotPassword)
    {
        $this->forgotPassword = $forgotPassword;
    }
    
    // forget-password by user
    public function forgetpassword(ForgotPasswordRequest $request)
    {
        return $this->forgotPassword->handleForgotPassword($request->validated());
    }

    //  otp verification
    public function otpverification(OtpVerificationRequest $request)
    {
        return $this->forgotPassword->handleOtpVerification($request->validated());
    }

    // reset-password by user
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->forgotPassword->handleForgotPassword($request->validated());
    }
}
