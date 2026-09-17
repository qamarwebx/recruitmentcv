<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('partner_id')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Domain Details
            |--------------------------------------------------------------------------
            */

            $table->string('domain_name')->unique();

            /*
            |--------------------------------------------------------------------------
            | Company Branding
            |--------------------------------------------------------------------------
            */

            $table->string('company_name')->nullable();
            $table->string('company_name_ar')->nullable();

            $table->string('website_logo')->nullable();
            $table->string('website_logo_ar')->nullable();

            $table->text('company_address')->nullable();
            $table->text('company_address_ar')->nullable();

            $table->string('company_mobile')->nullable();
            $table->string('company_email')->nullable();

            /*
            |--------------------------------------------------------------------------
            | DNS / SSL Management
            |--------------------------------------------------------------------------
            */

            $table->boolean('dns_verified')->default(false);
            $table->boolean('ssl_verified')->default(false);

            $table->timestamp('dns_verified_at')->nullable();
            $table->timestamp('ssl_verified_at')->nullable();

            $table->string('server_ip')->nullable();
            $table->date('ssl_expiry_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Domain Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'pending',
                'active',
                'inactive',
                'suspended'
            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};