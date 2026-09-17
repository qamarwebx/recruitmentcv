<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->text('delay_frequency')
                  ->nullable()
                  ->comment('Delay between WhatsApp messages in seconds')
                  ->after('sch_type'); // adjust column if needed
        });
    }

    public function down(): void
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->dropColumn('delay_frequency');
        });
    }
};
