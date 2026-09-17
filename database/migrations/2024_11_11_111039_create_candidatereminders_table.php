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
        Schema::create('candidatereminders', function (Blueprint $table) {
            $table->id();
            $table->integer('candidate_id');
            $table->string('title')->nullable();
            $table->string('desc')->nullable();
            $table->integer('todolabel_id')->nullable();
            $table->date('due_date')->nullable();
            $table->time('time')->nullable();
            $table->integer('careoff_id')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('admin_id')->nullable();
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
        Schema::dropIfExists('candidatereminders');
    }
};
