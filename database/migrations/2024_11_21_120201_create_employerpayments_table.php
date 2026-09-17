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
        Schema::create('employerpayments', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id')->nullable();
            $table->integer('bookingpayment_id')->nullable();
            $table->integer('empplus_id')->nullable();
            $table->integer('emp_id')->nullable();
            $table->integer('partneroffice_id')->nullable();
            $table->integer('cand_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('payment_status')->nullable();
            $table->integer('admin_id');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('employerpayments');
    }
};
