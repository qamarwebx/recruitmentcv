<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fund_advance_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fund_advance_transaction_id')->nullable();
            $table->unsignedBigInteger('fund_advance_settlement_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('module')->nullable();
            $table->json('activity')->nullable();
            $table->timestamp('created_at', 6)->nullable();
            $table->timestamp('updated_at', 6)->nullable();

            $table->index('fund_advance_transaction_id');
            $table->index('fund_advance_settlement_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fund_advance_activity_logs');
    }
};
