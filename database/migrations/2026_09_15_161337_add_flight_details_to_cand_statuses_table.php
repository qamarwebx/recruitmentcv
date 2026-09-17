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
        Schema::table('cand_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('flight_from_city')->nullable()->after('waiting_for_flight_ticket');
            $table->unsignedBigInteger('flight_to_city')->nullable()->after('flight_from_city');
            $table->dateTime('flight_datetime')->nullable()->after('flight_to_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cand_statuses', function (Blueprint $table) {
            $table->dropColumn(['flight_from_city', 'flight_to_city', 'flight_datetime']);
        });
    }
};
