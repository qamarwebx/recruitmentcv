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
        Schema::create('contactpfilters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->boolean('country_filter')->default(false);
            $table->boolean('city_filter')->default(false);
            $table->boolean('group_name_filter')->default(false);
            $table->boolean('life_cycle_status_filter')->default(false);
            $table->boolean('lead_stage_filter')->default(false);
            $table->boolean('business_type_filter')->default(false);
            $table->boolean('created_date_filter')->default(false);
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
        Schema::dropIfExists('contactpfilters');
    }
};
