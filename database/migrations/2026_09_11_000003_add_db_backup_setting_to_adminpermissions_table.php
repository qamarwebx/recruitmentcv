<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->boolean('db_backup_setting')->default(false);
        });
    }

    public function down()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn(['db_backup_setting']);
        });
    }
};
