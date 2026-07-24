<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_journals', function (Blueprint $table) {
            $table->id();

            $table->string('source_type', 20); // 'schedule' | 'booking'
            $table->unsignedBigInteger('source_id');

            $table->foreignId('resource_id')->constrained('resources');
            $table->foreignId('time_slot_id')->nullable()->constrained('time_slots');
            $table->date('journal_date');

            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('teacher_name');
            $table->string('subject_name')->nullable();
            $table->string('class_name')->nullable();
            $table->text('activity')->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('filled_at')->nullable();
            $table->timestamps();

            $table->unique(['source_type', 'source_id', 'journal_date'], 'lab_journals_source_date_unique');
            $table->index(['resource_id', 'journal_date']);
        });

        DB::statement("ALTER TABLE lab_journals ADD CONSTRAINT lab_journals_source_type_check CHECK (source_type IN ('schedule', 'booking'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_journals');
    }
};