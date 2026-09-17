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
        Schema::create('contactreminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contactp_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('title');
            $table->text('desc');
            $table->string('reminder_type')->nullable();
            $table->date('due_date');
            $table->time('time');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('contactreminders');
    }
};
