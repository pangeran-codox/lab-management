<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Tugas berkelanjutan: semua tugas dalam satu rangkaian materi
            // berbagi series_id yang sama, diurutkan oleh session_number.
            // Keduanya null berarti tugas berdiri sendiri (bukan bagian dari series).
            $table->uuid('series_id')->nullable()->after('id');
            $table->unsignedInteger('session_number')->nullable()->after('series_id');

            $table->index(['series_id', 'session_number']);
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex(['series_id', 'session_number']);
            $table->dropColumn(['series_id', 'session_number']);
        });
    }
};