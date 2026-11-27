<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('shared_booking_id')->nullable();
            $table->string('original_name');
            $table->string('storage_key');
            $table->string('display_name')->nullable();
            $table->string('asset_type', 64);
            $table->text('description')->nullable();
            $table->string('mime_type', 127)->nullable();
            $table->bigInteger('size_bytes')->nullable();
            $table->string('preview_storage_key', 512)->nullable();
            $table->dateTime('shared_at')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();

            $table->foreign(['project_id', 'shared_booking_id'], 'project_asset_shared_booking')
                ->references(['project_id', 'id'])
                ->on('bookings');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_assets');
    }
};
