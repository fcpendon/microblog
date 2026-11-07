<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

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
    Route::get('/posts/{user?}', [PostController::class, 'get_posts']);
    Route::post('/post', [PostController::class, 'create']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/like', [PostController::class, 'like_post']);
    Route::delete('/posts/{id}/like', [PostController::class, 'unlike_post']);
});
