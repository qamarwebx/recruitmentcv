<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable(); // Reference to deals table
            $table->unsignedBigInteger('uploaded_by')->nullable(); // Who uploaded
            $table->string('file_name'); // Original file name
            $table->string('file_path'); // Stored path
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_files');
    }
};
