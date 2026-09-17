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
        Schema::create('admin_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('device_id')->index();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_approved')->default(true); // existing devices default true
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->string('approved_by_device_type')->nullable();
            $table->string('approved_by_browser')->nullable();
            $table->string('approved_by_os')->nullable();
            $table->string('approved_by_ip')->nullable();
            $table->string('approved_by_location')->nullable();
            $table->text('comments')->nullable(); 
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('approved_by_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_devices');
    }
};
