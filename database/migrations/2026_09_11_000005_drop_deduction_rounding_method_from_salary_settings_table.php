<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `deduction_rounding_method` was added in the previous migration for
     * Payroll Deduction Calculation, but only one rounding behavior was ever
     * implemented (complete slabs only, via floor()) and it was never
     * actually read by PayrollDeductionService - so per request it's removed
     * completely rather than kept as a dead/unused setting.
     */
    public function up()
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            $table->dropColumn('deduction_rounding_method');
        });
    }

    public function down()
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            $table->string('deduction_rounding_method', 20)->default('round_down')->after('deduction_per_slab');
        });
    }
};
