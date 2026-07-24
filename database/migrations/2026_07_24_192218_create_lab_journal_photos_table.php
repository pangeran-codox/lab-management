<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_journal_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_journal_id')->constrained('lab_journals')->cascadeOnDelete();
            $table->string('photo_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_journal_photos');
    }
};