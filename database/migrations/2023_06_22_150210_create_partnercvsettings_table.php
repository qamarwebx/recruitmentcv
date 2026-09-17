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
        Schema::create('partnercvsettings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id');
            $table->string('label_name');
            $table->string('db_field_name');
            $table->string('x_axis')->nullable();
            $table->string('y_axis')->nullable();
            $table->string('font_name')->nullable();
            $table->string('font_size')->nullable();
            $table->string('font_color')->nullable();
            $table->string('font_family')->nullable();
            $table->string('width')->nullable();
            $table->string('filename')->nullable();
            $table->string('add_y_axis')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
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
        Schema::dropIfExists('partnercvsettings');
    }
};
