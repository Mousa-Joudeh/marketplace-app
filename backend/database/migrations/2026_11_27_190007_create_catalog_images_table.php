<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->nullable()->constrained('spaces');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('package_id')->nullable()->constrained('packages');
            $table->foreignId('equipment_id')->nullable()->constrained('equipment');
            $table->string('storage_key');
            $table->integer('sort_order');
            $table->boolean('is_cover')->default(false);
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->string('thumbnail_storage_key', 512)->nullable();
            $table->integer('width_px')->nullable();
            $table->integer('height_px')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_images');
    }
};
