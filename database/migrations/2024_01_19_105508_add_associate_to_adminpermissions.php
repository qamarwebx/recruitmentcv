n<?php

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
            $table->boolean('associate')->default(false);
            $table->boolean('add_associate')->default(false);
            $table->boolean('edit_associate')->default(false);
            $table->boolean('view_associate')->default(false);
            $table->boolean('delete_associate')->default(false);
            $table->boolean('publish_associate')->default(false);
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
            $table->dropColumn('associate');
            $table->dropColumn('add_associate');
            $table->dropColumn('edit_associate');
            $table->dropColumn('view_associate');
            $table->dropColumn('delete_associate');
            $table->dropColumn('publish_associate');
        });
    }
};
