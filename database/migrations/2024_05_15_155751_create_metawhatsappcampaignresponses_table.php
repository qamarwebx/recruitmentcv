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
        Schema::create('metawhatsappcampaignresponses', function (Blueprint $table) {
            $table->id();
            $table->string('message_status');
            $table->string('message_text');
            $table->text('error_data_field')->nullable();
            $table->string('name')->nullable();
            $table->string('mobile_no')->nullable();
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
        Schema::dropIfExists('metawhatsappcampaignresponses');
    }
};
