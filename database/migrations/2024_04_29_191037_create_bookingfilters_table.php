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
        Schema::create('bookingfilters', function (Blueprint $table) {
            $table->id();
            $table->boolean('partner_filter')->default(false);
            $table->boolean('customer_filter')->default(false);
            $table->boolean('orderno_filter')->default(false);
            $table->boolean('profession_filter')->default(false);
            $table->boolean('orderstatus_filter')->default(false);
            $table->boolean('paymentstatus_filter')->default(false);
            $table->boolean('city_filter')->default(false);
            $table->boolean('bookingdate_filter')->default(false);
            $table->unsignedBigInteger('staff_id');
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
        Schema::dropIfExists('bookingfilters');
    }
};
