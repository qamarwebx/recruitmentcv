<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_admin_save_filters', function (Blueprint $table) {
            $table->id();

            // Each admin has only one saved filter for leads
            $table->unsignedBigInteger('admin_id')->unique();

            // Lead filter fields
            $table->string('by_assignee')->nullable();          // selected assignees (comma-separated)
            $table->string('by_lead_date')->nullable();         // lead date range
            $table->string('by_is_qualified')->nullable();      // qualified / not qualified
            $table->string('by_driving_license')->nullable();   // selected driving licenses
            $table->string('by_job_title')->nullable();         // selected job titles
            $table->string('by_expected_days')->nullable();     // expected days
            $table->string('by_expected_country')->nullable();  // expected countries

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_admin_save_filters');
    }
};
