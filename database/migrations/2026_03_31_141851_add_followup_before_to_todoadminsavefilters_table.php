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
        Schema::table('todoadminsavefilters', function (Blueprint $table) {
            $table->string('followup_before')->nullable()->after('updated_date_range');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todoadminsavefilters', function (Blueprint $table) {
            $table->dropColumn('followup_before');
        });
    }
};
