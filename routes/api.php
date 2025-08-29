<?php

use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ManualPasswordResetController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);
Route::post('register',[RegisterController::class,'register']);
Route::post('forget-password',[ManualPasswordResetController::class,'forgetpassword']);
Route::post('verify-otp',[ManualPasswordResetController::class,'otpverification']);
Route::post('reset-password',[ManualPasswordResetController::class,'resetPassword']);




Route::middleware(['auth:api'])->group(function () {
    Route::post('/role-decision/{id}', [AdminRoleController::class, 'decision']);
    Route::get('/pending-requests',[AdminRoleController::class,'viewrolerequest']);
    Route::get('/requeststatus',[RoleController::class,'viewstatus']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('me',[AuthController::class,'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password',[PasswordChangeController::class,'Changepassword']);
    Route::post('/role-request', [RoleController::class, 'requestrole']);
    


});

Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.login');