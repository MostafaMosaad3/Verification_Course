<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\MerchantEnsureEmailsVerifiedMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('merchant')->name('merchant.')->group(function () {
    Route::middleware([\App\Http\Middleware\MerchantMiddleware::class , MerchantEnsureEmailsVerifiedMiddleware::class])->group(function () {
        Route::view('/' , 'merchant.home')->name('index');
    });

    require __DIR__.'/MerchantAuth.php';

});

require __DIR__.'/auth.php';
