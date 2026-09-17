<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            // Nullable, set only while linked to a DRAFT payroll for the same
            // admin/month/year - never auto-linked to a locked/paid payroll
            // (see AdvancePaymentService), and reset to null automatically
            // (onDelete('set null')) if that payroll is later deleted, so a
            // regenerated payroll picks the advance back up.
            $table->unsignedBigInteger('payroll_id')->nullable();

            // Denormalized from advance_date for fast, index-friendly
            // admin+month+year matching against payrolls - avoids a
            // whereMonth()/whereYear() scan for every payroll generated.
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');

            $table->dateTime('advance_date');
            $table->decimal('amount', 12, 2);
            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index(['admin_id', 'month', 'year'], 'advance_payments_admin_month_year_index');
            $table->index('payroll_id');

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('advance_payments');
    }
};
