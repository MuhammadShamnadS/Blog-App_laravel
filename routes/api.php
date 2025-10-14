<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\EditorPostController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminStatsController;
use App\Http\Controllers\AdminTagController;
use App\Http\Controllers\AdminUserBlockController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorFollowController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GuestPostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ManualPasswordResetController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthorPostController;
use App\Http\Controllers\CategoryBulkImportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchAndFilterController;
use Illuminate\Http\Request;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [RegisterController::class, 'register']);
Route::post('forget-password', [ManualPasswordResetController::class, 'forgetpassword']);
Route::post('verify-otp', [ManualPasswordResetController::class, 'otpverification']);
Route::post('reset-password', [ManualPasswordResetController::class, 'resetPassword']);
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.login');

Route::middleware('auth:api')->post('/webpush/subscribe', function (Request $request) {
    $request->user()->updatePushSubscription(
        $request->input('endpoint'),
        $request->input('keys.p256dh'),
        $request->input('keys.auth')
    );

    return response()->json(['success' => true]);
});


Route::middleware(['auth:api', 'role:admin,guest,author,editor'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [PasswordChangeController::class, 'Changepassword']);
    Route::get('/tags', [AuthorPostController::class, 'tags']);
});

#admin
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/get-users',[AdminUserController::class,'fetchUser']);  
    Route::delete('/delete-user/{id}',[AdminUserController::class,'userDelete']);  
    Route::post("block-user/{id}", [AdminUserBlockController::class, 'blockUser']);
    Route::post("unblock-user/{id}", [AdminUserBlockController::class, 'unblockUser']);
    Route::get('admin/dashboard-stats', [AdminStatsController::class, 'dashboardStats']);
    Route::post('/role-decision/{id}', [AdminRoleController::class, 'decision']);
    Route::get('/pending-requests', [AdminRoleController::class, 'viewrolerequest']);
    Route::get('/role-requests-history', [AdminRoleController::class, 'viewRoleRequestsHistory']);
    Route::get('/admin/published-posts', [AdminPostController::class, 'viewPublishedPost']);
    Route::get('/admin/archieved-posts', [AdminPostController::class, 'viewArchievedPost']);
    Route::get('/admin/featured-posts', [AdminPostController::class, 'viewFeaturedPost']);
    Route::get('/admin/scheduled-posts', [AdminPostController::class, 'viewScheduledPost']);
    Route::get('/categories', [AdminPostController::class, 'category']);
    Route::get('/admin/posts/submitted', [AdminPostController::class, 'submittedPosts']);
    Route::get('/categories/{category}/editors', [AdminPostController::class, 'editors']);
    Route::post('/admin/posts/{post}/assign-editor', [AdminPostController::class, 'assignEditor']);
    Route::get('admin/posts/{id}/', [AdminPostController::class, 'show']);
    Route::post('/posts/{id}/publish', [AdminPostController::class, 'publish']);
    Route::post('/posts/{id}/archive', [AdminPostController::class, 'archive']);
    Route::post('/posts/{id}/un-archive', [AdminPostController::class, 'unarchive']);
    Route::delete('/delete-post/{id}', [AdminPostController::class, 'destroy']);
    Route::post('posts/{id}/schedule', [AdminPostController::class, 'schedulePost']);
    Route::patch('/posts/{id}/feature', [AdminPostController::class, 'toggleFeatured']);
    Route::get('posts/editor-approved', [AdminPostController::class, 'getEditorApprovedPosts']);
    Route::post('add-categories', [AdminCategoryController::class, 'createCategory']);
    Route::put('edit-category/{id}', [AdminCategoryController::class, 'editCategory']);
    Route::delete('delete-category/{id}', [AdminCategoryController::class, 'deleteCategory']);
    Route::post('/categories/{id}/tags', [AdminTagController::class, 'addTags']);
    Route::get('/categories/{id}/tags', [AdminTagController::class, 'listTags']);
    Route::put('tag/{id}', [AdminTagController::class, 'editTag']);
    Route::delete('tag/{id}', [AdminTagController::class, 'deleteTag']);
    Route::get('admin/posts/{id}/comments', [CommentController::class, 'index']);
    Route::delete('admin/comments/{commentId}', [CommentController::class, 'destroy']);
    Route::post('admin/import/category', [CategoryBulkImportController::class, 'importCsv']); 
    Route::get('admin/posts/{id}/spam-comments', [CommentController::class, 'listSpam']);


});

#author 
Route::middleware(['auth:api', 'role:author'])->group(function () {
    Route::get('posts', [AuthorPostController::class, 'viewPost']);
    Route::get('posts/{id}', [AuthorPostController::class, 'viewSinglePost']);
    Route::delete('posts/{id}', [AuthorPostController::class, 'deleteSinglePost']);
    Route::post('create-post', [AuthorPostController::class, 'createPost']);
    Route::put('update-post/{id}', [AuthorPostController::class, 'editPost']);
    Route::post('posts/{id}/resubmit', [AuthorPostController::class, 'resubmitPost']);
    Route::delete('/media/{id}', [AuthorPostController::class, 'handleMediaDelete']);
    Route::get('/available-categories', [AuthorPostController::class, 'category']);
    Route::get('/categories/{id}/available-tags', [AuthorPostController::class, 'listTags']);
});

#editors
Route::middleware(['auth:api', 'role:editor'])->group(function () {
    Route::get('/editor/posts', [EditorPostController::class, 'myAssignedPosts']);
    Route::post('/editor/reviews/{review}', [EditorPostController::class, 'reviewPost']);
    Route::get('/editor/reviews/{id}', [EditorPostController::class, 'viewReview']);
});

#guests
Route::middleware(['auth:api', 'role:guest'])->group(function () {
    Route::get('guest/posts', [GuestPostController::class, 'index']);
    Route::get('/requeststatus', [RoleController::class, 'viewstatus']);
    Route::post('/role-request', [RoleController::class, 'requestrole']);
    Route::get('guest/post/{id}', [GuestPostController::class, 'singlePost']);
    Route::get('post/{id}/likes', [LikeController::class, 'postLike']);
    Route::post('/like/toggle', [LikeController::class, 'toggle']);
    Route::get('/posts/{postId}/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{commentId}', [CommentController::class, 'destroy']);
    Route::post('/comments/{commentId}', [CommentController::class, 'reportSpam']);
    Route::get('/authors', [AuthorFollowController::class, 'authors']);
    Route::post('author/follow', [AuthorFollowController::class, 'followToggle']);
    Route::get('/authors/{id}/profile', [AuthorFollowController::class, 'authorProfile']);
    Route::get('/authors/{id}/follow-toggle', [AuthorFollowController::class, 'singleAuthorFollow']);
    Route::get('/guests/followed-authors', [AuthorFollowController::class, 'getFollowList']);
    Route::post('/author/subscribe', [AuthorFollowController::class, 'subscribeToggle']);
    Route::get('/search', [SearchAndFilterController::class, 'searchFilter']);
    Route::get('/filter', [SearchAndFilterController::class, 'nameFilter']);
    Route::get('/get-available-categories', [GuestPostController::class, 'getCategories']);
    Route::get('category/{id}/available-tags', [GuestPostController::class, 'getTags']);
    Route::get('guest/posts/{id}/spam-comments', [CommentController::class, 'listSpam']);

});
