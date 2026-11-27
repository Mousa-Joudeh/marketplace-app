<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('space_id')->nullable()->constrained('spaces');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('package_id')->nullable()->constrained('packages');
            $table->foreignId('equipment_id')->nullable()->constrained('equipment');
            $table->foreignId('parent_booking_item_id')->nullable();
            $table->string('item_role');
            $table->string('title_snapshot');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 3);
            $table->string('pricing_unit_snapshot', 100);
            $table->integer('billable_minutes')->nullable();
            $table->decimal('line_total_amount', 12, 3);
            $table->json('details_snapshot')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['booking_id', 'id']);
            $table->foreign(['booking_id', 'parent_booking_item_id'], 'booking_item_parent')
                ->references(['booking_id', 'id'])
                ->on('booking_items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
