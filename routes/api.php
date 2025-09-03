<?php

use App\Http\Controllers\EditorPostController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ManualPasswordResetController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PostController;
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


    Route::get('/admin/posts/submitted', [AdminPostController::class, 'submittedPosts']);
    Route::post('/admin/posts/{post}/assign-editor', [AdminPostController::class, 'assignEditor']);
    Route::get('/editor/posts', [EditorPostController::class, 'myAssignedPosts']);
    Route::post('/editor/reviews/{review}', [EditorPostController::class, 'reviewPost']);
    Route::get('/editor/reviews/{id}', [EditorPostController::class, 'viewReview']);
    Route::get('/categories/{category}/editors', [AdminPostController::class, 'editors']);
    Route::post('posts/{id}/resubmit' ,[PostController::class,'resubmitPost']);
    Route::get('posts/editor-approved' ,[AdminPostController::class,'getEditorApprovedPosts']);
    Route::post('posts/{id}/schedule' ,[AdminPostController::class,'schedulePost']);
    Route::get('admin/posts/{id}/' ,[AdminPostController::class,'show']);
    Route::get('/admin/posts', [AdminPostController::class, 'index']);
    Route::post('/{id}/publish', [AdminPostController::class, 'publish']);
    Route::patch('/{id}/feature', [AdminPostController::class, 'toggleFeatured']);
    
});





Route::middleware(['auth:api'])->group(function () {
    Route::get('me',[AuthController::class,'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password',[PasswordChangeController::class,'Changepassword']);
    Route::post('/role-request', [RoleController::class, 'requestrole']);


    Route::apiResource('posts', PostController::class);
    Route::get('categories',[PostController::class,'category']);
    Route::get('/tags',[PostController::class,'tags']);
    Route::delete('/media/{id}',[PostController::class,'deleteMedia']);
    


});

Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.login');