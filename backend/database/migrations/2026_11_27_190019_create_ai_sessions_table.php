<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('project_id')->nullable()->constrained('projects');
            $table->json('inputs');
            $table->json('output')->nullable();
            $table->string('status');
            $table->string('tool_key', 100);
            $table->string('title')->nullable();
            $table->foreignId('source_ai_session_id')->nullable();
            $table->string('model_key', 150)->nullable();
            $table->string('prompt_version', 80)->nullable();
            $table->integer('input_tokens')->nullable();
            $table->integer('output_tokens')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->string('failure_code', 100)->nullable();
            $table->foreignId('ai_subscription_id')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['user_id', 'id']);
            $table->foreign(['user_id', 'source_ai_session_id'], 'ai_session_source')
                ->references(['user_id', 'id'])
                ->on('ai_sessions');
            $table->foreign(['user_id', 'ai_subscription_id'], 'ai_session_subscription')
                ->references(['user_id', 'id'])
                ->on('ai_subscriptions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_sessions');
    }
};
