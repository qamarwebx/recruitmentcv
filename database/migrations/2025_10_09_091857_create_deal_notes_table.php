<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deal_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable(); // Reference to deals table
            $table->unsignedBigInteger('created_by')->nullable(); // Who created the note
            $table->text('notes')->nullable();
            $table->string('conversation_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_notes');
    }
};
