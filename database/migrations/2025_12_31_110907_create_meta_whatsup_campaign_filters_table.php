<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('meta_whatsup_campaign_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->index();

            $table->json('audience')->nullable();
            $table->json('careoff')->nullable();
            $table->json('group')->nullable();
            $table->json('created_by')->nullable();

            $table->string('lead_date_range')->nullable();
            $table->string('send_date')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meta_whatsup_campaign_filters');
    }
};

