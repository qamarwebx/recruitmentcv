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
        Schema::create('employercandidates', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_id');
            $table->integer('cand_id');
            $table->integer('proff_id');
            $table->integer('assignbystaff_id')->nullable();
            $table->integer('assignbypartner_id')->nullable();
            $table->date('assignbydate')->nullable();
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
        Schema::dropIfExists('employercandidates');
    }
};
