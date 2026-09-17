<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('storage_usage_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('module_key')->unique();
            $table->string('label');
            $table->unsignedBigInteger('file_count')->default(0);
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('storage_usage_snapshots');
    }
};
