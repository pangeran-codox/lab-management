<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_maintenance_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('lab_inventory_id')->constrained('lab_inventory')->onDelete('cascade');
            $blueprint->foreignId('user_id')->constrained('users'); // Teknisi yang mencatat
            $blueprint->date('maintenance_date');
            $blueprint->string('maintenance_type'); // misal: Perbaikan, Penggantian, Perawatan Rutin
            $blueprint->text('description');
            $blueprint->decimal('cost', 15, 2)->default(0);
            $blueprint->string('status'); // misal: Selesai, Menunggu Suku Cadang
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_maintenance_logs');
    }
};
