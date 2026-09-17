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
        Schema::table('metawhatsappcampaignresponses', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id')->nullable()->after('allcontact_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metawhatsappcampaignresponses', function (Blueprint $table) {
            $table->dropColumn('lead_id');
        });
    }
};
