<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('sender_id')->constrained('users');
            $table->text('body')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->foreignId('reply_to_message_id')->nullable();
            $table->string('attachment_storage_key', 512)->nullable();
            $table->string('attachment_original_name')->nullable();
            $table->string('attachment_mime_type', 127)->nullable();
            $table->bigInteger('attachment_size_bytes')->nullable();
            $table->dateTime('edited_at')->nullable();
            $table->string('client_message_id', 64);
            $table->dateTime('created_at');

            $table->unique(['booking_id', 'id']);
            $table->unique(['booking_id', 'sender_id', 'client_message_id']);
            $table->foreign(['booking_id', 'reply_to_message_id'], 'booking_message_reply')
                ->references(['booking_id', 'id'])
                ->on('booking_messages');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_messages');
    }
};
