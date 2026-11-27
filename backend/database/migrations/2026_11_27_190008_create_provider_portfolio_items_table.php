<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles');
            $table->string('title');
            $table->string('media_reference');
            $table->text('description')->nullable();
            $table->enum('media_type', ['image', 'video']);
            $table->string('thumbnail_path', 512)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('space_id')->nullable()->constrained('spaces');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('package_id')->nullable()->constrained('packages');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_portfolio_items');
    }
};
