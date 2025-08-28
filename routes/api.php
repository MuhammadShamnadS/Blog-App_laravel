<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ManualPasswordResetController;
use App\Http\Controllers\PasswordChangeController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);
Route::post('register',[RegisterController::class,'register']);
Route::post('forget-password',[ManualPasswordResetController::class,'forgetpassword']);
Route::post('verify-otp',[ManualPasswordResetController::class,'otpverification']);
Route::post('reset-password',[ManualPasswordResetController::class,'resetPassword']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('me',[AuthController::class,'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password',[PasswordChangeController::class,'Changepassword']);

});

Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.login');