<?php

namespace App\Services\Schedule;

use App\Models\Booking;
use App\Models\ImportantSchedule;
use App\Models\Organization;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\SundayBooking;
use App\Models\Teacher;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ScheduleQueryService
{
    // ── TTL constants ──────────────────────────────────────────────
    private const TTL_RESOURCES     = 300;
    private const TTL_TIME_SLOTS    = 3600;
    private const TTL_ORGANIZATIONS = 3600;
    private const TTL_TEACHERS      = 300;
    private const TTL_SCHEDULES     = 300;
    private const TTL_CLASSES       = 3600;

    // ══════════════════════════════════════════════════════════════
    // MASTER DATA — cached, jarang berubah
    // ══════════════════════════════════════════════════════════════

    public function getActiveResources(?array $allowedResources = null): Collection
    {
        $cacheKey = $allowedResources ? 'active_resources_' . md5(serialize($allowedResources)) : 'active_resources';
        return Cache::remember($cacheKey, self::TTL_RESOURCES, fn () =>
            Resource::where('status', 'active')
                ->when($allowedResources, fn($q) => $q->whereIn('id', $allowedResources))
                ->orderBy('name')
                ->get(['id', 'name', 'building', 'capacity', 'status'])
        );
    }

    public function getActiveTimeSlots(): Collection
    {
        return Cache::remember('active_time_slots', self::TTL_TIME_SLOTS, fn () =>
            TimeSlot::where('is_active', 1)
                ->orderBy('slot_order')
                ->get()
        );
    }

    public function getActiveOrganizations(): Collection
    {
        return Cache::remember('active_organizations', self::TTL_ORGANIZATIONS, fn () =>
            Organization::where('is_active', 1)
                ->orderBy('name')
                ->get()
        );
    }

    public function getActiveTeachers(): Collection
    {
        return Cache::remember('active_teachers', self::TTL_TEACHERS, fn () =>
            Teacher::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'phone'])
        );
    }

    /**
     * Jadwal tetap per resource, di-group by "resourceId_dayOfWeek_slotId".
     * Cache key menyertakan hash resourceIds agar otomatis invalid saat resource berubah.
     */
    public function getActiveSchedules(Collection $resourceIds): Collection
    {
        $resourceHash = md5($resourceIds->sort()->implode(','));

        return Cache::remember("active_schedules_{$resourceHash}", self::TTL_SCHEDULES, fn () =>
            Schedule::with(['labClass'])
                ->where('status', 'active')
                ->whereIn('resource_id', $resourceIds)
                ->get()
                ->groupBy(fn ($s) => $s->resource_id . '_' . $s->day_of_week . '_' . $s->time_slot_id)
        );
    }

    /**
     * Classes per organisasi — cached per org_id.
     */
    public function getClassesByOrganization(int $organizationId): Collection
    {
        return Cache::remember("classes_org_{$organizationId}", self::TTL_CLASSES, fn () =>
            \App\Models\LabClass::where('organization_id', $organizationId)
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    // ══════════════════════════════════════════════════════════════
    // BOOKING DATA — TIDAK di-cache (berubah setiap ada booking)
    // ══════════════════════════════════════════════════════════════

    /**
     * Booking aktif dalam rentang tanggal, di-group by "resourceId_date_slotId".
     * Dioptimalkan: Menggunakan select spesifik dan raw grouping key.
     */
    public function getBookingsForWeek(
        Carbon $weekStart,
        Carbon $weekEnd,
        Collection $resourceIds
    ): Collection {
        return Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->active()
            ->get(['id', 'resource_id', 'booking_date', 'time_slot_id', 'status', 'teacher_name', 'title', 'class_name', 'subject_name', 'description', 'participant_count', 'teacher_phone'])
            ->groupBy(fn ($b) => $b->resource_id . '_' . $b->booking_date->toDateString() . '_' . $b->time_slot_id);
    }

    /**
     * Sunday booking aktif dalam rentang tanggal, di-group by "resourceId_date".
     */
    public function getSundayBookingsForWeek(
        Carbon $weekStart,
        Carbon $weekEnd,
        Collection $resourceIds
    ): Collection {
        return SundayBooking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->active()
            ->get(['id', 'resource_id', 'booking_date', 'status', 'teacher_name', 'title', 'class_name', 'subject_name', 'description', 'participant_count', 'teacher_phone'])
            ->groupBy(fn ($b) => $b->resource_id . '_' . $b->booking_date->toDateString());
    }

    /**
     * Important schedules dalam rentang tanggal, di-group by "resourceId_date".
     */
    public function getImportantSchedulesForWeek(
        Carbon $weekStart,
        Carbon $weekEnd,
        Collection $resourceIds
    ): Collection {
        return ImportantSchedule::with(['startSlot', 'endSlot'])
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->get()
            ->groupBy(fn ($s) => $s->resource_id . '_' . $s->date->toDateString());
    }

    /**
     * Versi ringan untuk endpoint poll — hanya field yang dibutuhkan JS client.
     */
    public function getBookingsForPoll(
        Carbon $weekStart,
        Carbon $weekEnd,
        Collection $resourceIds
    ): Collection {
        return Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get([
                'id', 'resource_id', 'time_slot_id', 'booking_date', 'status',
                'teacher_name', 'class_name', 'subject_name', 'title',
                'description', 'participant_count', 'teacher_phone', 'updated_at',
            ]);
    }

    public function getSundayBookingsForPoll(
        Carbon $weekStart,
        Carbon $weekEnd,
        Collection $resourceIds
    ): Collection {
        return SundayBooking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get([
                'id', 'resource_id', 'booking_date', 'status',
                'teacher_name', 'class_name', 'subject_name', 'title',
                'description', 'participant_count', 'teacher_phone', 'updated_at',
            ]);
    }

    // ══════════════════════════════════════════════════════════════
    // CACHE INVALIDATION
    // ══════════════════════════════════════════════════════════════

    public function forgetTeachersCache(): void
    {
        Cache::forget('active_teachers');
    }

    public function forgetResourcesCache(): void
    {
        Cache::forget('active_resources');
    }

    public function forgetClassesCache(int $organizationId): void
    {
        Cache::forget("classes_org_{$organizationId}");
    }
}