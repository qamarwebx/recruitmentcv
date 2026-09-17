<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Models\Metawhatsappapi;
use App\Models\Lead;
use App\Models\Metawhatsapptemplate;
use App\Models\Admin;
use App\Http\Controllers\AllContactController;
use App\Http\Controllers\TeamMemberPageController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('leads/store',[LeadController::class,'lead_store_new']);
Route::post('leads/checkexists',[LeadController::class,'leadexists']);

// Tighter throttle than the default api group's 60/min: these two return or
// mutate a single lead's PII by a guessable sequential id (mitigated by the
// mob_no/whatsapp_no ownership check in the controller), so slowing down
// brute-force id enumeration is worthwhile defense-in-depth.
Route::middleware('throttle:20,1')->group(function () {
    Route::post('leads/getlead',[LeadController::class,'leadshow']);
    Route::post('leads/update-contact',[LeadController::class,'update_contact']);
});

Route::post('leads/store2',[LeadController::class,'lead_store2']);
Route::post('lead/browser-meta-event', [LeadController::class, 'storeBrowserMeta']);
Route::post('/get-team-member', [TeamMemberPageController::class, 'getTeamMember']);

Route::get('/send-whatsapp-template', function () {

        // Get Meta API Details
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)", ['todo_notification'])->first();

        if (!$metaAPI) {
            return response()->json(['status' => 'error', 'message' => 'Meta API config not found!']);
        }

        // Required credentials
        $base_url     = $metaAPI->api_base_url;
        $vendor_id    = $metaAPI->vendor_uid;
        $access_token = $metaAPI->api_access_token;

        $endpoint_api = $base_url . '/' . $vendor_id . '/contact/send-template-message';

        // Prepare test payload
        $data = [
            "template_name"      => "lead_follow_up",
            "template_language"  => "en_US",
            "phone_number"       => "9327063401",
            "field_1"            => "Test User"
        ];

        // CURL Request
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $endpoint_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token
            ],
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($data)
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return response()->json([
            "status" => "sent",
            "data_sent" => $data,
            "response" => json_decode($response)
        ]);
    
});

Route::get('/send-whatsapp-template-test', function () {

    $lead = Lead::find(2);


  

    dd([
        'lead_id' => $lead->id,
        'payload' => $data,
        'response' => json_decode($response, true),
        'error' => $error ?: null,
    ]);

    // Log::info('WhatsApp template sent', [
    //     'lead_id' => $lead->id,
    //     'payload' => $data,
    //     'response' => json_decode($response, true),
    //     'error' => $error ?: null,
    // ]);



});

Route::post('all-contact/get-careoff-by-number',[AllContactController::class,'getCareoffByNumber']);

Route::post('all-contact/sync-user-contacts', [AllContactController::class, 'syncUserContacts']);