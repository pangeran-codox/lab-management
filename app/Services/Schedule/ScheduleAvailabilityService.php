<?php

namespace App\Services\Schedule;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScheduleAvailabilityService
{
    private array $dayMapReverse = [
        'Senin'  => 'Monday',
        'Selasa' => 'Tuesday',
        'Rabu'   => 'Wednesday',
        'Kamis'  => 'Thursday',
        'Jumat'  => 'Friday',
        'Sabtu'  => 'Saturday',
        'Minggu' => 'Sunday',
    ];

    /**
     * Pre-compute metadata untuk slot waktu (start, end, display time).
     */
    public function getSlotMeta(Collection $timeSlots): Collection
    {
        return $timeSlots->mapWithKeys(function ($slot) {
            return [$slot->id => [
                'start' => $slot->start_time ? substr($slot->start_time, 0, 5) : '',
                'end'   => $slot->end_time   ? substr($slot->end_time,   0, 5) : '',
                'time'  => substr($slot->start_time, 0, 5)
                         . ($slot->end_time ? '–' . substr($slot->end_time, 0, 5) : ''),
            ]];
        });
    }

    /**
     * Pre-compute metadata untuk tanggal dalam seminggu (isToday, isPast, format).
     */
    public function getDateMeta(array $weekDates): Collection
    {
        return collect($weekDates)->mapWithKeys(function ($date, $day) {
            $carbon = Carbon::parse($date);
            return [$day => [
                'date'      => $date,
                'isToday'   => $carbon->isToday(),
                'isPast'    => $carbon->isPast() && !$carbon->isToday(),
                'formatted' => $carbon->translatedFormat('d M Y'),
                'dm'        => $carbon->format('d/m'),
            ]];
        });
    }

    /**
     * Pre-compute isSlotPast per slot (hanya untuk hari ini).
     */
    public function getSlotPastMap(Collection $timeSlots): Collection
    {
        $today = Carbon::today();
        return $timeSlots->mapWithKeys(function ($slot) use ($today) {
            $slotTime = Carbon::parse($slot->start_time)->setDateFrom($today);
            return [$slot->id => $slotTime->isPast()];
        });
    }

    /**
     * Menghitung pemetaan slot yang sudah terisi per resource + tanggal.
     */
    public function getTakenSlotsMap(
        Collection $resources,
        array $weekDates,
        Collection $bookings,
        Collection $schedules,
        Collection $importantSchedules,
        Collection $timeSlots
    ): array {
        $takenSlotsMap = [];

        foreach ($resources as $resource) {
            foreach ($weekDates as $day => $date) {
                $dayEn = $this->dayMapReverse[$day];
                $key   = $resource->id . '_' . $date;

                // 1. Slot dari booking aktif
                $bookedIds = $bookings->filter(function ($group, $groupKey) use ($resource, $date) {
                    return str_starts_with($groupKey, $resource->id . '_' . $date . '_');
                })->keys()->map(fn($k) => (int) explode('_', $k)[2])->toArray();

                // 2. Slot dari jadwal rutin (tetap)
                $scheduledIds = $timeSlots->filter(function ($ts) use ($schedules, $resource, $dayEn) {
                    return !($ts->is_break ?? false)
                        && $schedules->has($resource->id . '_' . $dayEn . '_' . $ts->id);
                })->pluck('id')->map(fn($id) => (int) $id)->toArray();

                // 3. Slot dari jadwal penting (acara khusus)
                $importantIds = [];
                if (isset($importantSchedules[$key])) {
                    $allNonBreakIds = $timeSlots->where('is_break', false)->pluck('id')->map(fn($id) => (int)$id)->toArray();
                    foreach ($importantSchedules[$key] as $event) {
                        if ($event->is_full_day) {
                            $importantIds = array_merge($importantIds, $allNonBreakIds);
                        } else {
                            $startOrder = $event->startSlot?->slot_order ?? 0;
                            $endOrder   = $event->endSlot?->slot_order   ?? 0;
                            foreach ($timeSlots->where('is_break', false) as $ts) {
                                if ($ts->slot_order >= $startOrder && $ts->slot_order <= $endOrder) {
                                    $importantIds[] = (int) $ts->id;
                                }
                            }
                        }
                    }
                }

                $takenSlotsMap[$key] = array_values(array_unique(array_merge($bookedIds, $scheduledIds, $importantIds)));
            }
        }

        return $takenSlotsMap;
    }
}
