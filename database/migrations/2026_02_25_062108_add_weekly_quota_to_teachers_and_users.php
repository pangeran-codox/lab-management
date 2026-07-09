<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambah ke tabel teachers (untuk guru non-user)
        if (!Schema::hasColumn('teachers', 'weekly_quota')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->integer('weekly_quota')->default(5)->after('is_active');
            });
        }

        // Tambah ke tabel users (untuk guru yang juga user)
        if (!Schema::hasColumn('users', 'weekly_quota')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('weekly_quota')->default(5)->after('metadata');
            });
        }
    }

    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'weekly_quota')) {
                $table->dropColumn('weekly_quota');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'weekly_quota')) {
                $table->dropColumn('weekly_quota');
            }
        });
    }
};
