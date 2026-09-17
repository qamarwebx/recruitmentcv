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
            $table->boolean('dynamic')->default(false);
            $table->boolean('view_dynamic')->default(false);
            $table->boolean('delete_dynamic')->default(false);
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
            $table->dropColumn('dynamic');
            $table->dropColumn('view_dynamic');
            $table->dropColumn('delete_dynamic');
        });
    }
};
