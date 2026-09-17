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
        Schema::create('allcontact_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();

            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('allcontact_files');
    }
};
