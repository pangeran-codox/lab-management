<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Unique constraint phone di teachers
        // Pastikan sudah tidak ada duplikat sebelum ini
        Schema::table('teachers', function (Blueprint $table) {
            $table->unique('phone', 'uk_teachers_phone');
        });

        // 2. Composite index untuk cek konflik booking
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(
                ['resource_id', 'booking_date', 'time_slot_id', 'status'],
                'idx_booking_conflict'
            );
        });

        // 3. Fix user_sessions.expires_at — hapus ON UPDATE
        DB::statement("
            ALTER TABLE user_sessions 
            MODIFY expires_at TIMESTAMP NOT NULL 
            DEFAULT CURRENT_TIMESTAMP
        ");
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropUnique('uk_teachers_phone');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_booking_conflict');
        });

        DB::statement("
            ALTER TABLE user_sessions 
            MODIFY expires_at TIMESTAMP NOT NULL 
            DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");
    }
};