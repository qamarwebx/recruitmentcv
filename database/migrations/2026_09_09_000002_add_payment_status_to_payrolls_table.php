<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->enum('payment_status', ['Pending', 'Paid'])->default('Pending')->after('status');
            $table->string('payment_slip')->nullable()->after('payment_status');
            $table->unsignedBigInteger('paid_by')->nullable()->after('payment_slip');
            $table->dateTime('paid_at')->nullable()->after('paid_by');

            $table->foreign('paid_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['paid_by']);
            $table->dropColumn(['payment_status', 'payment_slip', 'paid_by', 'paid_at']);
        });
    }
};
