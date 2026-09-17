<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frontendwebsiteconfigs', function (Blueprint $table) {
            $table->text('bottom_contact_us_addr_arabic')
                  ->nullable()
                  ->after('bottom_contact_us_addr');
        });
    }

    public function down(): void
    {
        Schema::table('frontendwebsiteconfigs', function (Blueprint $table) {
            $table->dropColumn('bottom_contact_us_addr_arabic');
        });
    }
};
