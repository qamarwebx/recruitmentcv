<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            // Deal Pipeline Permissions
            $table->boolean('deal_pipeline')->default(0)->after('bulk_todo_delete');
            $table->boolean('deal_add')->default(0)->after('deal_pipeline');
            $table->boolean('deal_view')->default(0)->after('deal_add');
            $table->boolean('deal_edit')->default(0)->after('deal_view');
            $table->boolean('deal_delete')->default(0)->after('deal_edit');
            $table->boolean('deal_update_stage')->default(0)->after('deal_delete');
            $table->boolean('deal_update_recruit_status')->default(0)->after('deal_update_stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn([
                'deal_pipeline',
                'deal_add',
                'deal_view',
                'deal_edit',
                'deal_delete',
                'deal_update_stage',
                'deal_update_recruit_status',
            ]);
        });
    }
};
