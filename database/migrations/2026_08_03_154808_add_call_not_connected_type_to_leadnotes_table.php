<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leadnotes', function (Blueprint $table) {
            $table->string('call_not_connected_type')
                  ->nullable()
                  ->after('is_qualified');
        });
    }

    public function down(): void
    {
        Schema::table('leadnotes', function (Blueprint $table) {
            $table->dropColumn('call_not_connected_type');
        });
    }
};