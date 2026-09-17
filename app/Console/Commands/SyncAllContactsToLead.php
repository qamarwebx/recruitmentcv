<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAllContactsToLead extends Command
{
    protected $signature = 'sync:allcontact-to-lead';
    protected $description = 'Sync all contacts that exist in leads';

    public function handle()
    {
        $batchSize = 1000;

        // Last synced contact ID
        $lastContactId = DB::table('sync_allcontact_to_lead')->max('allcontact_id') ?? 0;

        // Maximum contact ID in allcontacts
        $maxContactId = DB::table('allcontacts')->max('id');

        if ($lastContactId >= $maxContactId) {
            $this->info('No new contacts to sync.');
            return self::SUCCESS;
        }

        $endId = $lastContactId + $batchSize;

        DB::statement("
            INSERT INTO sync_allcontact_to_lead (
                allcontact_id,
                lead_id,
                primary_no_wsp
            )
            SELECT
                x.allcontact_id,
                MIN(x.lead_id) AS lead_id,
                x.primary_no_wsp
            FROM (

                SELECT
                    a.id AS allcontact_id,
                    b.id AS lead_id,
                    a.primary_no_wsp
                FROM allcontacts a
                JOIN leads b
                    ON a.primary_no_wsp = b.whatsapp_no
                WHERE a.id > {$lastContactId}
                AND a.id <= {$endId}

                UNION ALL

                SELECT
                    a.id AS allcontact_id,
                    b.id AS lead_id,
                    a.primary_no_wsp
                FROM allcontacts a
                JOIN leads b
                    ON a.primary_no_wsp = b.mob_no
                WHERE a.id > {$lastContactId}
                AND a.id <= {$endId}

            ) x
            LEFT JOIN sync_allcontact_to_lead s
                ON s.allcontact_id = x.allcontact_id
            WHERE s.allcontact_id IS NULL
            GROUP BY
                x.allcontact_id,
                x.primary_no_wsp
        ");

        $this->info("Processed contacts {$lastContactId} - {$endId}");

        return self::SUCCESS;
    }
}