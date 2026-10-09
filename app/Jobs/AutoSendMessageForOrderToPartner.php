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

class AutoSendMessageForOrderToPartner implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $bookingId;

    /**
     * Which website's automation set to run: 'qamarhire' (qamarhire.com,
     * the default - unchanged behaviour) or 'recruitmentcv' (recruitmentcv.com
     * main + partner sites). Matches autometanotifications.template_for and
     * config('constants.template_for_map'). Declared defaults also apply to
     * jobs queued before this property existed.
     */
    protected $templateFor = 'qamarhire';

    /**
     * RecruitmentCV only: the partner whose subdomain the order was placed
     * on (resolved from the request host, never from input); null = main
     * site / qamarhire.com. Used to refuse any order whose office isn't that
     * partner, so one partner's site can never send another partner's data.
     */
    protected $sitePartnerId = null;

    /**
     * Autometanotification trigger_template_type to send. Defaults to the
     * plain order message; the reservation queue reuses this job with
     * 'reservation_next_priority_to_partner' for its "your turn" message.
     */
    protected $triggerType = 'order_to_partner';

    public function __construct($bookingId, $templateFor = 'qamarhire', $sitePartnerId = null, $triggerType = null)
    {
        if ($triggerType !== null) {
            $this->triggerType = (string) $triggerType;
        }
        $this->bookingId = $bookingId;
        $this->templateFor = array_key_exists((string) $templateFor, (array) config('constants.website_template_for'))
            ? (string) $templateFor
            : 'qamarhire';
        $this->sitePartnerId = $sitePartnerId !== null ? (int) $sitePartnerId : null;
        $this->onQueue('default');
    }

    public function handle()
    {
        try {

            Log::channel('OrderToPartner')->info('===== Order Job Started =====');

            $booking = Booking::find($this->bookingId);
            if (!$booking) return;

            if ($this->sitePartnerId !== null && (int) $booking->partner_id !== $this->sitePartnerId) {
                Log::channel('OrderToPartner')->warning('Order skipped: booking office is not the ordering partner site', [
                    'booking_id' => $booking->id,
                    'template_for' => $this->templateFor,
                ]);
                return;
            }

            $user      = User::find($booking->user_id);
            $partner   = Partner::find($booking->partner_id);
            $candidate = Candidate::find($booking->cand_id);

            $autometas = Autometanotification::whereIn(
                    'trigger_template_type',
                    [$this->triggerType]
                )
                ->where('template_for', $this->templateFor)
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
                            Log::channel('OrderToPartner')->warning(
                                'Invalid delay type for order automation',
                                [
                                    'automation_id' => $automation->id,
                                    'type' => $type
                                ]
                            );
                            break; // was `continue` - identical inside a switch (PHP warns); kept as break to preserve behaviour
                    }

                    if ($minutes > 0) {
                        Log::channel('OrderToPartner')->info(
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

                            Log::channel('OrderToPartner')->info(
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
                $mapping = config('constants.template_for_map.' . $this->templateFor);

                $preparedValues = [];

                foreach ($fieldVariables as $index => $fieldKey) {

                    $fieldKey     = trim($fieldKey);
                    $variableName = trim($assignVariables[$index] ?? '');
                    $value = '';

                    if ($variableName === 'Others') {

                        $file = $template->whatsapp_file ?? '';
                    
                        $value = 'admin/assets/images/template/' . $file;
                    }
                    elseif ($variableName === 'partner Consern Person Name Daynamic As Per Mobile Number') {
                        $value = 'dynamic_name_as_per_mobile_number';
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
                if ($meta->trigger_template_type !== $this->triggerType) {
                    return;
                }
                
                $phone_number_array = collect([
                    ['phone' => $partner->portal_mobile_no_1, 'name' => $partner->portal_consern_person_name1],
                    ['phone' => $partner->portal_mobile_no_2, 'name' => $partner->portal_consern_person_name2],
                    ['phone' => $partner->portal_mobile_no_3, 'name' => $partner->portal_consern_person_name3],
                    ['phone' => $partner->portal_mobile_no_4, 'name' => $partner->portal_consern_person_name4],
                ])
                ->filter(function ($item) {
                    return !empty(trim($item['phone'] ?? ''));
                })
                ->unique('phone') // ✅ remove duplicate numbers
                ->values();
                
                if ($phone_number_array->isEmpty()) {
                    return;
                }
                
                foreach ($phone_number_array as $contact) {
                
                    $phone_number = trim($contact['phone']);
                    $contact_name = $contact['name'] ?? '--';
                
                    $templateData = [
                        'phone_number'      => $phone_number,
                        'template_name'     => $template->template_name ?? '',
                        'template_language' => $template->language_code ?? 'en',
                    ];
                
                    foreach ($preparedValues as $key => $val) {
                
                        if ($val === 'dynamic_name_as_per_mobile_number') {
                            $templateData[$key] = $contact_name;
                        }
                        elseif (str_starts_with($key, 'button_')) {
                
                            $index = ((int) str_replace('button_', '', $key)) - 1;
                
                            if ($index >= 0) {
                                $templateData["button_$index"] = $val;
                            }
                
                        } else {
                            $templateData[$key] = $val;
                        }
                    }
                
                    if (!empty($template->document_name)) {
                        $templateData['header_document'] =
                            url("admin/assets/images/template/{$template->document_name}");
                
                        $templateData['header_document_name'] =
                            $template->document_name;
                    }
                
                    $templateData['contact'] = [
                        "first_name"    => $user->name ?? '',
                        "last_name"     => " ",
                        "email"         => $user->email ?? '',
                        "country"       => $user->country ?? '',
                        "language_code" => $template->language_code ?? 'en',
                    ];
                
                    Log::channel('OrderToPartner')->info('template payload', [
                        'phone' => $phone_number,
                        'name'  => $contact_name,
                    ]);
                
                    $this->sendMetaCampaignMessage(
                        $templateData,
                        $meta,
                        $booking->id,
                        $template->metaapi_id ?? null
                    );
                }
               
                
            }

            Log::channel('OrderToPartner')->info('===== Order Job Finished =====');

        } catch (\Exception $e) {
            Log::channel('OrderToPartner')->error('Error: '.$e->getMessage());
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

            Log::channel('OrderToPartner')->warning('response', [
                'data' => $responseGet
            ]);

            $msg_response = new Automessageresponse();
            $msg_response->template_for = $this->templateFor;
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
            Log::channel('OrderToPartner')->error('API Send Error: '.$e->getMessage());
        }
    }
}
