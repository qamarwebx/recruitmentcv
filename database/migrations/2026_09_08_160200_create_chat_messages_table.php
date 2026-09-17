<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->text('body')->nullable();
            // message_type: text | image | document | system
            $table->string('message_type')->default('text');
            $table->unsignedBigInteger('reply_to_message_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('chat_conversations')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('reply_to_message_id')->references('id')->on('chat_messages')->onDelete('set null');

            // Covers pagination ("older than id X in conversation Y") and the
            // unread-count range join (id > last_read_message_id) in one index.
            $table->index(['conversation_id', 'id']);
            $table->index('sender_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
