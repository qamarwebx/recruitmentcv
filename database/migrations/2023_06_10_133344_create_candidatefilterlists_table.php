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
        Schema::create('candidatefilterlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->boolean('pass_type_filter')->default(false);
            $table->boolean('job_type_filter')->default(false);
            $table->boolean('create_by_filter')->default(false);
            $table->boolean('create_date_filter')->default(false);
            $table->boolean('religion_filter')->default(false);
            $table->boolean('region_filter')->default(false);
            $table->boolean('city_filter')->default(false);
            $table->boolean('experience_region_filter')->default(false);
            $table->boolean('medical_expiry_date_filter')->default(false);
            $table->boolean('candidate_status_filter')->default(false);
            $table->boolean('publish_status_filter')->default(false);
            $table->boolean('new_candidate_status_filter')->default(false);
            $table->boolean('ready_for_published_status_filter')->default(false);
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
        Schema::dropIfExists('candidatefilterlists');
    }
};
