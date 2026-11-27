<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 32)->nullable();
            $table->string('avatar_path', 512)->nullable();
            $table->string('city', 120)->nullable();
            $table->enum('role', ['customer', 'provider', 'admin']);
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        if (DB::connection()->getDriverName() === 'sqlite') {
            foreach (['INSERT', 'UPDATE'] as $operation) {
                $triggerName = 'users_require_login_on_' . strtolower($operation);

                DB::statement(<<<SQL
                    CREATE TRIGGER {$triggerName}
                    BEFORE {$operation} ON users
                    FOR EACH ROW
                    WHEN NEW.password IS NULL AND NEW.google_id IS NULL
                    BEGIN
                        SELECT RAISE(ABORT, 'A user must have a login method.');
                    END;
                    SQL);
            }
        } else {
            DB::statement(<<<'SQL'
                ALTER TABLE users
                ADD CONSTRAINT chk_user_login_method
                CHECK (password IS NOT NULL OR google_id IS NOT NULL)
                SQL);
        }
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};