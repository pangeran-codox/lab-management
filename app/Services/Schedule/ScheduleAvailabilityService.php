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
     * Dioptimalkan untuk load tinggi (O(N) lookup).
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
        $availCounts   = [];

        $nonBreakSlots = $timeSlots->where('is_break', false);
        $allNonBreakIds = $nonBreakSlots->pluck('id')->map(fn($id) => (int)$id)->toArray();

        foreach ($resources as $resource) {
            foreach ($weekDates as $day => $date) {
                $dayEn = $this->dayMapReverse[$day];
                $key   = $resource->id . '_' . $date;
                $resId = $resource->id;

                $takenIds = [];

                // 1. Slot dari booking aktif (O(Slots) lookup)
                foreach ($nonBreakSlots as $ts) {
                    $lookupKey = "{$resId}_{$date}_{$ts->id}";
                    if ($bookings->has($lookupKey)) {
                        $takenIds[] = (int) $ts->id;
                    }
                }

                // 2. Slot dari jadwal rutin (tetap) (O(Slots) lookup)
                foreach ($nonBreakSlots as $ts) {
                    $lookupKey = "{$resId}_{$dayEn}_{$ts->id}";
                    if ($schedules->has($lookupKey)) {
                        $takenIds[] = (int) $ts->id;
                    }
                }

                // 3. Slot dari jadwal penting (acara khusus) (O(Events) lookup)
                if (isset($importantSchedules[$key])) {
                    foreach ($importantSchedules[$key] as $event) {
                        if ($event->is_full_day) {
                            $takenIds = array_merge($takenIds, $allNonBreakIds);
                        } else {
                            $startOrder = $event->startSlot?->slot_order ?? 0;
                            $endOrder   = $event->endSlot?->slot_order   ?? 0;
                            foreach ($nonBreakSlots as $ts) {
                                if ($ts->slot_order >= $startOrder && $ts->slot_order <= $endOrder) {
                                    $takenIds[] = (int) $ts->id;
                                }
                            }
                        }
                    }
                }

                $uniqueTaken = array_values(array_unique($takenIds));
                $takenSlotsMap[$key] = $uniqueTaken;
                
                // Hitung ketersediaan (untuk footer kartu hari)
                // Note: ini menyederhanakan perhitungan di blade
                $availCounts[$key] = count($allNonBreakIds) - count($uniqueTaken);
            }
        }

        return [
            'map'    => $takenSlotsMap,
            'avails' => $availCounts
        ];
    }
}
