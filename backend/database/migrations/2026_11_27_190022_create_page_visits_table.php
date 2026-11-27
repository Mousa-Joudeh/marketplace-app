<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('page_path');
            $table->dateTime('visited_at');
            $table->string('route_name', 160)->nullable();
            $table->string('analytics_session_id', 64)->nullable();
            $table->foreignId('space_id')->nullable()->constrained('spaces');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('package_id')->nullable()->constrained('packages');
            $table->foreignId('provider_profile_id')->nullable()->constrained('provider_profiles');
            $table->string('referrer_host')->nullable();
            $table->string('utm_source', 160)->nullable();
            $table->string('utm_medium', 160)->nullable();
            $table->string('utm_campaign', 160)->nullable();
            $table->enum('device_type', ['mobile', 'desktop', 'tablet'])->nullable();

            $table->index(['visited_at', 'route_name']);
            $table->index(['analytics_session_id', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
