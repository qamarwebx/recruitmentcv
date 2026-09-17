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
        Schema::create('associate_admin_save_filters', function (Blueprint $table) {
            $table->id();

            // Each admin has only one saved filter for the Associate module
            $table->unsignedBigInteger('admin_id')->unique();

            $table->string('by_country')->nullable();
            $table->string('by_city')->nullable();
            $table->string('by_region')->nullable();
            $table->string('by_created_by')->nullable();
            $table->string('by_careoff')->nullable();

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
        Schema::dropIfExists('associate_admin_save_filters');
    }
};
