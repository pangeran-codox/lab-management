<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RekapService
{
    private array $dayMap = [
        'Monday'    => 1, 'Tuesday' => 2, 'Wednesday' => 3,
        'Thursday'  => 4, 'Friday'  => 5, 'Saturday'  => 6, 'Sunday' => 0,
    ];

    private array $dayNameId = [
        'Monday'    => 'Senin',   'Tuesday'  => 'Selasa', 'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',   'Friday'   => 'Jumat',  'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];

    public function getMonthlyRekap(int $month, int $year): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();
        $totalDays = $startDate->daysInMonth;

        $resources = $this->getActiveResources();
        $timeSlots = $this->getNonBreakTimeSlots();
        $resourceIds = $resources->pluck('id')->toArray();

        $allSchedules = $this->getSchedulesByResources($resourceIds);
        $allBookings  = $this->getBookingsByRange($startDate, $endDate, $resourceIds);

        $dayOccurrences = $this->calculateDayOccurrences($year, $month, $totalDays);
        $totalSlotPerDay = $timeSlots->count();

        $labData = [];
        foreach ($resources as $resource) {
            $schedules = $allSchedules->get($resource->id, collect());
            $bookings  = $allBookings->get($resource->id, collect());

            $mappedSchedules = $this->mapScheduleDetails($schedules, $dayOccurrences);
            $scheduledSlots  = $mappedSchedules->sum('occurrences');
            $bookingSlots    = $bookings->count();
            
            $totalCapacity = $totalDays * $totalSlotPerDay;
            $totalUsed     = $scheduledSlots + $bookingSlots;
            
            $labData[] = [
                'resource'        => $resource,
                'totalCapacity'   => $totalCapacity,
                'scheduledSlots'  => $scheduledSlots,
                'bookingSlots'    => $bookingSlots,
                'totalUsed'       => $totalUsed,
                'totalFree'       => max(0, $totalCapacity - $totalUsed),
                'percentage'      => $totalCapacity > 0 ? round(($totalUsed / $totalCapacity) * 100, 1) : 0,
                'dailyData'       => $this->generateDailyData($year, $month, $totalDays, $schedules, $bookings, $totalSlotPerDay),
                'scheduleDetails' => $mappedSchedules,
                'bookingDetails'  => $bookings,
            ];
        }

        return [
            'labData'   => $labData,
            'summary'   => $this->calculateSummary($labData),
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'totalSlotPerDay' => $totalSlotPerDay
        ];
    }

    private function getActiveResources(): Collection
    {
        return Cache::remember('active_resources', 300, fn() =>
            Resource::where('status', 'active')->orderBy('name')->get(['id', 'name', 'building', 'capacity', 'status'])
        );
    }

    private function getNonBreakTimeSlots(): Collection
    {
        return Cache::remember('active_time_slots_nonbreak', 3600, fn() =>
            TimeSlot::where('is_active', 1)->where('is_break', 0)->orderBy('slot_order')->get()
        );
    }

    private function getSchedulesByResources(array $resourceIds): Collection
    {
        return Cache::remember('active_schedules', 300, fn() =>
            Schedule::whereIn('resource_id', $resourceIds)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->with(['timeSlot', 'labClass'])
                ->get()
                ->groupBy('resource_id')
        );
    }

    private function getBookingsByRange(Carbon $start, Carbon $end, array $resourceIds): Collection
    {
        return Booking::whereIn('resource_id', $resourceIds)
            ->whereBetween('booking_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'approved')
            ->with('timeSlot')
            ->orderBy('booking_date')
            ->get()
            ->groupBy('resource_id');
    }

    private function calculateDayOccurrences(int $year, int $month, int $totalDays): array
    {
        $occurrences = [];
        for ($d = 1; $d <= $totalDays; $d++) {
            $dayName = Carbon::create($year, $month, $d)->format('l');
            $occurrences[$dayName] = ($occurrences[$dayName] ?? 0) + 1;
        }
        return $occurrences;
    }

    private function mapScheduleDetails(Collection $schedules, array $dayOccurrences): Collection
    {
        return $schedules->map(function ($sch) use ($dayOccurrences) {
            $sch->occurrences = $dayOccurrences[$sch->day_of_week] ?? 0;
            $sch->day_name_id = $this->dayNameId[$sch->day_of_week] ?? $sch->day_of_week;
            return $sch;
        })->filter(fn($s) => $s->occurrences > 0)
          ->sortBy(fn($s) => $this->dayMap[$s->day_of_week] ?? 9);
    }

    private function generateDailyData(int $year, int $month, int $totalDays, Collection $schedules, Collection $bookings, int $capacity): array
    {
        $bookingByDate = $bookings->groupBy('booking_date');
        $scheduleByDay = $schedules->groupBy('day_of_week');
        $dailyData = [];

        for ($d = 1; $d <= $totalDays; $d++) {
            $date    = Carbon::create($year, $month, $d);
            $dayName = $date->format('l');
            $dateStr = $date->toDateString();

            $schedCount = $scheduleByDay->get($dayName, collect())->count();
            $bookCount  = $bookingByDate->get($dateStr, collect())->count();

            $dailyData[] = [
                'date'     => $date,
                'schedule' => $schedCount,
                'booking'  => $bookCount,
                'total'    => $schedCount + $bookCount,
                'capacity' => $capacity,
                'isSunday' => $date->dayOfWeek === 0,
                'isToday'  => $date->isToday(),
            ];
        }
        return $dailyData;
    }

    private function calculateSummary(array $labData): array
    {
        $labCollection = collect($labData);
        $summary = [
            'total_capacity'  => $labCollection->sum('totalCapacity'),
            'total_scheduled' => $labCollection->sum('scheduledSlots'),
            'total_booking'   => $labCollection->sum('bookingSlots'),
            'total_used'      => $labCollection->sum('totalUsed'),
        ];
        $summary['total_pct'] = $summary['total_capacity'] > 0
            ? round(($summary['total_used'] / $summary['total_capacity']) * 100, 1) : 0;
            
        return $summary;
    }
}
