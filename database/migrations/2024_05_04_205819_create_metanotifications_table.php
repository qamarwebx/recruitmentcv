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
        Schema::create('metanotifications', function (Blueprint $table) {
            $table->id();
            $table->string('template_name');
            $table->string('meta_template_name');
            $table->string('meta_field_name');
            $table->string('assign_var_name');
            $table->text('meta_message_body');
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('staff_id');
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
        Schema::dropIfExists('metanotifications');
    }
};
