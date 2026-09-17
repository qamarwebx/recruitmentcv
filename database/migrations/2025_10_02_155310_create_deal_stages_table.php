<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Stage name (e.g., "Prospecting", "Negotiation")
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_stages');
    }
};
