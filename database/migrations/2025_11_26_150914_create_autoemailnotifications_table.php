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
        Schema::create('autoemailnotifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('emailtemp_id')->nullable();
            $table->string('template_name')->nullable();
            $table->string('trigger_template_type')->nullable();
            $table->string('trigger_template_time')->nullable();
            $table->string('trigger_template_time_type')->nullable();
            $table->boolean('status')->default(false);
            $table->integer('staff_id');
            $table->string('template_for', 120)->nullable();
            $table->string('template_table_name')->nullable();
            $table->text('field_not_completed')->nullable();
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
        Schema::dropIfExists('autoemailnotifications');
    }
};
