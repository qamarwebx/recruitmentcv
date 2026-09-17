<?php

namespace App\Jobs;

use App\Models\Allcontact;
use App\Models\ExportAllContactEmailPortalHistory;
use App\Services\EmailQamrPortalService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExportAllContactToEmailPortalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum execution time (2 Hours)
     */
    public $timeout = 7200;

    /**
     * Number of attempts.
     */
    public $tries = 1;

    protected $historyId;
    protected int $adminId;

    /**
     * Same request-field => scope-method mapping used by
     * AllContactController::jsonData() so filtering logic isn't duplicated.
     */
    private const FILTER_SCOPES = [
        'country_id'                 => 'FilterCountry',
        'city_id'                    => 'FilterCity',
        'state_id'                   => 'FilterState',
        'lcs_id'                     => 'FilterLcs',
        'ls_id'                      => 'FilterLs',
        'business_type'              => 'FilterBusinesstype',
        'indust_id'                  => 'FilterIndustry',
        'group_id'                   => 'FilterGroup',
        'created_by'                 => 'FilterCreatedBy',
        'lead_priority'              => 'FilterLeadPriority',
        'careoff_id'                 => 'FilterLeadCareoff',
        'country_dial_code'          => 'FilterCountryDialCode',
        'country_dial_code_number'   => 'FilterCountryDialCodeField',
        'exlude_country_mobile_code' => 'FilterExludeCountryMobileCode',
        'conversation_type'          => 'FilterConversationType',
        'owner_id'                   => 'FilterLeadOwner',
        'followup_before'            => 'FilterFollowupBefore',
    ];

    /**
     * Create a new job instance.
     */
    public function __construct($historyId, $adminId)
    {
        $this->historyId = $historyId;
        $this->adminId = $adminId;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailQamrPortalService $service)
    {
        $history = ExportAllContactEmailPortalHistory::find($this->historyId);

        if (!$history) {
            return;
        }

        try {

            $history->update([
                'status'     => 'processing',
                'started_at' => now(),
            ]);

            $query = Allcontact::query();

            $this->applyFilters($query, $history->filters ?? []);

            $fieldMappings = $history->filters['field_mappings'] ?? [];

            $total = (clone $query)->count();

            $history->update([
                'total_records' => $total,
                'pending_count' => $total,
            ]);

            if ($total === 0) {
                $history->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);

                return;
            }

            $success = 0;
            $failed = 0;
            $processed = 0;
            $errorSamples = [];

            $query->orderBy('id')->chunkById(200, function ($contacts) use (
                $service,
                $history,
                $fieldMappings,
                &$success,
                &$failed,
                &$processed,
                &$errorSamples
            ) {

                foreach ($contacts as $contact) {

                    $processed++;

                    $email = $this->resolveEmail($contact);

                    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

                        $failed++;

                        if (count($errorSamples) < 20) {
                            $errorSamples[] = "Contact #{$contact->id}: missing/invalid email";
                        }

                    } else {

                        [$firstName, $lastName] = $this->splitName($contact->full_name);

                        $subscriberData = [
                            'EMAIL'      => $email,
                            'FIRST_NAME' => $firstName,
                            'LAST_NAME'  => $lastName,
                            'status'     => 'subscribed',
                        ];

                        foreach ($fieldMappings as $mapping) {
                            $column = $mapping['field'] ?? null;
                            $key = $mapping['key'] ?? null;

                            if (!$column || !$key) {
                                continue;
                            }

                            $subscriberData[$key] = (string) ($contact->{$column} ?? '');
                        }

                        $result = $service->createSubscriber(
                            $history->api_token,
                            $history->list_uid,
                            $subscriberData
                        );

                        if ($result['success']) {
                            $success++;
                        } else {

                            $failed++;

                            if (count($errorSamples) < 20) {
                                $errorSamples[] = "Contact #{$contact->id} ({$email}): " . ($result['message'] ?? 'Unknown error');
                            }
                        }
                    }

                    // Light rate control to avoid hammering the remote API
                    usleep(100000);
                }

                $history->update([
                    'success_count' => $success,
                    'failed_count'  => $failed,
                    'pending_count' => max($history->total_records - $processed, 0),
                ]);
            });

            $status = 'completed';

            if ($failed > 0) {
                $status = $success === 0 ? 'failed' : 'completed_with_errors';
            }

            $history->update([
                'status'        => $status,
                'success_count' => $success,
                'failed_count'  => $failed,
                'pending_count' => 0,
                'error_message' => $errorSamples ? implode("\n", $errorSamples) : null,
                'completed_at'  => now(),
            ]);

        } catch (Exception $e) {

            Log::channel('email_qamr_portal')->error('All Contact export job failed', [
                'history_id' => $this->historyId,
                'error'      => $e->getMessage(),
            ]);

            $history->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at'  => now(),
            ]);

            throw $e;
        }
    }

    private function applyFilters($query, array $filters): void
    {
        foreach (self::FILTER_SCOPES as $field => $scope) {
            $value = $filters[$field] ?? null;

            if (filled($value)) {
                $query->{$scope}($value);
            }
        }

        if (!empty($filters['created_date'])) {
            $query->FilterDateRange('created_at', $filters['created_date']);
        }

        if (!empty($filters['updated_date'])) {
            $query->FilterDateRange('updated_at', $filters['updated_date']);
        }

        if (!empty($filters['staff_updated_date'])) {
            $query->FilterDateRange('staff_updated_date', $filters['staff_updated_date']);
        }

        if (!empty($filters['search_text'])) {
            $query->FilterSearchText($filters['search_text']);
        }

        if (!empty($filters['scope_admin_id'])) {
            $query->where('careoff_id', $filters['scope_admin_id']);
        }
    }

    /**
     * Allcontact has 4 email columns; use the first non-empty one in
     * priority order: email, email0, email1, email2.
     */
    private function resolveEmail($contact): string
    {
        foreach (['email', 'email0', 'email1', 'email2'] as $column) {

            $value = trim((string) ($contact->{$column} ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function splitName(?string $fullName): array
    {
        $fullName = trim((string) $fullName);

        if ($fullName === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/', $fullName, 2);

        return [$parts[0], $parts[1] ?? ''];
    }
}
