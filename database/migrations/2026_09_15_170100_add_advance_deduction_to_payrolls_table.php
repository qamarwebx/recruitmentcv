<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Sum of Advance Payments currently linked to this payroll (see
            // AdvancePaymentService::recalcPayroll) - stored rather than
            // computed on read so Total Paid never needs an extra query per
            // row, and so a locked payroll's figure stays frozen even if
            // later advance activity for the same admin/month/year occurs
            // (which can no longer link to a locked row).
            $table->decimal('advance_deduction', 12, 2)->default(0)->after('extra_paid');
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('advance_deduction');
        });
    }
};
