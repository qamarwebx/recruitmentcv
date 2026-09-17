<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            // Fund & Advance Management Module Access
            $table->boolean('fund_advance')->default(false)->after('storage_usage_setting');
            $table->boolean('fund_advance_create')->default(false)->after('fund_advance');
            $table->boolean('fund_advance_edit')->default(false)->after('fund_advance_create');
            $table->boolean('fund_advance_delete')->default(false)->after('fund_advance_edit');
            $table->boolean('fund_advance_settlement_create')->default(false)->after('fund_advance_delete');
            $table->boolean('fund_advance_settlement_view')->default(false)->after('fund_advance_settlement_create');
            $table->boolean('fund_advance_adjustment_create')->default(false)->after('fund_advance_settlement_view');
            $table->boolean('fund_advance_ledger_view')->default(false)->after('fund_advance_adjustment_create');
            $table->boolean('fund_advance_reports_view')->default(false)->after('fund_advance_ledger_view');
            $table->boolean('fund_advance_export')->default(false)->after('fund_advance_reports_view');
        });
    }

    public function down()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn([
                'fund_advance',
                'fund_advance_create',
                'fund_advance_edit',
                'fund_advance_delete',
                'fund_advance_settlement_create',
                'fund_advance_settlement_view',
                'fund_advance_adjustment_create',
                'fund_advance_ledger_view',
                'fund_advance_reports_view',
                'fund_advance_export',
            ]);
        });
    }
};
