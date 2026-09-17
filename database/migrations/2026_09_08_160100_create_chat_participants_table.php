<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('admin_id');
            // High-water-mark read tracking (no per-message read rows - see
            // ChatController::unreadCount for why this keeps unread counts cheap
            // at large message volumes).
            $table->unsignedBigInteger('last_read_message_id')->nullable();
            $table->timestamp('last_read_at')->nullable();
            // Explicit WhatsApp-style "mark as unread" override, independent of
            // last_read_message_id, cleared the next time the conversation is opened.
            $table->boolean('is_unread_manual')->default(false);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('chat_conversations')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');

            $table->unique(['conversation_id', 'admin_id']);
            $table->index('admin_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_participants');
    }
};
