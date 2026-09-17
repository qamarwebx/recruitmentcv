<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fund_advance_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_no')->unique();
            $table->unsignedBigInteger('transaction_id');

            // settlement_type: settlement | adjustment
            $table->string('settlement_type')->default('settlement');

            $table->date('settlement_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode');
            $table->string('reference_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('attachment')->nullable();

            // status: Active | Reversed - reversal, not delete, preserves audit history
            $table->string('status')->default('Active');
            $table->text('reversed_reason')->nullable();
            $table->unsignedBigInteger('reversed_by')->nullable();
            $table->timestamp('reversed_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('fund_advance_transactions')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('reversed_by')->references('id')->on('admins')->onDelete('set null');

            $table->index('transaction_id');
            $table->index('status');
            $table->index('settlement_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fund_advance_settlements');
    }
};
