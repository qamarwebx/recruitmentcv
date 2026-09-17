<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_save_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->json('filter_data');
            $table->timestamps();

            $table->unique('admin_id');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_save_filters');
    }
};
