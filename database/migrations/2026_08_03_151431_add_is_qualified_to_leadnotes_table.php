<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leadnotes', function (Blueprint $table) {
            $table->tinyInteger('is_qualified')
                  ->nullable()
                  ->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('leadnotes', function (Blueprint $table) {
            $table->dropColumn('is_qualified');
        });
    }
};