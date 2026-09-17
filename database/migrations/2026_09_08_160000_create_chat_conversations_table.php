<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            // type: direct only for now (admin-to-admin). Kept for future group support.
            $table->string('type')->default('direct');
            $table->unsignedBigInteger('created_by')->nullable();
            // Denormalized pointers to the latest message, updated on every send,
            // so the sidebar can sort/preview without a MAX(id) subquery per row.
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');

            $table->index('last_message_at');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_conversations');
    }
};
