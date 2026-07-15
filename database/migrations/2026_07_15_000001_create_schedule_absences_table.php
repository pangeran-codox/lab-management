<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')
                  ->constrained('schedules')
                  ->cascadeOnDelete();
            $table->date('absent_date');
            $table->text('reason')->nullable();
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamps();

            // Index untuk query yang sering dipakai
            $table->index(['schedule_id', 'absent_date'], 'idx_sa_schedule_date');
            $table->unique(['schedule_id', 'absent_date'], 'idx_sa_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_absences');
    }
};
