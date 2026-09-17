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
        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('campaign_name')->nullable();
            $table->string('sms_campaign_name')->nullable();
            $table->string('audience')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('associate_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->integer('contactp_id')->nullable();
            $table->text('msg_body')->nullable();
            $table->string('message_status')->nullable();
            $table->string('message_text')->nullable();
            $table->string('error_data_field')->nullable();
            $table->text('field_var')->nullable();
            $table->text('assign_var')->nullable();
            $table->unsignedBigInteger('care_off_id')->nullable();
            $table->unsignedBigInteger('admin_id');
            $table->text('sms_response_id')->nullable();
            $table->integer('sms_temp_id')->nullable();
            $table->text('careoff_id')->nullable();
            $table->integer('allcontact_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('sms_api_id');
            $table->string('country_code', 200)->nullable();
            $table->text('business_type_contact')->nullable();
            $table->string('subscribe', 100)->nullable();
            $table->text('group_id')->nullable();
            $table->text('contact_type')->nullable();
            $table->string('sch_type', 60)->nullable();
            $table->longText('raw_request_data')->nullable();
            $table->dateTime('date_and_time')->nullable();
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
        Schema::dropIfExists('sms_campaigns');
    }
};
