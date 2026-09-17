<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            // Single flag gating the internal chat module (view + send + file
            // share together), matching this table's one-flag-per-module pattern.
            $table->boolean('chat')->default(false)->after('attendance');
        });
    }

    public function down()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn('chat');
        });
    }
};
