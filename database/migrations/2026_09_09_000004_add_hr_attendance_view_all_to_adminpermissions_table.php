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
            // Attendance "View All" - lets a staff member see every staff
            // member's attendance records (same scope as Super Admin / a
            // Manager's team), independent of the hr_attendance module toggle.
            $table->boolean('hr_attendance_view_all')->default(false)->after('hr_attendance');
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
            $table->dropColumn('hr_attendance_view_all');
        });
    }
};
