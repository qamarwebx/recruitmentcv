<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configurable Payroll Deduction Calculation - the per-occurrence
     * deduction amount (previously always `late_deduction_percent` of the
     * daily salary) can now be computed one of three ways. `late_deduction_percent`
     * is kept (not dropped) and reused as the percentage value when
     * `deduction_calculation_type` is "percentage" - no data loss, no second
     * config system.
     */
    public function up()
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            $table->string('deduction_calculation_type', 20)->default('fixed_slab')->after('late_deduction_percent');
            $table->decimal('deduction_slab_amount', 12, 2)->default(5000)->after('deduction_calculation_type');
            $table->decimal('deduction_per_slab', 12, 2)->default(50)->after('deduction_slab_amount');
            $table->string('deduction_rounding_method', 20)->default('round_down')->after('deduction_per_slab');
        });
    }

    public function down()
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            $table->dropColumn([
                'deduction_calculation_type',
                'deduction_slab_amount',
                'deduction_per_slab',
                'deduction_rounding_method',
            ]);
        });
    }
};
