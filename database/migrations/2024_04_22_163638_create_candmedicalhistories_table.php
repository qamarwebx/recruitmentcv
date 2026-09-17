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
        Schema::create('candmedicalhistories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cand_id');
            $table->string('medical_status');
            $table->date('medical_examination_date')->nullable();
            $table->date('medical_expiry_date')->nullable();
            $table->date('remedical_date')->nullable();
            $table->string('medical_expire_in')->nullable();
            $table->date('last_update_date')->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('candmedicalhistories');
    }
};
