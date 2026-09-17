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
        Schema::table('adminpermissions', function (Blueprint $table) {
            // HR Management Module Access
            $table->boolean('hr_management')->default(false)->after('finance_payment_delete');
            $table->boolean('hr_dashboard')->default(false)->after('hr_management');
            $table->boolean('hr_attendance')->default(false)->after('hr_dashboard');
            $table->boolean('hr_payroll')->default(false)->after('hr_attendance');
            $table->boolean('hr_settings')->default(false)->after('hr_payroll');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn([
                'hr_management',
                'hr_dashboard',
                'hr_attendance',
                'hr_payroll',
                'hr_settings',
            ]);
        });
    }
};
