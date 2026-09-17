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
        Schema::create('templatecampaigns', function (Blueprint $table) {
            $table->id();
            $table->string('template_name');
            $table->string('subject_name')->nullable();
            $table->text('msg_whatsapp')->nullable();
            $table->text('msg_email')->nullable();
            $table->text('msg_sms')->nullable();
            $table->string('file')->nullable();
            $table->boolean('public')->default(false);
            $table->boolean('status')->default(true);
            $table->unsignedInteger('staff_id');
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
        Schema::dropIfExists('templatecampaigns');
    }
};
