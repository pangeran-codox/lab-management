<?php

namespace App\Http\Controllers;

use App\Services\Schedule\BookingSubmissionService;
use App\Services\Schedule\ScheduleAvailabilityService;
use App\Services\Schedule\ScheduleQueryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    private array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    private array $dayMapReverse = [
        'Senin'  => 'Monday',
        'Selasa' => 'Tuesday',
        'Rabu'   => 'Wednesday',
        'Kamis'  => 'Thursday',
        'Jumat'  => 'Friday',
        'Sabtu'  => 'Saturday',
        'Minggu' => 'Sunday',
    ];

    public function __construct(
        private ScheduleQueryService        $query,
        private ScheduleAvailabilityService $availability,
        private BookingSubmissionService    $submission,
        private \App\Services\Booking\BookingAccessService $access
    ) {}

    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $weekParam = $request->get('week') ?: session('week');
        $weekStart = $weekParam
            ? Carbon::parse($weekParam)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(6);

        $allowed = $this->access->getAllowedResources();

        // ── Data Master ──
        $resources     = $this->query->getActiveResources($allowed);
        $timeSlots     = $this->query->getActiveTimeSlots();
        $organizations = $this->query->getActiveOrganizations();
        $teachers      = $this->query->getActiveTeachers();
        $resourceIds   = $resources->pluck('id');

        // ── Data Jadwal & Booking ──
        $schedules          = $this->query->getActiveSchedules($resourceIds);
        $bookings           = $this->query->getBookingsForWeek($weekStart, $weekEnd, $resourceIds);
        $sundayBookings     = $this->query->getSundayBookingsForWeek($weekStart, $weekEnd, $resourceIds);
        $importantSchedules = $this->query->getImportantSchedulesForWeek($weekStart, $weekEnd, $resourceIds);

        $weekDates = [];
        foreach ($this->days as $i => $day) {
            $weekDates[$day] = $weekStart->copy()->addDays($i)->toDateString();
        }

        // ── Pre-compute Availability & Metadata ──
        $slotMeta      = $this->availability->getSlotMeta($timeSlots);
        $dateMeta      = $this->availability->getDateMeta($weekDates);
        $slotPastMap   = $this->availability->getSlotPastMap($timeSlots);
        $availabilityData = $this->availability->getTakenSlotsMap(
            $resources, $weekDates, $bookings, $schedules, $importantSchedules, $timeSlots
        );
        $takenSlotsMap = $availabilityData['map'];
        $availCounts   = $availabilityData['avails'];

        $nonBreakSlots   = $timeSlots->where('is_break', false)->values();
        $firstNonBreakId = $nonBreakSlots->first()?->id;
        $sunRowspan      = $timeSlots->count();

        $prevWeek = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek = $weekStart->copy()->addWeek()->toDateString();

        return view('schedule.index', compact(
            'resources', 'timeSlots', 'schedules', 'bookings', 'sundayBookings',
            'weekDates', 'weekStart', 'weekEnd', 'organizations',
            'prevWeek', 'nextWeek', 'teachers',
            'slotMeta', 'dateMeta', 'takenSlotsMap', 'availCounts', 'slotPastMap',
            'firstNonBreakId', 'sunRowspan', 'importantSchedules'
        ))->with([
            'days' => $this->days,
            'dayMapReverse' => $this->dayMapReverse
        ]);
    }

    // POLL ENDPOINT (Deprecated - Using Reverb Realtime)
    public function poll(Request $request)
    {
        return response()->json(['message' => 'Use Reverb instead'], 410);
    }

    // ══════════════════════════════════════════════════════════════════
    // GET CLASSES (AJAX)
    // ══════════════════════════════════════════════════════════════════

    public function getClasses(Request $request)
    {
        $request->validate(['organization_id' => 'required|exists:organizations,id']);
        return response()->json($this->query->getClassesByOrganization($request->organization_id));
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE BOOKING
    // ══════════════════════════════════════════════════════════════════

    public function storeBooking(Request $request)
    {
        // Cek apakah booking publik sedang ditutup
        if (!\App\Models\Setting::isEnabled(\App\Models\Setting::BOOKING_OPEN)) {
            return back()->withErrors(['error' => 'Booking sedang ditutup sementara. Silakan hubungi admin.'])->withInput();
        }

        if ($request->filled('booking_date') && Carbon::parse($request->booking_date)->isSunday()) {
            return back()->withErrors(['error' => 'Booking hari Minggu menggunakan form khusus.'])->withInput();
        }

        $maxDays = (int) \App\Models\Setting::get(\App\Models\Setting::BOOKING_MAX_DAYS, 30);
        $maxDate = now()->addDays($maxDays)->toDateString();
        $request->validate([
            'resource_id'       => 'required|exists:resources,id',
            'time_slot_id'      => 'required|exists:time_slots,id',
            'organization_id'   => 'required|exists:organizations,id',
            'class_id'          => 'required|exists:classes,id',
            'booking_date'      => "required|date|after_or_equal:today|before_or_equal:{$maxDate}",
            'teacher_name'      => 'required|string|max:255',
            'teacher_phone'     => 'required|string|max:50',
            'subject_name'      => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string|max:500',
            'participant_count' => 'required|integer|min:1|max:200',
        ]);

        try {
            $result = $this->submission->submitRegularBooking($request);
            
            $slotInfo = $result['bookedCount'] . ' slot berhasil';
            if ($result['skippedCount'] > 0) {
                $slotInfo .= ", {$result['skippedCount']} slot dilewati (sudah terpakai)";
            }

            return redirect()->back()
                ->with('success', "Booking berhasil diajukan ({$slotInfo})! Teknisi akan segera memprosesnya.")
                ->with('week', $request->get('week'));

        } catch (\RuntimeException $e) {
            // Tampilkan pesan error yang jelas untuk kasus duplikat
            Log::error('Runtime Booking error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            Log::error('General Booking error: ' . $e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memproses booking: ' . $e->getMessage()])->withInput();
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE SUNDAY BOOKING
    // ══════════════════════════════════════════════════════════════════

    public function storeSundayBooking(Request $request)
    {
        // Cek apakah booking publik sedang ditutup
        if (!\App\Models\Setting::isEnabled(\App\Models\Setting::BOOKING_OPEN)) {
            return back()->withErrors(['error' => 'Booking sedang ditutup sementara. Silakan hubungi admin.'])->withInput();
        }

        $maxDays = (int) \App\Models\Setting::get(\App\Models\Setting::BOOKING_MAX_DAYS, 30);
        $maxDate = now()->addDays($maxDays)->toDateString();
        $request->validate([
            'resource_id'       => 'required|exists:resources,id',
            'organization_id'   => 'required|exists:organizations,id',
            'class_id'          => 'required|exists:classes,id',
            'booking_date'      => "required|date|after_or_equal:today|before_or_equal:{$maxDate}",
            'teacher_name'      => 'required|string|max:255',
            'teacher_phone'     => 'required|string|max:50',
            'subject_name'      => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string|max:500',
            'participant_count' => 'required|integer|min:1|max:200',
        ]);

        if (!Carbon::parse($request->booking_date)->isSunday()) {
            return back()->withErrors(['error' => 'Form ini hanya untuk hari Minggu.'])->withInput();
        }

        try {
            $this->submission->submitSundayBooking($request);
            return redirect()->back()
                ->with('success', 'Booking Minggu berhasil diajukan! Teknisi akan segera memprosesnya.')
                ->with('week', $request->get('week'));

        } catch (\RuntimeException $e) {
            // Tampilkan pesan error yang jelas untuk kasus duplikat
            Log::error('Runtime Sunday Booking error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            Log::error('General Sunday Booking error: ' . $e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memproses booking: ' . $e->getMessage()])->withInput();
        }
    }
}
