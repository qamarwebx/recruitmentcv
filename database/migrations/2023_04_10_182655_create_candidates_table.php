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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('cand_name');
            $table->string('pass_no');
            $table->string('pass_type');
            $table->date('doi');
            $table->string('experience');
            $table->string('expr_country_name')->nullable();
            $table->string('job_type')->nullable();
            $table->string('age');
            $table->string('religion')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('marital_status');
            $table->string('lang_known');
            $table->decimal('exp_sal',10,2);
            $table->string('mobile_no')->nullable();
            $table->text('pass_file')->nullable();
            $table->text('lic_file')->nullable();
            $table->text('cv_file')->nullable();
            $table->text('photo_file')->nullable();
            $table->unsignedBigInteger('admin_id');
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
        Schema::dropIfExists('candidates');
    }
};
