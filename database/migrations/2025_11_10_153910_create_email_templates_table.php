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
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smtp_id')->nullable(); // link to smtp setup
            $table->string('template_name');
            $table->string('subject')->nullable();
            $table->string('template_for')->nullable(); // leads, employer, etc.
            $table->string('template_used_for')->nullable(); // campaign, auto_message
            $table->text('email_body')->nullable(); // main message body
            $table->string('language')->default('en');
            $table->string('attachment')->nullable(); // optional
            $table->boolean('public')->default(0);
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('careoff_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('email_templates');
    }
};
