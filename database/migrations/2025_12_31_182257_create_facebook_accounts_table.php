<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_accounts', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('account_name'); // Internal name (eg: Qamr Saudi Ads)
            $table->string('business_manager_id')->nullable();

            // Meta Credentials
            $table->string('pixel_id');
            $table->text('capi_access_token');

            // Optional (for debugging)
            $table->string('test_event_code')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Tracking
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_accounts');
    }
};
