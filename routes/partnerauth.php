<?php

use App\Http\Controllers\PartnerAuth\AuthenticatedSessionController;
use App\Http\Controllers\PartnerAuth\ConfirmablePasswordController;
use App\Http\Controllers\PartnerAuth\EmailVerificationNotificationController;
use App\Http\Controllers\PartnerAuth\EmailVerificationPromptController;
use App\Http\Controllers\PartnerAuth\NewPasswordController;
use App\Http\Controllers\PartnerAuth\PasswordController;
use App\Http\Controllers\PartnerAuth\PasswordResetLinkController;
use App\Http\Controllers\PartnerAuth\RegisteredUserController;
use App\Http\Controllers\PartnerAuth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Prefixed with 'partner' so these URIs don't collide with the identically
// named candidate-facing routes in routes/auth.php (e.g. /register,
// /forgot-password, /reset-password/{token}, /verify-email, /confirm-password,
// /password) - previously unprefixed here, so being required after auth.php
// meant they silently shadowed the candidate routes of the same URI.
Route::group(['middleware' => ['guest:partner'],'prefix' => 'partner'],function(){
    Route::get('register', [RegisteredUserController::class, 'create'])->name('partner.register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login page stays at the group root so the URL remains /partner,
    // matching RouteServiceProvider::PARTNER_HOME and the Authenticate
    // middleware's is('partner') / is('partner/*') checks.
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('partner.login');

    Route::post('/', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('partner.password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('partner.password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('partner.password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('partner.password.store');
});

Route::group(['middleware' => ['auth:partner'],'prefix' => 'partner'],function(){

    Route::get('verify-email', EmailVerificationPromptController::class)->name('partner.verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('partner.verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('partner.verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('partner.password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('partner.password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('partner.logout');
});
