<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fund_advance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no')->unique();
            $table->date('transaction_date');

            // transaction_type: Fund | Advance | Loan | Receivable | Payable
            $table->string('transaction_type');
            // transaction_nature: Given | Received | Adjustment (Recovery/Settlement are
            // settlement-table events, never stored here - see fund_advance_settlements)
            $table->string('transaction_nature');

            // party_type: partner | contact | employee | other
            $table->string('party_type');
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_name')->nullable();

            $table->decimal('amount', 12, 2);
            $table->string('payment_mode');
            $table->string('reference_no')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();

            // status: Active | Cancelled - the only durable state. Pending/Partially
            // Settled/Settled are always derived live from amount vs. active settlements.
            $table->string('status')->default('Active');
            $table->text('cancel_reason')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('admins')->onDelete('set null');

            $table->index('transaction_type');
            $table->index('transaction_nature');
            $table->index(['party_type', 'party_id']);
            $table->index('status');
            $table->index('transaction_date');
            $table->index('created_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fund_advance_transactions');
    }
};
