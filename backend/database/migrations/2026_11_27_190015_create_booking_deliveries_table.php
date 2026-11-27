<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->string('title');
            $table->text('file_reference');
            $table->dateTime('delivered_at');
            $table->foreignId('delivered_by_user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->enum('source_type', ['upload', 'external_link']);
            $table->string('original_name')->nullable();
            $table->string('mime_type', 127)->nullable();
            $table->bigInteger('size_bytes')->nullable();
            $table->enum('delivery_type', ['draft', 'final']);
            $table->foreignId('previous_delivery_id')->nullable();
            $table->foreignId('booking_item_id')->nullable();

            $table->unique(['booking_id', 'id']);
            $table->foreign(['booking_id', 'previous_delivery_id'], 'previous_booking_delivery')
                ->references(['booking_id', 'id'])
                ->on('booking_deliveries');
            $table->foreign(['booking_id', 'booking_item_id'], 'booking_delivery_item')
                ->references(['booking_id', 'id'])
                ->on('booking_items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_deliveries');
    }
};
