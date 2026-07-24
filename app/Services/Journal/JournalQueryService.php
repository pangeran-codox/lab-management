<?php

namespace App\Services\Journal;

use App\Models\Booking;
use App\Models\LabJournal;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JournalQueryService
{
    private array $dayMapReverse = [
        'Senin'  => 'Monday',  'Selasa' => 'Tuesday', 'Rabu'   => 'Wednesday',
        'Kamis'  => 'Thursday','Jumat'  => 'Friday',  'Sabtu'  => 'Saturday',
        'Minggu' => 'Sunday',
    ];

    /**
     * Jadwal tetap yang jatuh pada hari (day_of_week) dari tanggal ini,
     * di-group by "resourceId_slotId" — konsisten pola ScheduleQueryService.
     */
    public function getSchedulesForDay(Carbon $date, Collection $resourceIds): Collection
    {
        $dayEn = $date->englishDayOfWeek; // Carbon native, hasil sama kayak dayMapReverse

        return Schedule::with(['labClass'])
            ->where('status', 'active')
            ->whereIn('resource_id', $resourceIds)
            ->where('day_of_week', $dayEn)
            ->get()
            ->groupBy(fn ($s) => $s->resource_id . '_' . $s->time_slot_id);
    }

    /**
     * Booking (sekali pakai) di tanggal ini, di-group by "resourceId_slotId".
     */
    public function getBookingsForDay(Carbon $date, Collection $resourceIds): Collection
    {
        return Booking::whereDate('booking_date', $date)
            ->whereIn('resource_id', $resourceIds)
            ->active() // scope sudah ada di model Booking
            ->get(['id', 'resource_id', 'time_slot_id', 'teacher_id', 'teacher_name', 'class_name', 'subject_name', 'title', 'description'])
            ->groupBy(fn ($b) => $b->resource_id . '_' . $b->time_slot_id);
    }

    /**
     * Jurnal yang sudah diisi untuk tanggal ini, di-group by "resourceId_slotId".
     */
    public function getJournalsForDay(Carbon $date, Collection $resourceIds): Collection
    {
        return LabJournal::with('photos')
            ->whereDate('journal_date', $date)
            ->whereIn('resource_id', $resourceIds)
            ->get()
            ->groupBy(fn ($j) => $j->resource_id . '_' . $j->time_slot_id);
    }
}