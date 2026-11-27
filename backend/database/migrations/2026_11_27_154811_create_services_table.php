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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title');
            $table->string('service_kind');
            $table->string('location_mode');
            $table->string('pricing_unit');
            $table->decimal('price', 12, 3);
            $table->boolean('is_addon_only');
            $table->string('status');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('deliverables')->nullable();
            $table->text('scope_limits')->nullable();
            $table->text('client_requirements')->nullable();
            $table->foreignId('space_id')->nullable()->constrained('spaces');
            $table->integer('estimated_duration_minutes')->nullable();
            $table->integer('min_booking_minutes')->nullable();
            $table->integer('max_booking_minutes')->nullable();
            $table->integer('booking_lead_time_hours')->nullable();
            $table->integer('delivery_turnaround_days')->nullable();
            $table->integer('revision_limit')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
