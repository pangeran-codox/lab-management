<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->char('session_id', 36)->nullable()->after('id')->index();
        });

        // Isi session_id untuk data lama:
        // Grup berdasarkan teacher_name + resource_id + booking_date + created_at (menit yang sama)
        $groups = DB::table('bookings')
            ->selectRaw("
                MIN(id) as first_id,
                teacher_name,
                resource_id,
                booking_date,
                TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI') as created_minute
            ")
            ->groupBy('teacher_name', 'resource_id', 'booking_date', DB::raw("TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI')"))
            ->get();

        foreach ($groups as $group) {
            $sessionId = (string) Str::uuid();
            DB::table('bookings')
                ->where('teacher_name', $group->teacher_name)
                ->where('resource_id', $group->resource_id)
                ->where('booking_date', $group->booking_date)
                ->whereRaw("TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI') = ?", [$group->created_minute])
                ->whereNull('session_id')
                ->update(['session_id' => $sessionId]);
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropColumn('session_id');
        });
    }
};