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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cand_id');
            $table->unsignedBigInteger('partner_id');
            $table->text('reference_no')->nullable();
            $table->string('amount')->nullable();
            $table->date('booking_date');
            $table->boolean('booking_status')->default(false);
            $table->boolean('visa_status')->default(false);
            $table->boolean('payment_status')->default(false);
            $table->unsignedBigInteger('payconfirm_admin_id')->nullable();
            $table->unsignedBigInteger('payconfirm_partner_id')->nullable();
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
        Schema::dropIfExists('bookings');
    }
};
