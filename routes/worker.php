<?php

use App\Http\Controllers\PartnerLanguageController;
use App\Http\Controllers\Worker\PartnerAuthController;
use App\Http\Controllers\Worker\PartnerHireController;
use App\Http\Controllers\Worker\PartnerPortalController;
use App\Http\Controllers\Worker\PartnerPaymentController;
use App\Http\Controllers\Worker\PartnerPriceController;
use App\Http\Controllers\Worker\PassportImageController;
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
// Passport + driving licence images (App\Support\PassportAccess - Registration
// Status rule): the blurred copies for the public gallery, and the document
// files themselves (public/.htaccess routes /admin/assets/images/candidate/
// *PPFRONT* / *PPBACK* / *-DL-* here): the original only where the rule
// allows it, else the blurred copy.
Route::get('/resumes/details/{id}/passport-preview', [PassportImageController::class, 'preview'])->name('worker.resume.passport');
Route::get('/resumes/details/{id}/licence-preview', [PassportImageController::class, 'licencePreview'])->name('worker.resume.licence');
Route::get('/admin/assets/images/candidate/{file}', [PassportImageController::class, 'file'])
    ->where('file', \App\Support\PassportAccess::DOCUMENT_FILE_PATTERN)->name('worker.passport.file');
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
// Partner-only aliases of the SAME OTP send/verify methods (no duplicated
// logic). routes/auth.php puts generate-otp2/validate-otp2 behind the
// customer `guest` middleware, which would 302 a browser that is logged in
// as a customer and break partner OTP - customer and partner auth are
// separate guards and must not block each other.
Route::post('/partner/otp/send', [\App\Http\Controllers\Auth\WhatsappOtpController::class, 'generateOtp2'])
    ->middleware(\App\Http\Middleware\ThrottlePartnerOtpSend::class) // per mobile + per IP, config/partner.php
    ->name('worker.partner.otp.send');
Route::post('/partner/otp/verify', [\App\Http\Controllers\Auth\WhatsappOtpController::class, 'validateOtp2'])->name('worker.partner.otp.verify');

// Dedicated Partner login/register page (the legacy email/password
// `partner.login` route is GET /partner, so this URL was free).
Route::get('/partner/login', [PartnerAuthController::class, 'loginPage'])->name('worker.partner.login.page');
Route::post('/partner/login-otp', [PartnerAuthController::class, 'login'])->name('worker.partner.login');
// Login with Password (mobile + country code + password) - same modal/page as OTP login.
Route::post('/partner/login-password', [PartnerAuthController::class, 'passwordLogin'])->name('worker.partner.login.password');
// Login with OTP by username / email: code emailed to that partner's verified email.
Route::post('/partner/login-email-otp/send', [PartnerAuthController::class, 'emailOtpSend'])
    ->middleware(\App\Http\Middleware\ThrottlePartnerOtpSend::class) // per identifier + per IP, config/partner.php
    ->name('worker.partner.login.email-otp.send');
Route::post('/partner/login-email-otp/verify', [PartnerAuthController::class, 'emailOtpVerify'])->name('worker.partner.login.email-otp.verify');
// Forgot Password (Partner Login): email code -> verify -> new password.
Route::post('/partner/password-reset/send', [PartnerAuthController::class, 'passwordResetSend'])
    ->middleware(\App\Http\Middleware\ThrottlePartnerOtpSend::class)
    ->name('worker.partner.password-reset.send');
Route::post('/partner/password-reset/verify', [PartnerAuthController::class, 'passwordResetVerify'])->name('worker.partner.password-reset.verify');
Route::post('/partner/password-reset/update', [PartnerAuthController::class, 'passwordResetUpdate'])->name('worker.partner.password-reset.update');
Route::post('/partner/register-otp', [PartnerAuthController::class, 'register'])->name('worker.partner.register');
// Fired right after Send OTP succeeds (register mode only) - saves the
// submitted details immediately, before the OTP is ever verified, so an
// abandoned registration still leaves a trackable record. register() above
// then finalizes this same row once OTP verification succeeds.
Route::post('/partner/register-pending', [PartnerAuthController::class, 'registerPending'])->name('worker.partner.register-pending');
Route::post('/partner/check-mobile', [PartnerAuthController::class, 'checkMobile'])->name('worker.partner.check-mobile');
Route::post('/partner/logout', [PartnerAuthController::class, 'logout'])->name('worker.partner.logout');

// Live Partners presence: heartbeat / hidden / leave signal of each open tab of a
// signed-in partner or team member (PartnerPresenceController). Throttle keyed
// by IP (partners aren't the default guard), so room for several tabs and
// several people behind one office IP.
Route::post('/partner-presence', \App\Http\Controllers\Worker\PartnerPresenceController::class)
    ->middleware('throttle:120,1')->name('worker.presence');

// Lands here after "Continue with Google" completes on qamarhire.com
// (SocialLoginController::handlePartnerGoogleCallback) and hands off a
// short-lived token to establish a real session on this domain.
Route::get('/partner/google/complete', [PartnerAuthController::class, 'completeGoogleLogin'])->name('worker.partner.google.complete');
// CRM -> Partner -> "Login As Partner": the CRM's one-time, encrypted handoff (PartnerAuthController::completeCrmLogin).
Route::get('/partner/crm-login', [PartnerAuthController::class, 'completeCrmLogin'])->name('worker.partner.crm-login');
// Customer "Continue with Google" started on a partner's Own Domain: the sign-in handed back there.
Route::get('/auth/google/complete', [\App\Http\Controllers\SocialLoginController::class, 'completeCustomerGoogleLogin'])->name('customer.google.complete');

// Partner Portal - only reachable once logged in via the OTP flow above,
// which already refuses to establish a session unless registration_status
// is Approved (see PartnerAuthController::login).
// Legacy qamarhire.com customer booking path, closed on this site. These
// override the identical web.php routes (worker.php is registered last). The
// legacy endpoints (BookingController::store/storeN/storearN) take partner_id
// from the request with no availability/limit checks; on RecruitmentCV all
// hiring goes through worker.account.hire (CustomerHireController), which
// decides the partner server-side. The old Arabic candidate page is sent to
// the current candidate page, shown in Arabic (same session key as the
// language switcher).
Route::get('/ar/resumes/details/{id}', function (\Illuminate\Http\Request $request, $id) {
    $request->session()->put('locale', 'ar');

    return redirect()->route('worker.resume.details', $id, 301);
});
foreach (['resumes/details/booking', 'resumes/details/booking/2', 'resumes/details/booking/3', 'resumes/details/booking/4',
          'ar/resumes/details/booking', 'ar/resumes/details/booking/2', 'ar/resumes/details/booking/3', 'ar/resumes/details/booking/4'] as $legacyBookingUri) {
    Route::post($legacyBookingUri, fn () => abort(404));
}

// Legacy partner-panel order endpoints (PartnerBookingController via
// routes/web.php partner/booking/*), closed on this site: they took any
// booking id with no ownership check (view / confirm / visa / payment /
// cancel any order). The Partner Portal uses /partner/orders and
// /partner/candidates/{id}/hire instead. These override the identical
// web.php routes (same method + URI; worker.php is registered last) and
// answer 404 to everyone - signed-in partners included - on every host
// (recruitmentcv.com, partner subdomains, own domains). Data is untouched.
foreach ([
    ['GET', 'partner/booking'], ['GET', 'partner/booking/getData'], ['GET', 'partner/booking/view/{id}'],
    ['POST', 'partner/booking/sendOTP'], ['POST', 'partner/booking/confirm-otp'], ['POST', 'partner/booking/check-otp'],
    ['POST', 'partner/booking/getVisa'], ['POST', 'partner/booking/getVisa/store'], ['POST', 'partner/booking/getPayment'],
    ['POST', 'partner/booking/getPay/store'], ['POST', 'partner/booking/cancel'], ['POST', 'partner/booking/filterList/update'],
] as [$legacyMethod, $legacyPartnerBookingUri]) {
    Route::match([$legacyMethod], $legacyPartnerBookingUri, fn () => abort(404));
}

// Customer Sign Up: duplicate email/mobile check before the OTP is sent (the
// registration itself re-checks server-side). Rate-limited.
Route::post('/customer/register/check', \App\Http\Controllers\Worker\CustomerRegistrationCheckController::class)
    ->middleware('throttle:20,1')
    ->name('worker.customer.register.check');

// Customer account pages (`web` guard) - main domain and every partner
// subdomain. Guests are sent to the homepage Login modal and brought back.
Route::middleware('worker.customer.auth')->prefix('account')->name('worker.account.')->group(function () {
    Route::redirect('/', '/account/orders');
    Route::get('/orders', [\App\Http\Controllers\Worker\CustomerAccountController::class, 'orders'])->name('orders');
    Route::get('/profile', [\App\Http\Controllers\Worker\CustomerAccountController::class, 'profile'])->name('profile');
    Route::get('/security', [\App\Http\Controllers\Worker\CustomerAccountController::class, 'security'])->name('security');
    Route::get('/wishlist', [\App\Http\Controllers\Worker\CustomerAccountController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\Worker\CustomerAccountController::class, 'wishlistToggle'])->name('wishlist.toggle');
    Route::get('/notifications', [\App\Http\Controllers\Worker\CustomerAccountController::class, 'notifications'])->name('notifications');
    // Customer Hire Now - validates, decides the partner server-side, then
    // runs the existing BookingController::store4().
    Route::post('/hire/{slug}', [\App\Http\Controllers\Worker\CustomerHireController::class, 'store'])->name('hire');
});

Route::middleware('worker.partner.auth')->prefix('partner')->name('worker.partner.')->group(function () {
    Route::get('/portal', [PartnerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [PartnerPortalController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [PartnerPortalController::class, 'orderShow'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [PartnerHireController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/visa', [PartnerPortalController::class, 'orderVisaStore'])->name('orders.visa.store');
    Route::get('/candidates', [PartnerPortalController::class, 'candidates'])->name('candidates');
    Route::get('/candidates/{id}', [PartnerPortalController::class, 'candidateShow'])->name('candidates.show');
    // Download CV = this partner's own B2B CV (executed on demand) - see PartnerPortalController::candidateCv.
    Route::get('/candidates/{id}/cv', [PartnerPortalController::class, 'candidateCv'])->name('candidates.cv');
    // Blurred passport / driving licence image where App\Support\PassportAccess withholds the original (candidate gallery).
    Route::get('/candidates/{id}/passport-preview', [PartnerPortalController::class, 'candidatePassportPreview'])->name('candidates.passport');
    Route::get('/candidates/{id}/licence-preview', [PartnerPortalController::class, 'candidateLicencePreview'])->name('candidates.licence');
    Route::post('/candidates/{id}/hire', [PartnerHireController::class, 'store'])->name('candidates.hire');
    // Hire Now's "Already Hired" check (hired by another customer / partner?) - read-only.
    Route::get('/candidates/{id}/hire-status', [PartnerHireController::class, 'status'])->middleware('throttle:30,1')->name('candidates.hire-status');
    // Partner Activity alerts to CRM staff (App\Support\PartnerActivity): Candidate Detail
    // presence (visit + heartbeats) and Hire Now / Download CV clicks.
    Route::post('/candidates/{id}/activity/visit', [PartnerPortalController::class, 'candidateActivityVisit'])->middleware('throttle:30,1')->name('candidates.activity.visit');
    Route::post('/candidates/{id}/activity/heartbeat', [PartnerPortalController::class, 'candidateActivityHeartbeat'])->middleware('throttle:60,1')->name('candidates.activity.heartbeat');
    Route::post('/candidates/{id}/activity/click', [PartnerPortalController::class, 'candidateActivityClick'])->middleware('throttle:30,1')->name('candidates.activity.click');
    // Payment - this partner's CRM invoices, read-only (view/download PDF).
    Route::get('/payment', [PartnerPaymentController::class, 'index'])->name('payment');
    Route::get('/payment/{id}/view', [PartnerPaymentController::class, 'show'])->whereNumber('id')->name('payment.show');
    Route::get('/payment/{id}/download', [PartnerPaymentController::class, 'download'])->whereNumber('id')->name('payment.download');
    Route::get('/employer', [PartnerPortalController::class, 'employerPlus'])->name('employer');
    Route::post('/employer/store', [PartnerPortalController::class, 'employerStore'])->name('employer.store');
    Route::post('/employer/filter/save', [PartnerPortalController::class, 'employerFilterSave'])->name('employer.filter.save');
    Route::post('/employer/filter/reset', [PartnerPortalController::class, 'employerFilterReset'])->name('employer.filter.reset');
    Route::get('/employer/{id}', [PartnerPortalController::class, 'employerPlusShow'])->name('employer.show');
    Route::post('/employer/{id}/update', [PartnerPortalController::class, 'employerUpdate'])->name('employer.update');
    Route::post('/employer/{id}/delete', [PartnerPortalController::class, 'employerDelete'])->name('employer.delete');
    Route::get('/employer/{id}/assignable-candidates', [PartnerPortalController::class, 'employerAssignableCandidates'])->name('employer.assignable-candidates');
    Route::post('/employer/{id}/assign-candidate', [PartnerPortalController::class, 'employerAssignCandidate'])->name('employer.assign-candidate');
    Route::post('/employer/{id}/deassign-candidate', [PartnerPortalController::class, 'employerDeassignCandidate'])->name('employer.deassign-candidate');

    // Customers = users with users.partner_id = the logged-in partner.
    // Ownership is enforced inside every controller action (404 otherwise).
    // Website Visitor - this partner's own website_visitors records (same data as the CRM page).
    Route::get('/website-visitors', [\App\Http\Controllers\Worker\PartnerWebsiteVisitorController::class, 'index'])->name('website-visitors');
    Route::post('/website-visitors/filter/save', [\App\Http\Controllers\Worker\PartnerWebsiteVisitorController::class, 'saveFilter'])->name('website-visitors.filter.save');
    Route::post('/website-visitors/filter/reset', [\App\Http\Controllers\Worker\PartnerWebsiteVisitorController::class, 'resetFilter'])->name('website-visitors.filter.reset');
    Route::get('/customers', [\App\Http\Controllers\Worker\PartnerCustomersController::class, 'index'])->name('customers');
    Route::post('/customers/store', [\App\Http\Controllers\Worker\PartnerCustomersController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}', [\App\Http\Controllers\Worker\PartnerCustomersController::class, 'show'])->whereNumber('id')->name('customers.show');
    Route::post('/customers/{id}/update', [\App\Http\Controllers\Worker\PartnerCustomersController::class, 'update'])->whereNumber('id')->name('customers.update');
    Route::post('/customers/{id}/unlink', [\App\Http\Controllers\Worker\PartnerCustomersController::class, 'unlink'])->whereNumber('id')->name('customers.unlink');
    // Legacy bookmarked URL - the combined Profile/Settings page is now
    // split into Account (Personal Details) and Website (Company Profile/
    // Branding/Domain); redirects to Account rather than 404ing.
    Route::get('/profile', [PartnerPortalController::class, 'profile'])->name('profile');

    Route::get('/account', [PartnerPortalController::class, 'account'])->name('account');
    Route::post('/account', [PartnerPortalController::class, 'accountUpdate'])->name('account.update');
    Route::post('/account/password', [PartnerPortalController::class, 'accountPasswordUpdate'])->name('account.password');
    // Email change: code emailed to the new address, saved only after verification.
    Route::post('/account/email', [PartnerPortalController::class, 'accountEmailSend'])->name('account.email.send');
    Route::post('/account/email/verify', [PartnerPortalController::class, 'accountEmailVerify'])->name('account.email.verify');

    // Settings -> Price Update (this partner's own price per experience type + profession).
    Route::get('/prices', [PartnerPriceController::class, 'index'])->name('prices');
    Route::post('/prices', [PartnerPriceController::class, 'store'])->name('prices.store');
    Route::post('/prices/{id}', [PartnerPriceController::class, 'update'])->whereNumber('id')->name('prices.update');
    Route::post('/prices/{id}/delete', [PartnerPriceController::class, 'destroy'])->whereNumber('id')->name('prices.destroy');
    Route::get('/website', [PartnerPortalController::class, 'website'])->name('website');

    // Website Config (Home/About/Contact/Privacy/Terms) - one save endpoint,
    // {page} restricted to the known set so an unknown value 404s instead
    // of silently creating a stray row (see PartnerPageContent::PAGES).
    // Contact Us > Branches/Locations - its own endpoint (a list of
    // records with image uploads, not the flat locale-nested text fields
    // the generic {page} endpoint below validates) - registered first so
    // its literal /contact/branches segment is never shadowed by {page}.
    Route::post('/website-config/contact/branches', [PartnerPortalController::class, 'websiteConfigBranchesUpdate'])
        ->name('website-config.branches.update');

    Route::post('/website-config/{page}', [PartnerPortalController::class, 'websiteConfigUpdate'])
        ->where('page', implode('|', \App\Models\PartnerPageContent::PAGES))
        ->name('website-config.update');

    // Settings page tabs (Company Profile / Branding / Domain) - all three
    // read/write the logged-in partner's own Domain row (see
    // PartnerPortalController::settingsCompanyUpdate/settingsLogoUpdate/
    // settingsDomainUpdate for why these aren't the admin-guarded
    // DomainController routes).
    Route::post('/settings/company', [PartnerPortalController::class, 'settingsCompanyUpdate'])->name('settings.company');
    Route::post('/settings/logo', [PartnerPortalController::class, 'settingsLogoUpdate'])->name('settings.logo');
    Route::post('/settings/domain', [PartnerPortalController::class, 'settingsDomainUpdate'])->name('settings.domain');
    Route::post('/settings/whatsapp', [PartnerPortalController::class, 'settingsWhatsappUpdate'])->name('settings.whatsapp');
    // SMTP list (the "smtp" section; names resolve to the website module's
    // "update" action, as the former single save did).
    Route::post('/settings/smtp', [PartnerPortalController::class, 'smtpAdd'])->name('settings.smtp.add');
    Route::post('/settings/smtp/{smtp}', [PartnerPortalController::class, 'smtpSave'])->whereNumber('smtp')->name('settings.smtp.save');
    Route::post('/settings/smtp/{smtp}/remove', [PartnerPortalController::class, 'smtpRemove'])->whereNumber('smtp')->name('settings.smtp.remove');
    Route::post('/settings/smtp/{smtp}/move', [PartnerPortalController::class, 'smtpMove'])->whereNumber('smtp')->name('settings.smtp.move');
    Route::post('/settings/smtp/{smtp}/test', [PartnerPortalController::class, 'smtpTest'])->whereNumber('smtp')->name('settings.smtp.test');

    // Attaches+verifies a mobile number for the already-logged-in partner
    // (Hire Now gate, Profile page "Add/Change Number") - reuses the same
    // generate-otp2/validate-otp2 endpoints as login/register, just with a
    // different completion step (PartnerAuthController::verifyMobile).
    Route::post('/mobile/verify', [PartnerAuthController::class, 'verifyMobile'])->name('mobile.verify');

    // Team Members (owner only - App\Support\PartnerTeam::OWNER_ONLY). Every
    // query is scoped to the logged-in partner, never a request partner_id.
    Route::get('/team-members', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'index'])->name('team-members');
    Route::get('/team-members/create', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'create'])->name('team-members.create');
    Route::post('/team-members', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'store'])->name('team-members.store');
    Route::get('/team-members/{id}', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'show'])->whereNumber('id')->name('team-members.show');
    Route::get('/team-members/{id}/edit', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'edit'])->whereNumber('id')->name('team-members.edit');
    Route::post('/team-members/{id}', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'update'])->whereNumber('id')->name('team-members.update');
    Route::post('/team-members/{id}/delete', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'destroy'])->whereNumber('id')->name('team-members.destroy');
    // Permissions are managed on their own page (Team Members -> ⋮ -> Permission), not in Add/Edit.
    Route::get('/team-members/{id}/permissions', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'permissions'])->whereNumber('id')->name('team-members.permissions');
    Route::post('/team-members/{id}/permissions', [\App\Http\Controllers\Worker\PartnerTeamMemberController::class, 'updatePermissions'])->whereNumber('id')->name('team-members.permissions.update');

    // Reuses the existing PartnerLanguageController (already used elsewhere for
    // the same purpose) - validates against config('app.locales'), stores in
    // session, and LanguageSwitcher (registered globally in Kernel.php) applies
    // it via App::setLocale() on every subsequent request, so it persists.
    Route::get('/lang/{locale}', [PartnerLanguageController::class, 'SwitchLang'])->name('lang.switch');
});
