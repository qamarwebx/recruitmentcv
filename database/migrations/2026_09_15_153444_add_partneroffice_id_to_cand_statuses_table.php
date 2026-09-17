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
        Schema::table('cand_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('partneroffice_id')->nullable()->after('cand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cand_statuses', function (Blueprint $table) {
            $table->dropColumn('partneroffice_id');
        });
    }
};
