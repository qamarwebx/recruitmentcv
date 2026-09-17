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
        Schema::create('whatsappcampaignactivities', function (Blueprint $table) {
            $table->id();
            $table->string('audience')->comment('Contact+,Partner,Associate,Client')->nullable();
            $table->string('for_whatsapp')->comment('Normal Whatsapp','Meta Whatsapp')->nullable();
            $table->integer('contactp_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->integer('associate_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('campaignlist_id')->nullable();
            $table->datetime('senddate')->nullable();
            $table->text('message_response')->nullable();
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
        Schema::dropIfExists('whatsappcampaignactivities');
    }
};
