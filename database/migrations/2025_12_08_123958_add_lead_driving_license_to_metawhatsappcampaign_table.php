<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->string('lead_driving_license')->nullable()->after('leads_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->dropColumn('lead_driving_license');
        });
    }
};
