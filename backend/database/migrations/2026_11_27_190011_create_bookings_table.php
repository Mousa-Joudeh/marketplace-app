<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('provider_profile_id')->constrained('provider_profiles');
            $table->foreignId('project_id')->nullable()->constrained('projects');
            $table->string('status');
            $table->decimal('total_amount', 12, 3);
            $table->text('agreed_scope');
            $table->string('address')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('delivery_due_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->string('reference', 32)->unique();
            $table->char('currency', 3)->default('JOD');
            $table->decimal('subtotal_amount', 12, 3);
            $table->decimal('discount_amount', 12, 3)->default(0);
            $table->integer('participant_count')->nullable();
            $table->text('customer_notes')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->foreignId('cancelled_by_user_id')->nullable()->constrained('users');
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->dateTime('payment_due_at')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['project_id', 'id']);
            $table->unique(['id', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
