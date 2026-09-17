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
        Schema::table('todos', function (Blueprint $table) {
            $table->string('recurring_type')->nullable()->after('reminder_cycle');
            $table->time('recurring_time')->nullable()->after('recurring_type');
            $table->string('recurring_weekdays')->nullable()->after('recurring_time');
            $table->tinyInteger('recurring_month_day')->nullable()->after('recurring_weekdays');
            $table->string('recurring_year_month_day')->nullable()->after('recurring_month_day');
            $table->string('custom_start_date')->nullable()->after('recurring_year_month_day');
            $table->string('custom_end_date')->nullable()->after('custom_start_date');
            $table->string('custom_time')->nullable()->after('custom_end_date');
            $table->string('reminder_before')->nullable()->after('custom_end_date')
            ->comment('Minutes before task to trigger reminder');
            $table->string('reminder_at')->nullable()->after('reminder_before')->comment('Exact datetime to send reminder');

        });
    }

    /**
     * Reverse the migrations.
     *      
     * @return void
     */
    public function down()
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn([
                'recurring_type', 
                'recurring_time',
                'recurring_weekdays',
                'recurring_month_day',
                'recurring_year_month_day',
                'custom_start_date', 
                'custom_end_date', 
                'custom_time', 
                'reminder_before',
                'reminder_at'
            ]);
        });
    }
};
