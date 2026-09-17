<?php

namespace App\Jobs;

use App\Models\Allcontact;
use App\Models\Basepathstatus;
use App\Models\ExportAllContactHistory;
use App\Models\Allcontactadminsavefilter;
use App\Services\ContactExportService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;


class ExportAllContactHistoryJob implements ShouldQueue
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

    protected $exportId;
    protected int $adminId;

    /**
     * Create a new job instance.
     */
    public function __construct($exportId,$adminId)
    {
        $this->exportId = $exportId;
        $this->adminId = $adminId;

    }

    /**
     * Execute the job.
     */
    public function handle(ContactExportService $exportService)
    {
        $export = ExportAllContactHistory::find($this->exportId);

        if (!$export) {
            return;
        }

        try {

            $export->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            $columns = $export->columns ?? [];

            $query = Allcontact::with([
                'careoff',
                'group',
                'ls',
            ]);

            if (!$export->is_all_export) {

                $ids = array_filter(explode(',', $export->contact_ids));

                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                }
            }else{
                $this->applySavedFilters($query);
            }

            $totalRecords = (clone $query)->count();

            $export->update([
                'total_records' => $totalRecords,
            ]);

            /**
             * Decide export directory
             */
            $basepathstatus = Basepathstatus::first();

            if ($basepathstatus && $basepathstatus->base_path_status == 1) {
                $exportDirectory = base_path('public/exports');
            } else {
                $exportDirectory = base_path('public_html/exports');
            }

            if (!is_dir($exportDirectory)) {
                mkdir($exportDirectory, 0777, true);
            }

            /**
             * File
             */
            $fileName = 'contacts_export_' . time() . '.csv';

            $filePath = $exportDirectory . DIRECTORY_SEPARATOR . $fileName;

            $handle = fopen($filePath, 'w');

            if (!$handle) {
                throw new Exception('Unable to create export file.');
            }

            /**
             * UTF-8 BOM
             */
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            /**
             * Header
             */
            fputcsv(
                $handle,
                $exportService->getExportHeaders($columns)
            );

            $exported = 0;

            $query
                ->orderBy('id')
                ->chunkById(1000, function ($contacts) use (
                    &$exported,
                    $handle,
                    $columns,
                    $export,
                    $exportService
                ) {

                    foreach ($contacts as $contact) {

                        $row = $exportService->mapRow($contact, $columns);

                        fputcsv($handle, array_values($row));

                        $exported++;
                    }

                    // Update progress
                    $export->update([
                        'exported_records' => $exported,
                    ]);
                });

            fclose($handle);

            /**
             * Completed
             */
            $export->update([
                'status' => 'completed',
                'file_name' => $fileName,
                'file_path' => 'exports/' . $fileName,
                'exported_records' => $exported,
                'completed_at' => now(),
            ]);

        } catch (Exception $e) {

            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }

            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }


    private function applySavedFilters($query): void
    {
        $filter = Allcontactadminsavefilter::where('admin_id', $this->adminId)->first();

        if (!$filter) {
            return;
        }

        $getArray = fn($value) => $value ? explode(',', $value) : [];

        $country_id                  = $getArray($filter->country_id);
        $city_id                     = $getArray($filter->city_id);
        $state_id                    = $getArray($filter->state_id);
        $lcs_id                      = $getArray($filter->lcs_id);
        $ls_id                       = $getArray($filter->ls_id);
        $lead_type                   = $getArray($filter->lead_type);
        $industry_id                 = $getArray($filter->indust_id);
        $group_id                    = $getArray($filter->group_id);
        $created_by                  = $getArray($filter->user_id);
        $lead_priority               = $getArray($filter->lead_prority);
        $careoff_id                  = $getArray($filter->careoff_id);
        $owner_id                    = $getArray($filter->owner_id);
        $country_dial_code           = $getArray($filter->country_dial_code);
        $country_dial_code_number    = $getArray($filter->country_dial_code_number);
        $exclude_country_mobile_code = $getArray($filter->exlude_country_mobile_code);
        $conversation_type           = $getArray($filter->conversation_type);

        if (filled($filter->followup_before)) {

            $days = (int) $filter->followup_before;

            $today = Carbon::today();

            $futureDate = $days === 1
                ? Carbon::today()
                : Carbon::today()->addDays($days);

            $query->whereBetween('updated_at', [
                $today->startOfDay(),
                $futureDate->endOfDay(),
            ]);
        }

        $query
            ->FilterCountry($country_id)
            ->FilterCity($city_id)
            ->FilterState($state_id)
            ->FilterLcs($lcs_id)
            ->FilterLs($ls_id)
            ->FilterBusinesstype($lead_type)
            ->FilterIndustry($industry_id)
            ->FilterGroup($group_id)
            ->FilterCreatedBy($created_by)
            ->FilterLeadPriority($lead_priority)
            ->FilterLeadCareoff($careoff_id)
            ->FilterCountryDialCode($country_dial_code)
            ->FilterCountryDialCodeField($country_dial_code_number)
            ->FilterExludeCountryMobileCode($exclude_country_mobile_code)
            ->FilterDateRange('created_at', $filter->by_created_date)
            ->FilterDateRange('updated_at', $filter->by_updated_date)
            ->FilterDateRange('staff_updated_date', $filter->by_staff_updated_date)
            ->FilterConversationType($conversation_type)
            ->FilterLeadOwner($owner_id);
    }
}