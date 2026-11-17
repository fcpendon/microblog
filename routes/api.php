<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function () {
    return Auth::user() ? Auth::user()->only(['name', 'username', 'email', 'email_verified_at', 'public_image_path']) : null;
});

Route::post('/register', [RegistrationController::class, 'register']);
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->middleware('throttle:password-reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:password-reset');
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationNotification'])
    ->middleware(['auth:sanctum', 'throttle:email-verification']);

Route::group(['middleware' => ['auth:sanctum', 'verified']], function() {
    Route::get('/search-user', [UserController::class, 'search']);
    Route::get('/profiles/{user}', [UserController::class, 'get_profile']);
    Route::post('/settings/details', [SettingsController::class, 'update_details']);
    Route::post('/settings/password', [SettingsController::class, 'update_password']);
    Route::post('/settings/upload-avatar', [SettingsController::class, 'upload_avatar']);
    Route::get('/posts/{user:username?}', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::post('/posts/{post}/like', [PostController::class, 'like']);
    Route::delete('/posts/{post}/like', [PostController::class, 'unlike']);
    // Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    // Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    // Route::put('/comments/{comment}', [CommentController::class, 'update']);
    // Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    // Route::post('/comments/{comment}/like', [CommentController::class, 'like']);
    // Route::delete('/comments/{comment}/like', [CommentController::class, 'unlike']);
});
