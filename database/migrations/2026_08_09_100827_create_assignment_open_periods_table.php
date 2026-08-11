<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_open_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();

            // Ke berapa kalinya tugas ini dibuka (1 = pertama kali dibuat, 2 = dibuka ulang pertama, dst)
            $table->unsignedInteger('round_number');

            $table->dateTime('opened_at');
            $table->dateTime('deadline'); // deadline khusus untuk round ini

            // Kalau true: siswa yang sudah pernah submit di round sebelumnya
            // boleh submit ulang selama round ini masih terbuka.
            $table->boolean('allow_resubmit')->default(false);

            $table->dateTime('closed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_open_periods');
    }
};