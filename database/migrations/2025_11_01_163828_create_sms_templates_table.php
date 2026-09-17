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
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sms_api_id')->nullable();
            $table->string('template_name');
            $table->string('template_type', 120)->nullable();
            $table->string('template_used_for', 120)->nullable();
            $table->string('field_variable')->nullable();
            $table->string('assign_variable')->nullable();
            $table->string('sms_file')->nullable();
            $table->text('sms_message')->nullable();
            $table->text('msg_sms_ar')->nullable();
            $table->boolean('public')->nullable()->default(false);
            $table->unsignedBigInteger('admin_id');
            $table->boolean('status')->default(true);
            $table->text('sms_field_var')->nullable();
            $table->text('sms_assign_ar')->nullable();
            $table->string('template_for', 150)->nullable();
            $table->string('sms_url_type', 120)->nullable();
            $table->string('static_url', 160)->nullable();
            $table->string('document_name', 150)->nullable();
            $table->integer('careoff_id')->nullable();
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
        Schema::dropIfExists('sms_templates');
    }
};
