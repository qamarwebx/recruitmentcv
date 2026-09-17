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
            $table->boolean('today_reminder_task')->default(0)->after('updated_date_range');
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
            $table->dropColumn('today_reminder_task');
        });
    }
};
