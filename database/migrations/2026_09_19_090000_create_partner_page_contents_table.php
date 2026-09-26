<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Partner Website Config (Home/About/Contact/Privacy/Terms). One row per
 * partner+page; `content` is a JSON object keyed by locale ('en'/'ar'),
 * e.g. {"en": {"hero": {...}}, "ar": {"hero": {...}}} - each field inside
 * is an OVERRIDE only, never a full copy of the page. A partner who hasn't
 * set a given field (or has no row at all) falls back to the existing
 * frontendwebsiteconfigs-driven / hardcoded-translated default that
 * already renders today - see WorkerPageController's pageContent() and
 * the public Blade views' `?? __('locale...')` fallbacks.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('partner_page_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id');
            $table->string('page', 20);
            $table->json('content')->nullable();
            $table->timestamps();

            $table->unique(['partner_id', 'page']);
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_page_contents');
    }
};
