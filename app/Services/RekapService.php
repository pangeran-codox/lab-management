<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Organization;
use App\Models\LabClass;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        // Gunakan cache untuk rekap per bulan
        return Cache::remember("rekap_monthly_{$month}_{$year}", 3600, function () use ($month, $year) {
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
            
            // Hitung total penggunaan guru dari schedule + booking
            $teacherUsage = [];
            
            // Tambah dari jadwal tetap
            foreach ($mappedSchedules as $sch) {
                if (!empty($sch->teacher_name)) {
                    $name = trim($sch->teacher_name);
                    if (!isset($teacherUsage[$name])) {
                        $teacherUsage[$name] = 0;
                    }
                    $teacherUsage[$name] += $sch->occurrences;
                }
            }
            
            // Tambah dari booking
            foreach ($bookings as $book) {
                if (!empty($book->teacher_name)) {
                    $name = trim($book->teacher_name);
                    if (!isset($teacherUsage[$name])) {
                        $teacherUsage[$name] = 0;
                    }
                    $teacherUsage[$name] += 1;
                }
            }
            
            // Urutkan dari yang terbanyak
            arsort($teacherUsage);
            
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
                'teacherUsage'    => $teacherUsage,
            ];
        }

        // ── Hitung penggunaan lembaga ────────────────────────────────────
        // Sumber 1: Jadwal tetap → lembaga dari classes.organization_id
        //           karena jadwal tetap selalu milik kelas dari lembaga itu sendiri
        // Sumber 2: Booking → lembaga dari bookings.organization_id
        //           karena semua sekolah bisa pakai lab manapun

        // Load semua organizations sekali untuk lookup nama
        $organizations = Cache::remember('all_organizations', 3600, fn() =>
            Organization::pluck('name', 'id')->toArray()
        );

        // Load mapping class_id → organization_id sekali (dari tabel classes)
        $classOrgMap = Cache::remember('class_org_map', 3600, fn() =>
            DB::table('classes')->pluck('organization_id', 'id')->toArray()
        );

        $lembagaUsage = [];

        // Sumber 1: Jadwal tetap — hitung per organization via class_id
        foreach ($labData as $lab) {
            foreach ($lab['scheduleDetails'] as $sch) {
                $orgId   = $classOrgMap[$sch->class_id] ?? null;
                $orgName = $orgId ? ($organizations[$orgId] ?? 'Tidak Ada Lembaga') : 'Tidak Ada Lembaga';

                if (!isset($lembagaUsage[$orgName])) {
                    $lembagaUsage[$orgName] = [
                        'scheduledSlots' => 0,
                        'bookingSlots'   => 0,
                        'sessionCount'   => 0,
                        'teacherUsage'   => [],
                    ];
                }

                $lembagaUsage[$orgName]['scheduledSlots'] += $sch->occurrences;
                $lembagaUsage[$orgName]['sessionCount']   += $sch->occurrences;

                if (!empty($sch->teacher_name)) {
                    $name = trim($sch->teacher_name);
                    $lembagaUsage[$orgName]['teacherUsage'][$name] =
                        ($lembagaUsage[$orgName]['teacherUsage'][$name] ?? 0) + $sch->occurrences;
                }
            }
        }

        // Sumber 2: Booking — hitung per organization_id dari booking
        foreach ($labData as $lab) {
            foreach ($lab['bookingDetails'] as $book) {
                $orgId   = $book->organization_id ?? null;
                $orgName = $orgId ? ($organizations[$orgId] ?? 'Tidak Ada Lembaga') : 'Tidak Ada Lembaga';

                if (!isset($lembagaUsage[$orgName])) {
                    $lembagaUsage[$orgName] = [
                        'scheduledSlots' => 0,
                        'bookingSlots'   => 0,
                        'sessionCount'   => 0,
                        'teacherUsage'   => [],
                    ];
                }

                $lembagaUsage[$orgName]['bookingSlots'] += 1;
                $lembagaUsage[$orgName]['sessionCount'] += 1;

                if (!empty($book->teacher_name)) {
                    $name = trim($book->teacher_name);
                    $lembagaUsage[$orgName]['teacherUsage'][$name] =
                        ($lembagaUsage[$orgName]['teacherUsage'][$name] ?? 0) + 1;
                }
            }
        }

        // Urutkan berdasarkan sessionCount terbanyak
        uasort($lembagaUsage, fn($a, $b) => $b['sessionCount'] <=> $a['sessionCount']);

        // Urutkan teacherUsage di setiap lembaga
        foreach ($lembagaUsage as &$lembaga) {
            arsort($lembaga['teacherUsage']);
        }
        unset($lembaga);
        
            return [
                'labData'   => $labData,
                'summary'   => $this->calculateSummary($labData),
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'totalSlotPerDay' => $totalSlotPerDay,
                'lembagaUsage' => $lembagaUsage
            ];
        });
    }

    private function getActiveResources(): Collection
    {
        // Cache key berbeda dari ScheduleQueryService karena include 'organization' relation
        return Cache::remember('rekap_active_resources', 300, fn() =>
            Resource::with('organization')->where('status', 'active')->orderBy('name')->get(['id', 'name', 'building', 'capacity', 'status', 'organization_id'])
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
        return Schedule::whereIn('resource_id', $resourceIds)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->with(['timeSlot', 'labClass'])
            ->get()
            ->groupBy('resource_id');
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
