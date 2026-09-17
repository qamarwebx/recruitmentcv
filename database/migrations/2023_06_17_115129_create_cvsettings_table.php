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
        Schema::create('cvsettings', function (Blueprint $table) {
            $table->id();
            $table->string('label_name');
            $table->string('db_field_name');
            $table->string('x_axis');
            $table->string('y_axis');
            $table->string('font_name')->nullable();
            $table->string('font_size')->nullable();
            $table->string('font_color')->nullable();
            $table->string('font_family')->nullable();
            $table->unsignedBigInteger('admin_id'); 
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
        Schema::dropIfExists('cvsettings');
    }
};
