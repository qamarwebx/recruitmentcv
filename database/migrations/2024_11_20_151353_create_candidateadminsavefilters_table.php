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
        Schema::create('candidateadminsavefilters', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id');
            $table->text('pass_type')->nullable();
            $table->text('jobtype_id')->nullable();
            $table->text('careoff_id')->nullable();
            $table->text('createby_id')->nullable();
            $table->text('religion_id')->nullable();
            $table->text('city')->nullable();
            $table->text('region_id')->nullable();
            $table->text('experience_region')->nullable();
            $table->text('publish_status')->nullable();
            $table->text('craete_date_range')->nullable();
            $table->text('medical_expiry_date_filter')->nullable();
            $table->text('sourcing_date_range')->nullable();
            $table->text('cand_status')->nullable();
            $table->text('cand_medical_status')->nullable();
            $table->text('cand_payment_status')->nullable();
            $table->text('expwp_id')->nullable();

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
        Schema::dropIfExists('candidateadminsavefilters');
    }
};
