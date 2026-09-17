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
        Schema::table('lead_admin_save_filters', function (Blueprint $table) {
            $table->text('by_call_not_connected_type')
                ->nullable()
                ->after('by_is_qualified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_admin_save_filters', function (Blueprint $table) {
            $table->dropColumn('by_call_not_connected_type');
        });
    }
};
