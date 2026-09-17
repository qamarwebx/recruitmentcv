<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('meta_ads_store_records', function (Blueprint $table) {

            $table->id();

            // =================================================
            // LEAD / USER DETAILS
            // =================================================
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // =================================================
            // EVENT DETAILS
            // =================================================
            $table->string('event')->nullable();      // lead / purchase
            $table->string('page')->nullable();       // thank-you-saudi
            $table->string('source')->nullable();     // qamrjob
            $table->string('platform')->nullable();   // website / whatsapp

            // =================================================
            // LOCATION (GLOBAL READY)
            // =================================================
            $table->string('country')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('city')->nullable();

            // =================================================
            // META TRACKING
            // =================================================
            $table->string('event_id')->nullable();    // deduplication
            $table->string('pixel_id')->nullable();
            $table->string('fbtrace_id')->nullable();

            // =================================================
            // SYSTEM DETAILS
            // =================================================
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_ads_store_records');
    }
};
