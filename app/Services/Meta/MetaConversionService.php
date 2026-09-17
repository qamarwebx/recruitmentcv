<?php

namespace App\Services\Meta;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Lead;
use App\Models\FacebookAccount;
use App\Models\MetaLeadLog;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;

class MetaConversionService
{
    private const GRAPH_URL = 'https://graph.facebook.com/v18.0';

    private const EVENT_LEAD = 'Lead';

    private const EVENT_LEAD_CONTACTED = 'LeadContacted';

    private const EVENT_CALL_NOT_CONNECTED = 'CallNotConnected';

    private const EVENT_QUALIFIED = 'QualifiedLead';

    private const EVENT_NOT_QUALIFIED = 'LeadNotQualified';

    public function sendLeadEvent(Lead $lead,Request $request): bool {

        if (blank($request->event_id)) {

            Log::channel('facebook_capi')->warning(
                'Meta Lead Event skipped. Missing event_id.',
                [
                    'lead_id' => $lead->id,
                ]
            );

            return false;
        }

        $facebookAccounts = FacebookAccount::active()->get();

        if ($facebookAccounts->isEmpty()) {

            Log::channel('facebook_capi')->warning(
                'Meta Lead Event skipped. No active Meta Pixel found.',
                [
                    'lead_id' => $lead->id,
                ]
            );

            return false;
        }

        $sent = false;

        foreach ($facebookAccounts as $facebookAccount) {

            if (
                $this->sendEvent(
                    account: $facebookAccount,
                    lead: $lead,
                    request: $request,
                    eventName: self::EVENT_LEAD
                )
            ) {
                $sent = true;
            }
        }

        if ($sent) {
            $lead->update(['meta_lead_sent' => true]);
        }

        return $sent;
    }

    public function sendLeadStatusEvent(Lead $lead,Request $request): bool {

        $eventName = $this->getEventName($lead);

        if (!$eventName) {

            Log::channel('facebook_capi')->warning(
                'Meta Lead Status Event skipped. Invalid lead status.',
                [
                    'lead_id' => $lead->id,
                    'status'  => $lead->is_qualified,
                ]
            );

            return false;
        }

        $facebookAccounts = FacebookAccount::active()->get();

        if ($facebookAccounts->isEmpty()) {

            Log::channel('facebook_capi')->warning(
                'Meta Lead Status Event skipped. No active Meta Pixel found.',
                [
                    'lead_id' => $lead->id,
                ]
            );

            return false;
        }

        $sent = false;

        foreach ($facebookAccounts as $facebookAccount) {

            if (
                $this->sendEvent(
                    account: $facebookAccount,
                    lead: $lead,
                    request: $request,
                    eventName: $eventName
                )
            ) {
                $sent = true;
            }
        }

        if ($sent) {
            $lead->update(['meta_lead_sent' => true]);
        }

        return $sent;
    }

    private function sendEvent(FacebookAccount $account,Lead $lead,Request $request,string $eventName): bool {

        try {

            $payload = $this->buildPayload(
                account: $account,
                lead: $lead,
                request: $request,
                eventName: $eventName
            );

            $response = Http::timeout(30)->post(
                self::GRAPH_URL . "/{$account->pixel_id}/events",
                $payload
            );

            if ($response->successful()) {

                Log::channel('facebook_capi')->info(
                    'Meta Event Sent Successfully',
                    [
                        'lead_id'         => $lead->id,
                        'event_name'      => $eventName,
                        'pixel_id'        => $account->pixel_id,
                        'fbtrace_id'      => $response->json('fbtrace_id'),
                        'events_received' => $response->json('events_received'),
                    ]
                );

                $this->logMetaActivity(
                    lead: $lead,
                    eventName: $eventName,
                    status: true,
                    response: $response->json()
                );

                return true;
            }

            Log::channel('facebook_capi')->error(
                'Meta Event Failed',
                [
                    'lead_id'    => $lead->id,
                    'event_name' => $eventName,
                    'pixel_id'   => $account->pixel_id,
                    'response'   => $response->json(),
                ]
            );

            $this->logMetaActivity(
                lead: $lead,
                eventName: $eventName,
                status: false,
                response: $response->json(),
                message: data_get($response->json(), 'error.message', $response->body())
            );

        } catch (\Throwable $exception) {

            Log::channel('facebook_capi')->error(
                'Meta Event Exception',
                [
                    'lead_id'    => $lead->id,
                    'event_name' => $eventName,
                    'pixel_id'   => $account->pixel_id,
                    'message'    => $exception->getMessage(),
                ]
            );

            $this->logMetaActivity(
                lead: $lead,
                eventName: $eventName,
                status: false,
                message: $exception->getMessage()
            );
        }

        return false;
    }

    // private function sendEvent(FacebookAccount $account,Lead $lead,Request $request,string $eventName): bool {

    //     try {

    //         $payload = $this->buildPayload(
    //             account: $account,
    //             lead: $lead,
    //             request: $request,
    //             eventName: $eventName
    //         );

    //         $response = Http::timeout(30)->post(
    //             self::GRAPH_URL . "/{$account->pixel_id}/events",
    //             $payload
    //         );

    //         if ($response->successful()) {

    //             Log::channel('facebook_capi')->info(
    //                 'Meta Event Sent Successfully',
    //                 [
    //                     'lead_id'    => $lead->id,
    //                     'event_name' => $eventName,
    //                     'pixel_id'   => $account->pixel_id,
    //                     'fbtrace_id' => $response->json('fbtrace_id'),
    //                 ]
    //             );

    //             $this->logMetaActivity(lead: $lead,account: $account,eventName: $eventName,success: true,response: $response->json());

    //             return true;
    //         }

    //         Log::channel('facebook_capi')->error(
    //             'Meta Event Failed',
    //             [
    //                 'lead_id'    => $lead->id,
    //                 'event_name' => $eventName,
    //                 'pixel_id'   => $account->pixel_id,
    //                 'response'   => $response->json(),
    //             ]
    //         );

    //         $this->logMetaActivity(
    //             lead: $lead,
    //             account: $account,
    //             eventName: $eventName,
    //             success: false,
    //             response: $response->json(),
    //             message: $response->json('error.message') ?? $response->body()
    //         );


    //     } catch (\Throwable $exception) {

    //         Log::channel('facebook_capi')->error(
    //             'Meta Event Exception',
    //             [
    //                 'lead_id'    => $lead->id,
    //                 'event_name' => $eventName,
    //                 'pixel_id'   => $account->pixel_id,
    //                 'message'    => $exception->getMessage(),
    //             ]
    //         );

    //         $this->logMetaActivity(
    //             lead: $lead,
    //             account: $account,
    //             eventName: $eventName,
    //             success: false,
    //             response: null,
    //             message: $exception->getMessage()
    //         );

            
    //     }

    //     return false;
    // }

    private function buildPayload(FacebookAccount $account,Lead $lead,Request $request,string $eventName): array {

        $page = $request->input('page', 'thank-you-saudi');

        $payload = [

            'access_token' => $account->capi_access_token,

            'data' => [[

                'event_name'       => $eventName,

                'event_time'       => time(),

                'event_id'         => $request->event_id,

                'action_source'    => 'website',

                'event_source_url' => "https://qamrjob.com/{$page}.html",

                'user_data' => $this->buildUserData($lead, $request),

                'custom_data' => [

                    'lead_id'     => $lead->id,

                    'lead_status' => $this->getLeadStatus($lead),

                    'source'      => 'qamrjob',

                    'platform'    => 'website',

                    'page'        => $page,

                    'currency'    => 'INR',

                    'value'       => 0,

                ],

            ]]

        ];

        if (filled($account->test_event_code)) {

            $payload['test_event_code'] = $account->test_event_code;

        }

        return $payload;
    }

    private function buildUserData(Lead $lead,Request $request): array {

        $phone = $lead->mob_no ?: $lead->whatsapp_no;
        $email = $lead->email;
        $name  = $lead->cand_name;

        $fbp = $this->normalizeBrowserId($request->fbp);
        $fbc = $this->normalizeBrowserId($request->fbc);

        $userData = [

            'external_id'        => hash('sha256', (string) $lead->id),

            'client_ip_address'  => $request->ip(),

            'client_user_agent'  => $request->userAgent(),

        ];

        /*
        |--------------------------------------------------------------------------
        | Phone
        |--------------------------------------------------------------------------
        */

        if (filled($phone)) {

            $phone = preg_replace('/\D/', '', $phone);

            $userData['ph'] = hash('sha256', $phone);

        }

        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        if (filled($email)) {

            $userData['em'] = hash(
                'sha256',
                strtolower(trim($email))
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Full Name
        |--------------------------------------------------------------------------
        */

        if (filled($name)) {

            $userData['fn'] = hash(
                'sha256',
                strtolower(trim($name))
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Browser FBP
        |--------------------------------------------------------------------------
        */

        if (
            filled($fbp) &&
            preg_match('/^fb\.1\.\d+\..+$/', $fbp)
        ) {

            $userData['fbp'] = $fbp;

        }

        /*
        |--------------------------------------------------------------------------
        | Browser FBC
        |--------------------------------------------------------------------------
        */

        if (
            filled($fbc) &&
            preg_match('/^fb\.1\.\d+\..+$/', $fbc)
        ) {

            $userData['fbc'] = $fbc;

        }

        return $userData;
    }

    private function normalizeBrowserId(?string $value): ?string {
        if (blank($value)) {
            return null;
        }

        $value = trim($value);

        if (in_array(strtolower($value), ['null', 'undefined'], true)) {
            return null;
        }

        return $value;
    }

    private function getEventName(Lead $lead): ?string
    {
        return match ((int) $lead->is_qualified) {
            1 => self::EVENT_LEAD_CONTACTED,
            2 => self::EVENT_CALL_NOT_CONNECTED,
            3 => self::EVENT_QUALIFIED,
            4 => self::EVENT_NOT_QUALIFIED,
            default => self::EVENT_LEAD,
        };
    }

    private function getLeadStatus(Lead $lead): string
    {
        return match ((int) $lead->is_qualified) {
            1 => 'followed_up',
            2 => 'call_not_connected',
            3 => 'qualified',
            4 => 'not_qualified',
            default => 'lead',
        };
    }

    private function logMetaActivity(Lead $lead,string $eventName,bool $status,?array $response = null,?string $message = null): void {

        Helper::leadActivityLog(
            $lead->id,
            [
                'action'          => 'Meta Conversion API',
                'event_name'      => $eventName,
                'status'          => $status ? 'Success' : 'Failed',
                'events_received' => $response['events_received'] ?? null,
                'fbtrace_id'      => $response['fbtrace_id'] ?? null,
                'message'         => $message,
            ],
            Auth::guard('admin')->id(),
            'meta_capi'
        );
    }
   

}