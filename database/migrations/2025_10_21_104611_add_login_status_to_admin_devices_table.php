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
        Schema::table('admin_devices', function (Blueprint $table) {
            $table->enum('login_status', ['Active', 'Inactive'])->default('Inactive')->after('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_devices', function (Blueprint $table) {
            $table->dropColumn('login_status');
        });
    }
};
