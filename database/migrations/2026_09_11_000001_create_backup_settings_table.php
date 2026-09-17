<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('daily_enabled')->default(false);
            $table->string('daily_time', 5)->default('02:00');
            $table->unsignedInteger('daily_retention')->default(7);
            $table->boolean('interval_enabled')->default(false);
            $table->unsignedInteger('interval_value')->default(30);
            $table->unsignedInteger('interval_retention')->default(5);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('backup_settings');
    }
};
