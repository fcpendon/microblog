<?php

use App\Http\Controllers\EmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:5,1'])
    ->name('verification.verify');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
