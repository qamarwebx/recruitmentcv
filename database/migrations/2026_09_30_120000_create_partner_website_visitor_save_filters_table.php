<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Partner Portal -> Website Visitor: each partner's saved filter (Save /
 * Reset Filter), the partner-side counterpart of the CRM's
 * website_visitor_admin_save_filters (same by_* fields, minus Website /
 * Partner, which are fixed to the logged-in partner). One row per partner.
 * Purely additive - visitor data (website_visitors) is not touched.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('partner_website_visitor_save_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id')->unique();
            $table->string('by_custom_date', 30)->nullable();
            $table->string('by_visitor', 20)->nullable();
            $table->string('by_device', 20)->nullable();
            $table->string('by_browser', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_website_visitor_save_filters');
    }
};
