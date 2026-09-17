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
        Schema::create('partnerfiliterlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->boolean('country_filter')->default(false);
            $table->boolean('city_filter')->default(false);
            $table->boolean('status_filter')->default(false);
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
        Schema::dropIfExists('partnerfiliterlists');
    }
};
