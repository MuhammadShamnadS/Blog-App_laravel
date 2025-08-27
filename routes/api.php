<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);
Route::post('register',[RegisterController::class,'register']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('me',[AuthController::class,'me']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

});

Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.login');