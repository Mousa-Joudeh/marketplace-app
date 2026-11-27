<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->integer('quantity');
            $table->boolean('is_optional');
            $table->decimal('addon_price', 12, 3)->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('addon_pricing_unit', ['per_booking', 'per_hour'])->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['service_id', 'equipment_id', 'is_optional']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_equipment');
    }
};
