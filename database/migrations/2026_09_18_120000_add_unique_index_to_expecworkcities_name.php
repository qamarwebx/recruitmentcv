<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backs Expecworkcity::findOrCreateByName()'s concurrency safety (Partner
 * Employer "City of Work" -> Other): the `name` column's collation is
 * already utf8mb4_unicode_ci (case-insensitive), so a plain unique index
 * naturally rejects "Dubai"/"dubai"/"DUBAI" as duplicates too - the
 * application layer trims before every insert, so no separate
 * generated/normalized column is needed to also catch whitespace
 * variants. Verified zero existing case/whitespace-variant duplicate
 * rows before writing this migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expecworkcities', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::table('expecworkcities', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
