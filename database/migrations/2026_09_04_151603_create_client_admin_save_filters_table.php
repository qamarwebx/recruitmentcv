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
        Schema::create('client_admin_save_filters', function (Blueprint $table) {
            $table->id();

            // Each admin has only one saved filter for the Client module
            $table->unsignedBigInteger('admin_id')->unique();

            $table->string('by_country')->nullable();
            $table->string('by_city')->nullable();
            $table->string('by_created_date')->nullable();

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
        Schema::dropIfExists('client_admin_save_filters');
    }
};
