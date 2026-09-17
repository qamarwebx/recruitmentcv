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
        Schema::table('meta_whatsup_campaign_filters', function (Blueprint $table) {
            $table->string('sch_type')->nullable()->after('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meta_whatsup_campaign_filters', function (Blueprint $table) {
            $table->dropColumn('sch_type');
        });
    }
};
