<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title');
            $table->integer('stock_quantity');
            $table->decimal('addon_price', 12, 3);
            $table->string('status');
            $table->string('brand', 120)->nullable();
            $table->string('model', 160)->nullable();
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->text('usage_notes')->nullable();
            $table->string('inventory_code', 100)->nullable();
            $table->integer('out_of_service_quantity')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();

            $table->unique(['provider_profile_id', 'inventory_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
