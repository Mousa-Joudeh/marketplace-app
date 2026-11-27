<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->string('title', 160)->nullable();
            $table->tinyInteger('quality_rating')->nullable();
            $table->tinyInteger('communication_rating')->nullable();
            $table->tinyInteger('value_rating')->nullable();
            $table->text('provider_reply')->nullable();
            $table->dateTime('provider_replied_at')->nullable();
            $table->enum('status', ['published', 'hidden']);
            $table->text('moderation_reason')->nullable();
            $table->foreignId('moderated_by_user_id')->nullable()->constrained('users');
            $table->dateTime('moderated_at')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->json('review_media')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
