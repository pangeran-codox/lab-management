<?php

namespace App\Services\Journal;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class JournalAvailabilityService
{
    /**
     * Tentukan slot mana yang "eligible" diisi jurnal untuk tanggal tertentu:
     * - Tanggal di masa depan  → semua slot belum eligible (belum kejadian)
     * - Tanggal di masa lalu   → semua slot eligible (udah lewat)
     * - Hari ini               → eligible cuma kalau start_time slot sudah lewat jam sekarang
     */
    public function getSlotEligibilityMap(Collection $timeSlots, Carbon $date): Collection
    {
        if ($date->isFuture() && !$date->isToday()) {
            return $timeSlots->mapWithKeys(fn ($slot) => [$slot->id => false]);
        }

        if ($date->isPast() && !$date->isToday()) {
            return $timeSlots->mapWithKeys(fn ($slot) => [$slot->id => true]);
        }

        // Hari ini
        return $timeSlots->mapWithKeys(function ($slot) use ($date) {
            $slotTime = Carbon::parse($slot->start_time)->setDateFrom($date);
            return [$slot->id => $slotTime->isPast()];
        });
    }
}