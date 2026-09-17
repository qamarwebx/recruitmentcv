<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->boolean('branch')->default(false);
            $table->boolean('add_branch')->default(false);
            $table->boolean('edit_branch')->default(false);
            $table->boolean('view_branch')->default(false);
            $table->boolean('delete_branch')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn(['branch', 'add_branch', 'edit_branch', 'view_branch', 'delete_branch']);
        });
    }
};
