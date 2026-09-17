<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->boolean('file_manager')->default(false);
            $table->boolean('add_file_manager')->default(false);
            $table->boolean('upload_file_manager')->default(false);
            $table->boolean('download_file_manager')->default(false);
            $table->boolean('preview_file_manager')->default(false);
            $table->boolean('rename_file_manager')->default(false);
            $table->boolean('move_file_manager')->default(false);
            $table->boolean('delete_file_manager')->default(false);
            $table->boolean('manage_all_file_manager')->default(false);
            $table->boolean('file_manager_setting')->default(false);
        });
    }

    public function down()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn([
                'file_manager',
                'add_file_manager',
                'upload_file_manager',
                'download_file_manager',
                'preview_file_manager',
                'rename_file_manager',
                'move_file_manager',
                'delete_file_manager',
                'manage_all_file_manager',
                'file_manager_setting',
            ]);
        });
    }
};
