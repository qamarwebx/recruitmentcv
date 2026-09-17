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
        Schema::table('lead_admin_save_filters', function (Blueprint $table) {
            $table->string('by_location')->nullable()->after('by_expected_country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_admin_save_filters', function (Blueprint $table) {
            $table->dropColumn('by_location');
        });
    }
};
