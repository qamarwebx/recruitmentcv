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
        Schema::create('employeradminsavefilters', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id');
            $table->text('proff_id')->nullable();
            $table->text('issuing_authority')->nullable();
            $table->text('wpcity_id')->nullable();
            $table->text('businesstype')->nullable();
            $table->text('visa_date_range')->nullable();
            $table->text('createbyadmin')->nullable();
            $table->text('createbypartner')->nullable();
            $table->text('partneroffice')->nullable();
            $table->text('careoff')->nullable();
            $table->text('wakala_status')->nullable();
            $table->text('visa_received_date_range')->nullable();
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
        Schema::dropIfExists('employeradminsavefilters');
    }
};
