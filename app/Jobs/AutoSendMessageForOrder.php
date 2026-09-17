<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Models\Booking;
use App\Models\User;
use App\Models\Partner;
use App\Models\Candidate;
use App\Models\Autometanotification;
use App\Models\Metawhatsapptemplate;
use App\Models\Whatsappchaturl;
use App\Models\Automessageresponse;
use App\Models\Metawhatsappapi;
use App\Models\Country;

class AutoSendMessageForOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $bookingId;

    public function __construct($bookingId)
    {
        $this->bookingId = $bookingId;
        $this->onQueue('default');
    }

    public function handle()
    {
        try {

            Log::channel('CustomerBooking')->info('===== Order Job Started =====');

            $booking = Booking::find($this->bookingId);
            if (!$booking) return;

            $user      = User::find($booking->user_id);
            $country = Country::where('id',$user->country_id)->first();
            $partner   = Partner::find($booking->partner_id);
            $candidate = Candidate::find($booking->cand_id);

            $autometas = Autometanotification::whereIn(
                    'trigger_template_type',
                    ['order']
                )
                ->where('template_for','qamarhire')
                ->where('status',1)
                ->get();

            if ($autometas->isEmpty()) return;


            foreach ($autometas as $meta) {

                /*
                |--------------------------------------------------------------------------
                | WAIT LOGIC (NOW INSIDE JOB)
                |--------------------------------------------------------------------------
                */

                if ($meta->action_type === 'wait') {

                    $minutes = 0;
                    $time = (int) $meta->trigger_template_time;
                    $type = strtolower($meta->trigger_template_time_type);

                    switch ($type) {

                        case 'minute':
                            $minutes = $time;
                            break;

                        case 'hour':
                            $minutes = $time * 60;
                            break;

                        case 'day':
                            $minutes = $time * 60 * 24;
                            break;

                        case 'week':
                            $minutes = $time * 60 * 24 * 7;
                            break;

                        case 'month':
                            $minutes = $time * 60 * 24 * 30;
                            break;

                        case 'year':
                            $minutes = $time * 60 * 24 * 365;
                            break;

                        default:
                            Log::channel('CustomerBooking')->warning(
                                'Invalid delay type for order automation',
                                [
                                    'automation_id' => $automation->id,
                                    'type' => $type
                                ]
                            );
                            continue;
                    }

                    if ($minutes > 0) {
                        Log::channel('CustomerBooking')->info(
                            "Waiting {$minutes} minutes before sending message"
                        );

                        sleep($minutes * 60);
                    }
                }

                
                $template = Metawhatsapptemplate::where('id',$meta->metatemp_id)
                    ->where('status',1)
                    ->first();

                if (!$template) continue;

                /*
                |--------------------------------------------------------------------------
                | 🔥 FIELD NOT COMPLETED LOGIC (BOOKINGS TABLE)
                |--------------------------------------------------------------------------
                */

                if (!empty($meta->field_not_completed)) {

                    $fieldsToCheck = json_decode($meta->field_not_completed, true);

                    if (is_array($fieldsToCheck) && !empty($fieldsToCheck)) {

                        $shouldSend = false;

                        foreach ($fieldsToCheck as $fieldName) {

                            $fieldName = trim($fieldName);

                            $value = $booking->{$fieldName} ?? null;

                            if (is_null($value) || trim((string)$value) === '') {
                                $shouldSend = true;
                                break;
                            }
                        }

                        if (!$shouldSend) {

                            Log::channel('CustomerBooking')->info(
                                '⛔ Skipping automation - all required fields completed',
                                [
                                    'automation_id' => $meta->id,
                                    'booking_id'    => $booking->id
                                ]
                            );

                            continue;
                        }
                    }
                }

                $fieldVariables  = explode(',', $template->field_variable);
                $assignVariables = explode(',', str_replace(['[',']'],'',$template->assign_variable));
                $mapping = config('constants.template_for_map.qamarhire');

                $preparedValues = [];

                foreach ($fieldVariables as $index => $fieldKey) {

                    $fieldKey     = trim($fieldKey);
                    $variableName = trim($assignVariables[$index] ?? '');
                    $value = '';

                    if ($variableName === 'Others') {

                        $file = $template->whatsapp_file ?? '';
                    
                        $value = 'admin/assets/images/template/' . $file;
                    }
                    else{
                        
                          if (!isset($mapping[$variableName])) {
                                $preparedValues[$fieldKey] = '';
                                continue;
                            }

                        [$table, $column] = explode('.', $mapping[$variableName]);

                        switch ($table) {
    
                            case 'users':
                                $value = $user->$column ?? '';
                                break;
    
                            case 'partners':
    
                                $normalizedVariable = strtolower(trim($variableName));
                            
                                // ✅ Care Of WhatsApp Link
                                if ($normalizedVariable === 'careof whatsup link') {
    
                                    $careoffId = $partner->partner_careoff_id ?? null;
    
                                    if ($careoffId) {
    
                                        $chatUrl = Whatsappchaturl::where('staff_id', $careoffId)
                                            ->value('chat_url');
    
                                        if ($chatUrl) {
    
                                            // ✅ Remove domain, keep only path
                                            $parsedUrl = parse_url($chatUrl);
                                            $value = $parsedUrl['path'] ?? '/newchat/not-found-url';
    
                                        } else {
                                            $value = '/newchat/not-found-url';
                                        }
    
                                    } else {
                                        $value = '/newchat/not-found-url';
                                    }
                                }
                            
                                // ✅ Partner WhatsApp Number

                                elseif ($normalizedVariable === 'partner whatsup number') {

                                    $partner_whatsapp_number = $partner->partner_whatsapp_number ?? null;
                                
                                    if ($partner_whatsapp_number) {
                                
                                        $cleanNumber = preg_replace('/[^0-9]/', '', $partner_whatsapp_number);
                                
                                        if (strlen($cleanNumber) == 10) {
                                            $cleanNumber = '91' . $cleanNumber;
                                        }
                                
                                        $value = $cleanNumber
                                            ? '/partner-whatsapp/' . $cleanNumber
                                            : '/partner-whatsapp/0000000000';
                                
                                    } else {
                                
                                        $value = '/partner-whatsapp/0000000000';
                                    }
                                }
                            
                                // ✅ Partner Calling Number
                                elseif ($normalizedVariable === 'partner calling number') {
                            
                                    $callingNumber = $partner->partner_calling_number ?? '';
                                    $cleanNumber = preg_replace('/[^0-9+]/', '', $callingNumber);
                            
                                    $value = $cleanNumber
                                        ? '/call/' . $cleanNumber
                                        : '/call/0000000000';
                                }
                            
                                else {
                                    $value = $partner->$column ?? '';
                                }
                            
                                break;
    
                            case 'candidates':
                                $value = $candidate->$column ?? '';
                                break;
    
                           case 'bookings':
                                $normalizedVariable = strtolower(trim($variableName));

                                if($normalizedVariable === 'work location city'){
                                    $value = '';
                                    $worklocation = $booking->$column ?? '';
                                    if(isset($worklocation)){
                                        $expwps = \DB::table('expecworkcities')->where('id',$worklocation)->first();
                                        if(isset($expwps)){
                                            $value = $expwps->name;
                                        }
                                    }
                                    
                                }else{
                                    $value = $booking->$column ?? '';
                                }
                                
                                break;
                        }
    
                    }

                   
                    $preparedValues[$fieldKey] = (string) $value;
                }

                /*
                |--------------------------------------------------------------------------
                | 🔥 BUILD PAYLOAD
                |--------------------------------------------------------------------------
                */
                $phone_number = '';

                if($meta->trigger_template_type == 'order'){
                    if(isset($country->country_code)){
                        $phone_number = $country->country_code.$user->mobile_no;
                    }else{
                        $phone_number = $user->mobile_no ?? '';
                    }
                } 

                $templateData = [
                    'phone_number'      => $phone_number ?? '',
                    'template_name'     => $template->template_name ?? '',
                    'template_language' => $template->language_code ?? 'en'
                ];

                foreach ($preparedValues as $key => $val) {

                    if (str_starts_with($key, 'button_')) {

                        $btnNumber = (int) str_replace('button_', '', $key);
                        $newIndex  = $btnNumber - 1;

                        if ($newIndex >= 0) {
                            $templateData['button_'.$newIndex] = $val;
                        }

                    } else {
                        $templateData[$key] = $val;
                    }
                }

                if (!empty($template->document_name)) {

                    $templateData['header_document'] =
                        url('admin/assets/images/template/'.$template->document_name);

                    $templateData['header_document_name'] =
                        $template->document_name;
                }

                $templateData['contact'] = [
                    "first_name"    => $user->name ?? '',
                    "last_name"     => " ",
                    "email"         => $user->email ?? '',
                    "country"       => $user->country ?? '',
                    "language_code" => $template->language_code ?? 'en'
                ];

                Log::channel('CustomerBooking')->warning('template payload', [
                    'data' => $templateData
                ]);


                $this->sendMetaCampaignMessage(
                    $templateData,
                    $meta,
                    $booking->id,
                    $template->metaapi_id ?? null
                );
            }

            Log::channel('CustomerBooking')->info('===== Order Job Finished =====');

        } catch (\Exception $e) {
            Log::channel('CustomerBooking')->error('Error: '.$e->getMessage());
        }
    }

    private function sendMetaCampaignMessage($data, $metanotification, $allcontactId, $metaapi_id)
    {
        try {

            $metaApi = Metawhatsappapi::where('id',$metaapi_id)
                ->where('status',1)
                ->first();

            if (!$metaApi) return;

            $endpoint_api = rtrim($metaApi->api_base_url,'/')
                .'/'.trim($metaApi->vendor_uid,'/')
                .'/contact/send-template-message';

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$metaApi->api_access_token
                ],
                CURLOPT_URL => $endpoint_api,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data)
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $response = json_encode([
                    'result' => 'Failed',
                    'message' => curl_error($curl)
                ]);
            }

            curl_close($curl);

            $responseGet = json_decode($response);

            Log::channel('CustomerBooking')->warning('response', [
                'data' => $responseGet
            ]);

            $msg_response = new Automessageresponse();
            $msg_response->template_for = "qamarhire";
            $msg_response->autometanotification_id = $metanotification->id;
            $msg_response->metatemplate_id = $metanotification->metatemp_id;
            $msg_response->metaapi_id = $metaapi_id;
            $msg_response->allcontact_id = $allcontactId;
            $msg_response->mobile_no = $data['phone_number'] ?? '';
            $msg_response->main_response = json_encode($responseGet);
            $msg_response->message_status = $responseGet->result ?? "Failed";
            $msg_response->message_text   = $responseGet->message ?? "Unknown Error";
            $msg_response->error_message_text = json_encode($responseGet->errors ?? []);
            $msg_response->save();

        } catch (\Exception $e) {
            Log::channel('CustomerBooking')->error('API Send Error: '.$e->getMessage());
        }
    }
}