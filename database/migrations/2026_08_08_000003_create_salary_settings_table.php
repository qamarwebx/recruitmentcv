<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->time('office_start_time')->default('10:00:00');
            $table->time('qualifying_late_end_time')->default('10:40:00');
            $table->time('late_window_end_time')->default('12:00:00');
            $table->time('half_day_after_time')->default('12:00:00');
            $table->unsignedInteger('free_late_count')->default(4);
            $table->unsignedInteger('free_late_max_minutes')->default(40);
            $table->decimal('late_deduction_percent', 5, 2)->default(1.00);
            $table->boolean('retroactive_late_deduction')->default(true);
            $table->boolean('sat_absent_sunday_deduction')->default(true);
            $table->boolean('mon_absent_prev_sunday_deduction')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('admins')->onDelete('set null');
        });

        // Single-row configuration table - seed the default row used by SalarySetting::current().
        DB::table('salary_settings')->insert([
            'office_start_time' => '10:00:00',
            'qualifying_late_end_time' => '10:40:00',
            'late_window_end_time' => '12:00:00',
            'half_day_after_time' => '12:00:00',
            'free_late_count' => 4,
            'free_late_max_minutes' => 40,
            'late_deduction_percent' => 1.00,
            'retroactive_late_deduction' => true,
            'sat_absent_sunday_deduction' => true,
            'mon_absent_prev_sunday_deduction' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('salary_settings');
    }
};
