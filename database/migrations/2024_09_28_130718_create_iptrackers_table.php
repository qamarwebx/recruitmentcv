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
        Schema::create('iptrackers', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);  // IP address
            $table->timestamp('visit_time')->useCurrent();  // Visit time
            $table->string('user_agent', 255)->nullable();  // User agent
            $table->string('referrer', 255)->nullable();  // Referrer
            $table->string('country', 100)->nullable();  // Country
            $table->string('region', 100)->nullable();  // Region
            $table->string('city', 100)->nullable();  // City
            $table->decimal('latitude', 9, 6)->nullable();  // Latitude
            $table->decimal('longitude', 9, 6)->nullable();  // Longitude
            $table->string('session_id', 255)->nullable();  // Session ID
            $table->string('page_url', 255)->nullable();  // Page URL
            $table->integer('response_status')->nullable();  // Response status
            $table->integer('duration')->nullable();  // Duration
            $table->string('referral_source', 255)->nullable();  // Referral source
            $table->boolean('cookies_enabled')->nullable();  // Cookies enabled
            $table->string('traffic_source', 255)->nullable();  // Traffic source
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iptrackers');
    }
};
