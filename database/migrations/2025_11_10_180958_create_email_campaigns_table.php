<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('campaign_name')->nullable();
            $table->string('audience')->nullable();

            // Foreign keys
            $table->unsignedBigInteger('smtp_id')->nullable();
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();

            // Email content
            $table->string('email_subject')->nullable();
            $table->longText('email_body')->nullable();
            $table->string('attachment')->nullable(); // optional

            // Status
            $table->string('email_status')->default('Pending');
            $table->text('email_response')->nullable();

            // Schedule
            $table->enum('schedule_type', ['Now', 'Scheduled'])->default('Now');
            $table->dateTime('schedule_datetime')->nullable();

            // Raw data / misc
            $table->longText('raw_request_data')->nullable();
            $table->text('careoff_id')->nullable();
            $table->text('group_id')->nullable();
            $table->text('field_var')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
