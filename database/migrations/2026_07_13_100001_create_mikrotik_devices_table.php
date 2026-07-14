<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel perangkat MikroTik (1 device = 1 router) ──────────────
        Schema::create('mikrotik_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // label: "MikroTik Gedung A"
            $table->string('host');                          // IP address / domain
            $table->unsignedSmallInteger('port')->default(8728);
            $table->string('username')->default('admin');
            $table->string('password');
            $table->string('bot_url');                       // URL bot Python untuk device ini
            $table->string('bot_token')->nullable();         // token auth ke bot
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Tabel lab yang di-handle tiap device (1 device max 2 lab) ───
        Schema::create('mikrotik_labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mikrotik_device_id')
                  ->constrained('mikrotik_devices')
                  ->cascadeOnDelete();
            $table->foreignId('resource_id')
                  ->constrained('resources')
                  ->cascadeOnDelete();
            $table->string('lab_key')->unique();             // "lab7", "lab8", dll
            $table->integer('bot_lab_id');                   // ID lab di bot Python
            $table->string('nat_comment')->nullable();       // komentar rule NAT
            $table->string('interface')->nullable();         // nama interface di MikroTik
            $table->string('dhcp_server')->nullable();
            $table->string('network')->nullable();           // "192.168.70.0/24"
            $table->unsignedSmallInteger('vlan_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrotik_labs');
        Schema::dropIfExists('mikrotik_devices');
    }
};
