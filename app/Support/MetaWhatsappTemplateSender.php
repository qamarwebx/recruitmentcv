<?php

namespace App\Support;

use App\Models\Automessageresponse;
use App\Models\Metawhatsappapi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends one approved WhatsApp template through the existing CRM WhatsApp
 * provider setup - the same Metawhatsappapi row (API base URL, vendor uid,
 * access token), the same {base}/{vendor}/contact/send-template-message
 * endpoint and payload shape, and the same Automessageresponse log as the
 * order automations (AutoSendMessageForOrder*). No new integration.
 * Twin file in both apps.
 */
class MetaWhatsappTemplateSender
{
    /**
     * @param array $data phone_number, template_name, template_language, field_n...
     * @param array $log  template_for, autometanotification_id, metatemplate_id, reference_id
     */
    public static function send(array $data, ?int $metaApiId, array $log): void
    {
        $api = $metaApiId ? Metawhatsappapi::where('id', $metaApiId)->where('status', 1)->first() : null;
        if (!$api) {
            Log::warning('WhatsApp template not sent: no active WhatsApp API for the template', ['metaapi_id' => $metaApiId, 'template_for' => $log['template_for'] ?? null]);

            return;
        }

        $endpoint = rtrim($api->api_base_url, '/') . '/' . trim($api->vendor_uid, '/') . '/contact/send-template-message';

        try {
            $response = Http::withToken($api->api_access_token)->acceptJson()->timeout(30)->post($endpoint, $data);
            $body = $response->json() ?? ['result' => 'Failed', 'message' => 'HTTP ' . $response->status()];
        } catch (\Throwable $e) {
            $body = ['result' => 'Failed', 'message' => $e->getMessage()];
        }

        try {
            $row = new Automessageresponse();
            $row->template_for = $log['template_for'] ?? null;
            $row->autometanotification_id = $log['autometanotification_id'] ?? null;
            $row->metatemplate_id = $log['metatemplate_id'] ?? null;
            $row->metaapi_id = $api->id;
            $row->allcontact_id = $log['reference_id'] ?? null;
            $row->mobile_no = $data['phone_number'] ?? '';
            $row->main_response = json_encode($body);
            $row->message_status = $body['result'] ?? 'Failed';
            $row->message_text = $body['message'] ?? 'Unknown Error';
            $row->error_message_text = json_encode($body['errors'] ?? []);
            $row->save();
        } catch (\Throwable $e) {
            Log::warning('WhatsApp template response not logged', ['error' => $e->getMessage()]);
        }
    }
}
