<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title');
            $table->text('deliverables');
            $table->decimal('price', 12, 3);
            $table->string('status');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('scope_limits')->nullable();
            $table->text('client_requirements')->nullable();
            $table->integer('session_duration_minutes')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('booking_lead_time_hours')->nullable();
            $table->integer('delivery_turnaround_days')->nullable();
            $table->integer('revision_limit')->nullable();
            $table->dateTime('bookable_from')->nullable();
            $table->dateTime('bookable_until')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
