<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id');
            $table->enum('method', ['cash', 'cliq', 'stripe']);
            $table->string('status');
            $table->decimal('amount', 12, 3);
            $table->string('reference', 64)->unique();
            $table->dateTime('paid_at')->nullable();
            $table->char('currency', 3);
            $table->enum('transaction_type', ['charge', 'refund']);
            $table->foreignId('parent_payment_id')->nullable();
            $table->string('external_transaction_id')->nullable();
            $table->string('idempotency_key', 128)->unique();
            $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users');
            $table->string('failure_code', 100)->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->text('refund_reason')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['booking_id', 'currency', 'id']);
            $table->unique(['method', 'external_transaction_id']);
            $table->foreign(['booking_id', 'currency'], 'booking_payment')
                ->references(['id', 'currency'])
                ->on('bookings');
            $table->foreign(['booking_id', 'currency', 'parent_payment_id'], 'payment_parent')
                ->references(['booking_id', 'currency', 'id'])
                ->on('payments');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
