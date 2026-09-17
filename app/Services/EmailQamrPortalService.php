<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailQamrPortalService
{
    /**
     * The API returns a flat array of lists with no pagination metadata,
     * so "has more" is inferred from whether a full page was returned.
     */
    private const LISTS_PAGE_SIZE = 10;

    private function baseUrl(): string
    {
        return rtrim(config('services.email_qamr.base_url', 'https://email.qamr.in'), '/');
    }

    private function maskToken(string $token): string
    {
        return substr($token, 0, 4) . str_repeat('*', max(strlen($token) - 4, 0));
    }

    /**
     * GET /api/v1/lists?api_token=...&page=...
     */
    public function getLists(string $token, int $page = 1): array
    {
        try {
            $response = Http::timeout(20)->connectTimeout(10)
                ->acceptJson()
                ->get($this->baseUrl() . '/api/v1/lists', [
                    'api_token' => $token,
                    'page'      => $page,
                ]);

        } catch (\Throwable $e) {
            Log::channel('email_qamr_portal')->error('getLists request failed', [
                'token' => $this->maskToken($token),
                'page'  => $page,
                'error' => $e->getMessage(),
            ]);

            return [
                'success'  => false,
                'lists'    => [],
                'has_more' => false,
                'message'  => 'Unable to reach email.qamr.in. Please try again.',
            ];
        }

        $json = $response->json();

        // Success looks like a bare JSON array of list objects, e.g.
        // [{"id":5,"uid":"65aa58e927618","name":"Associate Agent List",...}, ...]
        // Failure looks like {"message": "Unauthenticated."} (or similar single-object error).
        if (!$response->successful() || !is_array($json) || !array_is_list($json)) {

            $message = data_get($json, 'message') ?? data_get($json, 'error');

            Log::channel('email_qamr_portal')->warning('getLists returned an error', [
                'token'  => $this->maskToken($token),
                'page'   => $page,
                'status' => $response->status(),
                'body'   => $json ?? $response->body(),
            ]);

            return [
                'success'  => false,
                'lists'    => [],
                'has_more' => false,
                'message'  => $message ?: 'Invalid API token or email.qamr.in error.',
            ];
        }

        $lists = [];

        foreach ($json as $record) {
            $uid = $record['uid'] ?? null;
            $name = $record['name'] ?? 'Untitled List';

            if (blank($uid)) {
                continue;
            }

            $lists[] = [
                'uid'  => $uid,
                'name' => $name,
            ];
        }

        return [
            'success'  => true,
            'lists'    => $lists,
            'has_more' => count($lists) >= self::LISTS_PAGE_SIZE,
        ];
    }

    /**
     * GET /api/v1/lists/{uid}?api_token=...
     */
    public function getListFields(string $token, string $listUid): array
    {
        try {
            $response = Http::timeout(20)->connectTimeout(10)
                ->acceptJson()
                ->get($this->baseUrl() . '/api/v1/lists/' . rawurlencode($listUid), [
                    'api_token' => $token,
                ]);

        } catch (\Throwable $e) {
            Log::channel('email_qamr_portal')->error('getListFields request failed', [
                'token'    => $this->maskToken($token),
                'list_uid' => $listUid,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'fields'  => [],
                'message' => 'Unable to reach email.qamr.in. Please try again.',
            ];
        }

        $json = $response->json();
        $list = data_get($json, 'list');

        // Success looks like {"list": {"uid":..., "name":..., "fields": [{"key":...,"label":...,"type":...}, ...]}}
        // Failure looks like {"message": "Mail list not found"}.
        if (!$response->successful() || !is_array($list)) {

            $message = data_get($json, 'message') ?? data_get($json, 'error');

            Log::channel('email_qamr_portal')->warning('getListFields returned an error', [
                'token'    => $this->maskToken($token),
                'list_uid' => $listUid,
                'status'   => $response->status(),
                'body'     => $json ?? $response->body(),
            ]);

            return [
                'success' => false,
                'fields'  => [],
                'message' => $message ?: 'Unable to load fields for this list.',
            ];
        }

        $fields = [];

        foreach (($list['fields'] ?? []) as $field) {
            $key = $field['key'] ?? null;

            if (blank($key)) {
                continue;
            }

            $fields[] = [
                'key'   => $key,
                'label' => $field['label'] ?? $key,
                'type'  => $field['type'] ?? null,
            ];
        }

        return [
            'success' => true,
            'fields'  => $fields,
        ];
    }

    /**
     * POST /api/v1/subscribers (params sent on the query string, per the vendor's curl spec)
     */
    public function createSubscriber(string $token, string $listUid, array $subscriber): array
    {
        $params = array_merge([
            'api_token' => $token,
            'list_uid'  => $listUid,
        ], $subscriber);

        $attempts = 0;
        $maxAttempts = 2;

        while (true) {
            $attempts++;

            try {
                $response = Http::timeout(20)->connectTimeout(10)
                    ->acceptJson()
                    ->withOptions(['query' => $params])
                    ->post($this->baseUrl() . '/api/v1/subscribers');
            } catch (\Throwable $e) {
                if ($attempts < $maxAttempts) {
                    usleep(300_000);
                    continue;
                }

                Log::channel('email_qamr_portal')->error('createSubscriber request failed', [
                    'token'    => $this->maskToken($token),
                    'list_uid' => $listUid,
                    'email'    => $subscriber['EMAIL'] ?? null,
                    'error'    => $e->getMessage(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Network/timeout error contacting email.qamr.in.',
                ];
            }

            $json = $response->json();

            // Success looks like {"status":1,"message":"Subscriber was successfully created","subscriber_id":354789}
            // Failure looks like {"message": "Unauthenticated."} or {"status":0,"message":"...","data":{...}}
            if ($response->successful() && (int) data_get($json, 'status') === 1) {
                return ['success' => true, 'duplicate' => false];
            }

            $message = data_get($json, 'message') ?? data_get($json, 'error');

            if (blank($message) && is_array(data_get($json, 'data'))) {
                $message = collect($json['data'])->flatten()->first();
            }

            // Laravel-style validation error object with no wrapper, e.g.
            // {"EMAIL": ["The e m a i l field has already been taken."]}
            if (blank($message) && is_array($json) && !array_is_list($json)) {
                $message = collect($json)->flatten()->first();
            }

            $message = $message ?: $response->body();

            $isDuplicate = $message && (
                stripos($message, 'already') !== false ||
                stripos($message, 'exist') !== false
            );

            if ($isDuplicate) {
                return ['success' => true, 'duplicate' => true];
            }

            if ($response->serverError() && $attempts < $maxAttempts) {
                usleep(300_000);
                continue;
            }

            Log::channel('email_qamr_portal')->warning('createSubscriber rejected', [
                'token'    => $this->maskToken($token),
                'list_uid' => $listUid,
                'email'    => $subscriber['EMAIL'] ?? null,
                'status'   => $response->status(),
                'message'  => $message,
            ]);

            return [
                'success' => false,
                'message' => $message ?: 'Unknown error from email.qamr.in.',
            ];
        }
    }
}
