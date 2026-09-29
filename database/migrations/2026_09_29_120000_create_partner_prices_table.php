<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Partner Portal -> Settings -> Price Update: each partner's own Service
 * Price + Departure Days per Experience Type + Profession, shown on candidate
 * detail pages in place of the global customercosts default. Same column
 * names as customercosts (exp_type / proff_id / cost / days). Purely
 * additive - a new table, nothing existing is touched.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('partner_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id');
            // candidates.gulfexperience code: 1 = Indian Experience, 2 = Ex-Abroad.
            $table->unsignedTinyInteger('exp_type');
            // professions.id (candidates.jobtype_id).
            $table->unsignedBigInteger('proff_id');
            $table->decimal('cost', 10, 2);
            $table->unsignedSmallInteger('days');
            $table->timestamps();

            // One price per combination per partner (also covers partner_id lookups).
            $table->unique(['partner_id', 'exp_type', 'proff_id'], 'partner_prices_partner_exp_proff_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_prices');
    }
};
