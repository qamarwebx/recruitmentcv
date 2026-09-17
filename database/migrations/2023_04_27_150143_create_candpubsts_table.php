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
        Schema::create('candpubsts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cand_id');
            $table->boolean('passport_st')->default(false);
            $table->boolean('skill_exp_st')->default(false);
            $table->boolean('document_st')->default(false);
            $table->boolean('publish')->default(false);
            $table->string('stage_name')->nullable();
            $table->unsignedBigInteger('admin_id');
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
        Schema::dropIfExists('candpubsts');
    }
};
