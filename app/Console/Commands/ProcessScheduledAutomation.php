<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\ScheduledSendMsgAutomation;
use App\Models\Autometanotification;
use App\Models\Metawhatsappapi;

class ProcessScheduledAutomation extends Command
{
    protected $signature = 'automation:process-scheduled';
    protected $description = 'Process scheduled automation messages every minute';

    /**
     * Map template_for → WhatsApp mobile fields
     * Multiple fields allowed (comma separated)
     */
    protected $mobileMap = [
        'contactpluses'  => 'prim_contact',
        'allcontacts'    => 'primary_no_wsp',
        'associates'     => 'pty_mobile',
        'partners'       => 'primary_mob',
        'employers'      => 'mobile_no',
        'leads'          => 'mob_no,whatsapp_no',
        'todos'          => 'mobile_number',
    ];

    public function handle()
    {
        Log::channel('scheduled_automation')->info("🚀 CRON STARTED");

        try {
            $now = Carbon::now();

            $pending = ScheduledSendMsgAutomation::where('status', 0)
                ->whereBetween('calculated_time', [
                    $now->copy()->startOfMinute(),
                    $now->copy()->endOfMinute()
                ])
                ->get([
                    'id',
                    'template_table_name',
                    'template_for',
                    'autometanotifications_id',
                    'metatemp_id',
                    'send_user_to',
                    'trigger_template_time',
                    'calculated_time'
                ]);

            Log::channel('scheduled_automation')->info('📌 Pending Jobs', [
                'count' => $pending->count()
            ]);

            if ($pending->isEmpty()) {
                Log::channel('scheduled_automation')->info("⛔ No jobs found");
                return;
            }

            foreach ($pending as $job) {
                $this->processSingleJob($job);
            }

        } catch (\Exception $ex) {
            Log::channel('scheduled_automation')->error(
                "🔥 FATAL CRON ERROR",
                $this->err($ex)
            );
        }

        Log::channel('scheduled_automation')->info("🏁 CRON FINISHED");
    }

    /**
     * Process a single job
     */
    private function processSingleJob($job)
    {
        try {
            $prepared = $this->prepareMessage($job);

            if (!$prepared) {
                Log::channel('scheduled_automation')->warning(
                    "⚠ Preparation failed",
                    ['job_id' => $job->id]
                );
                return;
            }

            $this->sendWhatsAppTemplate($job, $prepared);

            // Mark job done
            $job->update(['status' => 1]);

            Log::channel('scheduled_automation')->info(
                '✅ Job completed',
                ['job_id' => $job->id]
            );

        } catch (\Exception $ex) {
            Log::channel('scheduled_automation')->error(
                "❌ Error in job",
                $this->err($ex, ['job_id' => $job->id])
            );
        }
    }

    /**
     * Prepare message (DB only, no API calls)
     */
    private function prepareMessage($job)
    {
        // Load automation meta
        $meta = Autometanotification::select(
            'id',
            'metatemp_id',
            'meta_template_name',
            'meta_message_body',
            'field_not_completed'
        )->find($job->autometanotifications_id);

        if (!$meta) {
            return $this->fail("Meta automation missing", $job);
        }

        // Load meta template
        $metaTemplate = $this->getMetaTemplate($meta->metatemp_id);
        if (!$metaTemplate) {
            return $this->fail("Meta template missing", $job);
        }

        // Validate table
        if (!Schema::hasTable($job->template_table_name)) {
            return $this->fail("Table not found", $job, [
                'table' => $job->template_table_name
            ]);
        }

        // Load user row
        $user = DB::table($job->template_table_name)
            ->find($job->send_user_to);

        if (!$user) {
            return $this->fail("User record missing", $job);
        }

        // Collect multiple mobile numbers
        $mobileFields = $this->mobileMap[$job->template_for] ?? null;

        if (!$mobileFields) {
            return $this->fail("Mobile mapping missing", $job);
        }

        $mobiles = [];

        foreach (explode(',', $mobileFields) as $field) {
            $field = trim($field);
            if (!empty($user->{$field})) {
                $mobiles[] = $user->{$field};
            }
        }

        // Remove duplicates
        $mobiles = array_values(array_unique($mobiles));

        if (empty($mobiles)) {
            return $this->fail("No valid mobile numbers", $job, [
                'expected_fields' => $mobileFields
            ]);
        }

        // Field-not-completed rule
        if (!$this->passesFieldNotCompletedCheck($meta, $user)) {
            Log::channel('scheduled_automation')->info(
                "⛔ Skip: All fields completed",
                ['job_id' => $job->id]
            );
            return false;
        }

        return [
            'job_id'        => $job->id,
            'user'          => $user,
            'user_mobiles'  => $mobiles,
            'meta'          => $meta,
            'meta_template' => $metaTemplate
        ];
    }

    /**
     * Field-not-completed rule
     */
    private function passesFieldNotCompletedCheck($meta, $user)
    {
        if (empty($meta->field_not_completed)) {
            return true;
        }

        $fields = json_decode($meta->field_not_completed, true);
        if (!is_array($fields)) return true;

        foreach ($fields as $field) {
            if (empty(trim((string)($user->{$field} ?? '')))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetch meta template
     */
    private function getMetaTemplate($id)
    {
        return $id
            ? DB::table('metawhatsapptemplates')->find($id)
            : false;
    }

    /**
     * Send WhatsApp template to ALL mobile numbers
     */
    private function sendWhatsAppTemplate($job, $prepared)
    {
        $metaTemplate = $prepared['meta_template'];

        if (empty($metaTemplate->metaapi_id)) {
            Log::channel('scheduled_automation')->error(
                "⚠ Meta API not assigned",
                ['job_id' => $job->id]
            );
            return false;
        }

        $metaAPI = Metawhatsappapi::find($metaTemplate->metaapi_id);
        if (!$metaAPI) {
            Log::channel('scheduled_automation')->error(
                "⚠ Meta API config missing",
                ['job_id' => $job->id]
            );
            return false;
        }

        $endpoint = rtrim($metaAPI->api_base_url, '/')
            . '/' . trim($metaAPI->vendor_uid, '/')
            . '/contact/send-template-message';

        foreach ($prepared['user_mobiles'] as $mobile) {

            $payload = $this->buildTemplatePayload(
                $job,
                $prepared['user'],
                $metaTemplate,
                $mobile
            );

            $response = $this->callMetaAPI(
                $endpoint,
                $metaAPI->api_access_token,
                $payload
            );

            Log::channel('scheduled_automation')->info(
                "📤 WhatsApp Sent",
                [
                    'job_id'  => $job->id,
                    'mobile'  => $mobile,
                    'payload' => $payload,
                    'response'=> $response
                ]
            );
        }

        return true;
    }

    /**
     * Build WhatsApp payload
     */
    private function buildTemplatePayload($job, $user, $metaTemplate, $mobile)
    {
        $data = [
            'template_name'     => $metaTemplate->template_name ?? 'lead_follow_up',
            'template_language' => $metaTemplate->template_language ?? 'en',
            'phone_number'      => $mobile,
        ];

        if (!empty($metaTemplate->meta_field_var) && !empty($metaTemplate->meta_assign_ar)) {

            $metaVars   = array_map('trim', explode(',', $metaTemplate->meta_field_var));
            $assignCols = array_map('trim', explode(',', $metaTemplate->meta_assign_ar));

            foreach ($metaVars as $i => $metaKey) {

                $assign = $assignCols[$i] ?? '';
            
                if (!$assign) {
                    $data[$metaKey] = '';
                    continue;
                }
            
                $data[$metaKey] = $this->resolveTemplateValue(
                    $assign,
                    $i,
                    $user,
                    $metaTemplate,
                    'https://wa.me'
                );
            }

             // Header file path (same behavior as controller)
             if ($metaTemplate->meta_url_type == 0) {
                $header_file_path = $metaTemplate->whatsapp_file
                    ? url('admin/assets/images/template/'.$metaTemplate->whatsapp_file)
                    : "";
            } elseif ($metaTemplate->meta_url_type == 1) {
                $header_file_path = $metaTemplate->static_url ?? "";
            } else {
                $header_file_path = "";
            }

            $data['header_document'] = $header_file_path;

            // Required by Meta
            $data['header_document_name'] = $metaTemplate->document_name ?? basename($header_file_path);
        }

        return $data;
    }


    private function resolveTemplateValue($assign,$index,$user,$metaTemplate,$defaultChatUrl = '') {
        // STATIC REPLACEMENTS
        $staticReplace = [
            '[Careoff Name]'      => $user->name ?? '',
            '[Careoff Contact 1]' => $user->care_no_1 ?? '',
            '[Careoff Contact 2]' => $user->care_no_2 ?? '',
            '[Whatsapp Chat Dynamic URL]' => $defaultChatUrl,
        ];
    
        if (isset($staticReplace[$assign])) {
            return $staticReplace[$assign];
        }
    
        // STATIC CAREOFF
        if ($assign === '[Enter Static Careoff]') {
    
            $careoffIds = array_map(
                'trim',
                explode(',', (string) $metaTemplate->careoff_id_static)
            );
    
            $careoffFields = array_map(
                'trim',
                explode(',', (string) $metaTemplate->careoff_field_static)
            );
    
            $careoffId    = $careoffIds[$index] ?? null;
            $careoffField = $careoffFields[$index] ?? null;
    
            if (!$careoffId || !$careoffField) return '';
    
            $admin = \App\Models\Admin::where('id', $careoffId)
                ->where('status', true)
                ->first();
    
            if (!$admin) return '';
    
            return match ($careoffField) {
                '[Static Careoff Name]'      => $admin->name ?? '',
                '[Static Careoff Contact 1]' => $admin->care_no_1 ?? '',
                '[Static Careoff Contact 2]' => $admin->care_no_2 ?? '',
                '[Calling Number]'           => 'call/'.$admin->calling_number,
                default => ''
            };
        }
    
        // DYNAMIC WHATSAPP CHAT URL
        if ($assign === '[Whatsapp Chat Static URL]') {
    
            $staticChatIds = array_map(
                'trim',
                explode(',', (string) $metaTemplate->whatsup_chat_careoff_id_static)
            );
    
            $careoffId = $staticChatIds[$index] ?? null;
    
            if (!$careoffId) return $defaultChatUrl;
    
            $chat = \App\Models\Whatsappchaturl::where('staff_id', $careoffId)
                ->where('status', true)
                ->latest('id')
                ->first();
    
            return $chat->chat_url ?? $defaultChatUrl;
        }
    
        // OTHERS (Header file)
        if ($assign === '[Others]') {
            return $metaTemplate->header_file_path ?? '';
        }
    
        // DYNAMIC COLUMN FROM USER
        $clean = str_replace(['[', ']'], '', $assign);
        return data_get($user, $clean, '');
    }
    
    /**
     * Call Meta API
     */
    private function callMetaAPI($endpoint, $token, $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::channel('scheduled_automation')->error("cURL Error", [
                'error' => $error
            ]);
        }

        return $response;
    }

    /**
     * Exception helper
     */
    private function err($ex, $extra = [])
    {
        return array_merge([
            'error' => $ex->getMessage(),
            'file'  => $ex->getFile(),
            'line'  => $ex->getLine(),
        ], $extra);
    }

    /**
     * Failure helper
     */
    private function fail($msg, $job, $extra = [])
    {
        Log::channel('scheduled_automation')->warning(
            "⚠ $msg",
            array_merge(['job_id' => $job->id], $extra)
        );
        return false;
    }
}
