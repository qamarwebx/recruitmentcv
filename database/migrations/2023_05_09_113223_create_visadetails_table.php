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
        Schema::create('visadetails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cand_id')->nullable();
            $table->string('visa_no')->nullable();
            $table->string('id_no')->nullable();
            $table->unsignedBigInteger('proff_id')->nullable();
            $table->string('employer_name')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->unsignedBigInteger('wpcity_id')->nullable();
            $table->string('salary')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('visadetails');
    }
};
