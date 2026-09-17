<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DealReminder;
use App\Models\DealPipeline;
use App\Models\Admin;
use App\Models\Metawhatsappapi;
use Carbon\Carbon;
use Log;
use Mail;
use App\Mail\DealReminderMail;

class ManageDealPipelineReminder extends Command
{
    protected $signature = 'reminders:ManageDealPipelineReminders';
    protected $description = 'Handle Deal Pipeline Reminder Notifications';

    public function handle()
    {
        $now = Carbon::now();

        $reminders = DealReminder::where('is_done', 0)
        ->where('is_notified', 0)
        ->where('reminder_at', '<=', $now)
        ->get();

        // dd($reminders);

        foreach ($reminders as $reminder) {

            $deal = DealPipeline::find($reminder->deal_id);
            $user = Admin::find($reminder->created_by);
        
            if (!$deal || !$user) {
                continue;
            }
        
            try {
        
                // ✅ WhatsApp (Main action)
                $this->sendWhatsappReminder($deal, $user, $reminder->whatsapp_description);
        
                // ✅ Email (Secondary, should not break main flow)
                try {
        
                    if (!empty($user->working_email)) {
        
                        Mail::to($user->working_email)->send(
                            new DealReminderMail($deal, $user, $reminder->whatsapp_description)
                        );
        
                        Log::channel('deal_reminder_logs')->info("📧 Email sent", [
                            'reminder_id' => $reminder->id,
                            'email' => $user->working_email
                        ]);
        
                    } else {
                        Log::channel('deal_reminder_logs')->warning("⚠️ No email found", [
                            'reminder_id' => $reminder->id,
                            'user_id' => $user->id
                        ]);
                    }
        
                } catch (\Exception $mailException) {
        
                    Log::channel('deal_reminder_logs')->error("❌ Email failed", [
                        'reminder_id' => $reminder->id,
                        'error' => $mailException->getMessage()
                    ]);
                }
        
                // ✅ Mark success (based on WhatsApp)
                $reminder->update([
                    'is_notified' => 1,
                    'is_done' => 1,
                    'notified_at' => now()
                ]);
        
            } catch (\Exception $e) {
        
                // ❌ WhatsApp failed → mark as failed
                $reminder->update([
                    'is_notified' => 0,
                    'is_done' => 1,
                    'notified_at' => now()
                ]);
        
                Log::channel('deal_reminder_logs')->error("❌ Reminder failed", [
                    'reminder_id' => $reminder->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info('Deal pipeline reminders processed.');
    }

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Sender (Your Template Adapted)
    |--------------------------------------------------------------------------
    */

    private function sendWhatsappReminder($deal, $user, $whatsapp_description)
    {
        $metaAPI = Metawhatsappapi::whereRaw(
            "FIND_IN_SET (?, api_assign_to)",
            ['todo_notification']
        )->first();

        if (!$metaAPI) {
            Log::channel('deal_reminder_logs')->error("WhatsApp API config not found.");
            return;
        }

        $endpoint_api = $metaAPI->api_base_url . '/' . $metaAPI->vendor_uid . '/contact/send-template-message';
        $token = "Authorization: Bearer " . $metaAPI->api_access_token;

        $data = [
            'template_name'     => "reminder_2",
            'template_language' => "en",
            'phone_number'      => $user->phone ?? '---',
            'field_1' => $user->name ?? '---',
            'field_2' => $deal->company ?? $deal->candidate ?? 'Deal Pipeline Reminder',
            'field_3' => $whatsapp_description ?? "Kindly follow up on this deal to ensure timely progress.",
            'field_4' =>  Carbon::parse($deal->created_at)->format('d M Y h:i A'),
            'field_5' => $user->name ?? "System",
            'button_0' => "admin/dealPipeline/list?open_deal_model=true&deal_model_id=" . $deal->id,
            'contact' => [
                'first_name'   => $user->name ?? '---',
                'last_name'    => "--",
                'email'        => $user->working_email ?? '---',
                'country'      => "India",
                'language_code'=> "en"
            ]
        ];

        Log::channel('deal_reminder_logs')->info("📨 Deal Reminder Payload", $data);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', $token],
            CURLOPT_URL            => $endpoint_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($data),
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $responseGet = json_decode($response);

        Log::channel('deal_reminder_logs')->info("📨 Deal Reminder Response", [$responseGet]);
    }
}