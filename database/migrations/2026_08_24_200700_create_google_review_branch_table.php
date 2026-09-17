<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_review_branch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('google_review_id');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();

            $table->foreign('google_review_id')->references('id')->on('google_reviews')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->unique(['google_review_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_review_branch');
    }
};
