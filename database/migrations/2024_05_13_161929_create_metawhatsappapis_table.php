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
        Schema::create('metawhatsappapis', function (Blueprint $table) {
            $table->id();
            $table->text('api_access_token');
            $table->text('api_base_url');
            $table->text('vendor_uid');
            $table->unsignedBigInteger('staff_id');
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
        Schema::dropIfExists('metawhatsappapis');
    }
};
