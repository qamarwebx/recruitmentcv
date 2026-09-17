<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * These columns are filtered/sorted/looked-up on every Leads list request
     * (jsonData's default sort + status/date filters), every public lead
     * submission (mob_no/whatsapp_no duplicate checks), and every row-level
     * ownership check (leadassign_id) — all currently full table scans since
     * `leads` has no secondary indexes at all (verified via EXPLAIN/SHOW INDEX
     * against ~15.8k rows before this migration).
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->index('leadassign_id');
            $table->index('lead_date');
            $table->index('mob_no');
            $table->index('whatsapp_no');
            $table->index('is_qualified');
            $table->index('country');

            // required_service is a TEXT column — MySQL requires a prefix
            // length to index it; 100 chars comfortably covers these values
            // (short job-title strings, see LeadController::jsonData()).
            $table->rawIndex('required_service(100)', 'leads_required_service_index');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['leadassign_id']);
            $table->dropIndex(['lead_date']);
            $table->dropIndex(['mob_no']);
            $table->dropIndex(['whatsapp_no']);
            $table->dropIndex(['is_qualified']);
            $table->dropIndex(['country']);
            $table->dropIndex('leads_required_service_index');
        });
    }
};
