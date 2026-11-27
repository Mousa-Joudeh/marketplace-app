<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('favorite_id')->constrained('favorites');
            $table->foreignId('space_id')->nullable()->constrained('spaces');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('package_id')->nullable()->constrained('packages');
            $table->text('notes')->nullable();
            $table->integer('sort_order')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['favorite_id', 'space_id']);
            $table->unique(['favorite_id', 'service_id']);
            $table->unique(['favorite_id', 'package_id']);
            $table->index(['favorite_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_items');
    }
};
