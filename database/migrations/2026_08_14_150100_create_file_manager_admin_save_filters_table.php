<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('file_manager_admin_save_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->unique();
            $table->string('by_type')->nullable();
            $table->string('by_category')->nullable();
            $table->string('by_owner')->nullable();
            $table->unsignedBigInteger('by_size_min')->nullable();
            $table->unsignedBigInteger('by_size_max')->nullable();
            $table->string('by_date_from')->nullable();
            $table->string('by_date_to')->nullable();
            $table->string('by_location')->default('current');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_manager_admin_save_filters');
    }
};
