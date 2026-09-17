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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('partneroffice_id')->nullable();
            $table->integer('employer_id')->nullable();
            $table->string('invoice_amount')->nullable();
            $table->date('invoice_date')->nullable();
            $table->text('empcand_id')->nullable();
            $table->text('service_charge')->nullable();
            $table->text('terms_condtion')->nullable();
            $table->string('payment_status')->comment('Paid','Partially Paid','Unpaid');
            $table->integer('admin_id');
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
        Schema::dropIfExists('invoices');
    }
};
