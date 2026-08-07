<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
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

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'weekly_quota')) {
                $table->integer('weekly_quota')->default(5)->after('is_active');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'weekly_quota')) {
                $table->integer('weekly_quota')->default(5)->after('metadata');
            }
        });
    }
};
