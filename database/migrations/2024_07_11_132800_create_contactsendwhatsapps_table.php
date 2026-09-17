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
        Schema::create('contactsendwhatsapps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contactp_id');
            $table->string('for_whatsapp');
            $table->unsignedBigInteger('metatemplate_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->text('msg_body_temp')->nullable();
            $table->text('msg_body_temp_ar')->nullable();
            $table->string('template_file')->nullable();
            $table->string('meta_whatsapp_api')->nullable();
            $table->string('whatsapp_api')->nullable();
            $table->string('contact_type')->nullable();
            $table->string('campaign_type')->nullable();
            $table->dateTime('date_time')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('contactsendwhatsapps');
    }
};
