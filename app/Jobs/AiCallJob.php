<?php

namespace App\Jobs;

use App\Models\AllContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contactId;

    public $tries = 3;

    public $timeout = 120;

    public function __construct($contactId)
    {
        $this->contactId = $contactId;
    }

    public function handle()
    {
        Log::channel('ai_call_log')->info('==============================================');
        Log::channel('ai_call_log')->info('AI CALL JOB STARTED', [
            'contact_id' => $this->contactId,
            'started_at' => now(),
        ]);

        $contact = AllContact::find($this->contactId);

        if (!$contact) {

            Log::channel('ai_call_log')->warning('Contact Not Found', [
                'contact_id' => $this->contactId
            ]);

            return;
        }

        Log::channel('ai_call_log')->info('Contact Loaded', [
            'id'     => $contact->id,
            'name'   => $contact->name ?? '',
            'mobile' => $contact->primary_no_wsp,
            'status' => $contact->ai_call_status,
        ]);

        if (empty($contact->primary_no_wsp)) {

            $contact->update([
                'ai_call_status' => 'failed',
                'ai_call_response' => json_encode([
                    'message' => 'Phone number not found.'
                ])
            ]);

            Log::channel('ai_call_log')->warning('Phone Number Missing', [
                'contact_id' => $contact->id
            ]);

            return;
        }

        $contact->update([
            'ai_call_status' => 'calling'
        ]);

        Log::channel('ai_call_log')->info('Status Updated', [
            'contact_id' => $contact->id,
            'status' => 'calling'
        ]);

        try {

            $payload = [

                "assistantId" => env('VAPI_ASSISTANT_ID'),

                "phoneNumberId" => env('VAPI_PHONE_NUMBER_ID'),

                "customer" => [
                    "number" => $contact->primary_no_wsp
                ],

                "metadata" => [
                    "contact_id" => $contact->id
                ]

            ];

            Log::channel('ai_call_log')->info('Sending Request To Vapi', [
                'url' => 'https://api.vapi.ai/call',
                'payload' => $payload
            ]);

            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer '.env('VAPI_API_KEY'),
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.vapi.ai/call', $payload);

            Log::channel('ai_call_log')->info('Vapi Response Received', [
                'http_status' => $response->status(),
                'successful'  => $response->successful(),
            ]);

            Log::channel('ai_call_log')->debug('Vapi Full Response', [
                'response' => $response->json() ?? $response->body()
            ]);

            if ($response->successful()) {

                $contact->update([
                    'ai_call_status'   => 'processing',
                    'ai_call_response' => $response->body(),
                ]);

                Log::channel('ai_call_log')->info('Call Submitted Successfully', [
                    'contact_id' => $contact->id,
                    'status' => 'processing'
                ]);

            } else {

                $contact->update([
                    'ai_call_status'   => 'failed',
                    'ai_call_response' => $response->body(),
                ]);

                Log::channel('ai_call_log')->error('Vapi API Failed', [
                    'contact_id' => $contact->id,
                    'status_code' => $response->status(),
                    'response' => $response->body(),
                ]);
            }

        } catch (\Throwable $e) {

            $contact->update([
                'ai_call_status' => 'failed',
                'ai_call_response' => json_encode([
                    'error' => $e->getMessage(),
                ]),
            ]);

            Log::channel('ai_call_log')->critical('AI CALL EXCEPTION', [
                'contact_id' => $contact->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

        }

        Log::channel('ai_call_log')->info('AI CALL JOB COMPLETED', [
            'contact_id' => $contact->id,
            'final_status' => $contact->fresh()->ai_call_status,
            'completed_at' => now(),
        ]);

        Log::channel('ai_call_log')->info('==============================================');
    }

    public function backoff()
    {
        return [30, 60, 120];
    }
}