<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fund_advance_transaction_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->unique();
            $table->string('transaction_type')->nullable();
            $table->string('transaction_nature')->nullable();
            $table->string('party_type')->nullable();
            $table->string('status')->nullable();
            $table->string('payment_mode')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('date_range')->nullable();
            $table->string('amount_min')->nullable();
            $table->string('amount_max')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fund_advance_transaction_filters');
    }
};
