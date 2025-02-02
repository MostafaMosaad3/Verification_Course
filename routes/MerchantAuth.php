<?php

use App\Http\Controllers\EmailVerificaionTokenController;
use App\Http\Controllers\MerchantAuth\AuthenticatedSessionController;
use App\Http\Controllers\MerchantAuth\EmailVerificationNotificationController;
use App\Http\Controllers\MerchantAuth\EmailVerificationPromptController;
use App\Http\Controllers\MerchantAuth\RegisteredUserController;
use App\Http\Controllers\MerchantAuth\VerifyEmailController;
use App\Http\Controllers\OtpAuthenticationController;
use App\Http\Controllers\PasswordlessVerificationController;
use App\Http\Middleware\GuestMiddleware;
use App\Http\Middleware\MerchantMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([GuestMiddleware::class .':merchant'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    if(config('verification.way') == 'passwordless')
    {
        Route::post('login', [PasswordlessVerificationController::class, 'store']);
        Route::get('verify-email/{id}', [PasswordlessVerificationController::class , 'verify'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('login.verify');
    }
    elseif(config('verification.way') == 'otp')
    {
        Route::post('login', [OtpAuthenticationController::class, 'store']);
        Route::post('verify-otp', [OtpAuthenticationController::class, 'verify'])->name('verify.otp');
    }
    else
    {
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    }
});

Route::middleware(MerchantMiddleware::class)->group(function () {
    if (config('verification.way') == 'email')
    {
        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

    }

    if (config('verification.way') == 'cvt')
    {
        Route::get('verify-email', [EmailVerificaionTokenController::class , 'notice'])
            ->name('verification.notice');

        Route::get('verify-email/{id}/{token}', [EmailVerificaionTokenController::class , 'verify'])
            ->middleware(['throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificaionTokenController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

    }
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
