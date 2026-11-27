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
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles');
            $table->foreignId('category_id')->constrained('categories');

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('hourly_rate', 12, 3);
            $table->string('status');

            $table->string('address');
            $table->string('city', 120);
            $table->string('district', 120)->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();

            $table->integer('capacity');
            $table->decimal('area_sqm', 10, 2)->nullable();
            $table->json('amenities')->nullable();
            $table->text('house_rules')->nullable();
            $table->integer('min_booking_minutes')->nullable();
            $table->integer('max_booking_minutes')->nullable();
            $table->integer('booking_buffer_minutes')->default(0);
            $table->text('check_in_instructions')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
        $validSpace = <<<'SQL'
            capacity > 0
            AND booking_buffer_minutes >= 0
            AND (area_sqm IS NULL OR area_sqm > 0)
            AND (min_booking_minutes IS NULL OR min_booking_minutes > 0)
            AND (
                max_booking_minutes IS NULL
                OR (
                    max_booking_minutes > 0
                    AND (min_booking_minutes IS NULL OR max_booking_minutes >= min_booking_minutes)
                )
            )
            SQL;

        if (DB::connection()->getDriverName() === 'sqlite') {
            $validNewRow = strtr($validSpace, [
                'capacity' => 'NEW.capacity',
                'booking_buffer_minutes' => 'NEW.booking_buffer_minutes',
                'area_sqm' => 'NEW.area_sqm',
                'min_booking_minutes' => 'NEW.min_booking_minutes',
                'max_booking_minutes' => 'NEW.max_booking_minutes',
            ]);

            foreach (['INSERT', 'UPDATE'] as $operation) {
                $triggerName = 'spaces_validate_' . strtolower($operation);

                DB::statement(<<<SQL
                    CREATE TRIGGER {$triggerName}
                    BEFORE {$operation} ON spaces
                    FOR EACH ROW
                    WHEN NOT ({$validNewRow})
                    BEGIN
                        SELECT RAISE(ABORT, 'Invalid space capacity or booking settings.');
                    END;
                    SQL);
            }

            return;
        }

        DB::statement(<<<SQL
            ALTER TABLE spaces
            ADD CONSTRAINT chk_space_capacity_duration
            CHECK ({$validSpace})
            SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};