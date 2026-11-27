<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages');
            $table->foreignId('space_id')->nullable()->constrained('spaces');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('equipment_id')->nullable()->constrained('equipment');
            $table->integer('quantity');
            $table->boolean('is_optional');
            $table->decimal('addon_price', 12, 3)->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('addon_pricing_unit', ['per_booking', 'per_hour'])->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_items');
    }
};
