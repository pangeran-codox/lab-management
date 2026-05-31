<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('important_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')
                  ->constrained('resources')
                  ->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['exam', 'olympiad', 'event', 'other'])->default('event');
            $table->date('date');
            $table->boolean('is_full_day')->default(false);
            $table->foreignId('start_slot_id')
                  ->nullable()
                  ->constrained('time_slots')
                  ->nullOnDelete();
            $table->foreignId('end_slot_id')
                  ->nullable()
                  ->constrained('time_slots')
                  ->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#EF4444'); // warna badge di kalender
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamps();

            // Index untuk query di tampilan jadwal (resource + date)
            $table->index(['resource_id', 'date'], 'idx_is_resource_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('important_schedules');
    }
};
