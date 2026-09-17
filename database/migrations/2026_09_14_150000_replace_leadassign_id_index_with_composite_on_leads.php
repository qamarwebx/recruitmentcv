<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restricted-view staff (leads_view = 0) hit `WHERE leadassign_id = ?
     * ORDER BY lead_date DESC LIMIT 10` on every Leads list load/filter/sort/
     * page. With only the single-column leadassign_id index, MySQL sometimes
     * chooses the lead_date index instead and filters as it goes — fine when
     * a staff member's leads are recent, but measured at ~57ms for a staff
     * member whose most recent lead is several months stale relative to the
     * table's newest rows (it has to skip every newer row from other staff
     * first). A composite (leadassign_id, lead_date) index lets MySQL jump
     * straight to that staff member's rows already in date order — measured
     * at ~0.7ms for the same query. The composite's leftmost column also
     * fully covers every existing plain `leadassign_id` equality/DISTINCT
     * lookup elsewhere (verified via EXPLAIN), so the old single-column
     * index becomes redundant weight on every write and is dropped.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_leadassign_id_index');
            $table->index(['leadassign_id', 'lead_date']);
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['leadassign_id', 'lead_date']);
            $table->index('leadassign_id');
        });
    }
};
