<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * leadnotes.lead_id has no index — every "View Lead" modal open eager
     * loads notes via Lead::with('notes'), which is a full table scan on
     * leadnotes (~14.7k rows) for every single request.
     */
    public function up(): void
    {
        Schema::table('leadnotes', function (Blueprint $table) {
            $table->index('lead_id');
        });
    }

    public function down(): void
    {
        Schema::table('leadnotes', function (Blueprint $table) {
            $table->dropIndex(['lead_id']);
        });
    }
};
