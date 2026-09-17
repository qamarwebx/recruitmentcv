<?php

use App\Http\Controllers\PartnerLanguageController;
use App\Http\Controllers\Worker\PartnerAuthController;
use App\Http\Controllers\Worker\PartnerHireController;
use App\Http\Controllers\Worker\PartnerPortalController;
use App\Http\Controllers\Worker\WorkerPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Worker Frontend Routes
|--------------------------------------------------------------------------
|
| These routes are only ever loaded for requests to the worker.qamarhire.com
| domain (see App\Providers\RouteServiceProvider). Keep this file limited to
| the Worker portal pages only - admin/backend routes belong in web.php.
|
*/

Route::get('/', [WorkerPageController::class, 'home'])->name('worker.home');
Route::get('/resumes', [WorkerPageController::class, 'resumes'])->name('worker.resumes');
Route::get('/resumes/details/{id}', [WorkerPageController::class, 'resumeDetails'])->name('worker.resume.details');
Route::get('/privacy-policy', [WorkerPageController::class, 'privacyPolicy'])->name('worker.privacy');
Route::get('/terms-of-service', [WorkerPageController::class, 'termsOfService'])->name('worker.terms');
Route::get('/about-us', [WorkerPageController::class, 'aboutUs'])->name('worker.about');
Route::get('/contact-us', [WorkerPageController::class, 'contactUs'])->name('worker.contact');
Route::post('/contact-us', [WorkerPageController::class, 'contactUsStore'])->name('worker.contact.store');

// Public language switch (no auth required - anyone browsing the site can
// switch). Reuses the same PartnerLanguageController/session/middleware as
// the portal's switcher (see worker.partner.lang.switch below) - one
// centralized mechanism, not a duplicate.
Route::get('/lang/{locale}', [PartnerLanguageController::class, 'SwitchLang'])->name('worker.lang.switch');

// Partner login/register modal completion steps. OTP send/verify itself
// reuses the existing generate-otp2 / validate-otp2 endpoints (routes/auth.php),
// which are already reachable on this domain (no domain restriction there).
Route::post('/partner/login-otp', [PartnerAuthController::class, 'login'])->name('worker.partner.login');
Route::post('/partner/register-otp', [PartnerAuthController::class, 'register'])->name('worker.partner.register');
Route::post('/partner/check-mobile', [PartnerAuthController::class, 'checkMobile'])->name('worker.partner.check-mobile');
Route::post('/partner/logout', [PartnerAuthController::class, 'logout'])->name('worker.partner.logout');

// Lands here after "Continue with Google" completes on qamarhire.com
// (SocialLoginController::handlePartnerGoogleCallback) and hands off a
// short-lived token to establish a real session on this domain.
Route::get('/partner/google/complete', [PartnerAuthController::class, 'completeGoogleLogin'])->name('worker.partner.google.complete');

// Partner Portal - only reachable once logged in via the OTP flow above,
// which already refuses to establish a session unless registration_status
// is Approved (see PartnerAuthController::login).
Route::middleware('worker.partner.auth')->prefix('partner')->name('worker.partner.')->group(function () {
    Route::get('/portal', [PartnerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [PartnerPortalController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [PartnerPortalController::class, 'orderShow'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [PartnerHireController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/visa', [PartnerPortalController::class, 'orderVisaStore'])->name('orders.visa.store');
    Route::get('/candidates', [PartnerPortalController::class, 'candidates'])->name('candidates');
    Route::get('/candidates/{id}', [PartnerPortalController::class, 'candidateShow'])->name('candidates.show');
    Route::post('/candidates/{id}/hire', [PartnerHireController::class, 'store'])->name('candidates.hire');
    Route::get('/employer', [PartnerPortalController::class, 'employerPlus'])->name('employer');
    Route::post('/employer/store', [PartnerPortalController::class, 'employerStore'])->name('employer.store');
    Route::post('/employer/filter/save', [PartnerPortalController::class, 'employerFilterSave'])->name('employer.filter.save');
    Route::post('/employer/filter/reset', [PartnerPortalController::class, 'employerFilterReset'])->name('employer.filter.reset');
    Route::get('/employer/{id}', [PartnerPortalController::class, 'employerPlusShow'])->name('employer.show');
    Route::post('/employer/{id}/update', [PartnerPortalController::class, 'employerUpdate'])->name('employer.update');
    Route::post('/employer/{id}/delete', [PartnerPortalController::class, 'employerDelete'])->name('employer.delete');
    Route::get('/profile', [PartnerPortalController::class, 'profile'])->name('profile');
    Route::post('/profile', [PartnerPortalController::class, 'profileUpdate'])->name('profile.update');

    // Attaches+verifies a mobile number for the already-logged-in partner
    // (Hire Now gate, Profile page "Add/Change Number") - reuses the same
    // generate-otp2/validate-otp2 endpoints as login/register, just with a
    // different completion step (PartnerAuthController::verifyMobile).
    Route::post('/mobile/verify', [PartnerAuthController::class, 'verifyMobile'])->name('mobile.verify');

    // Reuses the existing PartnerLanguageController (already used elsewhere for
    // the same purpose) - validates against config('app.locales'), stores in
    // session, and LanguageSwitcher (registered globally in Kernel.php) applies
    // it via App::setLocale() on every subsequent request, so it persists.
    Route::get('/lang/{locale}', [PartnerLanguageController::class, 'SwitchLang'])->name('lang.switch');
});
