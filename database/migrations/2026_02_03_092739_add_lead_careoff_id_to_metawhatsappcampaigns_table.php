<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->string('lead_careoff_id')
                  ->nullable()
                  ->after('leads_contact_type');
        });
    }

    public function down(): void
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->dropColumn('lead_careoff_id');
        });
    }
};
