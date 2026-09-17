<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backs the RecruitmentCV partner-subdomain branding system
 * (*.recruitmentcv.com). Deliberately separate from the `domains` table
 * (2026_04_16_155749_create_domains_table.php) which backs the CRM's
 * existing "Domain"/"Website" tabs and the add-domain.sh symlink-based
 * custom-domain feature - that system is infra-level (one arbitrary
 * domain -> one symlinked docroot) and must not be touched or reused;
 * this one is app-level (Host header resolved by middleware against
 * this table on every request to the recruitmentcv.com install).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_website_domains', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('partner_id');

            $table->string('domain')->unique();

            $table->string('english_logo')->nullable();
            $table->string('arabic_logo')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('inactive');

            $table->timestamps();

            $table->index('partner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_website_domains');
    }
};
