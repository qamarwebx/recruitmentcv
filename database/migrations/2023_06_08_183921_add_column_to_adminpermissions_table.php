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
            $table->boolean('personalise_class')->default(false);
            $table->boolean('template')->default(false);
            $table->boolean('add_personalise_class')->default(false);
            $table->boolean('edit_personalise_class')->default(false);
            $table->boolean('delete_personalise_class')->default(false);
            $table->boolean('view_personalise_class')->default(false);
            $table->boolean('add_template')->default(false);
            $table->boolean('edit_template')->default(false);
            $table->boolean('delete_template')->default(false);
            $table->boolean('view_template')->default(false);

            
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
            $table->dropColumn('personalise_class');
            $table->dropColumn('template');
            $table->dropColumn('add_personalise_class');
            $table->dropColumn('edit_personalise_class');
            $table->dropColumn('delete_personalise_class');
            $table->dropColumn('view_personalise_class');
            $table->dropColumn('add_template');
            $table->dropColumn('edit_template');
            $table->dropColumn('delete_template');
            $table->dropColumn('view_template');
        });
    }
};
