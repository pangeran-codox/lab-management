<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ScheduleAbsenceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'absent_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        // Cek apakah tanggal sesuai dengan hari jadwal
        $date = \Illuminate\Support\Carbon::parse($request->absent_date);
        $dayName = $date->format('l');
        if ($dayName !== $schedule->day_of_week) {
            return back()->withErrors(['absent_date' => 'Tanggal tidak sesuai dengan hari jadwal (' . $schedule->day_of_week . ')']);
        }

        ScheduleAbsence::create([
            'schedule_id' => $request->schedule_id,
            'absent_date' => $request->absent_date,
            'reason' => $request->reason,
            'created_by' => auth()->id(),
        ]);

        // Hapus cache rekap
        Cache::forget('rekap_monthly_' . $date->month . '_' . $date->year);

        return back()->with('success', 'Absen jadwal berhasil ditambahkan.');
    }

    public function destroy(ScheduleAbsence $absence)
    {
        $date = $absence->absent_date;
        $absence->delete();

        // Hapus cache rekap
        Cache::forget('rekap_monthly_' . $date->month . '_' . $date->year);

        return back()->with('success', 'Absen jadwal berhasil dihapus.');
    }
}
