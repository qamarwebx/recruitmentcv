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
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_cancelled_by')->nullable()->after('booking_status');
            $table->timestamp('booking_cancelled_at')->nullable()->after('booking_cancelled_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('booking_cancelled_by');
            $table->dropColumn('booking_cancelled_at');
        });
    }
};
