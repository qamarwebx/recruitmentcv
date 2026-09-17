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
            $table->string('by_updated_date')->nullable()->after('by_location');
            $table->string('by_followup_before')->nullable()->after('by_updated_date');
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
            $table->dropColumn('by_updated_date');
            $table->dropColumn('by_followup_before');
        });
    }
};
