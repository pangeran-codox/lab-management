<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\ImportantSchedule;
use App\Models\Schedule;
use App\Models\TimeSlot;
use Carbon\Carbon;

/**
 * ConflictCheckerService
 * Cek apakah slot tertentu bentrok dengan:
 *   1. Jadwal rutin    (Schedule)          — day_of_week + time_slot_id + resource_id
 *   2. Jadwal penting  (ImportantSchedule) — date + resource_id + range slot / full_day
 *   3. Booking lain    (Booking)           — booking_date + time_slot_id + resource_id
 */
class ConflictCheckerService
{
    // ══════════════════════════════════════════════════════════════════
    // PUBLIC — cek untuk Booking model yang sudah ada
    // ══════════════════════════════════════════════════════════════════

    /** Cek semua konflik untuk satu Booking. Return [] jika aman. */
    public function check(Booking $booking): array
    {
        return $this->checkAll(
            resourceId:  $booking->resource_id,
            timeSlotId:  $booking->time_slot_id,
            bookingDate: $booking->booking_date instanceof \Carbon\Carbon
                ? $booking->booking_date->format('Y-m-d')
                : (string) $booking->booking_date,
            excludeId:   $booking->id,
        );
    }

    /** Return true jika ada konflik apapun. */
    public function hasConflict(Booking $booking): bool
    {
        return count($this->check($booking)) > 0;
    }

    /** Cek bentrok untuk group booking. Return [ booking_id => [konflik...] ] */
    public function checkGroup(iterable $bookings): array
    {
        $bookings = collect($bookings);

        if ($bookings->isEmpty()) {
            return [];
        }

        // Ambil semua data yang dibutuhkan dalam 3 query flat (bukan N×3)
        $firstBooking = $bookings->first();
        $resourceId   = $firstBooking->resource_id;
        $date         = $firstBooking->booking_date instanceof \Carbon\Carbon
            ? $firstBooking->booking_date->format('Y-m-d')
            : (string) $firstBooking->booking_date;
        $dayOfWeek    = Carbon::parse($date)->locale('en')->dayName;
        $slotIds      = $bookings->pluck('time_slot_id')->unique()->toArray();
        $bookingIds   = $bookings->pluck('id')->toArray();

        // Query 1: Jadwal rutin untuk semua slot sekaligus
        $routineConflicts = Schedule::where('resource_id', $resourceId)
            ->whereIn('time_slot_id', $slotIds)
            ->where('day_of_week', $dayOfWeek)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('time_slot_id');

        // Query 2: Jadwal penting untuk tanggal ini (beserta relasi slot)
        $importants = ImportantSchedule::where('resource_id', $resourceId)
            ->where('date', $date)
            ->with(['startSlot', 'endSlot'])
            ->get();

        // Query 3: Semua booking lain yang konflik di slot yang sama
        $otherBookings = Booking::where('resource_id', $resourceId)
            ->whereIn('time_slot_id', $slotIds)
            ->where('booking_date', $date)
            ->whereNotIn('id', $bookingIds)
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->keyBy('time_slot_id');

        // Query 4: Load semua TimeSlot yang dibutuhkan sekaligus (untuk cek slot_order)
        $timeSlots = TimeSlot::whereIn('id', $slotIds)->get()->keyBy('id');

        // Evaluasi konflik per booking di memory (tanpa query tambahan)
        $results = [];
        foreach ($bookings as $booking) {
            $conflicts  = [];
            $tsId       = $booking->time_slot_id;
            $slotOrder  = $timeSlots->get($tsId)?->slot_order;

            // 1. Cek jadwal rutin
            if ($routine = $routineConflicts->get($tsId)) {
                $conflicts[] = sprintf(
                    'Bentrok jadwal rutin: %s (%s) setiap %s.',
                    $routine->teacher_name,
                    $routine->subject_name ?? '-',
                    $dayOfWeek
                );
            }

            // 2. Cek jadwal penting
            foreach ($importants as $important) {
                if ($important->is_full_day) {
                    $conflicts[] = sprintf(
                        'Tanggal %s diblokir full day untuk: %s (%s).',
                        Carbon::parse($date)->translatedFormat('d M Y'),
                        $important->title,
                        $important->type_label ?? $important->type
                    );
                    break;
                }
                if ($slotOrder !== null) {
                    $startOrder = $important->startSlot?->slot_order ?? PHP_INT_MAX;
                    $endOrder   = $important->endSlot?->slot_order   ?? PHP_INT_MIN;
                    if ($slotOrder >= $startOrder && $slotOrder <= $endOrder) {
                        $conflicts[] = sprintf(
                            'Slot terblokir oleh jadwal penting: %s (%s) — %s s/d %s.',
                            $important->title,
                            $important->type_label ?? $important->type,
                            $important->startSlot?->name ?? '-',
                            $important->endSlot?->name   ?? '-'
                        );
                    }
                }
            }

            // 3. Cek booking lain
            if ($other = $otherBookings->get($tsId)) {
                $conflicts[] = sprintf(
                    'Bentrok booking %s: %s — %s.',
                    $other->status === 'approved' ? 'yang sudah disetujui' : 'pending lain',
                    $other->teacher_name,
                    $other->title ?? '-'
                );
            }

            if (!empty($conflicts)) {
                $results[$booking->id] = $conflicts;
            }
        }

        return $results;
    }

    /**
     * Cek bentrok untuk booking BARU (belum tersimpan).
     * Digunakan saat store dari tabel mingguan.
     */
    public function checkNew(
        int    $resourceId,
        int    $timeSlotId,
        string $bookingDate,
        ?int   $excludeId = null
    ): array {
        return $this->checkAll($resourceId, $timeSlotId, $bookingDate, $excludeId);
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE — core logic
    // ══════════════════════════════════════════════════════════════════

    private function checkAll(
        int    $resourceId,
        int    $timeSlotId,
        string $bookingDate,
        ?int   $excludeId = null
    ): array {
        $conflicts = [];

        if ($msg = $this->checkRoutineSchedule($resourceId, $timeSlotId, $bookingDate)) {
            $conflicts[] = $msg;
        }

        if ($msg = $this->checkImportantSchedule($resourceId, $timeSlotId, $bookingDate)) {
            $conflicts[] = $msg;
        }

        if ($msg = $this->checkOtherBookings($resourceId, $timeSlotId, $bookingDate, $excludeId)) {
            $conflicts[] = $msg;
        }

        return $conflicts;
    }

    // ──────────────────────────────────────────────────────────────────
    // 1. Jadwal rutin
    // ──────────────────────────────────────────────────────────────────

    private function checkRoutineSchedule(
        int    $resourceId,
        int    $timeSlotId,
        string $bookingDate
    ): ?string {
        $dayOfWeek = Carbon::parse($bookingDate)->locale('en')->dayName;
        // Format: 'Monday', 'Tuesday', dst — sesuai kolom day_of_week di tabel schedules

        $conflict = Schedule::where('resource_id',  $resourceId)
            ->where('time_slot_id', $timeSlotId)
            ->where('day_of_week',  $dayOfWeek)
            ->where('status',       'active')
            ->whereNull('deleted_at')
            ->first();

        if (!$conflict) return null;

        return sprintf(
            'Bentrok jadwal rutin: %s (%s) setiap %s.',
            $conflict->teacher_name,
            $conflict->subject_name ?? '-',
            $dayOfWeek
        );
    }

    // ──────────────────────────────────────────────────────────────────
    // 2. Jadwal penting (ImportantSchedule)
    //    Logika: slot booking_date + time_slot_id masuk dalam range
    //    start_slot_id..end_slot_id, atau is_full_day = true
    // ──────────────────────────────────────────────────────────────────

    private function checkImportantSchedule(
        int    $resourceId,
        int    $timeSlotId,
        string $bookingDate
    ): ?string {
        // Ambil semua jadwal penting di resource + tanggal ini
        $importants = ImportantSchedule::where('resource_id', $resourceId)
            ->where('date', $bookingDate)
            ->with(['startSlot', 'endSlot'])
            ->get();

        if ($importants->isEmpty()) return null;

        // Ambil slot_order dari time_slot yang dicek
        $slot = TimeSlot::find($timeSlotId);
        if (!$slot) return null;

        $slotOrder = $slot->slot_order;

        foreach ($importants as $important) {
            // Full day — blokir semua slot
            if ($important->is_full_day) {
                return sprintf(
                    'Tanggal %s diblokir full day untuk: %s (%s).',
                    Carbon::parse($bookingDate)->translatedFormat('d M Y'),
                    $important->title,
                    $important->type_label ?? $important->type
                );
            }

            // Range slot — cek apakah slot_order berada di antara start dan end
            $startOrder = $important->startSlot?->slot_order ?? PHP_INT_MAX;
            $endOrder   = $important->endSlot?->slot_order   ?? PHP_INT_MIN;

            if ($slotOrder >= $startOrder && $slotOrder <= $endOrder) {
                return sprintf(
                    'Slot terblokir oleh jadwal penting: %s (%s) — %s s/d %s.',
                    $important->title,
                    $important->type_label ?? $important->type,
                    $important->startSlot?->name ?? '-',
                    $important->endSlot?->name   ?? '-'
                );
            }
        }

        return null;
    }

    // ──────────────────────────────────────────────────────────────────
    // 3. Booking lain
    // ──────────────────────────────────────────────────────────────────

    private function checkOtherBookings(
        int    $resourceId,
        int    $timeSlotId,
        string $bookingDate,
        ?int   $excludeId = null
    ): ?string {
        $query = Booking::where('resource_id',  $resourceId)
            ->where('time_slot_id', $timeSlotId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status',     ['approved', 'pending']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $conflict = $query->first();

        if (!$conflict) return null;

        return sprintf(
            'Bentrok booking %s: %s — %s.',
            $conflict->status === 'approved' ? 'yang sudah disetujui' : 'pending lain',
            $conflict->teacher_name,
            $conflict->title ?? '-'
        );
    }
}