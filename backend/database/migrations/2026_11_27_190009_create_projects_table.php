<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->string('title');
            $table->text('brief');
            $table->decimal('budget', 12, 3)->nullable();
            $table->enum('status', ['draft', 'in_progress', 'completed', 'archived'])->default('draft');
            $table->string('brand_name')->nullable();
            $table->text('objective')->nullable();
            $table->text('target_audience')->nullable();
            $table->json('target_channels')->nullable();
            $table->json('planned_deliverables')->nullable();
            $table->date('target_delivery_date')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
