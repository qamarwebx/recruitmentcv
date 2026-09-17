<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');

            $table->decimal('monthly_salary', 12, 2)->default(0);
            $table->decimal('daily_salary', 12, 2)->default(0);
            $table->unsignedTinyInteger('days_in_month')->default(0);

            $table->unsignedSmallInteger('present_days')->default(0);
            $table->unsignedSmallInteger('absent_days')->default(0);
            $table->unsignedSmallInteger('half_days')->default(0);
            $table->unsignedSmallInteger('qualifying_late_count')->default(0);
            $table->unsignedSmallInteger('late_count')->default(0);
            $table->unsignedSmallInteger('leave_days')->default(0);
            $table->unsignedSmallInteger('holiday_days')->default(0);

            $table->decimal('late_deduction', 12, 2)->default(0);
            $table->decimal('absent_deduction', 12, 2)->default(0);
            $table->decimal('half_day_deduction', 12, 2)->default(0);
            $table->decimal('sunday_deduction', 12, 2)->default(0);
            $table->decimal('total_deduction', 12, 2)->default(0);
            $table->decimal('net_payable', 12, 2)->default(0);

            $table->enum('status', ['draft', 'locked'])->default('draft');
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->dateTime('generated_at')->nullable();
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->dateTime('locked_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['admin_id', 'month', 'year']);
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('locked_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
};
