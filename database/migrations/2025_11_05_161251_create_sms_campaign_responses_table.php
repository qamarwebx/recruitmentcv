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
        Schema::create('sms_campaign_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('message_status');
            $table->text('message_text')->nullable();
            $table->text('error_data_field')->nullable();
            $table->string('name')->nullable();
            $table->string('mobile_no')->nullable();
            $table->integer('lead_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->integer('associate_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('contactp_id')->nullable();
            $table->integer('allcontact_id')->nullable();
            $table->integer('sms_campaign_id')->nullable();
            $table->text('main_response')->nullable();
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
        Schema::dropIfExists('sms_campaign_responses');
    }
};

