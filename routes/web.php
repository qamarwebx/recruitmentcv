<?php
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AllContactController;
use App\Http\Controllers\BackEndController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\GoogleReviewController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\CountryCityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\MailConfigController;
use App\Http\Controllers\PartnerBookingController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerDashboardController;
use App\Http\Controllers\PdfGeneratorController;
use App\Http\Controllers\PlaceofIssueController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\WebsiteConfigController;
use App\Http\Controllers\WhatsappApiController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ReligionController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\AssociateController;
use App\Http\Controllers\ExpectedWorkLocationController;
use App\Http\Controllers\ArabicPageController;
use App\Http\Controllers\AutoNotificationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SearchController;
use App\Models\Websiteconfig;
use App\Http\Controllers\WhatsappCampaignController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\ContactpController;
use App\Http\Controllers\ContactPlusEmailPortalController;
use App\Http\Controllers\AllContactEmailPortalController;
use App\Http\Controllers\CheckDataController;
use App\Http\Controllers\OfficialWhatsappController;
use App\Http\Controllers\MetaNotificationController;
use App\Http\Controllers\MetaAutomationController;
use App\Http\Controllers\MetaWhatsappLogController;
use App\Http\Controllers\MetaWhatsappController;
use App\Http\Controllers\MetaWhatsappApiController;
use App\Http\Controllers\SMSCampaignController;
use App\Http\Controllers\SMSTemplateController;
use App\Http\Controllers\SmsApiController;
use App\Http\Controllers\EmailSMTPController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\CheckNetFileController;
use App\Http\Controllers\PartnerLanguageController;
use App\Http\Controllers\CrmCheckDataTableController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\ImageHostController;
use App\Http\Controllers\IptrackerController;
use App\Http\Controllers\CheckExistanceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FundAdvanceController;
use App\Http\Controllers\FundAdvanceSettlementController;
use App\Http\Controllers\FundAdvanceLedgerController;
use App\Http\Controllers\FundAdvanceReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\FileManagerQuotaController;
use App\Http\Controllers\StorageUsageController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\UnsubscribedynamicurlController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\DealPipelineController;
use App\Http\Controllers\DealNoteController;
use App\Http\Controllers\TodoNoteController;
use App\Http\Controllers\DealFileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\DealStagecontroller;
use App\Http\Controllers\RecruitStatusController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\IpLoginController;
use App\Http\Controllers\AdminDeviceController;
use App\Http\Controllers\AdminDeviceApprovalController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PaymentController;
use App\Models\Allcontact;
use App\Models\Allcontactreminder;
use App\Models\Autometanotification;
use App\Models\Expensecategory;
use App\Models\Metawhatsappcampaign;
use App\Models\Metawhatsapptemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Models\AdminDevice;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EmailAutomationController;
use App\Http\Controllers\FacebookAccountController;
use App\Http\Controllers\PartnerCustomerController;
use App\Http\Controllers\PartnerInvoiceController;
use App\Http\Controllers\PartnerPaymentController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\TeamMemberPageController;
use App\Http\Controllers\ImageUrlController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\GoogleContactController;

Route::get('/capture-muqawil', function () {

    try {
        $apiKey = '2990843320592e0d2d3a3709d47a2263fa288582';
        $targetUrl = 'https://muqawil.org/en/contractors/20056546/143';

        // =========================
        // ✅ STEP 1: GET HTML (FAST + DATA EXTRACTION)
        // =========================
        $htmlUrl = "https://api.zenrows.com/v1/?apikey={$apiKey}"
            . "&url=" . urlencode($targetUrl)
            . "&js_render=true"
            . "&wait=5000";

        $response = Http::timeout(120)
            ->retry(3, 2000)
            ->get($htmlUrl);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'error' => $response->body()
            ]);
        }

        $html = $response->body();

        // =========================
        // ✅ STEP 2: PARSE HTML
        // =========================
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $getText = function($xpath, $query) {
            $node = $xpath->query($query)->item(0);
            return $node ? trim($node->textContent) : null;
        };

        // =========================
        // ✅ STEP 3: EXTRACT DATA
        // =========================
        $data = [
            'membership_number' => $getText($xpath, "//*[contains(text(),'Membership Number')]/following::*[1]"),
            'membership' => $getText($xpath, "//*[contains(text(),'Membership')]/following::*[1]"),
            'member_since' => $getText($xpath, "//*[contains(text(),'Member Since')]/following::*[1]"),
            'company_size' => $getText($xpath, "//*[contains(text(),'Company Size')]/following::*[1]"),
            'training_hours' => $getText($xpath, "//*[contains(text(),'Training Credit Hours')]/following::*[1]"),
            'mobile' => $getText($xpath, "//*[contains(text(),'Organization Mobile Number')]/following::*[1]"),
            'email' => $getText($xpath, "//*[contains(text(),'Organization Email')]/following::*[1]"),
            'city' => $getText($xpath, "//*[contains(text(),'City')]/following::*[1]"),
            'region' => $getText($xpath, "//*[contains(text(),'Region')]/following::*[1]"),
        ];

        // // =========================
        // // ✅ STEP 4: OPTIONAL SCREENSHOT
        // // =========================
        // $screenshotUrl = "https://api.zenrows.com/v1/?apikey={$apiKey}"
        //     . "&url=" . urlencode($targetUrl)
        //     . "&mode=auto&screenshot=true";

        // $imgResponse = Http::timeout(60)->get($screenshotUrl);

        // $imageUrl = null;

        // if ($imgResponse->successful()) {
        //     $folder = $_SERVER['DOCUMENT_ROOT'] . '/screenshots';

        //     if (!file_exists($folder)) {
        //         mkdir($folder, 0755, true);
        //     }

        //     $fileName = Str::uuid() . '.png';
        //     $filePath = $folder . '/' . $fileName;

        //     file_put_contents($filePath, $imgResponse->body());

        
        //     $imageUrl = url('screenshots/' . $fileName);
        // }

        // =========================
        // ✅ FINAL RESPONSE
        // =========================
        return response()->json([
            'success' => true,
            // 'image' => $imageUrl,
            'data' => $data
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }

});


Route::get('/call/{number}', function ($number) {
    $number = preg_replace('/[^0-9+]/', '', $number);
    return redirect("tel:$number");
});

Route::get('/admin/test-email', function () {

    try {

        Mail::raw('✅ SMTP test mail sent successfully from Laravel.', function ($message) {
            $message->to('qamarwebx@gmail.com') // change this
                    ->subject('Laravel SMTP Test Mail');
        });

        return response()->json([
            'status' => true,
            'message' => 'Mail sent successfully ✅'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'error' => $e->getMessage()
        ], 500);
    }
})->name('test-email');


Route::get('/admin/login-message', function () {
    $message = request()->query('message', 'Your device login is being verified by admin. Please wait until approval.');
    $device_id = request()->query('device_id') ?? null;
    return view('admin.auth.login-message', compact('message','device_id'));
})->name('auth.login-message'); 

Route::get('/admin/check-device-status', function () {
    $device_id = request()->query('device_id');

    if (!$device_id) {
        return Response::json([
            'status' => false,
            'message' => 'Device ID missing'
        ]);
    }

    $device = AdminDevice::where('device_id', $device_id)->first();

    if (!$device) {
        return Response::json([
            'status' => false,
            'message' => 'Device not found'
        ]);
    }

    if ($device->is_approved == 1) {
        return Response::json([
            'status' => true,
            'message' => 'Device approved'
        ]);
    }

    // If device exists but not approved yet
    return Response::json([
        'status' => false,
        'message' => 'Pending approval'
    ]);
})->name('admin.check-device-status');

Route::get('/admin/auto-login', function (Request $request) {
   
    $device_id = $request->query('device_id');

    if (!$device_id) {
        return Response::json(['status' => false, 'message' => 'Device ID missing']);
    }

    $device = AdminDevice::where('device_id', $device_id)->first();
    if (!$device) {
        return Response::json(['status' => false, 'message' => 'Device not found']);
    }

    if (!$device->is_approved) {
        return Response::json(['status' => false, 'message' => 'Device not approved']);
    }

    $updated = Admin::whereId($device->admin_id)->update(['last_login_at' => now()]);

    if (! $updated) {
        return Response::json([
            'status' => false,
            'message' => 'Admin not found',
        ]);
    }
    
    $admin = Admin::find($device->admin_id);
    
    // ✅ Log in the admin automatically
    Auth::guard('admin')->login($admin);
    $request->session()->regenerate();
    $device->update(['login_status' => 'Active']);
  

    return Response::json([
        'status' => true,
        'message' => 'Auto-login successful',
        'redirect_url' => route('admin.dashboard') // or RouteServiceProvider::ADMIN_HOME
    ]);
})->name('admin.auto-login');

Route::get('/videos/{filename}', function ($filename) {
    
    $path = storage_path('app/public/testimonials/videos/' . $filename);
   
    if (!file_exists($path)) {
        abort(404, 'Video not found');
    }

    $mimeType = \Illuminate\Support\Facades\File::mimeType($path);

    return Response::make(file_get_contents($path), 200, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="'.$filename.'"'
    ]);
})->where('filename', '.*');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/partner-whatsapp/{number}', function ($number) {

    $cleanNumber = preg_replace('/[^0-9]/', '', $number);

    if (strlen($cleanNumber) == 10) {
        $cleanNumber = '91' . $cleanNumber;
    }

    $url = "https://api.whatsapp.com/send/?phone={$cleanNumber}&text&type=phone_number&app_absent=0";

    return redirect()->away($url);
});
// -------------------------------------------------------------------------------------------------
// Forntend website route start
// -------------------------------------------------------------------------------------------------

// Arabic Section
Route::get('ar',[ArabicPageController::class,'index'])->name('ar.welcome');
Route::get('ar/about-us',[ArabicPageController::class,'about'])->name('ar.about');
Route::get('ar/services',[ArabicPageController::class,'services'])->name('ar.service');
Route::get('ar/contact-us',[ArabicPageController::class,'contact'])->name('ar.contact');
Route::get('ar/resumes',[ArabicPageController::class,'resumes'])->name('ar.resumes');
Route::get('ar/resumes/details/{id}',[ArabicPageController::class,'fullresumes'])->name('ar.fullresume');
// Route::get('ar/resumes/download/{id}',[ArabicPageController::class,'downloadCV'])->name('ar.download.cv');

// Front End
Route::get('/',[FrontEndController::class,'index'])->name('welcome');
Route::get('about-us',[FrontEndController::class,'about'])->name('about');
Route::get('services',[FrontEndController::class,'services'])->name('service');
Route::get('contact-us',[FrontEndController::class,'contact'])->name('contact');
Route::get('resumes',[FrontEndController::class,'resumes'])->name('resumes');
Route::get('resumes/details/{id}',[FrontEndController::class,'fullresumes'])->name('fullresume');
Route::get('resumes/download/{id}',[FrontEndController::class,'downloadCV'])->name('download.cv');

// update language
Route::post('language/update',[LanguageController::class,'updateLangpage'])->name('language.update');

Route::post('resumes/details/get/expcitywork',[FrontEndController::class,'getexpectedwork']);
Route::post('resumes/details/get/expcitywork2',[FrontEndController::class,'getexpectedwork2']);
Route::post('resumes/details/get/expcitywork3',[FrontEndController::class,'getexpectedwork3']);
Route::post('resumes/details/get/expcitywork4',[FrontEndController::class,'getexpectedwork4']);

Route::get('resumes/details/get/visaembassyfor',[FrontEndController::class,'getvisaembassyfor']);

// Check Email is Exist or Not
Route::get('user/check/email',[FrontEndController::class,'checkUserMailExists']);
Route::get('user/check/email2',[FrontEndController::class,'checkUserMailExists2']);

Route::post('import-new-customer', [UserLoginController::class, 'userStr'])->middleware(['throttle:5,1']);

// Check Login
Route::post('check/login/email',[UserLoginController::class,'checkloginemail']);
Route::post('check/login/password',[UserLoginController::class,'checkloginpass']);
Route::post('check/signup/email',[UserLoginController::class,'checksignupemail']);
Route::post('forgot/password/email/find',[UserLoginController::class,'findemailf']);

// Reset Password
Route::post('user-auth/forget-password/email',[UserLoginController::class,'sendPasswordResetLink'])->name('password.forget.email');
Route::get('user-auth/reset-password/{token}',[UserLoginController::class,'showResetPasswordForm'])->name('password.reset.form');
Route::post('user-auth/reset-password/store/{email}',[UserLoginController::class,'storeResetPassword'])->name('password.reset.store');

/* Social Media Login Section Start */

// Facebook Login
Route::get('auth/facebook',[SocialLoginController::class,'redirectToFacebook'])->name('facbookLogin');
Route::get('auth/facebook/callback',[SocialLoginController::class,'handleFacebookCallback']);

Route::get('ar/auth/facebook',[SocialLoginController::class,'arredirectToFacebook'])->name('ar.facbookLogin');
Route::get('ar/auth/facebook/callback',[SocialLoginController::class,'arhandleFacebookCallback']);

// Google Login
// Route::get('auth/google',[SocialLoginController::class,'redirectToGoogle'])->name('googleLogin');
// Route::get('auth/google/callback',[SocialLoginController::class,'handleGoogleCallback']);

// Google Login
Route::get('auth/google', [SocialLoginController::class, 'redirectToGoogle'])
    ->name('googleLogin');

Route::get('auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// Worker Partner "Continue with Google" entry point. Must stay on this
// (qamarhire.com) host: Google's OAuth app only whitelists
// https://qamarhire.com/auth/google/callback as a redirect URI, so the
// whole handshake has to run here even though the login modal itself lives
// on worker.qamarhire.com. See SocialLoginController::redirectToGooglePartner().
Route::get('partner-google/start', [SocialLoginController::class, 'redirectToGooglePartner'])
    ->name('partner.google.start');

Route::get('ar/auth/google',[SocialLoginController::class,'arredirectToGoogle'])->name('ar.googleLogin');
Route::get('ar/auth/google/callback',[SocialLoginController::class,'arhandleGoogleCallback']);


/* Social Media Login Section End */

Route::get('/path-change-status',[FrontEndController::class,'basepathstatus'])->name('basepathstatus');
Route::post('/path-change-status/update',[FrontEndController::class,'basepathstatusupdt'])->name('basepathstatusupdt');



// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Test Official Whatsapp
Route::get('test-official-whatsapp',[OfficialWhatsappController::class,'index']);
Route::post('test-official-whatsapp/send',[OfficialWhatsappController::class,'SendTextMessage'])->name('official.whatsapp');

// Check Netfile Whatsapp Controller
Route::get('netfile-check-test/test',[CheckNetFileController::class,'index']);
Route::post('netfile-check-test/send',[CheckNetFileController::class,'sendTest']);

// Unscubsribe URL for Whatsapp Click
Route::get('whatsapp/unsubscribe/request/{randomgenerate}/{contactid}',[UnsubscribedynamicurlController::class,'index']);
Route::post('whatsapp/unsubscribe/request/{randomgenerate}/{contactid}/stop',[UnsubscribedynamicurlController::class,'stopWhatsapp']);

Route::get('whatsapp/unsubscribe/request/allcontact/{randomgenerate}/{allcontactID}',[UnsubscribedynamicurlController::class,'index2']);
Route::post('whatsapp/unsubscribe/request/allcontact/{randomgenerate}/{allcontactID}/stop',[UnsubscribedynamicurlController::class,'stopWhatsapp2']);

// Redirect to Whatsapp Chat
Route::get('/newchat/{randomstring}/{staff}',[FrontEndController::class,'redirecttowhatsapp']);
Route::get('https://crm.qamarhire.com/newchat/{randomstring}/{staff}',[FrontEndController::class,'redirecttowhatsapp']);
Route::get('https://qamarhire.com/newchat/{randomstring}/{staff}',[FrontEndController::class,'redirecttowhatsapp']);

// -------------------------------------------------------------------------------------------------
// Forntend website route end
// -------------------------------------------------------------------------------------------------

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::redirect('/dashboard','/my-order',301);

    // My Order
    Route::get('/my-order',[DashboardController::class,'myorder'])->name('myorder');
    Route::get('/my-order/details/{id}', [BookingController::class, 'getDetails']);
    // My Order in arabic
    Route::get('ar/my-order',[DashboardController::class,'armyorder'])->name('ar.myorder');
    Route::get('ar/my-order/details/{id}',[DashboardController::class,'getDetails']);

    // Cancel Booking
    Route::post('/booking/cancel',[DashboardController::class,'cancelBooking'])->name('user.candidate.booking.cancel');

    // Profile
    Route::get('/my-profile',[DashboardController::class,'myprofile'])->name('myprofile');
    Route::post('/my-profile/update',[DashboardController::class,'updateMyprofile'])->name('myprofile.update');

    // Mobile Verification
    Route::post('/mobile-verification-get',[DashboardController::class,'getOTPFORVER'])->name('mobile.getOTPVerification');
    Route::post('/mobile-verification-get2',[DashboardController::class,'getOTPFORVER2'])->name('mobile.getOTPVerification2');
    Route::post('/mobile-verification-get2/validate',[DashboardController::class,'getOTPValidation'])->name('mobile.getOTPValidation');

    // Profile arabic
    Route::get('ar/my-profile',[DashboardController::class,'armyprofile'])->name('ar.myprofile');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Update profile through resume
    Route::post('/profile/update/res',[DashboardController::class,'updprofileBKC'])->name('booking.profile.update');
    Route::post('/profile/update/res/email',[DashboardController::class,'updprofileEmailBKC'])->name('booking.profile.emailUpdate');

    Route::post('profile/update/res/otpandupdate',[DashboardController::class,'getMobileOTPandUpdate'])->name('booking.profile.otpandupdate');
    Route::post('profiel/update/res/validate',[DashboardController::class,'getMobileOTPandUpdateValidate'])->name('booking.profile.validate-otp2');
    Route::post('profiel/update/res/ar/validate',[DashboardController::class,'getMobileOTPandUpdateValidateAr'])->name('booking.profile.arvalidate-otp2');

    Route::post('profile/update/email/res/otpandupdate',[DashboardController::class,'getEmailOTPandUpdate'])->name('booking.profile.emailotpandupdate');
    Route::post('profile/update/email/res/validate',[DashboardController::class,'getEmailOTPandUpdateValidate'])->name('booking.profile.validate-otp2e');
    Route::post('profile/update/email/res/ar/validate',[DashboardController::class,'getEmailOTPandUpdateValidateAr'])->name('booking.profile.arvalidate-otp2e');

    // Check Mobile No
    Route::get('profile/mobile/check',[DashboardController::class,'checkMobileExists'])->name('booking.profile.checkMobile');
    Route::get('profile/mobile/check2',[DashboardController::class,'checkMobileExists2'])->name('booking.profile.checkmobile2');


    // Create Booking by User
    Route::post('resumes/details/booking',[BookingController::class,'store']);  // When Office
    Route::post('resumes/details/booking/2',[BookingController::class,'store2']); // When Partner
    Route::post('resumes/details/booking/3',[BookingController::class,'store3']); // When Partner
    Route::post('resumes/details/booking/4',[BookingController::class,'store4']); // When Partner

    Route::post('ar/resumes/details/booking',[BookingController::class,'storear']);
    Route::post('ar/resumes/details/booking/2',[BookingController::class,'storear2']);
    Route::post('ar/resumes/details/booking/3',[BookingController::class,'storear3']);
    Route::post('ar/resumes/details/booking/4',[BookingController::class,'storear4']);

    // Get Partner ID from Booking
    Route::post('getPartner/details',[DashboardController::class,'getPartnerDetail'])->name('getpartner.detail');

    // Get Candidate Details
    Route::post('getCandidate/details',[DashboardController::class,'getCandidateDetail'])->name('getCandidate.detail');

    Route::post('getOrderCandStatusPopUp',[DashboardController::class,'checkloginorderstatus'])->name('checkloginorderstatus');


});

require __DIR__.'/auth.php';


Route::group(['middleware' => ['guest:admin'],'prefix' => 'admin','as' => 'admin.'],function(){
    Route::get('auth/forgot-password',[AdminLoginController::class,'forgotpassword'])->name('forgotpass');
    Route::post('auth/check/email/forgot',[AdminLoginController::class,'findmailf']);
    Route::post('auth/forgot-password/email',[AdminLoginController::class,'sendPasswordResetLink'])->name('password.forget.email');
});

Route::get('testimonials/link/{encrypted_id}',[TestimonialController::class,'uploadVideoForm'])->name('testimonials.upload_video_form');

// web.php
Route::post('/testimonials/upload-video', [TestimonialController::class, 'uploadVideoProcess'])->name('testimonials.upload_video_process');

// Scoped file access for the public testimonial link page — filename is restricted to a
// single path segment (no slashes) and further validated against the testimonial's own
// uploaded_video list in the controller, so a filename can't be reused across testimonials.
Route::get('testimonials/link/{encrypted_id}/file/{filename}/download', [TestimonialController::class, 'downloadTestimonialFile'])
    ->where('filename', '[^/]+')
    ->name('testimonials.download_file');
Route::get('testimonials/link/{encrypted_id}/file/{filename}/preview', [TestimonialController::class, 'previewTestimonialFile'])
    ->where('filename', '[^/]+')
    ->name('testimonials.preview_file');


Route::group(['middleware' => ['auth:admin','blockURL'],'prefix' => 'admin','as' => 'admin.'],function(){

    Route::get('google-contacts/connect', [GoogleContactController::class, 'redirect']);
    Route::get('google-contacts/callback', [GoogleContactController::class, 'callback']);


    // Redesigned CRM dashboard (role-based: admin vs staff). The previous
    // dashboard is preserved for restore at BackEndController@index +
    // resources/views/admin/dashboard_backup_pre_redesign.blade.php — it is
    // simply no longer routed.
    Route::get('dashboard',[AdminDashboardController::class,'index'])->name('dashboard');
    Route::get('dashboard/staff-summary',[AdminDashboardController::class,'staffSummary'])->name('dashboard.staffSummary');
    Route::get('dashboard/admin-summary',[AdminDashboardController::class,'adminSummary'])->name('dashboard.adminSummary');
    Route::get('dashboard/staff-performance',[AdminDashboardController::class,'staffPerformance'])->name('dashboard.staffPerformance');
    Route::get('dashboard/work-report',[AdminDashboardController::class,'workReport'])->name('dashboard.workReport');
    Route::get('dashboard/pipeline-movement',[AdminDashboardController::class,'pipelineMovement'])->name('dashboard.pipelineMovement');
    Route::get('dashboard/activity-summary',[AdminDashboardController::class,'activitySummary'])->name('dashboard.activitySummary');

    // Global navbar search (resources/views/layout/admin/admin_layout.blade.php's
    // existing `.search-input`). Available on every authenticated admin page
    // since it lives in the shared layout, not a specific view.
    Route::get('global-search',[GlobalSearchController::class,'search'])->name('search');

    Route::post('store-pageview-event',[FacebookAccountController::class, 'storePageView'])->name('store.pageview');

    // Facebook Accounts
    Route::get('facebook-accounts', [FacebookAccountController::class,'index'])->name('facebook.accounts');
    Route::post('facebook-accounts/list/json', [FacebookAccountController::class,'indexjson']);

    Route::post('facebook-accounts/store', [FacebookAccountController::class,'store'])->name('facebook.accounts.store');
    Route::post('facebook-accounts/edit', [FacebookAccountController::class,'editlist']);
    Route::post('facebook-accounts/update', [FacebookAccountController::class,'update'])->name('facebook.accounts.update');

    Route::get('facebook-accounts/view/{id}', [FacebookAccountController::class,'show'])->name('facebook.accounts.view');

    Route::post('facebook-accounts/delete', [FacebookAccountController::class,'delete'])->name('facebook.accounts.delete');

    Route::post('facebook-accounts/update-status/active', [FacebookAccountController::class,'activate'])->name('facebook.accounts.active');
    Route::post('facebook-accounts/update-status/deactive', [FacebookAccountController::class,'deactivate'])->name('facebook.accounts.deactive');


    // Staff
    Route::get('staff',[StaffController::class,'index'])->name('staff');
    Route::post('staff/list/json',[StaffController::class,'indexjson']);
    Route::post('staff/store',[StaffController::class,'store'])->name('staff.store');
    Route::post('staff/edit',[StaffController::class,'editlist']);
    Route::post('staff/update',[StaffController::class,'update'])->name('staff.update');
    Route::get('staff/view/{id}',[StaffController::class,'show'])->name('staff.view');
    Route::post('staff/delete',[StaffController::class,'deleteStaff'])->name('staff.delete');
    Route::post('staff/check/exist',[StaffController::class,'checkStaffExist']);
    Route::post('staff/update-status/active',[StaffController::class,'updatetoactive'])->name('staff.active');
    Route::post('staff/update-status/deactive',[StaffController::class,'updatetodeactive'])->name('staff.deactive');
    Route::post('staff/update-careoff/active',[StaffController::class,'updatetoactiveCareoff'])->name('staff.careoff.active');
    Route::post('staff/update-careoff/deactive',[StaffController::class,'updatetodeactiveCareoff'])->name('staff.careoff.deactive');

    Route::post('staff/profile/upload/{id}',[StaffController::class,'uploadProfile'])->name('staff.profile.upload');
    Route::post('staff/password-change/upload/{id}',[StaffController::class,'passwordchange'])->name('staff.password.change');

    Route::post('staff/check/email',[StaffController::class,'checkemail']);
    Route::post('staff/check/username',[StaffController::class,'checkusername']);
    Route::post('staff/check/phone',[StaffController::class,'checkphone']);

    Route::post('staff/edit/check/email',[StaffController::class,'edcheckemail']);
    Route::post('staff/edit/check/username',[StaffController::class,'edcheckusername']);
    Route::post('staff/edit/check/phone',[StaffController::class,'edcheckphone']);
    Route::post('staff/check/password',[StaffController::class,'checkpassword']);


    Route::prefix('domains')->group(function () {
        Route::post('/store', [DomainController::class, 'store'])->name('domains.store');
        Route::post('websitelogo/update/{id}', [DomainController::class, 'websitelogoupdt'])->name('domains.websitelogoupdt');
        Route::post('address-update', [DomainController::class, 'updateAddress'])->name('domains.address.update');
        Route::post('generate-dns', [DomainController::class, 'generateDns'])->name('domains.generate.dns');
        Route::post('/delete', [DomainController::class, 'delete'])->name('domains.delete');
    });

    // Candidate Status
    /*     Route::get('candidate/status',[StaffController::class,'index'])->name('staff');
    Route::post('candidate/status/list/json',[StaffController::class,'indexjson']);
    Route::post('candidate/status/store',[StaffController::class,'store'])->name('staff.store');
    Route::post('candidate/status/edit',[StaffController::class,'editlist']);
    Route::post('candidate/status/update',[StaffController::class,'update'])->name('staff.update');
    Route::get('candidate/status/view/{id}',[StaffController::class,'show'])->name('staff.view');
    Route::post('candidate/status/delete',[StaffController::class,'deleteStaff'])->name('staff.delete');
    Route::post('candidate/status/check/exist',[StaffController::class,'checkStaffExist']);
    */
    // Booking Controller
    Route::get('booking',[BookingController::class,'index'])->name('booking');
    Route::get('booking/view/{id}',[BookingController::class,'view'])->name('booking.view');
    Route::post('booking/cancel',[BookingController::class,'cancel'])->name('booking.cancel');

    // Visa Details
    Route::post('booking/getVisa',[BookingController::class,'getVisa']);
    Route::post('booking/getVisa/store',[BookingController::class,'visaStr'])->name('visaStr');

    // Payment Details
    Route::post('booking/getPayment',[BookingController::class,'getPayment']);
    Route::post('booking/getPay/store',[BookingController::class,'paymentStr'])->name('paymentStr');

    // orderstatus Details
    Route::post('booking/getorderstatus',[BookingController::class,'getorderstatus']);
    Route::post('booking/getstatus/store',[BookingController::class,'orderstatusStr'])->name('orderstatusStr');

    // OTP Booking
    Route::post('booking/sendOTP',[BookingController::class,'sendOTP']);
    Route::post('booking/confirm-otp',[BookingController::class,'confirmOTP'])->name('otpconfirm');
    Route::post('booking/check-otp',[BookingController::class,'checkOTP']);

    // Replace candidate
    Route::post('booking/getCandidate',[BookingController::class,'getCandidate']);
    Route::post('booking/replace/candidate',[BookingController::class,'replacecandidate'])->name('booking.repcand');

    // OrderStatus
    Route::get('orderStatus',[OrderStatusController::class,'index'])->name('orderStatus');
    Route::post('orderStatus/list/json',[OrderStatusController::class,'indexjson']);
    Route::post('orderStatus/store',[OrderStatusController::class,'store'])->name('orderStatus.store');
    Route::post('orderStatus/edit',[OrderStatusController::class,'edit']);
    Route::post('orderStatus/update',[OrderStatusController::class,'update'])->name('orderStatus.update');
    Route::post('orderStatus/delete',[ProfessionController::class,'delete'])->name('orderStatus.delete');

    // Profession
    Route::get('profession',[ProfessionController::class,'index'])->name('profession');
    Route::post('profession/list/json',[ProfessionController::class,'indexjson']);
    Route::post('profession/store',[ProfessionController::class,'store'])->name('profession.store');
    Route::post('profession/edit',[ProfessionController::class,'edit']);
    Route::post('profession/update',[ProfessionController::class,'update'])->name('profession.update');

    Route::post('profession/check/engname',[ProfessionController::class,'checkengname']);
    Route::post('profession/check/arname',[ProfessionController::class,'checkarname']);

    Route::post('profession/check/edit/engname',[ProfessionController::class,'edcheckengname']);
    Route::post('profession/check/edit/arname',[ProfessionController::class,'edcheckarname']);

    Route::post('profession/check/exist',[ProfessionController::class,'checkDelProf']);
    Route::post('profession/delete',[ProfessionController::class,'deleteProf'])->name('profession.delete');

    // Branch
    Route::get('branch',[BranchController::class,'index'])->name('branch');
    Route::post('branch/list/json',[BranchController::class,'indexjson']);
    Route::post('branch/store',[BranchController::class,'store'])->name('branch.store');
    Route::post('branch/edit',[BranchController::class,'edit']);
    Route::post('branch/update',[BranchController::class,'update'])->name('branch.update');
    Route::post('branch/check/name',[BranchController::class,'checkname']);
    Route::post('branch/check/edit/name',[BranchController::class,'edcheckname']);
    Route::post('branch/check/exist',[BranchController::class,'checkDelBranch']);
    Route::post('branch/delete',[BranchController::class,'deleteBranch'])->name('branch.delete');

    Route::get('education/list',[EducationController::class,'index'])->name('education');
    Route::post('education/list/json',[EducationController::class,'indexJson']);
    Route::post('education/store',[EducationController::class,'store'])->name('education.store');
    Route::post('education/edit',[EducationController::class,'edit']);
    Route::post('education/update',[EducationController::class,'update'])->name('education.update');
    Route::post('education/check/name',[EducationController::class,'checkEducation']);

    Route::get('religion/list',[ReligionController::class,'index'])->name('religion');
    Route::post('religion/list/json',[ReligionController::class,'indexJson']);
    Route::post('religion/store',[ReligionController::class,'store'])->name('religion.store');
    Route::post('religion/edit',[ReligionController::class,'edit']);
    Route::post('religion/update',[ReligionController::class,'update'])->name('religion.update');
    Route::post('religion/check/name',[ReligionController::class,'checkreligion']);

    // source-menagement
    Route::get('/source-management', [SourceController::class, 'index'])->name('source-management');
    Route::get('/source-management/json', [SourceController::class, 'indexJson'])->name('source-management.json');
    Route::post('/source-management/store', [SourceController::class, 'store'])->name('source-management.store');
    Route::post('/source-management/edit', [SourceController::class, 'edit'])->name('source-management.edit');
    Route::post('/source-management/update', [SourceController::class, 'update'])->name('source-management.update');
    Route::post('/source-management/delete', [SourceController::class, 'delete'])->name('source-management.delete');


    // Invoice Section
    Route::get('sales-invoice/list',[InvoiceController::class,'index'])->name('invoice.list');
    Route::post('sales-invoice/store',[InvoiceController::class,'store'])->name('invoice.store');
    Route::get('sales-invoice/getcandidate/list',[InvoiceController::class,'getCandidate'])->name('invoice.getcandidate');
    Route::post('sales-invoice/checkinvoicenumber',[InvoiceController::class,'checkinvoicenumber'])->name('invoice.checkinvoicenumber');
    Route::get('sales-invoice/edit',[InvoiceController::class,'edit'])->name('invoice.edit');
    Route::post('sales-invoice/update',[InvoiceController::class,'update'])->name('invoice.update');
    Route::get('sales-invoice/show/{id}',[InvoiceController::class,'show'])->name('invoice.show');
    Route::get('sales-invoice/generate-pdf/{id}',[InvoiceController::class,'generatedInvoicePDF'])->name('invoice.generatedpdf');
    Route::post('sales-invoice/delete',[InvoiceController::class,'destroy'])->name('invoice.delete');
    Route::post('sales-invoice/filter/save',[InvoiceController::class,'saveFilter'])->name('invoice.saveFilter');

    // Payment Section
    Route::get('payment/list',[PaymentController::class,'index'])->name('payment.list');
    Route::get('payment/getInvoices/list',[PaymentController::class,'getInvoices'])->name('payment.getinvoice');
    Route::post('payment/store',[PaymentController::class,'store'])->name('payment.store');
    Route::get('payment/list/edit',[PaymentController::class,'edit'])->name('payment.edit');
    Route::post('payment/list/update',[PaymentController::class,'update'])->name('payment.update');
    Route::post('payment/list/delete',[PaymentController::class,'delete'])->name('payment.delete');

    // Account Details
    Route::get('account-details/list',[InvoiceController::class,'accountdetailslist'])->name('account_details.list');
    Route::post('account-details/list/json',[InvoiceController::class,'accountdetailslistJson']);
    Route::post('account-details/store',[InvoiceController::class,'accountdetailsStore'])->name('account_details.store');
    Route::get('account-details/edit',[InvoiceController::class,'accountdetailsEdit'])->name('account_details.edit');
    Route::post('account-details/update',[InvoiceController::class,'accountdetailsUpdate'])->name('account_details.update');
    Route::post('/account-details/delete', [InvoiceController::class, 'delete'])->name('account_details.delete');

    // Expense Section
    Route::get('expense-list',[ExpenseController::class,'index'])->name('expense.list');
    Route::post('expense-list/store',[ExpenseController::class,'store'])->name('expense.store');
    Route::get('expense-list/edit',[ExpenseController::class,'edit'])->name('expense.edit');
    Route::get('expense-list/view/{id}', [ExpenseController::class, 'show'])->name('expense.view');
    Route::post('expense-list/update',[ExpenseController::class,'update'])->name('expense.update');
    Route::post('expense-list/delete',[ExpenseController::class,'delete'])->name('expense.delete');
    Route::post('expense/remove-receipt',[ExpenseController::class, 'removeReceipt'])->name('expense.removeReceipt');

    Route::post('expense-list/saveadminfilter',[ExpenseController::class,'saveadminfilter'])->name('expense.saveadminfilter');
    Route::post('expense-list/resetadminfilter',[ExpenseController::class,'resetadminfilter'])->name('expense.resetadminfilter');

    // File Manager Section
    Route::get('file-manager/list',[FileManagerController::class,'index'])->name('file_manager.list');
    Route::get('file-manager/browse/json',[FileManagerController::class,'browse'])->name('file_manager.browse');
    Route::get('file-manager/stats/json',[FileManagerController::class,'stats'])->name('file_manager.stats');
    Route::get('file-manager/tree/{parentId?}',[FileManagerController::class,'folderTree'])->name('file_manager.tree');
    Route::get('file-manager/breadcrumb/{itemId?}',[FileManagerController::class,'breadcrumb'])->name('file_manager.breadcrumb');
    Route::post('file-manager/folder/create',[FileManagerController::class,'createFolder'])->name('file_manager.folder.create');
    Route::post('file-manager/upload',[FileManagerController::class,'upload'])->name('file_manager.upload');
    Route::post('file-manager/rename',[FileManagerController::class,'rename'])->name('file_manager.rename');
    Route::post('file-manager/move',[FileManagerController::class,'move'])->name('file_manager.move');
    Route::post('file-manager/bulk-move',[FileManagerController::class,'bulkMove'])->name('file_manager.bulk_move');
    Route::post('file-manager/favorite',[FileManagerController::class,'favorite'])->name('file_manager.favorite');
    Route::post('file-manager/bulk-favorite',[FileManagerController::class,'bulkFavorite'])->name('file_manager.bulk_favorite');
    Route::post('file-manager/delete',[FileManagerController::class,'delete'])->name('file_manager.delete');
    Route::post('file-manager/bulk-delete',[FileManagerController::class,'bulkDelete'])->name('file_manager.bulk_delete');
    Route::post('file-manager/restore',[FileManagerController::class,'restore'])->name('file_manager.restore');
    Route::post('file-manager/bulk-restore',[FileManagerController::class,'bulkRestore'])->name('file_manager.bulk_restore');
    Route::post('file-manager/force-delete',[FileManagerController::class,'forceDelete'])->name('file_manager.force_delete');
    Route::post('file-manager/bulk-force-delete',[FileManagerController::class,'bulkForceDelete'])->name('file_manager.bulk_force_delete');
    Route::post('file-manager/filter/save',[FileManagerController::class,'saveFilter'])->name('file_manager.filter.save');
    Route::post('file-manager/filter/reset',[FileManagerController::class,'resetFilter'])->name('file_manager.filter.reset');
    Route::get('file-manager/{item}/download',[FileManagerController::class,'download'])->name('file_manager.download');
    Route::get('file-manager/{item}/preview',[FileManagerController::class,'preview'])->name('file_manager.preview');
    Route::get('file-manager/{item}/thumbnail',[FileManagerController::class,'thumbnail'])->name('file_manager.thumbnail');

    // File Manager Settings Section
    Route::get('settings/file-manager',[FileManagerQuotaController::class,'index'])->name('settings.file_manager.index');
    Route::get('settings/file-manager/json',[FileManagerQuotaController::class,'browseUsers'])->name('settings.file_manager.json');
    Route::post('settings/file-manager/quota/update',[FileManagerQuotaController::class,'updateQuota'])->name('settings.file_manager.quota.update');
    Route::get('settings/file-manager/user/{admin}/files',[FileManagerQuotaController::class,'userFiles'])->name('settings.file_manager.user.files');

    // Storage Usage Settings Section
    Route::get('settings/storage-usage',[StorageUsageController::class,'index'])->name('settings.storage_usage.index');
    Route::get('settings/storage-usage/json',[StorageUsageController::class,'json'])->name('settings.storage_usage.json');
    Route::post('settings/storage-usage/recalculate',[StorageUsageController::class,'recalculate'])->name('settings.storage_usage.recalculate');
    Route::get('settings/storage-usage/status',[StorageUsageController::class,'status'])->name('settings.storage_usage.status');

    // DB Backup Settings Section (Settings > Backup > DB Backup)
    Route::get('settings/db-backup',[BackupController::class,'index'])->name('settings.db_backup.index');
    Route::get('settings/db-backup/json',[BackupController::class,'json'])->name('settings.db_backup.json');
    Route::get('settings/db-backup/status',[BackupController::class,'status'])->name('settings.db_backup.status');
    Route::post('settings/db-backup/daily',[BackupController::class,'saveDaily'])->name('settings.db_backup.daily.save');
    Route::post('settings/db-backup/interval',[BackupController::class,'saveInterval'])->name('settings.db_backup.interval.save');
    Route::post('settings/db-backup/generate/{type}',[BackupController::class,'generate'])->whereIn('type', ['daily','interval'])->name('settings.db_backup.generate');
    Route::get('settings/db-backup/{backup}/download',[BackupController::class,'download'])->whereNumber('backup')->name('settings.db_backup.download');
    Route::post('settings/db-backup/{backup}/delete',[BackupController::class,'delete'])->whereNumber('backup')->name('settings.db_backup.delete');

    // Google Drive Backup (Settings > Backup > DB Backup > Google Drive) -
    // same page/controller/permission as DB Backup above, not a new module.
    Route::get('settings/db-backup/drive/json',[BackupController::class,'driveJson'])->name('settings.db_backup.drive.json');
    Route::get('settings/db-backup/drive/status',[BackupController::class,'driveStatus'])->name('settings.db_backup.drive.status');
    Route::post('settings/db-backup/drive/enabled',[BackupController::class,'saveDriveEnabled'])->name('settings.db_backup.drive.enabled');
    Route::get('settings/db-backup/drive/connect',[BackupController::class,'driveConnect'])->name('settings.db_backup.drive.connect');
    Route::get('settings/db-backup/drive/callback',[BackupController::class,'driveCallback'])->name('settings.db_backup.drive.callback');
    Route::post('settings/db-backup/drive/disconnect',[BackupController::class,'driveDisconnect'])->name('settings.db_backup.drive.disconnect');
    Route::post('settings/db-backup/drive/test',[BackupController::class,'driveTestConnection'])->name('settings.db_backup.drive.test');
    Route::post('settings/db-backup/drive/{driveBackup}/delete',[BackupController::class,'driveDelete'])->whereNumber('driveBackup')->name('settings.db_backup.drive.delete');

    // Attendance Section
    // Top-level sidebar "Attendance" (grouped with Associate, gated by the
    // standalone `attendance` permission) uses this alias so it has its own
    // route name distinct from HR Management -> Attendance, and only one of
    // the two sidebar menu items lights up as active at a time. Same
    // controller method/view as attendance.list - no behavior change.
    Route::get('attendance',[AttendanceController::class,'index'])->name('attendance.top');
    Route::get('attendance/list',[AttendanceController::class,'index'])->name('attendance.list');
    Route::get('attendance/list/json',[AttendanceController::class,'datatable'])->name('attendance.json');
    Route::get('attendance/create',[AttendanceController::class,'create'])->name('attendance.create');
    Route::post('attendance/store',[AttendanceController::class,'store'])->name('attendance.store');
    Route::post('attendance/approve',[AttendanceController::class,'approve'])->name('attendance.approve');
    Route::post('attendance/reject',[AttendanceController::class,'reject'])->name('attendance.reject');
    Route::post('attendance/final-approve',[AttendanceController::class,'finalApprove'])->name('attendance.finalapprove');
    Route::post('attendance/final-reject',[AttendanceController::class,'finalReject'])->name('attendance.finalreject');
    Route::get('attendance/approval-history/{id}',[AttendanceController::class,'approvalHistory'])->name('attendance.approvalhistory');
    Route::get('attendance/edit/{id}',[AttendanceController::class,'edit'])->name('attendance.edit');
    Route::post('attendance/update',[AttendanceController::class,'update'])->name('attendance.update');
    Route::post('attendance/delete',[AttendanceController::class,'delete'])->name('attendance.delete');
    Route::post('attendance/filter/save',[AttendanceController::class,'saveFilter'])->name('attendance.filter.save');
    Route::post('attendance/filter/reset',[AttendanceController::class,'resetFilter'])->name('attendance.filter.reset');
    Route::get('attendance/slip/{adminId}/{month}/{year}',[AttendanceController::class,'downloadSlip'])->name('attendance.slip.download');

    // Chat Section
    Route::group(['prefix' => 'chat', 'as' => 'chat.', 'middleware' => ['chat.access']], function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('conversations', [ChatController::class, 'conversations'])->name('conversations');
        Route::post('conversations/start', [ChatController::class, 'start'])->name('conversations.start');
        Route::get('conversations/{conversation}/messages', [ChatController::class, 'messages'])->name('conversations.messages');
        Route::post('conversations/{conversation}/read', [ChatController::class, 'markRead'])->name('conversations.read');
        Route::post('conversations/{conversation}/unread', [ChatController::class, 'markUnread'])->name('conversations.unread');
        Route::post('messages/send', [ChatController::class, 'send'])->middleware('throttle:30,1')->name('messages.send');
        Route::post('messages/attachment', [ChatController::class, 'attachment'])->middleware('throttle:30,1')->name('messages.attachment');
        Route::post('messages/{message}/delete', [ChatController::class, 'deleteMessage'])->name('messages.delete');
        Route::post('typing', [ChatController::class, 'typing'])->middleware('throttle:60,1')->name('typing');
        Route::get('unread-count', [ChatController::class, 'unreadCount'])->name('unread_count');
        Route::get('admins/search', [ChatController::class, 'adminSearch'])->name('admins.search');
        Route::get('attachments/{attachment}/view', [ChatController::class, 'viewAttachment'])->name('attachments.view');
        Route::get('attachments/{attachment}/download', [ChatController::class, 'downloadAttachment'])->name('attachments.download');
    });

    // Salary Section
    Route::get('salary/dashboard', [SalaryController::class, 'dashboard'])->name('salary.dashboard');
    Route::get('salary/settings', [SalaryController::class, 'settingsPage'])->name('salary.settings.page');
    Route::get('salary/rules', [SalaryController::class, 'rulesPage'])->name('salary.rules');
    Route::get('salary/slip/{adminId}/{month}/{year}', [SalaryController::class, 'downloadSlip'])->name('salary.slip.download');
    Route::get('salary', [SalaryController::class, 'index'])->name('salary.index');
    Route::get('salary/live', [SalaryController::class, 'live'])->name('salary.live');
    Route::post('salary/staff/update', [SalaryController::class, 'updateStaffSalary'])->name('salary.staff.update');
    Route::post('salary/staff/role/update', [SalaryController::class, 'updateStaffRole'])->name('salary.staff.role.update');
    Route::post('salary/staff/final-approval/update', [SalaryController::class, 'updateStaffFinalApprovalAccess'])->name('salary.staff.finalapproval.update');
    Route::post('salary/settings/update', [SalaryController::class, 'settingsUpdate'])->name('salary.settings.update');
    Route::get('salary/payroll/json', [SalaryController::class, 'payrollDatatable'])->name('salary.payroll.json');
    Route::post('salary/payroll/filter/save', [SalaryController::class, 'payrollFilterSave'])->name('salary.payroll.filter.save');
    Route::post('salary/payroll/filter/reset', [SalaryController::class, 'payrollFilterReset'])->name('salary.payroll.filter.reset');
    Route::get('salary/payroll/show/{id}', [SalaryController::class, 'payrollShow'])->name('salary.payroll.show');
    Route::post('salary/payroll/generate', [SalaryController::class, 'payrollGenerate'])->name('salary.payroll.generate');
    Route::post('salary/payroll/generate-all', [SalaryController::class, 'payrollGenerateAll'])->name('salary.payroll.generate.all');
    Route::post('salary/payroll/lock', [SalaryController::class, 'payrollLock'])->name('salary.payroll.lock');
    Route::post('salary/payroll/update', [SalaryController::class, 'payrollUpdate'])->name('salary.payroll.update');
    Route::post('salary/payroll/delete', [SalaryController::class, 'payrollDelete'])->name('salary.payroll.delete');
    Route::post('salary/payroll/mark-paid', [SalaryController::class, 'payrollMarkPaid'])->name('salary.payroll.mark_paid');
    Route::get('salary/payroll/{id}/payment-slip/view', [SalaryController::class, 'payrollSlipView'])->name('salary.payroll.slip.view');
    Route::get('salary/payroll/{id}/payment-slip/download', [SalaryController::class, 'payrollSlipDownload'])->name('salary.payroll.slip.download');
    Route::get('salary/payroll/{id}/advances', [SalaryController::class, 'payrollAdvances'])->name('salary.payroll.advances');

    // Advance Payment Section
    Route::get('salary/advance/json', [SalaryController::class, 'advancePaymentDatatable'])->name('salary.advance.json');
    Route::get('salary/advance/show/{id}', [SalaryController::class, 'advancePaymentShow'])->name('salary.advance.show');
    Route::post('salary/advance/store', [SalaryController::class, 'advancePaymentStore'])->name('salary.advance.store');
    Route::post('salary/advance/update', [SalaryController::class, 'advancePaymentUpdate'])->name('salary.advance.update');
    Route::post('salary/advance/delete', [SalaryController::class, 'advancePaymentDelete'])->name('salary.advance.delete');

    // Holiday Section
    Route::get('holidays/list/json', [HolidayController::class, 'datatable'])->name('holidays.json');
    Route::post('holidays/store', [HolidayController::class, 'store'])->name('holidays.store');
    Route::get('holidays/edit/{id}', [HolidayController::class, 'edit'])->name('holidays.edit');
    Route::post('holidays/update', [HolidayController::class, 'update'])->name('holidays.update');
    Route::post('holidays/delete', [HolidayController::class, 'delete'])->name('holidays.delete');

    // Expense Category Section
    Route::get('expense/category/list',[ExpenseController::class,'expensecatlist'])->name('expense.category.list');
    Route::post('expense/category/store',[ExpenseController::class,'expensecatstore'])->name('expense.category.store');
    Route::post('expense/category/check/name',[ExpenseController::class,'checkcategoryname']);
    Route::get('expense/category/edit',[ExpenseController::class,'checkcategoryedit'])->name('expense.category.edit');
    Route::post('expense/category/update',[ExpenseController::class,'checkcategoryupdt'])->name('expense.category.update');
    Route::get('expense/category/delete/getdatacount',[ExpenseController::class,'getexpensedata'])->name('expense.category.getdatafordelet');
    Route::post('expense/category/delete',[ExpenseController::class,'expensecatdelete'])->name('expense.category.delete');

    // Expense For
    Route::get('expense/expensefor/list',[ExpenseController::class,'expenseforlist'])->name('expense.expensefor.list');
    Route::post('expense/expensefor/store',[ExpenseController::class,'expenseforstore'])->name('expense.expensefor.store');
    Route::post('expense/expensefor/check/name',[ExpenseController::class,'checkexpenseforname']);
    Route::get('expense/expensefor/edit',[ExpenseController::class,'expenseforedit'])->name('expense.expensefor.edit');
    Route::post('expense/expensefor/update',[ExpenseController::class,'expenseforupdate'])->name('expense.expensefor.update');
    Route::get('expense/expensefor/delete/getdatacount',[ExpenseController::class,'getexpensefordata'])->name('expense.expensefor.getdatafordelet');
    Route::post('expense/expensefor/delete',[ExpenseController::class,'expensefordelete'])->name('expense.expensefor.delete');

    // Fund & Advance Management Section
    Route::get('fund-advance/dashboard',[FundAdvanceController::class,'dashboard'])->name('fund_advance.dashboard');
    Route::get('fund-advance/parties/search',[FundAdvanceController::class,'partiesSearch'])->name('fund_advance.parties.search');

    Route::get('fund-advance/transactions',[FundAdvanceController::class,'index'])->name('fund_advance.transactions.list');
    Route::post('fund-advance/transactions/store',[FundAdvanceController::class,'store'])->name('fund_advance.transactions.store');
    Route::get('fund-advance/transactions/edit',[FundAdvanceController::class,'edit'])->name('fund_advance.transactions.edit');
    Route::post('fund-advance/transactions/update',[FundAdvanceController::class,'update'])->name('fund_advance.transactions.update');
    Route::get('fund-advance/transactions/view/{id}',[FundAdvanceController::class,'show'])->name('fund_advance.transactions.view');
    Route::post('fund-advance/transactions/cancel',[FundAdvanceController::class,'cancel'])->name('fund_advance.transactions.cancel');
    Route::post('fund-advance/transactions/filter/save',[FundAdvanceController::class,'saveFilter'])->name('fund_advance.transactions.saveFilter');
    Route::get('fund-advance/transactions/export/{format}',[FundAdvanceController::class,'export'])->name('fund_advance.transactions.export');
    Route::get('fund-advance/transactions/{id}/attachment/view',[FundAdvanceController::class,'viewAttachment'])->name('fund_advance.transactions.attachment.view');
    Route::get('fund-advance/transactions/{id}/attachment/download',[FundAdvanceController::class,'downloadAttachment'])->name('fund_advance.transactions.attachment.download');

    Route::get('fund-advance/settlements/pending',[FundAdvanceSettlementController::class,'pending'])->name('fund_advance.settlements.pending');
    Route::post('fund-advance/settlements/store',[FundAdvanceSettlementController::class,'store'])->name('fund_advance.settlements.store');
    Route::get('fund-advance/settlements/history',[FundAdvanceSettlementController::class,'history'])->name('fund_advance.settlements.history');
    Route::post('fund-advance/settlements/reverse',[FundAdvanceSettlementController::class,'reverse'])->name('fund_advance.settlements.reverse');
    Route::get('fund-advance/settlements/{id}/attachment/view',[FundAdvanceSettlementController::class,'viewAttachment'])->name('fund_advance.settlements.attachment.view');
    Route::get('fund-advance/settlements/{id}/attachment/download',[FundAdvanceSettlementController::class,'downloadAttachment'])->name('fund_advance.settlements.attachment.download');

    Route::get('fund-advance/ledgers/party',[FundAdvanceLedgerController::class,'partyLedger'])->name('fund_advance.ledgers.party');
    Route::get('fund-advance/ledgers/employee',[FundAdvanceLedgerController::class,'employeeLedger'])->name('fund_advance.ledgers.employee');
    Route::get('fund-advance/ledgers/fund',[FundAdvanceLedgerController::class,'fundLedger'])->name('fund_advance.ledgers.fund');

    Route::get('fund-advance/reports/outstanding',[FundAdvanceReportController::class,'outstanding'])->name('fund_advance.reports.outstanding');
    Route::get('fund-advance/reports/advances',[FundAdvanceReportController::class,'advances'])->name('fund_advance.reports.advances');
    Route::get('fund-advance/reports/loans',[FundAdvanceReportController::class,'loans'])->name('fund_advance.reports.loans');
    Route::get('fund-advance/reports/funds',[FundAdvanceReportController::class,'funds'])->name('fund_advance.reports.funds');
    Route::get('fund-advance/reports/settlements',[FundAdvanceReportController::class,'settlements'])->name('fund_advance.reports.settlements');
    Route::get('fund-advance/reports/transactions',[FundAdvanceReportController::class,'transactions'])->name('fund_advance.reports.transactions');
    Route::get('fund-advance/reports/{report}/export/{format}',[FundAdvanceReportController::class,'export'])->name('fund_advance.reports.export');

    // Candidate
    Route::get('candidate/list',[CandidateController::class,'index'])->name('candidate');
    Route::post('candidate/list/json',[CandidateController::class,'indexjson']);
    Route::post('candidate/store',[CandidateController::class,'store'])->name('candidate.store');
    Route::post('candidate/edit',[CandidateController::class,'edit']);
    Route::post('candidate/update',[CandidateController::class,'update'])->name('candidate.update');
    Route::post('candidate/work-city/update',[CandidateController::class,'updateWorkCity'])->name('candidate.updateWorkCity');
    Route::get('candidate/view/{id}',[CandidateController::class,'show'])->name('candidate.show');
    Route::post('candidate/reminder/store',[CandidateController::class,'candreminderstore'])->name('candidate.reminder.store');
    Route::get('candidate/reminder/edit',[CandidateController::class,'candreminderEdit'])->name('candidate.reminder.edit');
    Route::post('candidate/reminder/update',[CandidateController::class,'candreminderUpdate'])->name('candidate.reminder.update');
    Route::post('candidate/assicate/confirmby',[CandidateController::class,'candassocconfirmby'])->name('candidate.assoc.confirmby');
    Route::post('candidate/saveshortformcode',[CandidateController::class,'saveshortformcode'])->name('candidate.saveshortformcode');
    Route::post('/candidate/reset-status', [CandidateController::class,'resetStatus'])->name('candidate.resetstatus');
    Route::get('/candidate/video-delete/{id}', [CandidateController::class, 'deleteVideo'])->name('candidate.video.delete');

    Route::get('/recruitment-partners/search',[CandidateController::class, 'searchRecruitmentPartners'])->name('recruitment-partners.search');
    Route::get('/employers-by-partner',[CandidateController::class, 'employersByPartner'])->name('employers.by.partner');
    Route::get('/professions-by-employer',[CandidateController::class, 'professionsByEmployer'])->name('professions.by.employer');
    // testimonials
    Route::get('testimonial/list',[TestimonialController::class,'index'])->name('testimonial');
    Route::post('testimonial/generate_link',[TestimonialController::class,'generateLink'])->name('testimonials.generate_link');
    Route::get('testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('testimonials.edit');
    Route::post('testimonials/update', [TestimonialController::class, 'update'])->name('testimonials.update');
    Route::get('testimonials/{id}', [TestimonialController::class, 'show'])->name('testimonials.show');
    Route::post('testimonials/delete', [TestimonialController::class, 'destroy'])->name('testimonials.delete');
    Route::post('testimonials/delete-video', [TestimonialController::class, 'destroyVideo'])->name('testimonials.delete_video');
    Route::post('testimonial/saveFilter', [TestimonialController::class, 'saveFilter'])->name('testimonials.saveFilter');
    Route::post('testimonial/resetFilter', [TestimonialController::class, 'resetFilter'])->name('testimonials.resetFilter');
    Route::post('testimonials/approve', [TestimonialController::class, 'approve'])->name('testimonials.approve');
    Route::post('testimonials/reject', [TestimonialController::class, 'reject'])->name('testimonials.reject');
    Route::post('testimonials/mark-paid', [TestimonialController::class, 'markPaid'])->name('testimonials.mark_paid');
    Route::post('testimonials/video-received/update', [TestimonialController::class, 'updateVideoReceived'])->name('testimonials.video_received.update');
    Route::post('testimonials/social-media/update', [TestimonialController::class, 'updateSocialMedia'])->name('testimonials.social_media.update');
    Route::get('testimonials/{id}/activity', [TestimonialController::class, 'activity'])->name('testimonials.activity');
    Route::get('testimonials/{id}/payment-slip/view', [TestimonialController::class, 'viewPaymentSlip'])->name('testimonials.payment_slip.view');
    Route::get('testimonials/{id}/payment-slip/download', [TestimonialController::class, 'downloadPaymentSlip'])->name('testimonials.payment_slip.download');


    Route::get('/link/{encrypted_id}', [TestimonialController::class, 'show'])->name('testimonials.show');

    // google reviews
    Route::get('google-review/list',[GoogleReviewController::class,'index'])->name('google_review');
    Route::post('google-review/store',[GoogleReviewController::class,'store'])->name('google_review.store');
    Route::post('google-review/edit', [GoogleReviewController::class, 'edit'])->name('google_review.edit');
    Route::post('google-review/update', [GoogleReviewController::class, 'update'])->name('google_review.update');
    Route::get('google-review/{id}', [GoogleReviewController::class, 'show'])->name('google_review.show');
    Route::post('google-review/delete', [GoogleReviewController::class, 'destroy'])->name('google_review.delete');
    Route::post('google-review/saveFilter', [GoogleReviewController::class, 'saveFilter'])->name('google_review.saveFilter');
    Route::post('google-review/resetFilter', [GoogleReviewController::class, 'resetFilter'])->name('google_review.resetFilter');
    Route::post('google-review/approve', [GoogleReviewController::class, 'approve'])->name('google_review.approve');
    Route::post('google-review/reject', [GoogleReviewController::class, 'reject'])->name('google_review.reject');
    Route::post('google-review/mark-paid', [GoogleReviewController::class, 'markPaid'])->name('google_review.mark_paid');
    Route::get('google-review/{id}/screenshot/view', [GoogleReviewController::class, 'viewScreenshot'])->name('google_review.screenshot.view');
    Route::get('google-review/{id}/screenshot/download', [GoogleReviewController::class, 'downloadScreenshot'])->name('google_review.screenshot.download');
    Route::get('google-review/{id}/payment-slip/view', [GoogleReviewController::class, 'viewPaymentSlip'])->name('google_review.payment_slip.view');
    Route::get('google-review/{id}/payment-slip/download', [GoogleReviewController::class, 'downloadPaymentSlip'])->name('google_review.payment_slip.download');

    Route::Post('candidate/saveadminfilter',[CandidateController::class,'candsaveadminfilter'])->name('candidate.saveadminfilter');
    Route::Post('candidate/resetadminfilter',[CandidateController::class,'candresetadminfilter'])->name('candidate.resetadminfilter');

    Route::post('candidate/update/details',[CandidateController::class,'detailsupdate'])->name('candidate.update.details');
    Route::post('candidate/update/careoff',[CandidateController::class,'careoffupdate'])->name('candidate.update.careoff');
    Route::post('candidate/update/associate',[CandidateController::class,'assocupdate'])->name('candidate.update.assoc');
    Route::post('candidate/update/personal',[CandidateController::class,'personalupdate'])->name('candidate.update.personal');
    Route::post('candidate/update/passport',[CandidateController::class,'passportupdate'])->name('candidate.update.passport');
    Route::post('candidate/update/experience',[CandidateController::class,'experienceupdate'])->name('candidate.update.experience');
    Route::post('candidate/update/musaned',[CandidateController::class,'musanedupdate'])->name('candidate.update.musaned');
    // Medical Route
    Route::post('candidate/update/medicalFitupdate',[CandidateController::class,'medicalFitupdate'])->name('candidate.update.medicalFitupdate');
    Route::post('candidate/update/unfitmedical',[CandidateController::class,'unfitmedical'])->name('candidate.update.unfitmedical');
    Route::post('candidate/update/remedical',[CandidateController::class,'remedical'])->name('candidate.update.remedical');
    Route::post('candidate/update/onmedical',[CandidateController::class,'onmedical'])->name('candidate.update.onmedical');
    Route::post('candidate/update/waitingforfitnes',[CandidateController::class,'waitingforfitnes'])->name('candidate.update.waitingforfitnes');
    Route::post('candidate/update/medical',[CandidateController::class,'medicalupdate'])->name('candidate.update.medical');
    Route::post('candidate/update/mofano',[CandidateController::class,'mofaupdate'])->name('candidate.update.mofa');
    Route::post('candidate/update/flightdate',[CandidateController::class,'flightpdate'])->name('candidate.update.flight');

    Route::post('candidate/published/update/{id}',[CandidateController::class,'publishedSt'])->name('candidate.published.update');
    Route::post('canidate/bulk/published/update',[CandidateController::class,'publishedbulkSt'])->name('candidate.bulk.published.update');

    // Delete Candidate files
    Route::post('candidate/file/photo/delete/{id}',[CandidateController::class,'candPhotoDel'])->name('candidate.photoDel');
    Route::post('candidate/file/passport/delete/{id}',[CandidateController::class,'candPassportDel'])->name('candidate.passportDel');
    Route::post('candidate/file/passport/back/delete/{id}',[CandidateController::class,'candPassportBackDel'])->name('candidate.passportBackDel');
    Route::post('candidate/file/license/delete/{id}',[CandidateController::class,'candLicenseDel'])->name('candidate.licenseDel');
    Route::post('candidate/file/fullimage/delete/{id}',[CandidateController::class,'candfullimageDel'])->name('candidate.fullimageDel');
    Route::post('candidate/file/musaned/delete/{id}',[CandidateController::class,'candmusanedDel'])->name('candidate.musanedDel');
    Route::post('candidate/file/delete',[CandidateController::class,'candfileDel'])->name('candidate.candfile');

    Route::post('candidate/check/pass-no',[CandidateController::class,'checkpassno']);
    Route::post('candidate/check/pass-no/new',[CandidateController::class,'checkpassnoNew']);
    Route::post('candidate/check/mobile',[CandidateController::class,'checkmobno']);
    Route::post('candidate/check/edit/pass-no',[CandidateController::class,'edcheckpassno']);
    Route::post('candidate/check/edit/mobile',[CandidateController::class,'edcheckmobno']);

    Route::post('candidate/photo/upload/{id}',[CandidateController::class,'uploadPhoto'])->name('candidate.photo.store');
    Route::post('candidate/docs/upload/{id}',[CandidateController::class,'uploadDocs'])->name('candidate.docs.store');
    Route::post('candidate/docs2/upload/{id}',[CandidateController::class,'uploadDocs2'])->name('candidate.docs2.store');

    Route::get('candidate/publish/stage/{id}',[CandidateController::class,'publishstg'])->name('candidate.publish');
    Route::post('candidate/publish/stage/upload/pass',[CandidateController::class,'publishstgpass'])->name('candidate.publish.pass');
    Route::post('candidate/publish/stage/upload/skill-and-experience',[CandidateController::class,'publishskillandexp'])->name('candidate.publish.skillandexp');
    Route::post('candidate/publish/stage/upload/docsstage',[CandidateController::class,'publishDocs'])->name('candidate.publish.docsstg');
    Route::post('candidate/publish/stage/upload/publish',[CandidateController::class,'uppublish'])->name('candidate.publish.uppub');

    Route::get('candidate/publish/stage/skill-and-experience/back/{id}',[CandidateController::class,'backskillandexp'])->name('candidate.publish.backskillandexp');
    Route::get('candidate/publish/stage/docs-stage/back/{id}',[CandidateController::class,'backdocsstg'])->name('candidate.publish.backdocs');
    Route::get('candidate/publish/stage/publish-stg/back/{id}',[CandidateController::class,'backpublish'])->name('candidate.publish.backpublish');

    Route::post('candidate/filterList/update',[CandidateController::class,'updateFilterList']);

    Route::post('candidate/check/exist',[CandidateController::class,'checkdelcand']);
    Route::post('candidate/delete',[CandidateController::class,'candDel'])->name('candidate.delete');

    Route::get('candidate/execute/cvdataget',[CandidateController::class,'getCVData'])->name('candidate.getCVData');

    Route::post('candidate/booking/limit/update/{id}',[CandidateController::class,'candlimitupdt'])->name('booking.limit');

    Route::post('candidate/add-service-charge/{id}',[CandidateController::class,'candscharge'])->name('candscharge');

    Route::post('candidate/status',[CandidateController::class,'statusChange']);
    Route::post('candidate/status/update',[CandidateController::class,'statusUpdate'])->name('status.update');

    Route::post('candidate/add-payment/{id}',[CandidateController::class,'candamtStr'])->name('candamtStr');
    Route::post('candidate/addpayment/edit',[CandidateController::class,'candamtEdit']);
    Route::post('candidate/service/edit',[CandidateController::class,'SercandamtEdit']);
    Route::post('candidate/service/update',[CandidateController::class,'SercandamtUpdt'])->name('SercandamtUpdt');
    Route::post('candidate/addpayment/update',[CandidateController::class,'candamtUpdt'])->name('candamtUpdt');
    Route::post('candidate/check/transaction-number',[CandidateController::class,'checkTxn']);
    Route::post('candidate/candamt/delete',[CandidateController::class,'deleteAmt']);
    Route::post('candidate/service/delete',[CandidateController::class,'deleteSerAmt']);
    Route::post('candidate/servicecharge/deactive',[CandidateController::class,'deactiveServiceCharge']);
    Route::post('candidate/servicecharge/active',[CandidateController::class,'activeServiceCharge']);

    Route::get('candidate/candamt/paymentslip',[CandidateController::class,'paymentSlipView']);

    Route::post('candidate/videolink/{id}',[CandidateController::class,'videoLinkStore'])->name('candidate.video.store');
    Route::post('candidate/testvideolink/{id}',[CandidateController::class,'testvideoLinkStore'])->name('candidate.testvideo.store');
    Route::post('candidate/videoFile/{id}',[CandidateController::class,'videoFileStore'])->name('candidate.videoFile.store');
    Route::get('/videos/{videoName}', [CandidateController::class,'showVideo'])->name('video.show');

    // Transaction
    Route::get('candidate/transaction/list',[CandidateController::class,'candidateTransactionList'])->name('candidate.transactionList');
    Route::post('candidate/transaction/list/json',[CandidateController::class,'candidateTransactionListJson']);

    // Candidate Status
    Route::post('candidate/status/get',[CandidateController::class,'candstatusGet']);
    Route::post('candidate/status/newcandidateupdate',[CandidateController::class,'newcandidateupdate'])->name('candidate.newcandidateupdate');
    Route::post('candidate/status/publishedforslection',[CandidateController::class,'publishforselectionupdate'])->name('candidate.publishforselectionupdate');
    Route::post('candidate/status/selected',[CandidateController::class,'selectedupdate'])->name('candidate.selected');
    Route::post('candidate/status/visareceived',[CandidateController::class,'visareceivedupdate'])->name('candidate.visareceived');
    Route::post('candidate/status/passportinembassy',[CandidateController::class,'passportinembassyupdate'])->name('candidate.passportinembassy');
    Route::post('candidate/status/visastamped',[CandidateController::class,'visastampedupdate'])->name('candidate.visastamped');
    Route::post('candidate/status/appliedforemigration',[CandidateController::class,'appliedforemigrationupdate'])->name('candidate.appliedforemigration');
    Route::post('candidate/status/emigrationapproved',[CandidateController::class,'emigrationapprovedupdate'])->name('candidate.emigrationapproved');
    Route::post('candidate/status/waitingforticket',[CandidateController::class,'waitingforticketupdate'])->name('candidate.waitingforticket');
    Route::post('candidate/status/ticketconfirmed',[CandidateController::class,'ticketconfirmedupdate'])->name('candidate.ticketconfirmed');
    Route::post('candidate/status/deployed',[CandidateController::class,'deployedupdate'])->name('candidate.deployed');
    Route::post('candidate/status/cancelled',[CandidateController::class,'cancelledupdate'])->name('candidate.cancelled');
    Route::post('candidate/status/hold',[CandidateController::class,'holdupdate'])->name('candidate.hold');
    Route::post('candidate/status/visacancelled',[CandidateController::class,'visacancelledupdate'])->name('candidate.visacancelled');

    Route::post('candidate/previous-status-get',[CandidateController::class,'candprevstatusGet']);

    // Clients
    Route::get('client',[CustomerController::class,'index'])->name('client');
    Route::post('client/list/json',[CustomerController::class,'indexjson']);
    Route::post('client/delete',[CustomerController::class,'delete'])->name('client.delete');
    Route::get('client/edit',[CustomerController::class,'edit'])->name('client.edit');
    Route::post('client/update',[CustomerController::class,'update'])->name('client.update');
    Route::post('client/status/update', [CustomerController::class, 'updateStatus'])->name('client.status.update');
    Route::get('client/view/{id}', [CustomerController::class, 'view'])->name('client.view');

    Route::post('client/filterList/update',[CustomerController::class,'updateFilterList']);
    Route::post('client/saveFilter',[CustomerController::class,'saveFilter'])->name('client.saveFilter');
    Route::post('client-list/check/email',[CustomerController::class,'checkemailexist']);
    Route::post('client-list/check/mobile',[CustomerController::class,'checkmobileexists']);


    // Booking Filter
    Route::post('booking/filterList/update',[BookingController::class,'bookingFilter']);

    // Partner
    Route::get('partner',[PartnerController::class,'index'])->name('partner');
    Route::post('partner/list/json',[PartnerController::class,'indexjson']);
    Route::post('partner/store',[PartnerController::class,'store'])->name('partner.store');
    Route::post('partner/edit',[PartnerController::class,'editP']);
    Route::post('partner/update',[PartnerController::class,'update'])->name('partner.update');
    Route::get('partner/view/{id}',[PartnerController::class,'show'])->name('partner.show');
    Route::get('partner/get/data',[PartnerController::class,'getData']);
    Route::post("partner/status/update",[PartnerController::class,'statusUpdate'])->name('partner.statusupdate');
    Route::post('partner/portalstatus/update',[PartnerController::class,'portalstatusUpdate'])->name('partner.portalstatusUpdate');
    Route::post('partner/registrationstatus/update',[PartnerController::class,'registrationStatusUpdate'])->name('partner.registrationstatusUpdate');
    Route::get('partner/checkpartnerexists',[PartnerController::class,'checkpartnerexists']);
    Route::post('partner/delete',[PartnerController::class, 'deletePartner'])->name('partner.delete');

    Route::post('partner/add-service-charge',[PartnerController::class,'addsercharge'])->name('partner.addsercharge');
    Route::get('partner/add-service-charge/edit',[PartnerController::class,'editsercharge'])->name('partner.addsercharge.edit');
    Route::post('partner/add-service-charge/update',[PartnerController::class,'updatesercharge'])->name('partner.addsercharge.update');
    Route::post('partner/add-service-charge/updateStatus',[PartnerController::class,'updateStatusSc'])->name('partner.addsercharge.updateStatus');

    Route::post('partner/websitelogo/update/{id}',[PartnerController::class,'websitelogoupdt'])->name('partner.websitelogoupdt');

    Route::post('partner/cv-setting/update/{id}',[PartnerController::class,'cvsettingupdte'])->name('partner.cvsetting');

    Route::post('partner/account/update/{id}',[PartnerController::class,'accountUpdate'])->name('partner.account.update');
    Route::post('partner/personal/update/{id}',[PartnerController::class,'personaUpdate'])->name('partner.personal.update');
    Route::post('partner/password/update/{id}',[PartnerController::class,'passwordUpdate'])->name('partner.password.update');
    Route::post('partner/portal/update/{id}',[PartnerController::class,'portalUpdate'])->name('partner.portal.update');
    Route::post('partner/sharecv/update/{id}',[PartnerController::class,'sharecvUpdate'])->name('partner.sharecv.update');

    Route::post('partner/check/primary-email',[PartnerController::class,'checkprimemail']);
    Route::post('partner/check/owner-contact-number',[PartnerController::class,'checkocn']);
    Route::post('partner/check/edit/primary-email',[PartnerController::class,'edcheckprimemail']);
    Route::post('partner/check/edit/owner-contact-number',[PartnerController::class,'edcheckocn']);

    Route::post('partner/check/edit/secondary-email',[PartnerController::class,'edsecondaryemail']);
    Route::post('partner/check/edit/office-number',[PartnerController::class,'edcheckofficeno']);
    Route::post('partner/check/edit/primary-mobile',[PartnerController::class,'edcheckprimaryno']);
    Route::post('partner/check/edit/secondary-mobile',[PartnerController::class,'edchecksecondaryno']);

    Route::post('partner/check/edit/email',[PartnerController::class,'edcheckemail']);
    Route::post('partner/check/edit/username',[PartnerController::class,'edcheckusername']);

    Route::post('partner/check/edit/password',[PartnerController::class,'edcheckpassword']);

    Route::post('partner/filterList/update',[PartnerController::class,'updateFilterList']);

    // Place of Issue
    Route::get('place-of-issue',[PlaceofIssueController::class,'index'])->name('placeofissue');
    Route::post('place-of-issue/json',[PlaceofIssueController::class,'indexjson']);
    Route::post('place-of-issue/store',[PlaceofIssueController::class,'store'])->name('placeofissue.store');
    Route::post('place-of-issue/edit',[PlaceofIssueController::class,'edit']);
    Route::post('place-of-issue/update',[PlaceofIssueController::class,'update'])->name('placeofissue.update');

    Route::post('place-of-issue/check/name',[PlaceofIssueController::class,'checkname']);
    Route::post('place-of-issue/check/edit/name',[PlaceofIssueController::class,'edcheckname']);

    Route::post('place-of-issue/check/arname',[PlaceofIssueController::class,'checkarname']);
    Route::post('place-of-issue/check/edit/arname',[PlaceofIssueController::class,'edcheckarname']);

    Route::post('placeofissue/check/exist',[PlaceofIssueController::class,'delCheck']);
    Route::post('placeofissue/delete',[PlaceofIssueController::class,'poidelete'])->name('placeofissue.delete');

    // Expected work location
    Route::get('expected-work-location',[ExpectedWorkLocationController::class,'index'])->name('expworklocation');
    Route::post('expected-work-location/json',[ExpectedWorkLocationController::class,'indexjson']);
    Route::post('expected-work-location/check/name',[ExpectedWorkLocationController::class,'checkname']);
    Route::post('expected-work-location/check/arname',[ExpectedWorkLocationController::class,'checkarname']);
    Route::post('expected-work-location/store',[ExpectedWorkLocationController::class,'store'])->name('expworklocation.store');
    Route::post('expected-work-location/edit',[ExpectedWorkLocationController::class,'edit']);
    Route::post('expected-work-location/update',[ExpectedWorkLocationController::class,'update'])->name('expworklocation.update');

    // Country
    Route::get('country',[CountryCityController::class,'contindex'])->name('country');
    Route::post('country/json',[CountryCityController::class,'contindexjson']);
    Route::post('country/store',[CountryCityController::class,'contstore'])->name('country.store');

    Route::post('country/edit',[CountryCityController::class,'contEdit']);
    Route::post('country/update',[CountryCityController::class,'contUpdate'])->name('country.update');

    Route::post('country/check/exist',[CountryCityController::class,'delCheck']);
    Route::post('country/delete',[CountryCityController::class,'deleteC'])->name('country.delete');

    Route::post('country/check/name',[CountryCityController::class,'checkcontname']);
    Route::post('country/check/edit/name',[CountryCityController::class,'edcheckcontname']);

    // City
    Route::get('city',[CountryCityController::class,'cityindex'])->name('city');
    Route::post('city/json',[CountryCityController::class,'cityindexjson']);
    Route::post('city/store',[CountryCityController::class,'citystore'])->name('city.store');

    Route::post('city/edit',[CountryCityController::class,'edcity']);
    Route::post('city/update',[CountryCityController::class,'cityUpdate'])->name('city.update');

    Route::post('city/check/name',[CountryCityController::class,'checkcityname']);
    Route::post('city/check/edit/name',[CountryCityController::class,'edcheckcityname']);

    Route::post('city/check/arname',[CountryCityController::class,'checkcityarname']);
    Route::post('city/check/edit/arname',[CountryCityController::class,'edcheckcityarname']);

    Route::post('city/check/exist',[CountryCityController::class,'delCityex']);
    Route::post('city/delete',[CountryCityController::class,'cityDelete'])->name('city.delete');

    // Regions
    Route::get('regions',[CountryCityController::class,'regionIndex'])->name('region');
    Route::post('regions/json',[CountryCityController::class,'regionIndexJson']);
    Route::post('regions/store',[CountryCityController::class,'regionStore'])->name('region.store');

    Route::post('region/edit',[CountryCityController::class,'regionEdit']);
    Route::post('region/update',[CountryCityController::class,'regionUpdate'])->name('region.update');

    Route::post('region/check/exist',[CountryCityController::class,'delregionex']);
    Route::post('region/delete',[CountryCityController::class,'regionDel'])->name('region.delete');

    Route::post('regions/check/name',[CountryCityController::class,'checkregionname']);
    Route::post('regions/check/edit/name',[CountryCityController::class,'edcheckregionname']);

    Route::post('regions/check/arname',[CountryCityController::class,'checkregionarname']);
    Route::post('regions/check/edit/arname',[CountryCityController::class,'edcheckregionarname']);

    // Website Cnfiguration
    Route::get('website-configuration',[WebsiteConfigController::class,'index'])->name('webconfig');
    Route::post('website-configuration/store',[WebsiteConfigController::class,'store'])->name('webconfigStr');

     // ip tracker
    Route::get('ip-tracker',[IptrackerController::class,'index'])->name('ip-tracker');
    Route::post('ip-tracker-getjson',[IptrackerController::class,'getJson']);
    Route::post('ip-tracker/store',[IptrackerController::class,'store'])->name('webconfigStr');
    Route::post('ip-tracker/delete',[IptrackerController::class,'destroy'])->name('destroy');

    // Allowed IP Address
    Route::get('allowed-ip-address',[IpLoginController::class,'index'])->name('allowedipaddress.index');
    Route::post('allowed-ip-address/store',[IpLoginController::class,'store'])->name('allowedipaddress.store');
    Route::get('allowed-ip-address/edit',[IpLoginController::class,'edit'])->name('allowedipaddress.edit');
    Route::post('allowed-ip-address/update',[IpLoginController::class,'update'])->name('allowedipaddress.update');
    Route::post('allowed-ip-address/delete',[IpLoginController::class,'delete'])->name('allowedipaddress.delete');

    // Unauthorize Login
    Route::get('unauthorize-login',[IpLoginController::class,'unauthorizelist'])->name('unauthorizedlogin.index');


   // Device Management
   Route::get('/device', [AdminDeviceController::class, 'index'])->name('device.index');
   Route::get('/device/pending', [AdminDeviceController::class, 'pending'])->name('device.pending');

   // AJAX Actions
   Route::post('/device/approve', [AdminDeviceController::class, 'approve'])->name('device.approve');
   Route::post('/device/revoke', [AdminDeviceController::class, 'revoke'])->name('device.revoke');
   Route::post('/device/delete', [AdminDeviceController::class, 'delete'])->name('device.delete');

   // Toggle Approve All Login Setting
   Route::post('/device/toggle-approve-all-login', [AdminDeviceController::class, 'toggleApproveAllLogin'])->name('device.toggleApproveAllLogin');


   // Admin Activity (AJAX partial view)
   Route::get('/device/activity/{adminId}', [AdminDeviceController::class, 'showAdminActivity'])
       ->name('device.activity');


    // Show approval form
    Route::get('/device/approval/{device}', [AdminDeviceApprovalController::class, 'showApprovalForm'])
    ->name('device.approval.form');

   // Handle approval submission
   Route::post('/device/approval/{device}', [AdminDeviceApprovalController::class, 'handleApproval'])
       ->name('device.approval.submit');


    // remove from server

    Route::post('social-google-auth-secret/updated',[WebsiteConfigController::class,'socialgoogleauth'])->name('googleauthupdated');
    Route::post('social-google-auth-secret/check/client-id',[WebsiteConfigController::class,'checkgclientid']);
    Route::post('social-google-auth-secret/check/secret-id',[WebsiteConfigController::class,'checkgclientsecret']);

    Route::post('social-facebook-auth-secret/updated',[WebsiteConfigController::class,'socialfacebookauth'])->name('facebookauthupdated');
    Route::post('social-facebook-auth-secret/check/client-id',[WebsiteConfigController::class,'checkfclientid']);
    Route::post('social-facebook-auth-secret/check/secret-id',[WebsiteConfigController::class,'checkfclientsecret']);

    // Front End Configuration
    Route::get('front-end-website',[WebsiteConfigController::class,'frontendwebsite'])->name('frontwebsiteconfig');
    Route::post('front-end-website/upload/logo',[WebsiteConfigController::class,'frontendwebsiteUploadLogo'])->name('frontwebsiteconfig.uploadlogo');
    Route::post('front-end-website/about-us/store',[WebsiteConfigController::class,'frontendwebsiteaboutusstore'])->name('frontwebsiteconfig.aboutusstore');
    Route::post('front-end-website/contact-us/store',[WebsiteConfigController::class,'frontendwebsitecontactusstore'])->name('frontwebsiteconfig.contactusstore');
    Route::post('front-end-website/contact-us-bottom/store',[WebsiteConfigController::class,'frontendwebsitecontactusbottomstore'])->name('frontwebsiteconfig.contactusbottomstore');

    // Employer List
    Route::get('employer-list-plus',[EmployerController::class,'indexp'])->name('employer.listp');
    Route::post('employer-list-plus/json',[EmployerController::class,'indexpJson']);
    Route::get('employer-list/show/{id}',[EmployerController::class,'showp'])->name('employer.show');
    Route::get('employer-list-plus/payment-status/get',[EmployerController::class,'getpaymentstatus'])->name('employer.getpaymentstatus');
    Route::post('employer-list-plus/payment-status/update',[EmployerController::class,'paymentstatusupdate'])->name('employer.paymentstatusupdate');


    // Add Visa Details From Employer List
    Route::post('employer-list/add-visa-cand',[EmployerController::class,'AddVisaCandidate']);
    Route::delete('employer-list/remove-visa-cand/{id}',[EmployerController::class,'RemoveVisaCandidate']);
    Route::post('employer-listp/add-visa-json',[EmployerController::class,'VisaIndexpJson']);
    Route::post('employer-list/add-visa-json',[EmployerController::class,'VisaIndexJson']);
    Route::get('employer-listp/add-visa-view/{id}',[EmployerController::class,'VisaDetViewp'])->name('employer.visaDetshowp');
    Route::get('employer-list/add-visa-view/{id}',[EmployerController::class,'VisaDetView'])->name('employer.visaDetshow');
    Route::post('employer-list/add-visa/store',[EmployerController::class,'empVisaStore'])->name('employer.storeVisaDet');
    Route::get('employer-listp/add-visa/edit',[EmployerController::class,'empVisaEditp'])->name('employer.editVisaDetp');
    Route::get('employer-list/add-visa/edit',[EmployerController::class,'empVisaEdit'])->name('employer.editVisaDet');
    Route::post('employer-list/add-visa/checkVisa',[EmployerController::class,'empCheckVisa']);
    Route::post('employer-list/add-visa/edcheckVisa',[EmployerController::class,'empedcheckVisa']);
    Route::post('employer-listp/add-visa/update',[EmployerController::class,'empVisaUpdateP'])->name('employer.updateVisaDetP');
    Route::post('employer-list/add-visa/update',[EmployerController::class,'empVisaUpdate'])->name('employer.updateVisaDet');
    Route::post('employer-list/generate-workagreement/{id}',[PdfGeneratorController::class,'generateworkagreement'])->name('employer.generateworkagreement');
    Route::get('employer-list/download-workagreement/{emp_id}/{cand_id}',[PdfGeneratorController::class, 'downloadWorkAgreement'])->name('employer.downloadworkagreement');
    
    // Check Visa No is existing
    Route::post('employer-list-plus/checkVisa',[EmployerController::class,'checkEmpVisa']);
    Route::post('employer-list-plus/edcheckVisa',[EmployerController::class,'edcheckEmpVisa']);

    Route::get('employer-listp/delete',[EmployerController::class,'deleteEmployerp'])->name('employer.deletep');
    Route::get('employer-list/delete',[EmployerController::class,'deleteEmployer'])->name('employer.delete');
    Route::post('employer-list/status/update',[EmployerController::class,'empStatusUpdate'])->name('employer.statusUpdate');
    Route::post('employer-listp/status/update',[EmployerController::class,'empStatusUpdatep'])->name('employer.statusUpdatep');

    Route::post('employer-list/assign-candidate-to-employer',[EmployerController::class,'assigncandtoemp'])->name('employer.assigncandtoemp');
    Route::get('assign-emp-cand-list/get',[EmployerController::class,'assigngetdata'])->name('employer.assigngetdata');
    Route::post('employer-list/deassign-candidate/update',[EmployerController::class,'deassigngetdata'])->name('employer.deassigncandidate');

    // Save Filter
    Route::post('employer-list/savefilter',[EmployerController::class,'saveEmpFilter'])->name('employer.savefilter');
    Route::post('employer-list/resetfilter',[EmployerController::class,'resetEmpFilter'])->name('employer.resetfilter');

    Route::post('employer-listp/savefilter',[EmployerController::class,'saveEmpPFilter'])->name('employer.savefilterP');
    Route::post('employer-listp/resetfilter',[EmployerController::class,'resetEmpPFilter'])->name('employer.resetfilterP');

    // Employer List from Online B2C
    Route::get('employer-list',[EmployerController::class,'index'])->name('employer');
    Route::post('employer-list/json',[EmployerController::class,'indexJson']);

    // Associate List
    Route::get('associate-list',[AssociateController::class,'index'])->name('associate');
    Route::post('associate-list/json',[AssociateController::class,'indexJson']);
    Route::post('associate-list/store',[AssociateController::class,'store'])->name('associate.store');
    Route::post('associate-list/edit',[AssociateController::class,'edit']);
    Route::post('associate-list/update',[AssociateController::class,'update'])->name('associate.update');
    Route::post('associate-list/check/email',[AssociateController::class,'checkemail']);
    Route::post('associate-list/check/mobile',[AssociateController::class,'checkmobile']);
    Route::post('associate-list/candidate/check/exist',[AssociateController::class,'checkassoccand']);
    Route::post('associate-list/delete',[AssociateController::class,'delete'])->name('associate.delete');
    Route::post('associate-list/deactive',[AssociateController::class,'deactiveStatus'])->name('associate.deactiveStatus');
    Route::post('associate-list/active',[AssociateController::class,'activeStatus'])->name('associate.activeStatus');
    Route::get('associate-list/show/{id}',[AssociateController::class,'show'])->name('associate.show');

    Route::post('associate-list/primary-mobile/verification/{id}',[AssociateController::class,'primmobverif'])->name('associate.primmobverif');
    Route::post('associate-list/primart-mobile/get-otp',[AssociateController::class,'primary_getotp'])->name('associate.primary_getotp');
    Route::post('associate-list/checkprimaryotp',[AssociateController::class,'primary_otp_validate'])->name('associate.primary_otp_validate');

    Route::post('associate-list/secondary-mobile/verification/{id}',[AssociateController::class,'secmobverif'])->name('associate.secmobverif');
    Route::post('associate-list/secondary-mobile/get-otp',[AssociateController::class,'secondary_getotp'])->name('associate.secondary_getotp');
    Route::post('associate-list/checksecondaryotp',[AssociateController::class,'secondary_otp_validate'])->name('associate.secondary_otp_validate');

    Route::post('associate-list/email/verification/{id}',[AssociateController::class,'emailverif'])->name('associate.emailverif');
    Route::post('associate-list/email/get-otp',[AssociateController::class,'email_getotp'])->name('associate.email_getotp');
    Route::post('associate-list/checkemailotp',[AssociateController::class,'email_otp_validate'])->name('associate.email_otp_validate');

    Route::get('associate-list/generate-membership-id/{id}',[AssociateController::class,'generate_memberid'])->name('associate.generate_memberid');

    Route::post('associate-list/show/status/inactive/{id}',[AssociateController::class,'showinactivestatus'])->name('associate.show.inactive');
    Route::post('associate-list/show/status/active/{id}',[AssociateController::class,'showactivestatus'])->name('associate.show.active');

    Route::post('associate-list/show/contact/notverified/{id}',[AssociateController::class,'shownotverifiedcontact'])->name('associate.show.notverified');
    Route::post('associate-list/show/contact/verified/{id}',[AssociateController::class,'showverifiedcontact'])->name('associate.show.verified');

    Route::post('associate-list/saveFilter',[AssociateController::class,'saveFilter'])->name('associate.saveFilter');

    Route::post('employer/filterList/update',[EmployerController::class,'updateFilterList']);

    // Testing Mail
    Route::get('test/mail',[TestingController::class,'mailtest'])->name('mailtest');
    Route::post('test/mail/send',[TestingController::class,'mailsend'])->name('mailtest.send');

    // Email Configuration
    Route::get('mail/setup',[MailConfigController::class,'index'])->name('mail.setup.index');
    Route::post('mail/setup/json',[MailConfigController::class,'indexJson']);
    Route::post('mail/setup/store',[MailConfigController::class,'store'])->name('mail.setup.store');
    Route::post('mail/setup/edit',[MailConfigController::class,'edit']);
    Route::post('mail/setup/update',[MailConfigController::class,'update'])->name('mail.setup.update');
    Route::post('mail/setup/check/status/del',[MailConfigController::class,'checkDelStatus']);
    Route::post('mail/setup/delete',[MailConfigController::class,'delete'])->name('mail.setup.delete');

    // Car Known
    Route::get('car-known/list',[BackEndController::class,'carknown'])->name('carknown.list');
    Route::post('car-known/json',[BackEndController::class,'carknownjson']);
    Route::post('car-known/store',[BackEndController::class,'carknownStore'])->name('carknown.store');
    Route::post('car/edit',[BackEndController::class,'editcar']);
    Route::post('car-known/update',[BackEndController::class,'carknownUpdate'])->name('carknown.update');

    Route::post('carknown/check/exist',[BackEndController::class,'cardelexist']);
    Route::post('carknown/delete',[BackEndController::class,'cardelete'])->name('carknown.delete');


    Route::post('carknown/check/name',[BackEndController::class,'checkcarknown']);
    Route::post('carknown/check/edit/name',[BackEndController::class,'edcheckcarknown']);

    // Update Assign Status by clicking


    Route::post('lead/userassignlead/active',[LeadController::class,'userleadassignstatus'])->name('lead.userleadassign.update');


    // Permission for Staff
    Route::get('permission',[BackEndController::class,'permissionIndex'])->name('permission');
    Route::post('permission/get',[BackEndController::class,'getPermission']);
    Route::post('permission/store',[BackEndController::class,'strPermission'])->name('permission.store');

    // Personalise Class
    Route::get('personalise-class',[BackEndController::class,'personaliseclass'])->name('personaliseclass');
    Route::post('personalise-class/json',[BackEndController::class,'personaliseclassJson']);

    Route::post('personalise-class/edit',[BackEndController::class,'personaliseclassEdit']);

    Route::post('personalise-class/store',[BackEndController::class,'personaliseStr'])->name('personalise.store');
    Route::post('personalise-class/update',[BackEndController::class,'personaliseUpdate'])->name('personalise.update');
    Route::post('personalise/check/name',[BackEndController::class,'checkPersonalise']);
    Route::post('personalise/check/edit/name',[BackEndController::class,'edcheckPersonalise']);
    Route::post('personalise-class/delete',[BackEndController::class,'personaliseDelete'])->name('personalise.delete');

    // Template
    Route::get('template/list',[BackEndController::class,'templateList'])->name('template.index');
    Route::post('template/list/json',[BackEndController::class,'templateListJson']);
    Route::post('template/list/store',[BackEndController::class,'templateStore'])->name('template.store');
    Route::post('template/edit',[BackEndController::class,'templateEdit']);
    Route::post('template/update',[BackEndController::class,'templateUpdt'])->name('template.update');
    Route::post('template/delete',[BackEndController::class,'templateDel'])->name('template.delete');
    Route::post('template/active',[BackEndController::class,'templateActive'])->name('template.activeSt');
    Route::post('template/deactive',[BackEndController::class,'templateDeactive'])->name('template.deactiveSt');
    Route::post('template/get/public',[BackEndController::class,'temppubget']);
    Route::post('template/publishstatus',[BackEndController::class,'templatepubStatus'])->name('template.publish');
    Route::post('template/get/status',[BackEndController::class,'tempstatusget']);
    Route::post('template/changestatus',[BackEndController::class,'tempchangestatus'])->name('template.changestatus');

    // Order Received Panel
    Route::get('order-received-panel',[BackEndController::class,'orderrep'])->name('order.received.panel');
    Route::post('order-received-panel/json',[BackEndController::class,'orderrepJson']);
    Route::post('order-received-panel/check',[BackEndController::class,'orderrepcheck']);
    Route::post('order-received-panel/update',[BackEndController::class,'orderrepupdate'])->name('order.received.panel.update');

    // CV
    Route::get('candidate/resume/{id}',[PdfGeneratorController::class,'singleresume'])->name('candidate.single.resume');
    Route::get('candidate/cv/execute/{id}',[PdfGeneratorController::class,'cvexecute'])->name('candidate.cv.execute');
    Route::get('candidate/cv/execute/{id}/{partner}',[PdfGeneratorController::class,'singlecvexecutepartner'])->name('candidate.cv.partner.execute');
    Route::post('candidate/cv/execute/multiple-cv',[PdfGeneratorController::class,'multiplecvexecutepartner'])->name('candidate.cv.partner.mexecute');

    // CV Setting
    Route::get('cv-setting',[PdfGeneratorController::class,'cvsetting'])->name('cv.setting');
    Route::post('cv-setting/json',[PdfGeneratorController::class,'cvsettingjson']);
    Route::post('cv-setting/check/label-name',[PdfGeneratorController::class,'checklabelname']);
    Route::post('cv-setting/update',[PdfGeneratorController::class,'cvsettingupdate'])->name('cv.setting.update');
    Route::post('cv-setting/get/details',[PdfGeneratorController::class,'cvsettinggetdet']);

    Route::post('cv-setting/update/image',[PdfGeneratorController::class,'updateImagecv'])->name('cv.setting.update.image');

    Route::post('download/candidate-cv/single/{id}',[PdfGeneratorController::class,'downloadcvcomp'])->name('download.cvcompany');

    Route::post('execute/candidate-cv/multiple',[PdfGeneratorController::class,'cvExecuteMultiple'])->name('cvexecute.multiple');

    Route::get('download/candidate-cv/{partner}/{id}',[PdfGeneratorController::class,'cvsinglemultdownload']);

    Route::post('execute/multiple/cv/partner',[PdfGeneratorController::class,'cvmultiplepartexec'])->name('cvmultiplepartexec');

    Route::post('shared/multiple/cv/partner',[PdfGeneratorController::class,'cvmultiplepartshare'])->name('cvmultisharepartner');

    // Whatsapp API
    Route::get('whatsapp-api-list',[WhatsappApiController::class,'index'])->name('whatsapp.api');
    Route::post('whatsapp-api-list/json',[WhatsappApiController::class,'indexJson']);
    Route::post('whatsapp-api-list/store',[WhatsappApiController::class,'store'])->name('whatsapp.store');
    Route::post('whatsapp-api/edit',[WhatsappApiController::class,'edit']);
    Route::post('whatsapp-api/update',[WhatsappApiController::class,'update'])->name('whatsapp.update');
    Route::post('whatsapp-api-list/get/data',[WhatsappApiController::class,'getStatus']);
    Route::post('whatsapp-api-list/status/update',[WhatsappApiController::class,'statusUpdt'])->name('whatsapp.statusUptd');
    Route::post('whatsapp-api-list/delete',[WhatsappApiController::class,'delete'])->name('whatsapp.delete');
    Route::post('whatsapp-api/check/send',[WhatsappApiController::class,'sendTest'])->name('whatsapp.testsend');

    // Whatsapp API +
    Route::get('whatsapp-api-plus-list',[MetaWhatsappApiController::class,'index'])->name('metawhatsapp.api');
    Route::post('whatsapp-api-plus-list/json',[MetaWhatsappApiController::class,'indexJson']);
    Route::post('whatsapp-api-plus-list/store',[MetaWhatsappApiController::class,'store'])->name('metawhatsapp.api.store');
    Route::post('whatsapp-api-plus-list/edit',[MetaWhatsappApiController::class,'edit'])->name('metawhatsapp.api.edit');
    Route::post('whatsapp-api-plus-list/update',[MetaWhatsappApiController::class,'update'])->name('metawhatsapp.api.update');
    Route::post('whatsapp-api-plus-list/delete',[MetaWhatsappApiController::class,'delete'])->name('metawhatsapp.api.delete');
    Route::post('whatsapp-api-plus-list/get/data',[MetaWhatsappApiController::class,'getStatus']);
    Route::post('whatsapp-api-plus-list/status/update',[MetaWhatsappApiController::class,'statusUpdt'])->name('metawhatsapp.api.statusUptd');
    Route::get('whatsapp-api-plus/get/apiassignto',[MetaWhatsappApiController::class,'getAssignto'])->name('metawhatsapp.api.getassignto');
    Route::post('whatsapp-api-plus/get/apiassignto/store',[MetaWhatsappApiController::class,'getAssigntoStore'])->name('metawhatsapp.api.getassigntoStore');
    Route::get('whatsapp-api-plus-list/checkassignto',[MetaWhatsappApiController::class,'checkassignto']);
    Route::post('whatsapp-api-plus-list/check-mobile',[MetaWhatsappApiController::class,'checkMobileUnique'])->name('metawhatsapp.api.check-mobile');

    // Whatsapp Campaign
    Route::get('whatsapp-campaign-list',[WhatsappCampaignController::class,'index'])->name('whatsapp.campaign');
    Route::post('whatsapp-campaign-list/json',[WhatsappCampaignController::class,'indexJson']);
    Route::post('whatsapp-campaig-list/store',[WhatsappCampaignController::class,'store'])->name('whatsapp.campaign.store');
    Route::get('whatsapp-campaign-list/template/get',[WhatsappCampaignController::class,'getTemp']);
    Route::get('whatsapp-campaign-list/template/getList',[WhatsappCampaignController::class,'getTempList'])->name('whatsapp.campaign.getTempList');
    Route::get('whatsapp-campaign-list/report/{id}',[WhatsappCampaignController::class,'whatsappreportshow'])->name('whatsapp.campaign.report');


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                                            // SMS Template for Campaign
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // SMS API
    Route::get('sms-api-list',[SmsApiController::class,'index'])->name('sms.api');
    Route::post('sms-api-list/store',[SmsApiController::class,'store'])->name('sms.api.store');
    Route::post('sms-api-list/edit',[SmsApiController::class,'edit'])->name('sms.api.edit');
    Route::post('sms-api-list/update',[SmsApiController::class,'update'])->name('sms.api.update');
    Route::post('sms-api-list/delete',[SmsApiController::class,'delete'])->name('sms.api.delete');
    Route::post('sms-api-list/json',[SmsApiController::class,'indexJson']);
    Route::post('sms-api-list/get/data',[SmsApiController::class,'getStatus']);
    Route::post('sms-api-list/status/update',[SmsApiController::class,'statusUpdt'])->name('sms.api.statusUptd');
    Route::post('sms-api-list/get/apiassignto/store',[SmsApiController::class,'getAssigntoStore'])->name('sms.api.getassigntoStore');
    Route::get('sms-api-list/get/apiassignto',[SmsApiController::class,'getAssignto'])->name('sms.api.getassignto');
    Route::get('sms-api-list/checkassignto',[SmsApiController::class,'checkassignto']);

    // Whatsapp Template for Campaign
    Route::get('sms-template-list',[SMSTemplateController::class,'templateList'])->name('sms.smstemplateList');
    Route::post('sms-template-list/json',[SMSTemplateController::class,'templateListJson']);
    Route::Post('sms-template-list/store',[SMSTemplateController::class,'templateStore'])->name('sms.templateStore');
    Route::get('sms-template-list/edit',[SMSTemplateController::class,'templateEdit'])->name('sms.templateEdit');
    Route::post('sms-template-list/update',[SMSTemplateController::class,'templateUpdate'])->name('sms.templateUpdate');
    Route::post('sms-template-list/active',[SMSTemplateController::class,'activeStatus'])->name('sms.activeStatus');
    Route::post('sms-template-list/deactive',[SMSTemplateController::class,'deactiveStatus'])->name('sms.deactiveStatus');
    Route::post('sms-template-list/delete',[SMSTemplateController::class,'templateDelete'])->name('sms.templateDelete');
    Route::get('sms-template-list/status',[SMSTemplateController::class,'templateStatus'])->name('sms.templateStatus');
    Route::post('sms-template-list/status/update',[SMSTemplateController::class,'templateStatusUpdate'])->name('sms.templateStatusUpdate');
    Route::get('sms-template-list/template/public',[SMSTemplateController::class,'templatePublic'])->name('sms.templatePublic');
    Route::post('sms-template-list/template/public/update',[SMSTemplateController::class,'templatePublicUpdate'])->name('sms.templatePublicUpdate');

    // SMS Campaign
    Route::get('sms-campaign-list',[SMSCampaignController::class,'index'])->name('smsCampaign.list');
    Route::get('sms-campaign-list/get',[SMSCampaignController::class,'getListTemp'])->name('smsCampaign.getList');
    Route::get('sms-campaign-list/get/id',[SMSCampaignController::class,'getListTempID'])->name('smsCampaign.getListID');
    Route::post('sms-campaign-list/store',[SMSCampaignController::class,'store'])->name('smsCampaign.campaignStore');
    Route::get('sms-campaign-list/getList',[SMSCampaignController::class,'getsendList'])->name('smsCampaign.getsendList');
    Route::get('sms-campaign-list/view/{id}',[SMSCampaignController::class,'show'])->name('smsCampaign.campaignShow');


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                                            // Email Template for Campaign
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Email SMTP Configuration
    Route::get('email-smtp-list',[EmailSMTPController::class,'index'])->name('email.smtp');
    Route::post('email-smtp-list/json',[EmailSMTPController::class,'indexJson']);
    Route::post('email-smtp-list/store',[EmailSMTPController::class,'store'])->name('email.smtp.store');
    Route::post('email-smtp-list/edit',[EmailSMTPController::class,'edit'])->name('email.smtp.edit');
    Route::post('email-smtp-list/update',[EmailSMTPController::class,'update'])->name('email.smtp.update');
    Route::post('email-smtp-list/delete',[EmailSMTPController::class,'delete'])->name('email.smtp.delete');

    Route::post('email-smtp-list/get/data',[EmailSMTPController::class,'getStatus']);
    Route::post('email-smtp-list/status/update',[EmailSMTPController::class,'statusUpdt'])->name('email.smtp.statusUpdate');

    Route::post('email-smtp-list/get/apiassignto/store',[EmailSMTPController::class,'getAssigntoStore'])->name('email.smtp.getassigntoStore');
    Route::get('email-smtp-list/get/apiassignto',[EmailSMTPController::class,'getAssignto'])->name('email.smtp.getassignto');
    Route::get('email-smtp-list/checkassignto',[EmailSMTPController::class,'checkassignto']);

    // Email Template for Campaign
    Route::get('email-template-list', [EmailTemplateController::class,'templateList'])->name('email.templateList');
    Route::post('email-template-list/json', [EmailTemplateController::class,'templateListJson']);
    Route::post('email-template-list/store', [EmailTemplateController::class,'templateStore'])->name('email.templateStore');
    Route::get('email-template-list/edit', [EmailTemplateController::class,'templateEdit'])->name('email.templateEdit');
    Route::post('email-template-list/update', [EmailTemplateController::class,'templateUpdate'])->name('email.templateUpdate');
    Route::post('email-template-list/active', [EmailTemplateController::class,'activeStatus'])->name('email.activeStatus');
    Route::post('email-template-list/deactive', [EmailTemplateController::class,'deactiveStatus'])->name('email.deactiveStatus');
    Route::post('email-template-list/delete', [EmailTemplateController::class,'templateDelete'])->name('email.templateDelete');
    Route::get('email-template-list/status', [EmailTemplateController::class,'templateStatus'])->name('email.templateStatus');
    Route::post('email-template-list/status/update', [EmailTemplateController::class,'templateStatusUpdate'])->name('email.templateStatusUpdate');
    Route::get('email-template-list/template/public', [EmailTemplateController::class,'templatePublic'])->name('email.templatePublic');
    Route::post('email-template-list/template/public/update', [EmailTemplateController::class,'templatePublicUpdate'])->name('email.templatePublicUpdate');
    Route::post('email-template-list/delete-attachment', [EmailTemplateController::class, 'deleteAttachment'])
    ->name('email.deleteAttachment');

    // Email Campaign
    Route::get('email-campaign-list',[EmailCampaignController::class,'index'])->name('emailCampaign.list');
    Route::get('email-campaign-list/get',[EmailCampaignController::class,'getListTemp'])->name('emailCampaign.getList');
    Route::get('email-campaign-list/get/id',[EmailCampaignController::class,'getListTempID'])->name('emailCampaign.getListID');
    Route::post('email-campaign-list/store',[EmailCampaignController::class,'store'])->name('emailCampaign.store');
    Route::get('email-campaign-list/getList',[EmailCampaignController::class,'getsendList'])->name('emailCampaign.getsendList');
    Route::get('email-campaign-list/view/{id}',[EmailCampaignController::class,'show'])->name('emailCampaign.show');
    
    // Email Automation
    Route::get('email-automation/list',[EmailAutomationController::class,'index'])->name('emailautomation.list');
    Route::post('email-automation/list/json',[EmailAutomationController::class,'indexJson']);
    Route::post('email-automation/store',[EmailAutomationController::class,'store'])->name('emailautomation.store');
    Route::post('email-automation/update',[EmailAutomationController::class,'update'])->name('emailautomation.update');
   
    Route::get('email-automation/template/{template_for}', [EmailAutomationController::class,'templateFor'])->name('emailautomation.templateFor');
    Route::post('email-automation/template/list/json', [EmailAutomationController::class,'templateForJson'])->name('emailautomation.templateForJson');
    Route::get('email-automation/templateFor/getListID',[EmailAutomationController::class,'getListTempID'])->name('emailautomation.templateFor.getListID');

    Route::get('email-automation/template/edit/{id}',[EmailAutomationController::class,'templateForEdit'])->name('emailautomation.templateFor.edit');
    Route::post('email-automation/status/update',[EmailAutomationController::class,'statusUpdt'])->name('emailautomation.status.update');

    Route::get('email-automation/status/get',[EmailAutomationController::class,'getStatus'])->name('emailautomation.getStatus');
    Route::post('email-automation/template/delete', [EmailAutomationController::class,'destroy'])->name('emailautomation.destroy');
 
    // Meta Whatsapp Campaign
    Route::get('meta-whatsapp-campaign-list',[MetaWhatsappController::class,'index'])->name('metawhatsapp.campaign');
    Route::post('meta-whatsapp-campaign-list/json',[MetaWhatsappController::class,'indexJson']);  
    Route::post('meta-whatsapp-campaign-list/filter/save',[MetaWhatsappController::class, 'saveCampaignFilter'])->name('metawhatsapp.filter.save');
    Route::post('meta-whatsapp-campaign-list/filter/reset',[MetaWhatsappController::class, 'resetCampaignFilter'])->name('metawhatsapp.filter.reset');

    
    Route::post('meta-whatsapp-campaign-list/store',[MetaWhatsappController::class,'store_leatest'])->name('metawhatsapp.campaignStore');
    // Route::post('meta-whatsapp-campaign-list/store',[MetaWhatsappController::class,'store'])->name('metawhatsapp.campaignStore');

    Route::get('meta-whatsapp-campaign-list/view/{id}',[MetaWhatsappController::class,'show'])->name('metawhatsapp.campaignShow');
    Route::get('meta-whatsapp-template-list/get',[MetaWhatsappController::class,'getListTemp'])->name('metatemplate.getList');
    Route::get('meta-whatsapp-template-list/auto-message/get',[MetaWhatsappController::class,'getListTempAuto'])->name('metatemplate.getListAuto');
    Route::get('meta-whatsapp-template-list/get/id',[MetaWhatsappController::class,'getListTempID'])->name('metatemplate.getListID');   
    Route::post('meta-whatsapp-campaign-list/bulk/resend/message',[MetaWhatsappController::class,'messageResendWhatsapp'])->name('metawhatsapp.campaign.resend');
    Route::get('meta-whatsapp-campaign-list/getList',[MetaWhatsappController::class,'getsendList'])->name('metawhatsapp.getsendList');
    Route::post('meta-whatsapp-campaign-list/delete',[MetaWhatsappController::class, 'campaignDelete'])->name('metawhatsapp.campaign.delete');
    Route::post('meta-whatsapp-campaign-list/update-mobile', [MetaWhatsappController::class, 'updateMobile'])->name('metawhatsapp.campaign.update.mobile');

    // Whatsapp Template for Campaign
    Route::get('whatsapp-template-list',[WhatsappCampaignController::class,'wtemplateList'])->name('whatsapp.templateList');
    Route::post('whatsapp-template-list/json',[WhatsappCampaignController::class,'wtemplateListJson']);
    Route::post('whatsapp-template-list/store',[WhatsappCampaignController::class,'wtemplateStore'])->name('whatsapp.templateStore');
    Route::post('whatsapp-template-list/edit',[WhatsappCampaignController::class,'wtemplateEdit']);
    Route::post('whatsapp-template-list/update',[WhatsappCampaignController::class,'wtemplateUpdate'])->name('whatsapp.templateUpdate');
    Route::post('whatsapp-template-list/delete',[WhatsappCampaignController::class,'wtemplateDelete'])->name('whatsapp.templateDel');
    Route::post('whatsapp-template-list/getStatus',[WhatsappCampaignController::class,'wtempGetStatus'])->name('whatsapp.getStatus');
    Route::post('whatsapp-template-list/getStatus/update',[WhatsappCampaignController::class,'wtempGetStatusUpdate'])->name('whatsapp.updateGetStatus');

    // Whatsapp Meta Template For Campaign
    Route::get('whatsapp-meta-template-list',[WhatsappCampaignController::class,'mwtemplateList'])->name('whatsapp.metatemplateList');
    Route::get('whatsapp-meta-template-list-back',[WhatsappCampaignController::class,'mwtemplateListBack'])->name('whatsapp.metatemplateListBack');
    Route::post('whatsapp-meta-template-lis/get-template-variables', [WhatsappCampaignController::class, 'getTemplateVariables'])
    ->name('whatsapp.getTemplateVariables');

    Route::post('whatsapp-meta-template-list/json',[WhatsappCampaignController::class,'mwtemplateListJson']);
    Route::Post('whatsapp-meta-template-list/store',[WhatsappCampaignController::class,'mwtemplateStore'])->name('whatsapp.metatemplateStore');
    Route::post('whatsapp-meta-template-list/duplicate',[WhatsappCampaignController::class, 'duplicateTemplate'])->name('whatsapp.metatemplateduplicate');
    Route::get('whatsapp-meta-template-list/edit',[WhatsappCampaignController::class,'mwtemplateEdit'])->name('whatsapp.metatemplateedit');
    Route::post('whatsapp-meta-template-list/update',[WhatsappCampaignController::class,'mwtemplateUpdate'])->name('whatsapp.metatemplateupdate');
    Route::get('whatsapp-meta-template-list/template/status',[WhatsappCampaignController::class,'metatemplateStatus'])->name('whatsapp.metatemplateStatus');
    Route::post('whatsapp-meta-template-list/template/status/update',[WhatsappCampaignController::class,'metatemplateStatusUpdate'])->name('whatsapp.metatemplateStatusUpdate');
    Route::get('whatsapp-meta-template-list/template/public',[WhatsappCampaignController::class,'metatemplatePublic'])->name('whatsapp.metatemplatePublic');
    Route::post('whatsapp-meta-template-list/template/public/update',[WhatsappCampaignController::class,'metatemplatePublicUpdate'])->name('whatsapp.metatemplatePublicUpdate');
    Route::post('whatsapp-meta-template-list/delete',[WhatsappCampaignController::class,'metatemplateDelete'])->name('whatsapp.metatemplateDelete');

    Route::get('whatsapp-meta-template-list/template/get',[WhatsappCampaignController::class,'metatemplateGet'])->name('whatsapp.metatemplateget');

    Route::get('whatsapp-chat-redirect-url/list',[WhatsappCampaignController::class,'whatsappchatredirecturllist'])->name('whatsapp.chatredirecturllist');
    Route::get('whatsapp-chat-redirect-url/staff/det',[WhatsappCampaignController::class,'whatsappredirecturlgetStaff'])->name('whatsapp.getstaffdet');
    Route::post('whatsapp-chat-redirect-url/store',[WhatsappCampaignController::class,'whatsappchatredirecturlStr'])->name('whatsapp.chatredirecturlStore');

    Route::get('whatsapp-chat-redirect-url/edit',[WhatsappCampaignController::class,'whatsappchatredirecturlEdit'])->name('whatsapp.editchatredirecturl');
    Route::post('whatsapp-chat-redirect-url/update',[WhatsappCampaignController::class,'whatsappchatredirecturlUpdt'])->name('whatsapp.chatredirecturlUpdate');
    Route::post('whatsapp-chat-redirect-url/delete',[WhatsappCampaignController::class,'whatsappchatredirecturlDelete'])->name('whatsapp.chatredirecturlDelete');

    // Team-member page

    Route::get('team-member-page/list',[TeamMemberPageController::class,'index'])->name('team-member-page.list');
    Route::post('team-member-page/store',[TeamMemberPageController::class,'store'])->name('team-member-page.store');
    Route::get('team-member-page/edit',[TeamMemberPageController::class,'edit'])->name('team-member-page.edit');
    Route::post('team-member-page/update',[TeamMemberPageController::class,'update'])->name('team-member-page.update');
    Route::post('team-member-page/delete',[TeamMemberPageController::class,'delete'])->name('team-member-page.delete');

    // dynamic Image Url
    Route::get('dynamic-image-url/list',[ImageUrlController::class,'index'])->name('dynamic-image-url.list');
    Route::post('dynamic-image-url/store',[ImageUrlController::class,'store'])->name('dynamic-image-url.store');
    Route::get('dynamic-image-url/edit',[ImageUrlController::class,'edit'])->name('dynamic-image-url.edit');
    Route::post('dynamic-image-url/update',[ImageUrlController::class,'update'])->name('dynamic-image-url.update');
    Route::post('dynamic-image-url/delete',[ImageUrlController::class,'delete'])->name('dynamic-image-url.delete');


    // Image Host
    Route::get('imagehost-list',[ImageHostController::class,'index'])->name('imagehost.list');
    Route::post('imagehost-list/store',[ImageHostController::class,'store'])->name('imagehost.store');
    Route::post('imagehost-list/delete',[ImageHostController::class,'delete'])->name('imagehost.delete');

    // Auto Search List
    Route::get('city-search/list',[SearchController::class,'citySearch'])->name('auto.citysearch');

    // Cost For Custorm
    Route::get('customer-cost-departure-list',[BackEndController::class,'custcostdepList'])->name('customer.costlist');
    Route::post('customer-cost-departure-list/json',[BackEndController::class,'custcostdepListJson']);
    Route::post('customer-cost-departure-list/store',[BackEndController::class,'custcostdepListStr'])->name('customer.costStr');
    Route::post('customer-cost-departure-list/edit',[BackEndController::class,'custcostdepListEdit']);
    Route::post('customer-cost-departure-list/update',[BackEndController::class,'custcostdepListUpdt'])->name('customer.costUpdt');

    // Booking Requirements
    Route::get('booking-requirement',[BackEndController::class,'bookingRequirement'])->name('booking.requirement');
    Route::post('booking-requirement/eng',[BackEndController::class,'checkRequirementEng']);
    Route::post('booking-requirement/ar',[BackEndController::class,'checkRequirementAr']);
    Route::post('booking-requirement/store',[BackEndController::class,'bookingRequirementStr'])->name('booking.requirementStr');

    // lead auto assign
    Route::get('lead-assign-change-status',[LeadController::class,'LeadAutoAssign'])->name('leads.auto_assign.change');
    Route::post('lead-assign-update-status',[LeadController::class,'changeLeadAutoAssignStatus'])->name('leads.auto_assign.update');


    // Contact Partners
    Route::get('contact-list',[ContactpController::class,'refinedcontact'])->name('contact.list');
    Route::get('contact-list-new',[ContactpController::class,'index_new'])->name('contact.list-new');

    Route::post('/contact-list/store_company_data', [ContactpController::class, 'companyDataStore'])->name('contact.store_company_data');

    // Route::get('contact-list',[ContactpController::class,'index'])->name('contact.list');
    Route::post('contact-list/json',[ContactpController::class,'indexJson']);
    Route::post('contact-list/store',[ContactpController::class,'store'])->name('contact.store');
    Route::post('contact-list/edit',[ContactpController::class,'edit']);
    Route::post('contact-list/update',[ContactpController::class,'update'])->name('contact.update');
    Route::get('contact-list/view/{id}',[ContactpController::class,'show'])->name('contact.show');
    Route::post('contact-list/delete',[ContactpController::class,'delete'])->name('contact.delete');
    Route::get('contact-list/check/group/limit',[ContactpController::class,'checkGrpLimit'])->name('contact.checkgroupLimit');
    Route::get('contact-list/unsubscribe/report',[ContactpController::class,'unsubscribereport'])->name('contact.unsubscribereport');
    Route::post('contact-list/bulk/delete',[ContactpController::class,'bulkdelete'])->name('contact.bulk.delete');

    Route::post('contact-list/csv-preview',[ContactpController::class,'previewCsvAllcontact'])->name('contactPlus.csv.import.preview');
    Route::post('contact-list/csv-upload',[ContactpController::class,'uploadCsvAllcontact'])->name('contactPlus.csv.upload');
    Route::post('contact-list/bulkexport', [ContactpController::class, 'bulkExport'])->name('contactPlus.bulkexport');

    Route::get('contact-plus-export-history',[BackEndController::class,'contact_plus_export_history'])->name('contact_plus_export_history');

    Route::get('contact-plus-export-history/json',[BackEndController::class,'contact_plus_export_history_json'])->name('contact_plus_export_history.json');
    Route::delete('contact-plus-export-history/{id}',[BackEndController::class, 'deleteExportPlusHistory'])->name('contact_plus_export_history.delete');
    Route::get('contact-plus-export-history/download/{id}', [BackEndController::class, 'downloadExportPlus'])->name('contact_plus_export_history.download');

    // Export to email.qamr.in Portal
    Route::post('email-qamr-portal/config',[ContactPlusEmailPortalController::class,'saveConfig'])->name('email_qamr_portal.config.save');
    Route::post('email-qamr-portal/lists',[ContactPlusEmailPortalController::class,'fetchLists'])->name('email_qamr_portal.lists');
    Route::get('email-qamr-portal/contact-fields',[ContactPlusEmailPortalController::class,'contactFields'])->name('email_qamr_portal.contact_fields');
    Route::post('email-qamr-portal/list-fields',[ContactPlusEmailPortalController::class,'listFields'])->name('email_qamr_portal.list_fields');
    Route::post('email-qamr-portal/filtered-count',[ContactPlusEmailPortalController::class,'filteredCount'])->name('email_qamr_portal.filtered_count');
    Route::post('email-qamr-portal/export',[ContactPlusEmailPortalController::class,'export'])->name('email_qamr_portal.export');
    Route::get('email-qamr-portal/export-history',[ContactPlusEmailPortalController::class,'history'])->name('email_qamr_portal.history');
    Route::get('email-qamr-portal/export-history/json',[ContactPlusEmailPortalController::class,'historyJson'])->name('email_qamr_portal.history.json');
    Route::get('email-qamr-portal/export-history/{id}/refresh',[ContactPlusEmailPortalController::class,'refreshHistoryRow'])->name('email_qamr_portal.history.refresh');
    Route::delete('email-qamr-portal/export-history/{id}',[ContactPlusEmailPortalController::class, 'deleteHistory'])->name('email_qamr_portal.history.delete');
    Route::post('email-qamr-portal/export-history/bulk-delete',[ContactPlusEmailPortalController::class, 'bulkDeleteHistory'])->name('email_qamr_portal.history.bulk_delete');
    Route::post('email-qamr-portal/export-history/filter/save',[ContactPlusEmailPortalController::class, 'saveExportHistoryFilter'])->name('email_qamr_portal.history.filter.save');
    Route::post('email-qamr-portal/export-history/filter/reset',[ContactPlusEmailPortalController::class, 'resetExportHistoryFilter'])->name('email_qamr_portal.history.filter.reset');

    // Export to email.qamr.in Portal - All Contact (shares the token-save-less lists endpoint above; config is module-specific)
    Route::post('email-qamr-portal/allcontact/config',[AllContactEmailPortalController::class,'saveConfig'])->name('email_qamr_portal.allcontact.config.save');
    Route::get('email-qamr-portal/allcontact/contact-fields',[AllContactEmailPortalController::class,'contactFields'])->name('email_qamr_portal.allcontact.contact_fields');
    Route::post('email-qamr-portal/allcontact/list-fields',[AllContactEmailPortalController::class,'listFields'])->name('email_qamr_portal.allcontact.list_fields');
    Route::post('email-qamr-portal/allcontact/filtered-count',[AllContactEmailPortalController::class,'filteredCount'])->name('email_qamr_portal.allcontact.filtered_count');
    Route::post('email-qamr-portal/allcontact/export',[AllContactEmailPortalController::class,'export'])->name('email_qamr_portal.allcontact.export');
    Route::get('email-qamr-portal/allcontact/export-history',[AllContactEmailPortalController::class,'history'])->name('email_qamr_portal.allcontact.history');
    Route::get('email-qamr-portal/allcontact/export-history/json',[AllContactEmailPortalController::class,'historyJson'])->name('email_qamr_portal.allcontact.history.json');
    Route::get('email-qamr-portal/allcontact/export-history/{id}/refresh',[AllContactEmailPortalController::class,'refreshHistoryRow'])->name('email_qamr_portal.allcontact.history.refresh');
    Route::delete('email-qamr-portal/allcontact/export-history/{id}',[AllContactEmailPortalController::class, 'deleteHistory'])->name('email_qamr_portal.allcontact.history.delete');
    Route::post('email-qamr-portal/allcontact/export-history/bulk-delete',[AllContactEmailPortalController::class, 'bulkDeleteHistory'])->name('email_qamr_portal.allcontact.history.bulk_delete');
    Route::post('email-qamr-portal/allcontact/export-history/filter/save',[AllContactEmailPortalController::class, 'saveExportHistoryFilter'])->name('email_qamr_portal.allcontact.history.filter.save');
    Route::post('email-qamr-portal/allcontact/export-history/filter/reset',[AllContactEmailPortalController::class, 'resetExportHistoryFilter'])->name('email_qamr_portal.allcontact.history.filter.reset');

    // All COntact List
    Route::get('allcontact-list',[AllContactController::class,'index_new'])->name('allcontact.list');
    Route::get('allcontact/filter/load',[AllcontactController::class,'loadFilter'])->name('allcontact.filter.load');
    Route::get('allcontact/load-all-filters',[AllcontactController::class,'loadAllFilters'])->name('allcontact.loadallfilters');
    Route::get('allcontact-list/json',[AllcontactController::class,'jsonData'])->name('allcontact.json');
    Route::get('/allcontact/groupwise-report',[AllContactController::class,'groupWiseReport'])->name('allcontact.groupwise.report');

    Route::post('allcontact/bulkexport', [AllContactController::class, 'bulkExport'])->name('allcontact.bulkexport');
    Route::get('contact-export-history',[BackEndController::class,'contact_export_history'])->name('contact_export_history');
    Route::get('contact-export-history/json',[BackEndController::class,'contact_export_history_json'])->name('contact_export_history.json');
    Route::delete('contact-export-history/{id}',[BackEndController::class, 'deleteExportHistory'])->name('contact_export_history.delete');
    Route::get('contact-export-history/download/{id}', [BackEndController::class, 'downloadExport'])->name('contact_export_history.download');

    Route::get('/allcontact/sync-with-google', [GoogleContactController::class, 'index'])->name('allcontact.sync-with-google');
    Route::get('/allcontact/google/redirect',[GoogleContactController::class, 'redirect'])->name('allcontact.google.redirect');
    Route::post('/google-account/delete', [GoogleContactController::class, 'delete'])->name('google-account.delete');

    Route::get('sync-contact-to-lead',[BackEndController::class,'sync_contact_to_lead'])->name('sync_contact_to_lead');
    Route::get('sync-contact-to-lead/json',[BackEndController::class,'jsonData'])->name('sync_contact_to_lead.json');

    Route::get('allcontact-list-new',[AllContactController::class,'index'])->name('allcontact.list.new');


    Route::get('allcontact-list/show/{id}',[AllContactController::class,'show'])->name('allcontact.show');
    Route::get('allcontact-list/edit',[AllContactController::class,'edit'])->name('allcontact.edit');
    Route::get('allcontact-list/getleadstage',[AllContactController::class,'getleadstage'])->name('allcontact.getleadstage');
    Route::post('allcontact-list/getleadstage/update',[AllContactController::class,'getleadstageupdt'])->name('allcontact.getleadstageupdate');
    Route::post('allcontact-list/update-lead-priority',[AllContactController::class,'leadpriorityupdate'])->name('allcontact.leadpriorityupdate');
    Route::get('allcontact-list/getoptin',[AllContactController::class,'allgetoptin'])->name('allcontact.getoptin');
    Route::post('allcontact-list/otpin/update',[AllContactController::class,'optinoutupdt'])->name('allcontact.optinupdate');
    Route::post('allcontact-list/bulkleadownertransfer',[AllContactController::class,'allbulkleadownertransfer'])->name('allcontact.bulkleadownertransfer');
    Route::post('allcontact-list/bulkcareofftransfer',[AllContactController::class,'allbulkcareofftransfer'])->name('allcontact.bulkcareofftransfer');
    Route::post('allcontact-list/bulkgrouptransfer',[AllContactController::class,'allbulkgrouptransfer'])->name('allcontact.bulkgrouptransfer');
    Route::get('allcontact-list/checkgroupLimit',[AllContactController::class,'allcheckgroupLimit'])->name('allcontact.checkgroupLimit');
    Route::post('allcontact-list/bulksendwhatsapp',[AllContactController::class,'allbulksendwhatsapp'])->name('allcontact.bulksendwhatsapp');
    Route::post('allcontact-list/update/lifecyclestatus',[AllContactController::class,"updatelifecyclestatus"])->name('allcontact.updatelifecyclestatus');
    Route::post('allcontact-list/update/leadstage',[AllContactController::class,'updateleadstage'])->name('allcontact.updateleadstage');
    Route::post('allcontact-lis/show/send-whatsapp/{id}',[AllContactController::class,'sendcontactwhatsapp'])->name('allcontact.sendwhatsapp');
    Route::post('allcontact-list/addreminder',[AllContactController::class,'addreminder'])->name('allcontact.addreminder');
    Route::post('allcontact-list/addnotes',[AllContactController::class,'addnotes'])->name('allcontact.addnotes');
    Route::post('allcontact-list/deletenotes',[AllContactController::class,'deletenotes'])->name('allcontact.deletenotes');
    Route::get('allcontact-list/addreminder/edit', [AllContactController::class,'addreminderedit'])->name('allcontact.addreminder.edit');
    Route::post('allcontact-list/updateaddreminder',[AllContactController::class,'updateaddreminder'])->name('allcontact.updateaddreminder');
    Route::post('allcontact-list/update',[AllContactController::class,'update'])->name('allcontact.update');
    Route::post('allcontact-list/store',[AllContactController::class,'store'])->name('allcontact.store');
    Route::post('allcontact-list/bulk/delete',[AllContactController::class,'bulkdeleteallc'])->name('allcontact.bulk.delete');
    Route::post('allcoontact-list/delete',[AllContactController::class,'deleteContact'])->name('allcontact.delete');
    Route::post('allcontact-list/upload-file', [AllContactController::class, 'uploadFile'])->name('allcontact.uploadFile');
    Route::delete('allcontact-list/delete-file/{id}', [AllContactController::class, 'deleteFile'])->name('allcontact.deleteFile');

    Route::post('allcontact-list/short/check-mobile',[AllContactController::class,'checkshortmobile'])->name('allcontact.checkshortmobile');
    Route::post('check-mobile-details', [AllcontactController::class, 'checkMobileDetails'])->name('checkMobileDetails');

    Route::post('allcontact-list/short/upload-store',[AllContactController::class,'uploadshortstore'])->name('allcontact.uploadstore');

    Route::post('allcontact-list/check-mobile',[AllContactController::class,'checkmobileall'])->name('allcontact.checkmobile');

    Route::post('allcontact-list/csv-preview',[AllContactController::class,'previewCsvAllcontact'])->name('allcontact.csv.import.preview');
    Route::post('allcontact-list/csv-upload',[AllContactController::class,'uploadCsvAllcontact'])->name('allcontact.csv.upload');

    Route::post('allcontact/import/store', [AllcontactController::class, 'storeBulkAllcontact'])->name('allcontact.import.store');

    Route::post('allcontact-list/bulk/updatecountrycode',[AllContactController::class,'bulkupdatecountrycode'])->name('allcontact.bulkupdatecountrycode');

    Route::post('allcontact-list/bulk/updatecountrycodefield',[AllContactController::class,'bulkupdatecountrycodefield'])->name('allcontact.bulkupdatecountrycodefield');


    Route::get('update-and-resolved-all-contact-country-code',[AllContactController::class,'updatewrongdata'])->name('allcontact.updatewrongcountry');

    Route::post('allcontact-list/adminfilter/save',[AllContactController::class,'alcontactsaveadminfilter'])->name('allcontact.saveadminfilter');
    Route::post('allcontact-list/adminfilter/reset',[AllContactController::class,'alcontactresetadminfilter'])->name('allcontact.resetadminfilter');

    Route::post('allcontact-list/saveshortformcode',[AllContactController::class,'saveshortformcode'])->name('allcontact.saveshortformcode');

    Route::get('allcontact-group/list',[AllContactController::class,'allcontactgrouplist'])->name('allcontactgroup.list');
    Route::post('allcontact-group/list/json',[AllContactController::class,'allcontactgrouplistJson']);
    Route::get('allcontact-group/check/name',[AllContactController::class,'allcontactgroupcheckname']);
    Route::post('allcontact-group/store',[AllContactController::class,'groupstore'])->name('allcontactgroup.store');
    Route::get('allcontact-group/edit',[AllContactController::class,'groupedit'])->name('allcontactgroup.edit');
    Route::post('allcontact-group/update',[AllContactController::class,'groupupdt'])->name('allcontactgroup.update');

    Route::post('allcontact-list/update-counry/querybased',[AllContactController::class,'updatecountrybasequery'])->name('allcontact.countryupdt.querycountry');

    Route::post('contact-list/saveshortformcode',[ContactpController::class,'saveshortformcode'])->name('contact.saveshortformcode');
    Route::post('contact-list/saveaddcompanyform',[ContactpController::class,'saveaddcompanyform'])->name('contact.saveaddcompanyform');
    
    Route::post('contact-list/saveadminfilter',[ContactpController::class,'saveadminfilter'])->name('contact.saveadminfilter');
    Route::post('contact-list/resetadminfilter',[ContactpController::class,'resetadminfilter'])->name('contact.resetadminfilter');

    Route::get('lifecycle-list',[ContactpController::class,'lifecyclelist'])->name('lifecycle.list');
    Route::post('lifecycle-list/json',[ContactpController::class,'lifecyclelistJson']);
    Route::post('lifecycle-list/store',[ContactpController::class,'lifecyclestr'])->name('lifecycle.store');
    Route::get('lifecycle-list/edit',[ContactpController::class,'lifecycleedit'])->name('lifecycle.edit');
    Route::post('lifecycle-list/update',[ContactpController::class,'lifecycleupdt'])->name('lifecycle.update');
    Route::get('lifecycle-list/check/name',[ContactpController::class,'checknameLifecycle']);

    Route::get('leadstage-list',[ContactpController::class,'leadstagelist'])->name('leadstage.list');
    Route::post('leadstage-list/json',[ContactpController::class,'leadstagelistJson']);
    Route::post('leadstage-list/store',[ContactpController::class,'leadstagestr'])->name('leadstage.store');
    Route::get('leadstage-list/edit',[ContactpController::class,'leadstageedit'])->name('leadstage.edit');
    Route::post('leadstage-list/update',[ContactpController::class,'leadstageupdt'])->name('leadstage.update');
    Route::get('leadstage-list/check/name',[ContactpController::class,'checknameLeadstage']);

    Route::get('industries-list',[ContactpController::class,'industrieslist'])->name('industries.list');
    Route::post('industries-list/json',[ContactpController::class,'industrieslistJson']);
    Route::post('industries-list/store',[ContactpController::class,'industriesStore'])->name('industires.store');
    Route::get('industries-list/edit',[ContactpController::class,'industriesedit'])->name('industries.edit');
    Route::post('industries-list/update',[ContactpController::class,'industriesupdate'])->name('industries.update');

    Route::get('industries/check/name',[ContactpController::class,'industriesCheckName'])->name('industries.checkname');

    // CheckExistance Controller
    Route::get('contact-list/checkmobilenoexist',[CheckExistanceController::class,'contactmobileexists'])->name('contact.checkmobileNo');

    // Bulk Contact Section Start
    Route::post('contact-list/bulk/whatsapp-send',[ContactpController::class,'bulksendwhatsapp'])->name('contact.bulksendwhatsapp');
    Route::post('contact-list/bulk/leadownertransfer',[ContactpController::class,'bulkleadownertransfer'])->name('contact.bulkleadownertransfer');
    Route::post('contact-list/bulk/careofftransfer',[ContactpController::class,'bulkcareofftransfer'])->name('contact.bulkcareofftransfer');
    Route::post('contact-list/bulk/grouptransfer',[ContactpController::class,'bulkgrouptransfer'])->name('contact.bulkgrouptransfer');

    Route::post('contactp/filterList/update',[ContactpController::class,'contactpfilter']);

    Route::post('contact-list/short/store',[ContactpController::class,'shortstore'])->name('contact.shortstore');

    Route::post('contact-list/subscribe/update',[ContactpController::class,'updateSubscribe'])->name('contact.updatesubscribe');

    Route::post('contact-lis/show/send-whatsapp/{id}',[ContactpController::class,'sendcontactwhatsapp'])->name('contact.sendwhatsapp');

    Route::get('contacts/getstage',[ContactpController::class,'getStageList']);
    Route::post('contact-list/stage/update',[ContactpController::class,'updateStage'])->name('contact.stageUpdate');

    Route::post('contacts/transfer-lead-owner',[ContactpController::class,'transferLeadOwner'])->name('contact.transfer.leadowner');
    Route::post('contacts/transfer-careoff',[ContactpController::class,'transfercareoff'])->name('contact.transfer.careoff');

    Route::post('contacts/add-notes',[ContactpController::class,'contactsAddNotes'])->name('contact.add.notes');
    Route::get('contacts/delete-notes/{id}',[ContactpController::class,'contactsDeleteNotes'])->name('contact.delete.notes');

    Route::post('contacts/add-reminder',[ContactpController::class,'contactaddreminder'])->name('contact.addreminder');
    Route::get('contacts/add-reminder/edit',[ContactpController::class,'contacteditreminder'])->name('contact.editreminder');
    Route::post('contacts/add-reminder/update',[ContactpController::class,'contactreminderupdate'])->name('contact.updatereminder');

    Route::get('contact-list/sendcontactwhatsapp/get',[ContactpController::class,'getSendContactList'])->name('contact.getsendwhatsapplist');

    // Meta Contactp Whatsapp Status

    // Check Data
    Route::get('check/visa/profession',[CheckDataController::class,'checkvisaprofessionAdmin']);
    Route::get('check/visa/worklocation',[CheckDataController::class,'checkvisaworklocationAdmin']);
    Route::get('check/visa/embassy',[CheckDataController::class,'checkvisaembassyAdmin']);
    Route::get('check/visa/payment',[CheckDataController::class,'checkvisapaymentAdmin']);

    // Auto Meta Notification Template
    Route::get('auto-meta-notification/list',[AutoNotificationController::class,'index'])->name('autometanotification.list');
    Route::post('auto-meta-notification/list/json',[AutoNotificationController::class,'indexJson']);
    Route::post('auto-meta-notification/store',[AutoNotificationController::class,'store'])->name('autometanotification.store');
    Route::get('auto-meta-notification/edit',[AutoNotificationController::class,'edit'])->name('autometanotification.edit');
    Route::post('auto-meta-notification/update',[AutoNotificationController::class,'update'])->name('autometanotification.update');
    Route::get('auto-meta-notification/status/get',[AutoNotificationController::class,'getStatus'])->name('autometanotification.getStatus');
    Route::post('auto-meta-notification/status/update',[AutoNotificationController::class,'statusUpdt'])->name('autometanotification.status.update');

    // Meta Notification Template
    Route::get('meta-notification/list',[MetaNotificationController::class,'index'])->name('metanotification.list');
    Route::post('meta-notification/list/json',[MetaNotificationController::class,'indexJson']);
    Route::post('meta-notification/store',[MetaNotificationController::class,'store'])->name('metanotification.store');
    Route::get('meta-notification/edit',[MetaNotificationController::class,'edit']);
    Route::post('meta-notification/update',[MetaNotificationController::class,'update'])->name('metanotification.update');
    Route::get('meta-notification/status/get',[MetaNotificationController::class,'getStatus']);
    Route::post('meta-notification/status/update',[MetaNotificationController::class,'statusUpdt'])->name('metanotification.status.update');

    // Meta Automation Template
    Route::get('meta-automation/list',[MetaAutomationController::class,'index'])->name('metaautomation.list');
    Route::post('automation-meta-notification/list/json',[MetaAutomationController::class,'indexJson']);
    Route::post('automation-meta-notification/store',[MetaAutomationController::class,'store'])->name('automation-metanotification.store');
    Route::get('automation-meta-notification/edit',[MetaAutomationController::class,'edit']);
    Route::post('automation-meta-notification/update',[MetaAutomationController::class,'update'])->name('automation-metanotification.update');
    Route::get('meta-automation/template/{template_for}', [MetaAutomationController::class,'templateFor'])->name('metaautomation.templateFor');
    Route::post('meta-automation/template/list/json', [MetaAutomationController::class,'templateForJson'])->name('metaautomation.templateForJson');
    Route::post('meta-automation/template/delete', [MetaAutomationController::class,'destroy'])->name('automation-metanotification.destroy');
    

    // Static Meta Notification Template
    Route::get('static-meta-otp-notification/list',[MetaNotificationController::class,'indexStatic'])->name('staticmetanotification.list');
    Route::post('static-meta-otp-notification/list/json',[MetaNotificationController::class,'indexJsonStatic']);
    Route::post('static-meta-otp-notification/store',[MetaNotificationController::class,'storeStatic'])->name('staticmetanotification.store');
    Route::get('static-meta-otp-notification/edit',[MetaNotificationController::class,'StaticEdit']);
    Route::post('static-meta-otp-notification/update',[MetaNotificationController::class,'updateStatic'])->name('staticmetanotification.update');

    // Meta Whatsapp Log
    Route::get('meta-whatsapp/logs',[MetaWhatsappLogController::class,'index'])->name('metawhatsapplogs.index');
    Route::post('meta-whatsapp/logs/json',[MetaWhatsappLogController::class,'indexJson']);

    // Language Update
    Route::get('lang/{locale}',[LanguageController::class,'SwitchLang'])->name('lang.switch');

    // CRM DataTable Check
    Route::get('crm-candidate-list',[CrmCheckDataTableController::class,'crmcandlist'])->name('crmcandlist');
    Route::get('crm-candidate-list2',[CrmCheckDataTableController::class,'crmcandlist2'])->name('crmcandlist2');
    Route::post('crm-candidate-list/json',[CrmCheckDataTableController::class,'crmcandlistJson']);

    // Business Type
    Route::get('business-type/list',[BusinessTypeController::class,'index'])->name('businesstype.list');
    Route::post('business-type/list/json',[BusinessTypeController::class,'indexJson']);
    Route::post('business-type/list/store',[BusinessTypeController::class,'store'])->name('businesstype.store');
    Route::get('business-type/list/get',[BusinessTypeController::class,'edit'])->name('businesstype.edit');
    Route::post('business-type/list/update',[BusinessTypeController::class,'update'])->name('businesstype.update');
    Route::get('business-type/list/check/name',[BusinessTypeController::class,'checkname']);


    // Todo Label
    Route::get('todo-label/list',[TodoController::class,'todolabelList'])->name('todolabel.list');
    Route::post('todo-label/list/json',[TodoController::class,'todolabelListJson']);
    Route::post('todo-label/list/store', [TodoController::class,'todolabelStore'])->name('todolabel.store');
    Route::get('todo-label/list/get',[TodoController::class,'todolabelEdit'])->name('todolabel.edit');
    Route::post('todo-label/list/update',[TodoController::class,'todolableUpdate'])->name('todolabel.update');
    Route::get('todo-label/check/name',[TodoController::class,'labelcheckname']);



    // Department
    Route::get('department/list',[TodoController::class,'departmentList'])->name('department.list');
    Route::post('department/list/json',[TodoController::class,'departmentListJson']);
    Route::post('department/list/store', [TodoController::class,'departmentStore'])->name('department.store');
    Route::get('department/list/get',[TodoController::class,'departmentEdit'])->name('department.edit');
    Route::post('department/list/update',[TodoController::class,'departmentUpdate'])->name('department.update');
    Route::get('department/check/name',[TodoController::class,'departmentcheckname']);

    // Group
    Route::get('groupm/list',[BackEndController::class,'groupmList'])->name('groupm.list');
    Route::post('groupm/list/json',[BackEndController::class,'groupmListJson']);
    Route::post('groupm/list/store',[BackEndController::class,'groupmListstore'])->name('groupm.store');
    Route::get('groupm/list/get',[BackEndController::class,'groupmListedit'])->name('groupm.edit');
    Route::post('groupm/list/update',[BackEndController::class,'groupmListupdate'])->name('groupm.update');
    Route::get('groupm/list/check/name',[BackEndController::class,'groupmcheckname']);

    // Todo
    Route::get('todo',[TodoController::class,'index'])->name('todo.list');
    // Global "open this task" link: loads the same Todo list page, which
    // then auto-opens the existing View Todo modal for this id.
    Route::get('todo/view/{todoId}', [TodoController::class, 'index'])
        ->where('todoId', '[0-9]+')
        ->name('todo.list.view');
    Route::get('todo/load-more-kanban',[TodoController::class,'loadMoreKanban'])->name('todo.loadMoreKanban');
    Route::post('todo-store',[TodoController::class,'store'])->name('todo.store');
    Route::get('todo-edit',[TodoController::class,'edit'])->name('todo.edit');
    Route::post('todo-update',[TodoController::class,'update'])->name('todo.update');
    Route::post('todo-update-status',[TodoController::class,'updateStatus'])->name('todo.updateStatus');
    Route::post('todo-delete',[TodoController::class,'todoDelete'])->name('todo.delete');
    Route::post('todo-kanban-reorder',[TodoController::class,'kanbanreorder'])->name('todo.kanbanreorder');
    Route::post('todo-kanban-update-status',[TodoController::class,'todokanbanstatusupdate'])->name('todo.kanbanstatusupdate');
    Route::post('todo/bulk-status/update', [TodoController::class, 'todobulkstatusupdate'])->name('todo.bulkstatus.update');
    Route::post('todo/bulk-priority/update',[TodoController::class, 'todobulkpriorityupdate'])->name('todo.bulkpriority.update');
    Route::post('todo/bulk-assignto/update',[TodoController::class, 'todobulkassigntoupdate'])->name('todo.bulkassignto.update');
    Route::post('todo/bulk-delete',[TodoController::class,'todobulkdelete'])->name('todo.bulkdelete');

    Route::get('todo-kanban-list',[TodoController::class,'todokanbanlist'])->name('todo.kanbanlist');

    Route::get('todo/intervalpopuplist',[TodoController::class,'todointervalpopup'])->name('todo.intervalpopuplist');
    
    Route::get('/todo/reschedule_todo_task_id/{taskId}', [TodoController::class, 'rescheduleTaskReminderView'])
    ->name('todo.reschedule');

    Route::post('/todo/reminder_reschedule_update', [TodoController::class, 'reminderRescheduleUpdate'])
    ->name('todo.reminder_reschedule_update');

    Route::post('todo/saveshortformcode',[TodoController::class,'saveshortformcode'])->name('todo.saveshortformcode');

    // todonotes
    Route::post('todonotes/store', [TodoNoteController::class, 'store'])->name('todonotes.store');
    Route::get('todonotes/list', [TodoNoteController::class, 'list'])->name('todonotes.list');
    Route::post('todonotes/delete', [TodoNoteController::class, 'destroy'])->name('todonotes.delete');

    // Save Todo Filter
    Route::post('todo/save-filter',[TodoController::class,'saveFilter'])->name('todo.savefilter');
    Route::post('todo/reset-filter',[TodoController::class,'resetfilter'])->name('todo.resetfilter');
    Route::post('todo/switch-to',[TodoController::class,'switchto'])->name('todo.switchto');

    // todo staff
    Route::post('todo/add-staff-work', [TodoController::class, 'addStaffWork']);
    Route::post('todo/delete-staff-work', [TodoController::class, 'deleteStaffWork']);

    // DealPipeline
    Route::get('dealPipeline/list',[DealPipelineController::class,'index'])->name('dealPipeline.list');
    // Global "open this deal" link: loads the same Deal Pipeline list page,
    // which then auto-opens the existing View Deal modal for this id.
    Route::get('dealPipeline/list/view/{dealId}', [DealPipelineController::class, 'index'])
        ->where('dealId', '[0-9]+')
        ->name('dealPipeline.list.view');
    Route::get('deal-pipeline/kanban/load-more', [DealPipelineController::class, 'loadMoreKanban'])->name('dealPipeline.kanban.loadMore');
    Route::get('deal-pipeline/kanban-list',[DealPipelineController::class, 'kanbanList'])->name('dealPipeline.kanban.list');
    Route::post('dealPipeline/store',[DealPipelineController::class,'store'])->name('dealPipeline.store');
    Route::post('deal-kanban-reorder',[DealPipelineController::class,'kanbanreorder'])->name('dealPipeline.kanbanReorder');
    Route::get('dealPipeline/get', [DealPipelineController::class, 'getDeal'])->name('dealPipeline.getDeal');
    Route::get('dealPipeline/activity', [DealPipelineController::class, 'dealActivity'])->name('dealPipeline.activity');
    Route::post('dealPipeline/update', [DealPipelineController::class, 'update'])->name('dealPipeline.update');    
    Route::post('dealPipeline/delete',[DealPipelineController::class,'destroy'])->name('dealPipeline.delete');
    Route::post('dealPipeline/updateDealStage',[DealPipelineController::class,'updateDealStage'])->name('dealPipeline.updateDealStage');
    Route::post('dealPipeline/updateRecruitStatus',[DealPipelineController::class,'updateRecruitStatus'])->name('dealPipeline.updateRecruitStatus');
    Route::post('dealPipeline/kanbanUpdateStatus',[DealPipelineController::class,'kanbanUpdateStatus'])->name('dealPipeline.kanbanUpdateStatus');

    Route::post('dealPipeline/saveFilter', [DealPipelineController::class, 'saveFilter'])->name('dealPipeline.saveFilter');
    Route::post('dealPipeline/resetfilter', [DealPipelineController::class, 'resetFilter'])->name('dealPipeline.resetfilter');
    Route::post('dealPipeline/switchto',[DealPipelineController::class,'switchto'])->name('dealPipeline.switchto');

    Route::post('/deal-pipeline/check-passport', [DealPipelineController::class, 'checkPassport'])->name('dealPipeline.checkPassport');
    Route::get('/deal-pipeline/search-allcontact', [DealPipelineController::class, 'searchAllcontact'])->name('dealPipeline.search-allcontact');

    // deal reminders
    Route::post('/dealPipeline/reminder/store', [DealPipelineController::class, 'storeReminder'])->name('dealPipeline.reminder.store');
    Route::get('/dealPipeline/reminder/list', [DealPipelineController::class, 'listReminder'])->name('dealPipeline.reminder.list');
    Route::post('/dealPipeline/reminder/delete', [DealPipelineController::class, 'deleteReminder'])->name('dealPipeline.reminder.delete');
    
    // dealnotes
    Route::post('dealnotes/store', [DealNoteController::class, 'store'])->name('dealnotes.store');
    Route::get('dealnotes/list', [DealNoteController::class, 'list'])->name('dealnotes.list');
    Route::post('dealnotes/delete', [DealNoteController::class, 'destroy'])->name('dealnotes.delete');

    // dealfiles
    Route::post('dealfiles/list', [DealFileController::class, 'list'])->name('dealfiles.list');
    Route::post('dealfiles/store', [DealFileController::class, 'store'])->name('dealfiles.store');
    Route::delete('dealfiles/{id}/delete', [DealFileController::class, 'destroy'])->name('dealfiles.destroy');
    Route::get('dealfiles/{id}/download', [DealFileController::class, 'download'])->name('dealfiles.download');

    // business
    Route::get('business/list', [BusinessController::class, 'index'])->name('business.list');
    Route::post('business/list/json',[BusinessController::class,'businessListJson']);
    Route::post('business/store', [BusinessController::class, 'store'])->name('business.store');
    Route::get('business/edit', [BusinessController::class, 'edit'])->name('business.edit');
    Route::post('business/update', [BusinessController::class, 'update'])->name('business.update');
    Route::post('business/delete', [BusinessController::class, 'destroy'])->name('business.delete');

    // dealStage
    Route::get('dealStage/list', [DealStagecontroller::class, 'index'])->name('dealStage.list');
    Route::post('dealStage/list/json',[DealStagecontroller::class,'DealStageListJson']);
    Route::post('dealStage/store', [DealStagecontroller::class, 'store'])->name('dealStage.store');
    Route::get('dealStage/edit', [DealStagecontroller::class, 'edit'])->name('dealStage.edit');
    Route::post('dealStage/update', [DealStagecontroller::class, 'update'])->name('dealStage.update');
    Route::post('dealStage/delete', [DealStagecontroller::class, 'destroy'])->name('dealStage.delete');

    Route::get('recruitStatus/list', [RecruitStatusController::class, 'index'])->name('recruitStatus.list');
    Route::post('recruitStatus/list/json',[RecruitStatusController::class,'recruitStatusListJson']);
    Route::post('recruitStatus/store', [RecruitStatusController::class, 'store'])->name('recruitStatus.store');
    Route::get('recruitStatus/edit', [RecruitStatusController::class, 'edit'])->name('recruitStatus.edit');
    Route::post('recruitStatus/update', [RecruitStatusController::class, 'update'])->name('recruitStatus.update');
    Route::post('recruitStatus/delete', [RecruitStatusController::class, 'destroy'])->name('recruitStatus.delete');

    Route::get('jobTitle/list', [JobTitleController::class, 'index'])->name('jobTitle.list');
    Route::post('jobTitle/list/json',[JobTitleController::class,'jobTitleListJson']);
    Route::post('jobTitle/store', [JobTitleController::class, 'store'])->name('jobTitle.store');
    Route::get('jobTitle/edit', [JobTitleController::class, 'edit'])->name('jobTitle.edit');
    Route::post('jobTitle/update', [JobTitleController::class, 'update'])->name('jobTitle.update');
    Route::post('jobTitle/delete', [JobTitleController::class, 'destroy'])->name('jobTitle.delete');


    // Lead list (AJAX + normal)
    // Route::get('leads/list-new', [LeadController::class, 'index'])->name('leads.list_new');
    // Route::get('leads/list-back', [LeadController::class, 'index_back'])->name('leads.listback');
    Route::get('leads/list', [LeadController::class, 'index_new'])->name('leads.list');
    // Global "open this lead" link: loads the same Leads list page, which
    // then auto-opens the existing View Lead modal for this id. Reusable
    // from anywhere in the CRM (e.g. the dashboard's "View Lead" action).
    Route::get('leads/list/view/{leadId}', [LeadController::class, 'index_new'])
        ->where('leadId', '[0-9]+')
        ->name('leads.list.view');
    Route::get('leads/json',[LeadController::class,'jsonData'])->name('leads.json');

    // Lead CRUD actions
    Route::post('leads/delete', [LeadController::class, 'delete'])->name('leads.delete');
    Route::post('leads/bulkdelete', [LeadController::class, 'bulkdelete'])->name('leads.bulkdelete');

    // Lead assignment
    Route::post('leads/assignto', [LeadController::class, 'assignto'])->name('leads.assignto');
    Route::post('leads/bulkassignto', [LeadController::class, 'bulkassignto'])->name('leads.bulkassignto');
    Route::post('leads/bulkexport', [LeadController::class, 'bulkExport'])->name('leads.bulkexport');
    Route::post('leads/bulkLeadSendToMeta', [LeadController::class, 'bulkLeadSendToMeta'])->name('leads.bulkLeadSendToMeta');

    // Lead view / qualified
    Route::get('leads/view/{id}', [LeadController::class, 'show'])->name('leads.show');
    Route::get('leads/{id}/qualified', [LeadController::class, 'getQualifiedStatus'])->name('leads.getQualifiedStatus');
    Route::post('leads/qualified', [LeadController::class, 'qualified'])->name('leads.qualified');
    Route::post('leads/addnotes', [LeadController::class, 'addnotes'])->name('leads.addnotes');
    Route::delete('leads/delete-note/{id}', [LeadController::class, 'deleteNote'])->name('leads.deleteNote');
    Route::post('leads/updateLeadDetails', [LeadController::class, 'updateLeadDetails'])->name('leads.updateLeadDetails');
    Route::post('leads/check-mobile-details', [LeadController::class, 'checkMobileDetails'])->name('leads.checkMobileDetails');
    Route::post('/leads/update-number', [LeadController::class, 'updateNumber'])->name('leads.update.number');
    Route::get('/lead/{lead}/activity', [LeadController::class, 'leadActivity'])->name('lead.activity');

    // Lead assign switch input
    Route::post('leads/deactiveassignee', [LeadController::class, 'deactiveassigneelead'])->name('leads.deactiveassignee');

    // Filters
    Route::post('leads/saveFilter', [LeadController::class, 'saveFilter'])->name('leads.saveFilter');
    Route::post('leads/resetFilter', [LeadController::class, 'resetFilter'])->name('leads.resetFilter');
    Route::get('leads/getFilter', [LeadController::class, 'getSavedFilter'])->name('leads.getFilter');
    Route::post('/bulk-change-status', [LeadController::class, 'bulkChangeStatus'])
    ->name('leads.bulkChangeStatus');

    
    // Flight Vendor
    Route::get('flight-vendor/list',[FlightController::class,'flightvendorList'])->name('flight.vendor_list');
    Route::post('flight-vendor/store',[FlightController::class,'flightvendorstore'])->name('flight.vendor_store');
    Route::get('flight-vendor/edit',[FlightController::class,'flightvendoredit'])->name('flight.vendor_edit');
    Route::post('flight-vendor/update',[FlightController::class,'flightvendorupdate'])->name('flight.vendor_update');
    Route::post('flight-vendor/check/contactno',[FlightController::class,'flightvendorcheckcontact'])->name('flight.vendor.checkcontactno');
    Route::post('flight-vendor/check/email',[FlightController::class,'flightvendorcheckemail'])->name('flight.vendor.checkemail');
    Route::post('flight-vendor/check/website',[FlightController::class,'flightvendorcheckwebsite'])->name('flight.vendor.checkwebsite');
});


Route::group(['middleware' => ['auth:partner'],'prefix' => 'partner','as' => 'partner.'],function(){
    // Route::get('dashboard',[PartnerDashboardController::class,'index'])->name('dashboard');

    // Partner Profile update
    Route::get('profile/{id}',[PartnerDashboardController::class,'profile'])->name('profile');

    // Partner Booking Get Data

    Route::get('booking/getData',[PartnerBookingController::class, 'getBookingData']);

    // Booking Section
    Route::get('booking',[PartnerBookingController::class,'index'])->name('booking');

    Route::get('booking/view/{id}',[PartnerBookingController::class,'show'])->name('booking.show');

    Route::post('booking/sendOTP',[PartnerBookingController::class,'sendOTP']);
    Route::post('booking/confirm-otp',[PartnerBookingController::class,'confirmOTP'])->name('otpconfirm');
    Route::post('booking/check-otp',[PartnerBookingController::class,'checkOTP']);

    Route::post('booking/getVisa',[PartnerBookingController::class,'getVisa']);
    Route::post('booking/getVisa/store',[PartnerBookingController::class,'visaStr'])->name('visaStr');

    Route::post('booking/getPayment',[PartnerBookingController::class,'getPayment']);
    Route::post('booking/getPay/store',[PartnerBookingController::class,'paymentStr'])->name('paymentStr');

    Route::post('booking/cancel',[PartnerBookingController::class,'cancelBooking'])->name('booking.cancel');

    Route::get('employer-list',[PartnerDashboardController::class,'employer'])->name('employer');
    Route::post('employer-list/json',[PartnerDashboardController::class,'employerJson']);
    Route::get('employer-list/view/{id}',[PartnerDashboardController::class,'employerView']);

    Route::post('partner/check/primary-email',[PartnerController::class,'checkprimemail']);
    Route::post('partner/check/owner-contact-number',[PartnerController::class,'checkocn']);
    Route::post('partner/check/edit/primary-email',[PartnerController::class,'edcheckprimemail']);
    Route::post('partner/check/edit/owner-contact-number',[PartnerController::class,'edcheckocn']);

    Route::post('partner/check/edit/secondary-email',[PartnerController::class,'edsecondaryemail']);
    Route::post('partner/check/edit/office-number',[PartnerController::class,'edcheckofficeno']);
    Route::post('partner/check/edit/primary-mobile',[PartnerController::class,'edcheckprimaryno']);
    Route::post('partner/check/edit/secondary-mobile',[PartnerController::class,'edchecksecondaryno']);

    Route::post('partner/check/edit/email',[PartnerController::class,'edcheckemail']);
    Route::post('partner/check/edit/username',[PartnerController::class,'edcheckusername']);

    Route::post('partner/check/edit/password',[PartnerController::class,'edcheckpassword']);

    Route::post('partner/account/update/{id}',[PartnerController::class,'accountUpdate2'])->name('partner.account.update');
    Route::post('partner/personal/update/{id}',[PartnerController::class,'personaUpdate'])->name('partner.personal.update');
    Route::post('partner/password/update/{id}',[PartnerController::class,'passwordUpdate'])->name('partner.password.update');

    // Partner Booking Filter
    Route::post('booking/filterList/update',[PartnerBookingController::class,'bookingFilterPartner']);

    // Check Data
    Route::get('check/visa/profession',[PartnerBookingController::class,'checkvisaprofessionAdmin']);
    Route::get('check/visa/worklocation',[PartnerBookingController::class,'checkvisaworklocationAdmin']);
    Route::get('check/visa/embassy',[PartnerBookingController::class,'checkvisaembassyAdmin']);
    Route::get('check/visa/payment',[PartnerBookingController::class,'checkvisapaymentAdmin']);

    // Language Update
    Route::get('lang/{locale}',[PartnerLanguageController::class,'SwitchLang'])->name('lang.switch');

    Route::get('client',[PartnerCustomerController::class,'index'])->name('client');
    Route::post('client/list/json',[PartnerCustomerController::class,'indexjson']);
    Route::get('client/edit',[PartnerCustomerController::class,'edit'])->name('client.edit');
    Route::post('client/update',[PartnerCustomerController::class,'update'])->name('client.update');
    Route::post('client/filterList/update',[PartnerCustomerController::class,'updateFilterList']);
    Route::post('client-list/check/email',[PartnerCustomerController::class,'checkemailexist']);
    Route::post('client-list/check/mobile',[PartnerCustomerController::class,'checkmobileexists']);
    Route::get('client/view/{id}', [PartnerCustomerController::class, 'view'])->name('client.view');
    Route::post('client/delete',[PartnerCustomerController::class,'delete'])->name('client.delete');
    Route::get('candidate/view/{id}',[PartnerCustomerController::class,'candidateView'])->name('candidate.show');
    Route::post('client/status/update', [PartnerCustomerController::class, 'updateStatus'])->name('client.status.update');


    Route::get('sales-invoice/list',[PartnerInvoiceController::class,'index'])->name('invoice.list');
    Route::post('sales-invoice/store',[PartnerInvoiceController::class,'store'])->name('invoice.store');
    Route::get('sales-invoice/getcandidate/list',[PartnerInvoiceController::class,'getCandidate'])->name('invoice.getcandidate');
    Route::post('sales-invoice/checkinvoicenumber',[PartnerInvoiceController::class,'checkinvoicenumber'])->name('invoice.checkinvoicenumber');
    Route::get('sales-invoice/edit',[PartnerInvoiceController::class,'edit'])->name('invoice.edit');
    Route::post('sales-invoice/update',[PartnerInvoiceController::class,'update'])->name('invoice.update');
    Route::get('sales-invoice/show/{id}',[PartnerInvoiceController::class,'show'])->name('invoice.show');
    Route::get('sales-invoice/generate-pdf/{id}',[PartnerInvoiceController::class,'generatedInvoicePDF'])->name('invoice.generatedpdf');


    // Payment Section
    Route::get('payment/list',[PartnerPaymentController::class,'index'])->name('payment.list');
    Route::get('payment/getInvoices/list',[PartnerPaymentController::class,'getInvoices'])->name('payment.getinvoice');
    Route::post('payment/store',[PartnerPaymentController::class,'store'])->name('payment.store');
    Route::get('payment/list/edit',[PartnerPaymentController::class,'edit'])->name('payment.edit');
    Route::post('payment/list/update',[PartnerPaymentController::class,'update'])->name('payment.update');


});

require __DIR__.'/adminauth.php';

require __DIR__.'/partnerauth.php';
