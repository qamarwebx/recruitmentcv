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
        Schema::create('scheduled_send_email_automation', function (Blueprint $table) {
            $table->id();
            $table->string('template_table_name')->nullable();
            $table->string('template_for')->nullable();
            $table->unsignedBigInteger('autoemailnotifications_id')->nullable();
            $table->unsignedBigInteger('emailtemp_id')->nullable();
            $table->string('send_user_to')->nullable();
            $table->string('calculated_time')->nullable();
            $table->string('trigger_template_time')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('scheduled_send_email_automation');
    }
};
