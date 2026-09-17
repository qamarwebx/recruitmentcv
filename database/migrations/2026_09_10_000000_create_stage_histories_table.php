<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Polymorphic stage-history log for both Leads (is_qualified) and Deal
     * Pipeline (deal_stage_id). One shared table instead of two near-identical
     * ones. Labels are snapshotted at write time since deal stage names have
     * been renamed/removed before — historical reports must not be corrupted
     * by a later rename.
     */
    public function up()
    {
        Schema::create('stage_histories', function (Blueprint $table) {
            $table->id();
            $table->string('trackable_type', 40);
            $table->unsignedBigInteger('trackable_id');
            $table->string('from_value', 50)->nullable();
            $table->string('to_value', 50)->nullable();
            $table->string('from_label', 100)->nullable();
            $table->string('to_label', 100)->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['trackable_type', 'trackable_id'], 'stage_histories_trackable_idx');
            $table->index(['trackable_type', 'to_value', 'created_at'], 'stage_histories_to_idx');
            $table->index(['trackable_type', 'from_value', 'created_at'], 'stage_histories_from_idx');
            $table->index(['changed_by', 'created_at'], 'stage_histories_changed_by_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stage_histories');
    }
};
