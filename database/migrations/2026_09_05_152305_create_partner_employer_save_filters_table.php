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
        Schema::create('partner_employer_save_filters', function (Blueprint $table) {
            $table->id();

            // Each partner has only one saved filter for the Employer Plus
            // listing - mirrors client_admin_save_filters' one-row-per-user
            // pattern rather than CRM's older Employerplusadminsavefilter.
            $table->unsignedBigInteger('partner_id')->unique();

            $table->string('status')->nullable();
            $table->string('profession')->nullable();
            $table->string('wpcity_id')->nullable();
            $table->string('businesstype')->nullable();
            $table->string('wakala_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('created_date')->nullable();

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
        Schema::dropIfExists('partner_employer_save_filters');
    }
};
