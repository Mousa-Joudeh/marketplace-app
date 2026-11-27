<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('plan_code');
            $table->string('status');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->integer('usage_limit');
            $table->decimal('price_amount', 12, 3);
            $table->char('currency', 3);
            $table->enum('billing_interval', ['monthly', 'yearly'])->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->dateTime('renewal_cancelled_at')->nullable();
            $table->string('billing_provider', 100)->nullable();
            $table->string('external_subscription_id')->nullable();
            $table->string('external_invoice_id')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['user_id', 'id']);
            $table->index(['user_id', 'starts_at']);
            $table->index(['billing_provider', 'external_subscription_id']);
            $table->unique(['billing_provider', 'external_invoice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_subscriptions');
    }
};
