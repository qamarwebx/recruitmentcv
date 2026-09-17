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
        Schema::create('metawhatsappcampaigns', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name')->nullable();
            $table->string('meta_campaign_name')->nullable();
            $table->string('audience')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('associate_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->text('msg_body')->nullable();
            $table->string('message_status');
            $table->string('message_text');
            $table->string('error_data_field')->nullable();
            $table->text('field_var');
            $table->text('assign_var');
            $table->unsignedBigInteger('care_off_id')->nullable();
            $table->unsignedBigInteger('admin_id');
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
        Schema::dropIfExists('metawhatsappcampaigns');
    }
};
