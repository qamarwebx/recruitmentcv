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
        Schema::create('employerpluses', function (Blueprint $table) {
            $table->id();
            $table->integer('visadetails_id')->nullable();
            $table->string('employer_name',150)->nullable();
            $table->string('employer_ar_name',150)->nullable();
            $table->string('visa_no',25)->nullable();
            $table->string('id_no',25)->nullable();
            $table->string('proff_id')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->integer('wpcity_id')->nullable();
            $table->string('salary')->nullable();
            $table->string('businesstype')->nullable();
            $table->string('visa_date')->nullable();
            $table->integer('admin_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('booking_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('cand_id')->nullable();
            $table->integer('partneroffice_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('mobile_no',50)->nullable();
            $table->integer('careoff_id')->nullable();
            $table->string('wakala_status')->nullable();
            $table->string('visa_received_date')->nullable();
            $table->string('openings',100)->nullable();
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
        Schema::dropIfExists('employerpluses');
    }
};
