<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('backup_histories', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // daily | interval
            $table->string('disk', 50)->default('db_backups');
            $table->string('path')->nullable(); // relative path within disk
            $table->string('filename')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('status', 20); // success | failed
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();

            // Retention cleanup and history listing both filter by
            // type (+status) and sort by generated_at - this composite
            // index keeps both queries index-only instead of scanning.
            $table->index(['type', 'status', 'generated_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('backup_histories');
    }
};
