<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_campaign_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('email_campaign_id')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('Pending');
            $table->text('response_message')->nullable();
            $table->text('error_data_field')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_campaign_responses');
    }
};
