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
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users');
            $table->string('display_name');
            $table->string('slug')->unique();
            $table->string('city');
            $table->string('tagline', 160)->nullable();
            $table->text('bio')->nullable();

            $table->string('district', 120)->nullable();
            $table->string('public_address')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();

            $table->string('logo_path', 512)->nullable();
            $table->string('cover_path', 512)->nullable();
            $table->string('instagram_url', 512)->nullable();

            $table->string('approval_status');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};