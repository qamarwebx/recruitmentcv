<?php

namespace App\Jobs;

use App\Models\Contactplus;
use App\Models\ExportContactPlusEmailPortalHistory;
use App\Services\EmailQamrPortalService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExportContactPlusToEmailPortalJob implements ShouldQueue
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
        $history = ExportContactPlusEmailPortalHistory::find($this->historyId);

        if (!$history) {
            return;
        }

        try {

            $history->update([
                'status'     => 'processing',
                'started_at' => now(),
            ]);

            $query = Contactplus::query();

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

                    $email = trim((string) ($contact->prim_email ?? ''));

                    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

                        $failed++;

                        if (count($errorSamples) < 20) {
                            $errorSamples[] = "Contact #{$contact->id}: missing/invalid email";
                        }

                    } else {

                        [$firstName, $lastName] = $this->splitName($contact->prim_concern_name);

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

            Log::channel('email_qamr_portal')->error('Export job failed', [
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
        $query
            ->FilterCountry($filters['country_id'] ?? null)
            ->FilterCity($filters['city_id'] ?? null)
            ->FilterLcs($filters['lcs_id'] ?? null)
            ->FilterLs($filters['ls_id'] ?? null)
            ->FilterBusinesstype($filters['businesstype_id'] ?? null)
            ->FilterIndustry($filters['industries'] ?? null)
            ->FilterGroup($filters['group_id'] ?? null)
            ->FilterCreatedBy($filters['created_by'] ?? null)
            ->FilterSendTag($filters['send_tag'] ?? null)
            ->FilterSubscribe($filters['subscribe'] ?? null)
            ->FilterDate('send_date', $filters['send_date'] ?? null)
            ->FilterDateRange('update_lead_status_date', $filters['update_lead_status_date'] ?? null)
            ->FilterDate('created_at', $filters['created_at'] ?? null)
            ->FilterDate('updated_at', $filters['updated_at'] ?? null)
            ->FilterSearchText($filters['search_text'] ?? null);

        if (!empty($filters['scope_admin_id'])) {
            $query->where('careoff_id', $filters['scope_admin_id']);
        }
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
