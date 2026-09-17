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
        Schema::create('meta_lead_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lead_id')->nullable();
            $table->string('event_id')->nullable();

            $table->string('status')->comment('success | failed');
            $table->string('page')->nullable();

            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->text('message')->nullable(); // error or info
            $table->longText('trace')->nullable();

            $table->timestamps();

            $table->index('lead_id');
            $table->index('event_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_lead_logs');
    }
};
