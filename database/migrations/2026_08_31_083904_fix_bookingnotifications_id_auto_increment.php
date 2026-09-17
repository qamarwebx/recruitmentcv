<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The live `bookingnotifications` table's `id` column is a bigint unsigned
 * PRIMARY KEY but is missing AUTO_INCREMENT (the migration that created it,
 * 2023_05_09_192547_create_bookingnotifications_table.php, uses $table->id()
 * which should set it - the live table just doesn't match, most likely from
 * an import/restore that dropped the attribute). Every insert that doesn't
 * supply an explicit id fails with "Field 'id' doesn't have a default
 * value" (SQLSTATE 1364) - this is what breaks BookingController::sendOTP()
 * on the admin "Confirm Booking" flow. Table has 0 rows, so this is a
 * pure schema fix with no data migration needed.
 */
return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE bookingnotifications MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down()
    {
        DB::statement('ALTER TABLE bookingnotifications MODIFY id BIGINT UNSIGNED NOT NULL');
    }
};
