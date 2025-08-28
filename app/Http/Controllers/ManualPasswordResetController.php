<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use App\Models\User; 
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPmail; 

class ManualPasswordResetController extends Controller
{
    public function forgetpassword(Request $request)
    {
        // Validate the email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); 
        }

        // Get user
        $user = User::where('email', $request->email)->first();

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
            Mail::raw("Your OTP for password reset is: $otp", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Reset OTP');
            });
            return response()->json(['message' => 'OTP sent successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Cannot send mail now, please try again later'], 500);
        }

    }
public function otpverification(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'otp'   => 'required|digits:4',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Get user
    $user = User::where('email', $request->email)->first();

    if($user->otp == null)
        {
        return response()->json(['error'=>'Please resend the OTP'],422);
        }
    
    if (!$user) 
        {
        return response()->json(['error' => 'User not found'], 404);
        }



    // Verify OTP
    if ($user->otp != $request->otp) {
        return response()->json(['error' => 'OTP is incorrect, please try again'], 422);
    }

    // Success
    return response()->json(['message' => 'OTP verified successfully'], 200);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:4',
        'password' => 'required|min:6',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['error' => 'Email not found'], 404);
    }

    if ($user->otp != $request->otp) {
        return response()->json(['error' => 'Invalid OTP'], 400);
    }

    $user->password = bcrypt($request->password);
    $user->otp = null;
    $user->save();

    return response()->json(['message' => 'Password reset successful'], 200);
}

}
