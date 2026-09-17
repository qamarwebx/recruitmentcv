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
        Schema::table('candidates', function (Blueprint $table) {
            $table->unsignedBigInteger('nation_id')->after('status')->nullable();
            $table->unsignedBigInteger('region_id')->after('nation_id')->nullable();
            $table->unsignedBigInteger('candcity_id')->after('region_id')->nullable();
            $table->unsignedBigInteger('plb_id')->after('candcity_id')->nullable();
            $table->unsignedBigInteger('expwp_id')->after('plb_id')->nullable();
            $table->string('google_map')->after('expwp_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('nation_id');
            $table->dropColumn('region_id');
            $table->dropColumn('candcity_id');
            $table->dropColumn('plb_id');
            $table->dropColumn('expwp_id');
            $table->dropColumn('google_map');
        });
    }
};
