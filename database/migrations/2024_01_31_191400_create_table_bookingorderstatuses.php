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
        Schema::create('bookingorderstatuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cand_id')->nullable();   
            $table->string('ord_status_id')->nullable();
            $table->unsignedBigInteger('payconfirm_admin_id')->nullable();
            $table->unsignedBigInteger('payconfirm_partner_id')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('bookingorderstatuses');
    }
};
