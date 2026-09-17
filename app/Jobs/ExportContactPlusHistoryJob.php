<?php

namespace App\Jobs;

use App\Models\Basepathstatus;
use App\Models\Contactplus;
use App\Models\ExportContactPlusHistory;
use App\Services\ContactPlusExportService;
use App\Models\Contactplusadminsavefilter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportContactPlusHistoryJob implements ShouldQueue
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
    public function handle(ContactPlusExportService $exportService)
    {
        $export = ExportContactPlusHistory::find($this->exportId);

        if (!$export) {
            return;
        }

        try {

            $export->update([
                'status'     => 'processing',
                'started_at' => now(),
            ]);

            $columns = $export->columns ?? [];

            $query = Contactplus::with([
                'ls',
                'city',
                'country',
                'group',
                'contactstatus',
            ]);

            if (!$export->is_all_export) {

                $ids = array_filter(explode(',', $export->contact_ids));

                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                }
            }
            else{
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
            $fileName = 'contactplus_export_' . time() . '.csv';

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
                'status'             => 'completed',
                'file_name'          => $fileName,
                'file_path'          => 'exports/' . $fileName,
                'exported_records'   => $exported,
                'completed_at'       => now(),
            ]);

        } catch (Exception $e) {

            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }

            $export->update([
                'status'         => 'failed',
                'error_message'  => $e->getMessage(),
                'completed_at'   => now(),
            ]);

            throw $e;
        }
    }

    private function applySavedFilters($query): void
    {
        $filter = Contactplusadminsavefilter::where('admin_id', $this->adminId)->first();

        if (!$filter) {
            return;
        }

        $country_id      = $filter->country_id ? explode(',', $filter->country_id) : [];
        $city_id         = $filter->city_id ? explode(',', $filter->city_id) : [];
        $group_id        = $filter->group_id ? explode(',', $filter->group_id) : [];
        $businesstype_id = $filter->businesstype_id ? explode(',', $filter->businesstype_id) : [];
        $industry_id     = $filter->industry_id ? explode(',', $filter->industry_id) : [];
        $lcs_id          = $filter->lcs_id ? explode(',', $filter->lcs_id) : [];
        $ls_id           = $filter->ls_id ? explode(',', $filter->ls_id) : [];
        $send_tag        = $filter->sned_tag ? explode(',', $filter->sned_tag) : [];
        $created_by      = $filter->created_by ? explode(',', $filter->created_by) : [];
        $subscribe       = $filter->subscribe ? explode(',', $filter->subscribe) : [];

        $query
            ->FilterCountry($country_id)
            ->FilterCity($city_id)
            ->FilterLcs($lcs_id)
            ->FilterLs($ls_id)
            ->FilterBusinesstype($businesstype_id)
            ->FilterIndustry($industry_id)
            ->FilterGroup($group_id)
            ->FilterCreatedBy($created_by)
            ->FilterSendTag($send_tag)
            ->FilterSubscribe($subscribe)
            ->FilterDate('send_date', $filter->send_date)
            ->FilterDateRange('update_lead_status_date', $filter->update_status_date)
            ->FilterDate('updated_at', $filter->updated_date)
            ->FilterDate('created_at', $filter->created_date);
    }
}