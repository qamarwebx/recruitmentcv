<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {

            $table->boolean('meta_automation')
                  ->default(false)
                  ->after('id');

            $table->boolean('add_meta_automation')
                  ->default(false)
                  ->after('meta_automation');

            $table->boolean('view_meta_automation')
                  ->default(false)
                  ->after('add_meta_automation');

            $table->boolean('edit_meta_automation')
                  ->default(false)
                  ->after('view_meta_automation');

            $table->boolean('delete_meta_automation')
                  ->default(false)
                  ->after('edit_meta_automation');
        });
    }

    public function down(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn([
                'meta_automation',
                'add_meta_automation',
                'view_meta_automation',
                'edit_meta_automation',
                'delete_meta_automation',
            ]);
        });
    }
};
