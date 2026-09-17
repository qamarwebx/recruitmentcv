<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendance_save_filters', function (Blueprint $table) {
            $table->dropColumn('filter_name');
            $table->unique('admin_id');
        });
    }

    public function down()
    {
        Schema::table('attendance_save_filters', function (Blueprint $table) {
            $table->dropUnique(['admin_id']);
            $table->string('filter_name')->after('admin_id');
        });
    }
};
