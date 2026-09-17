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
        Schema::create('metawhatsapplogs', function (Blueprint $table) {
            $table->id();
            $table->string('message_for');
            $table->text('message_for_data');
            $table->string('message_type');
            $table->string('template_name');
            $table->string('message_status');
            $table->text('message_text');
            $table->text('message_data_error');
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
        Schema::dropIfExists('metawhatsapplogs');
    }
};
