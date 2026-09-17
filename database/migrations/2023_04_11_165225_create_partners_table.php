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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('rec_off_name');
            $table->string('rec_office_arname')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_mobile_no')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('primary_email')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('office_no')->nullable();
            $table->string('primary_mob')->nullable();
            $table->string('secondary_mob')->nullable();
            $table->text('logo')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('partners');
    }
};
