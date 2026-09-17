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
            $table->boolean('access_setting')->default(false);
            $table->text('access_allowed_ip')->nullable();
            $table->boolean('access_allowed_ip_revoke')->default(false);
            $table->unsignedBigInteger('access_allowed_ip_approved_by')->nullable();
            $table->boolean('access_allowed_ip_delete')->default(false);
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
                'access_setting',
                'access_allowed_ip',
                'access_allowed_ip_revoke',
                'access_allowed_ip_approved_by',
                'access_allowed_ip_delete'
            ]);
        });
    }
};
