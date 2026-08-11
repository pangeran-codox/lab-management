<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Nullable & nullOnDelete karena submission lama (sebelum fitur ini ada)
            // tidak punya open_period — dianggap "round 1" implisit.
            $table->foreignId('open_period_id')->nullable()->after('assignment_id')
                ->constrained('assignment_open_periods')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('open_period_id');
        });
    }
};