<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordService
{
    public function handleForgotPassword($validatedData)
    {
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'User with this email not found'], 404);
        }

        // Check if user is manual
        if ($user->is_manual == 1) {
            return response()->json(['error' => 'Please login using your Google account'], 403);
        }

        // Generate OTP
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();

        // Send email
        try {
            Mail::raw("Hi $user->name , \n Your OTP for password reset is: $otp", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset OTP');
            });
            return response()->json(['message' => 'OTP sent successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Cannot send mail now, please try again later'], 500);
        }
    }

    public function handleOtpVerification($validatedData)
    {
        // Get user
        $user = User::where('email', $validatedData['email'])->first();

        if ($user->otp == null) {
            return response()->json(['error' => 'Please resend the OTP'], 422);
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }



        // Verify OTP
        if ($user->otp != $validatedData['otp']) {
            return response()->json(['error' => 'OTP is incorrect, please try again'], 422);
        }

        // Success
        return response()->json(['message' => 'OTP verified successfully'], 200);
    }
    public function handleResetPassword($validatedData)
    {
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'Email not found'], 404);
        }

        if ($user->otp != $validatedData['otp']) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        $user->password = bcrypt($validatedData['password']);
        $user->otp = null;
        $user->save();

        return response()->json(['message' => 'Password reset successful'], 200);
    }
}
