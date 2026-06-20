<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\LabInventory;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\ImportantSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get all dashboard data.
     */
    public function getDashboardData(?array $allowedResources): array
    {
        $today = today()->toDateString();
        $thisMonth = now()->month;
        $thisYear = now()->year;
        $lastMonthDate = now()->copy()->subMonth();

        // 1. Stats Utama
        $stats = Booking::query()
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->selectRaw("
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN CAST(booking_date AS DATE) = ? AND status IN ('pending', 'approved') THEN 1 END) as today_count,
                COUNT(CASE WHEN CAST(updated_at AS DATE) = ? AND status = 'approved' THEN 1 END) as approved_today_count,
                COUNT(CASE WHEN EXTRACT(MONTH FROM booking_date) = ? AND EXTRACT(YEAR FROM booking_date) = ? THEN 1 END) as this_month_count,
                COUNT(CASE WHEN EXTRACT(MONTH FROM booking_date) = ? AND EXTRACT(YEAR FROM booking_date) = ? THEN 1 END) as last_month_count
            ", [$today, $today, $thisMonth, $thisYear, $lastMonthDate->month, $lastMonthDate->year])
            ->first();

        $totalLab = Resource::where('status', 'active')
            ->when($allowedResources, fn($q) => $q->whereIn('id', $allowedResources))
            ->count();

        $totalSchedule = Schedule::where('status', 'active')
            ->whereNull('deleted_at')
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->count();

        $totalBroken = LabInventory::where('quantity_broken', '>', 0)
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->count();

        // 2. Booking per Hari Minggu Ini
        $weekStart = now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $weekStart->copy()->addDays(6);
        
        $weeklyData = Booking::query()
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->whereBetween('booking_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereIn('status', ['pending', 'approved'])
            ->selectRaw("CAST(booking_date AS DATE) as date, COUNT(*) as count")
            ->groupBy('date')
            ->pluck('count', 'date');

        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $bookingPerDay = [];
        foreach ($dayNames as $i => $name) {
            $date = $weekStart->copy()->addDays($i)->toDateString();
            $bookingPerDay[$name] = $weeklyData[$date] ?? 0;
        }

        // 3. Grafik Bulanan
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();
        $daysInMonth = now()->daysInMonth;

        $monthlyBookingData = Booking::query()
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->whereBetween('booking_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->whereIn('status', ['pending', 'approved']) // Only count valid bookings
            ->selectRaw("CAST(booking_date AS DATE) as date, COUNT(*) as count")
            ->groupBy('date')
            ->pluck('count', 'date');

        $scheduleByDay = Schedule::where('status', 'active')
            ->whereNull('deleted_at')
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->selectRaw('day_of_week, COUNT(*) as count')
            ->groupBy('day_of_week')
            ->pluck('count', 'day_of_week');

        $monthlyLabels = [];
        $monthlyBookings = [];
        $monthlySchedules = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateObj = $monthStart->copy()->addDays($d - 1);
            $dateStr = $dateObj->toDateString();
            $dayName = $dateObj->format('l');

            $monthlyLabels[] = $d;
            $monthlyBookings[] = $monthlyBookingData[$dateStr] ?? 0;
            $monthlySchedules[] = $scheduleByDay[$dayName] ?? 0;
        }

        // 4. Status Distribusi Bulan Ini
        $statusDistribution = Booking::query()
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->whereMonth('booking_date', $thisMonth)
            ->whereYear('booking_date', $thisYear)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        // 5. Lab Realtime Status & Usage
        $currentTime = now()->format('H:i:s');
        $currentDay = now()->format('l');
        $currentSlot = TimeSlot::where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->where('is_active', true)
            ->first();

        $allLabs = Resource::where('status', 'active')
            ->when($allowedResources, fn($q) => $q->whereIn('id', $allowedResources))
            ->orderBy('name')
            ->get();

        $todayActiveBookings = $currentSlot 
            ? Booking::whereDate('booking_date', $today)
                ->where('status', 'approved')
                ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
                ->where('time_slot_id', $currentSlot->id)
                ->with('resource', 'timeSlot')
                ->get()
                ->groupBy('resource_id')
            : collect();

        $activeSchedules = $currentSlot ? Schedule::with('resource', 'timeSlot')
            ->where('day_of_week', $currentDay)
            ->where('time_slot_id', $currentSlot->id)
            ->where('status', 'active')
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->get()
            ->groupBy('resource_id') : collect();

        $labStatuses = $allLabs->map(function($lab) use ($todayActiveBookings, $activeSchedules) {
            $activity = null; $type = null;
            $booking = $todayActiveBookings->get($lab->id)?->first();
            if ($booking) {
                $activity = $booking->title . ' (' . $booking->teacher_name . ')'; $type = 'booking';
            } else {
                $schedule = $activeSchedules->get($lab->id)?->first();
                if ($schedule) { $activity = $schedule->title . ' (' . $schedule->teacher_name . ')'; $type = 'schedule'; }
            }
            return ['lab' => $lab, 'activity' => $activity, 'type' => $type, 'is_occupied' => !empty($activity)];
        });

        // ── Optimasi Lab Usage: Ambil semua data dalam 1 query (Grup by resource_id) ──
        $monthlyUsageCounts = Booking::whereMonth('booking_date', $thisMonth)
            ->whereYear('booking_date', $thisYear)
            ->whereIn('status', ['approved', 'pending'])
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->selectRaw('resource_id, COUNT(*) as count')
            ->groupBy('resource_id')
            ->pluck('count', 'resource_id');

        $workingDays = 22;
        $maxSlots    = $workingDays * 6;

        $labUsage = $allLabs->map(function($lab) use ($monthlyUsageCounts, $maxSlots) {
            $count = $monthlyUsageCounts[$lab->id] ?? 0;
            $pct   = $maxSlots > 0 ? min(100, round($count / $maxSlots * 100)) : 0;
            return ['lab' => $lab, 'count' => $count, 'pct' => $pct];
        })->sortByDesc('pct')->values();

        // 6. Recent Data
        $pendingBookings = Booking::with('resource', 'timeSlot')
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentBookings = Booking::with('resource')
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $importantSchedules = ImportantSchedule::where('date', '>=', $today)
            ->when($allowedResources, fn($q) => $q->whereIn('resource_id', $allowedResources))
            ->orderBy('date')
            ->take(3)
            ->get();

        return compact(
            'stats', 'totalLab', 'totalSchedule', 'totalBroken',
            'bookingPerDay', 'statusDistribution', 'labStatuses',
            'pendingBookings', 'recentBookings', 'importantSchedules',
            'monthlyLabels', 'monthlyBookings', 'monthlySchedules', 'labUsage'
        );
    }
}
