<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->tinyInteger('leads_contact_type')
                  ->nullable()
                  ->comment('1=Mobile or WhatsApp, 2=Mobile only, 3=WhatsApp only')
                  ->after('lead_driving_license'); // adjust column position if needed
        });
    }

    public function down()
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->dropColumn('leads_contact_type');
        });
    }
};
