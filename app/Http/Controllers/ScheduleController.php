<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Booking;
use App\Models\SundayBooking;
use App\Models\Organization;
use App\Models\LabClass;
use App\Models\Teacher;
use Carbon\Carbon;

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

    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $weekStart = $request->get('week')
            ? Carbon::parse($request->get('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(6);

        // ── Cache data master yang jarang berubah ──

        $resources = Cache::remember('active_resources', 300, function () {
            return Resource::where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'building', 'capacity', 'status']);
        });

        $timeSlots = Cache::remember('active_time_slots', 3600, function () {
            return TimeSlot::where('is_active', 1)
                ->orderBy('slot_order')
                ->get();
        });

        $organizations = Cache::remember('active_organizations', 3600, function () {
            return Organization::where('is_active', 1)
                ->orderBy('name')
                ->get();
        });

        $teachers = Cache::remember('active_teachers', 300, function () {
            return Teacher::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'phone']);
        });

        $resourceIds = $resources->pluck('id');

        // FIX #3: Cache key menyertakan hash resourceIds agar tidak stale saat resource berubah
        $resourceHash = md5($resourceIds->sort()->implode(','));
        $schedules = Cache::remember("active_schedules_{$resourceHash}", 300, function () use ($resourceIds) {
            return Schedule::with(['labClass'])
                ->where('status', 'active')
                ->whereIn('resource_id', $resourceIds)
                ->get()
                ->groupBy(fn($s) => $s->resource_id . '_' . $s->day_of_week . '_' . $s->time_slot_id);
        });

        // ── Bookings TIDAK di-cache — berubah setiap ada booking baru ──

        $bookings = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->groupBy(fn($b) => $b->resource_id . '_' . Carbon::parse($b->booking_date)->toDateString() . '_' . $b->time_slot_id);

        $sundayBookings = SundayBooking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->groupBy(fn($b) => $b->resource_id . '_' . Carbon::parse($b->booking_date)->toDateString());

        $weekDates = [];
        foreach ($this->days as $i => $day) {
            $weekDates[$day] = $weekStart->copy()->addDays($i)->toDateString();
        }

        // FIX #4: Pre-compute Carbon::parse untuk slot times — hindari 300+ parse di view
        $slotMeta = $timeSlots->mapWithKeys(function ($slot) {
            return [$slot->id => [
                'start' => $slot->start_time ? substr($slot->start_time, 0, 5) : '',
                'end'   => $slot->end_time   ? substr($slot->end_time,   0, 5) : '',
                'time'  => substr($slot->start_time, 0, 5)
                         . ($slot->end_time ? '–' . substr($slot->end_time, 0, 5) : ''),
            ]];
        });

        // FIX #4: Pre-compute date meta — isToday, isPast, formatted per hari
        $dateMeta = collect($weekDates)->mapWithKeys(function ($date, $day) {
            $carbon = Carbon::parse($date);
            return [$day => [
                'date'      => $date,
                'isToday'   => $carbon->isToday(),
                'isPast'    => $carbon->isPast() && !$carbon->isToday(),
                'formatted' => $carbon->translatedFormat('d M Y'),
                'dm'        => $carbon->format('d/m'),
            ]];
        });

        // FIX #1: Pre-compute takenSlotIds per resource+date — hindari O(n²) flatten di view
        // Structure: ['resourceId_date' => [slotId1, slotId2, ...]]
        // Important Schedules — harus didefinisikan SEBELUM loop takenSlotsMap
        $importantSchedules = \App\Models\ImportantSchedule::with(['startSlot', 'endSlot'])
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->get()
            ->groupBy(fn($s) => $s->resource_id . '_' . $s->date->toDateString());

        $takenSlotsMap = [];
        foreach ($resources as $resource) {
            foreach ($weekDates as $day => $date) {
                $dayEn = $this->dayMapReverse[$day];
                $key   = $resource->id . '_' . $date;

                // Slot dari booking aktif
                $bookedIds = $bookings->filter(function ($group, $groupKey) use ($resource, $date) {
                    return str_starts_with($groupKey, $resource->id . '_' . $date . '_');
                })->keys()->map(fn($k) => (int) explode('_', $k)[2])->toArray();

                // Slot dari jadwal tetap
                $scheduledIds = $timeSlots->filter(function ($ts) use ($schedules, $resource, $dayEn) {
                    return !($ts->is_break ?? false)
                        && $schedules->has($resource->id . '_' . $dayEn . '_' . $ts->id);
                })->pluck('id')->map(fn($id) => (int) $id)->toArray();

                // Slot dari jadwal penting
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

       // FIX #4: Pre-compute isSlotPast per slot (hanya untuk hari ini)
        $today = Carbon::today();
        $slotPastMap = $timeSlots->mapWithKeys(function ($slot) use ($today) {
            $slotTime = Carbon::parse($slot->start_time)->setDateFrom($today);
            return [$slot->id => $slotTime->isPast()];
        });

        // Pre-compute nonBreakSlots sekali saja
        $nonBreakSlots   = $timeSlots->where('is_break', false)->values();
        $firstNonBreakId = $nonBreakSlots->first()?->id;
        $sunRowspan      = $timeSlots->count();

        $prevWeek = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek = $weekStart->copy()->addWeek()->toDateString();

        return view('schedule.index', compact(
            'resources', 'timeSlots', 'schedules', 'bookings', 'sundayBookings',
            'weekDates', 'weekStart', 'weekEnd', 'organizations',
            'prevWeek', 'nextWeek', 'teachers',
            'slotMeta', 'dateMeta', 'takenSlotsMap', 'slotPastMap',
            'firstNonBreakId', 'sunRowspan', 'importantSchedules'
        ))->with('days', $this->days)->with('dayMapReverse', $this->dayMapReverse);
    }

    // ══════════════════════════════════════════════════════════════════
    // POLL ENDPOINT — ringan, hanya return hash + data booking terbaru
    // Dipanggil oleh schedule.js setiap 30 detik
    // ══════════════════════════════════════════════════════════════════

    public function poll(Request $request)
    {
        $weekStart = $request->get('week')
            ? Carbon::parse($request->get('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(6);

        $resourceIds = Cache::remember('active_resources', 300, function () {
            return Resource::where('status', 'active')->orderBy('name')->get(['id', 'name', 'building', 'capacity', 'status']);
        })->pluck('id');

        // Hanya query booking — data yang berubah
        $bookings = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get(['id', 'resource_id', 'time_slot_id', 'booking_date', 'status',
                   'teacher_name', 'class_name', 'subject_name', 'title',
                   'description', 'participant_count', 'teacher_phone']);

        $sundayBookings = SundayBooking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('resource_id', $resourceIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get(['id', 'resource_id', 'booking_date', 'status',
                   'teacher_name', 'class_name', 'subject_name', 'title',
                   'description', 'participant_count', 'teacher_phone']);

        // Hash sederhana untuk deteksi perubahan di client
        $hash = md5($bookings->count() . '_' . $bookings->max('updated_at') . '_' . $sundayBookings->count());

        return response()->json([
            'hash'           => $hash,
            'bookings'       => $bookings,
            'sundayBookings' => $sundayBookings,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // GET CLASSES (AJAX)
    // ══════════════════════════════════════════════════════════════════

    public function getClasses(Request $request)
    {
        $request->validate(['organization_id' => 'required|exists:organizations,id']);

        $classes = Cache::remember('classes_org_' . $request->organization_id, 3600, function () use ($request) {
            return LabClass::where('organization_id', $request->organization_id)
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name']);
        });

        return response()->json($classes);
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE BOOKING
    // ══════════════════════════════════════════════════════════════════

    public function storeBooking(Request $request)
    {
        if ($request->filled('booking_date')) {
            if (Carbon::parse($request->booking_date)->format('l') === 'Sunday') {
                return back()->withErrors(['error' => 'Booking hari Minggu menggunakan form khusus.'])->withInput();
            }
        }

        $maxDate = now()->addWeek()->toDateString();

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

        $dayEn        = Carbon::parse($request->booking_date)->format('l');
        $teacherName  = trim(ucwords(strtolower($request->teacher_name)));
        $teacherPhone = $this->normalizePhone($request->teacher_phone);
        $allSlotIds   = $this->resolveSlotIds($request);
        $sessionId    = (string) Str::uuid();

        try {
            $result = DB::transaction(function () use ($request, $dayEn, $teacherName, $teacherPhone, $allSlotIds, $sessionId) {

                $takenBookingSlots = Booking::where('resource_id', $request->resource_id)
                    ->where('booking_date', $request->booking_date)
                    ->whereIn('status', ['pending', 'approved'])
                    ->whereIn('time_slot_id', $allSlotIds)
                    ->lockForUpdate()
                    ->pluck('time_slot_id')
                    ->toArray();

                $takenScheduleSlots = Schedule::where('resource_id', $request->resource_id)
                    ->where('day_of_week', $dayEn)
                    ->where('status', 'active')
                    ->whereIn('time_slot_id', $allSlotIds)
                    ->pluck('time_slot_id')
                    ->toArray();

                $takenSlotIds = array_unique(array_merge($takenBookingSlots, $takenScheduleSlots));

                if (in_array((int) $request->time_slot_id, $takenSlotIds)) {
                    throw new \Exception('Slot ini sudah dibooking atau ada jadwal tetap.');
                }

                $labClass = LabClass::findOrFail($request->class_id);
                $teacher  = $this->upsertTeacher($teacherName, $teacherPhone);

                $bookingData = [
                    'session_id'        => $sessionId,
                    'resource_id'       => $request->resource_id,
                    'organization_id'   => $request->organization_id,
                    'teacher_id'        => $teacher->id,
                    'booking_date'      => $request->booking_date,
                    'teacher_name'      => $teacherName,
                    'teacher_phone'     => $teacherPhone,
                    'class_name'        => $labClass->name,
                    'subject_name'      => $request->subject_name,
                    'title'             => $request->title,
                    'description'       => $request->description,
                    'participant_count' => $request->participant_count,
                    'status'            => 'pending',
                ];

                $bookedSlots  = [];
                $skippedCount = 0;

                foreach ($allSlotIds as $slotId) {
                    if (in_array((int) $slotId, $takenSlotIds)) {
                        $skippedCount++;
                        continue;
                    }
                    $bookedSlots[] = Booking::create(array_merge($bookingData, ['time_slot_id' => $slotId]));
                }

                // Clear cache teacher setelah upsert
                Cache::forget('active_teachers');

                return [
                    'bookedSlots'  => $bookedSlots,
                    'bookedCount'  => count($bookedSlots),
                    'skippedCount' => $skippedCount,
                    'labClass'     => $labClass,
                    'teacher'      => $teacher,
                ];
            });

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        if ($result['bookedCount'] > 0) {
            $this->sendBookingNotification(
                $request,
                $teacherName,
                $teacherPhone,
                $result['labClass'],
                $result['bookedSlots'],
                $sessionId
            );
        }

        $slotInfo = $result['bookedCount'] . ' slot berhasil';
        if ($result['skippedCount'] > 0) {
            $slotInfo .= ", {$result['skippedCount']} slot dilewati (sudah terpakai)";
        }

        return redirect()->back()
            ->with('success', "Booking berhasil diajukan ({$slotInfo})! Admin akan segera menghubungi via WhatsApp jika disetujui.")
            ->with('week', $request->get('week'));
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE SUNDAY BOOKING
    // ══════════════════════════════════════════════════════════════════

    public function storeSundayBooking(Request $request)
    {
        $maxDate = now()->addWeek()->toDateString();

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

        if (Carbon::parse($request->booking_date)->format('l') !== 'Sunday') {
            return back()->withErrors(['error' => 'Form ini hanya untuk hari Minggu.'])->withInput();
        }

        $teacherName  = trim(ucwords(strtolower($request->teacher_name)));
        $teacherPhone = $this->normalizePhone($request->teacher_phone);

        try {
            $sundayBooking = DB::transaction(function () use ($request, $teacherName, $teacherPhone) {

                $exists = SundayBooking::where('resource_id', $request->resource_id)
                    ->where('booking_date', $request->booking_date)
                    ->whereIn('status', ['pending', 'approved'])
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    throw new \Exception('Lab ini sudah ada booking di hari Minggu tersebut.');
                }

                $labClass = LabClass::findOrFail($request->class_id);
                $teacher  = $this->upsertTeacher($teacherName, $teacherPhone);

                // Clear cache teacher setelah upsert
                Cache::forget('active_teachers');

                return [
                    'booking'  => SundayBooking::create([
                        'teacher_id'        => $teacher->id,
                        'resource_id'       => $request->resource_id,
                        'organization_id'   => $request->organization_id,
                        'booking_date'      => $request->booking_date,
                        'teacher_name'      => $teacherName,
                        'teacher_phone'     => $teacherPhone,
                        'class_name'        => $labClass->name,
                        'subject_name'      => $request->subject_name,
                        'title'             => $request->title,
                        'description'       => $request->description,
                        'participant_count' => $request->participant_count,
                        'status'            => 'pending',
                    ]),
                    'labClass' => $labClass,
                    'lab'      => Resource::find($request->resource_id),
                ];
            });

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        $this->sendSundayBookingNotification(
            $request, $teacherName, $teacherPhone,
            $sundayBooking['booking'],
            $sundayBooking['labClass'],
            $sundayBooking['lab']
        );

        return redirect()->back()
            ->with('success', 'Booking Minggu berhasil diajukan! Admin akan segera menghubungi via WhatsApp jika disetujui.')
            ->with('week', $request->get('week'));
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        if (str_starts_with($phone, '0')) return '62' . substr($phone, 1);
        if (str_starts_with($phone, '+')) return ltrim($phone, '+');
        return $phone;
    }

    private function upsertTeacher(string $name, string $phone): Teacher
    {
        $teacher = Teacher::firstOrCreate(
            ['name' => $name],
            [
                'phone'     => $phone,
                'token'     => Teacher::generateUniqueToken(),
                'is_active' => true,
            ]
        );

        if ($teacher->phone !== $phone && $phone) {
            $teacher->update(['phone' => $phone]);
        }

        return $teacher;
    }

    private function resolveSlotIds(Request $request): array
    {
        $allSlotIds = [(int) $request->time_slot_id];

        if ($request->filled('extra_slot_ids')) {
            $extraIds   = is_array($request->extra_slot_ids)
                ? $request->extra_slot_ids
                : explode(',', $request->extra_slot_ids);
            $allSlotIds = array_merge($allSlotIds, array_map('intval', $extraIds));
        }

        return array_values(array_unique(array_filter($allSlotIds)));
    }

    private function sendBookingNotification(
        Request $request,
        string $teacherName,
        string $teacherPhone,
        LabClass $labClass,
        array $bookedSlots,
        string $sessionId
    ): void {
        try {
            $slotIds    = array_map(fn($b) => $b->time_slot_id, $bookedSlots);
            $timeSlots  = TimeSlot::whereIn('id', $slotIds)->orderBy('slot_order')->get()->keyBy('id');
            $bookingIds = array_map(fn($b) => $b->id, $bookedSlots);

            $slotTimes = collect($slotIds)->map(function ($slotId) use ($timeSlots) {
                $ts = $timeSlots->get($slotId);
                return $ts ? substr($ts->start_time, 0, 5) . '-' . substr($ts->end_time, 0, 5) : null;
            })->filter()->join(', ');

            $slotNames = collect($slotIds)->map(fn($id) => $timeSlots->get($id)?->name)->filter();
            $slotRange = $slotNames->count() > 1
                ? $slotNames->first() . ' – ' . $slotNames->last()
                : $slotNames->first();

            $lab = Resource::find($request->resource_id);

            Http::timeout(3)->post(config('mikrotik.bot_url') . '/api/webhook/lab-session', [
                'event'             => 'booking_pending',
                'session_id'        => $sessionId,
                'booking_ids'       => $bookingIds,
                'teacher_name'      => $teacherName,
                'teacher_phone'     => $teacherPhone,
                'lab_name'          => $lab->name ?? '-',
                'booking_date'      => Carbon::parse($request->booking_date)->translatedFormat('l, d M Y'),
                'slot_range'        => $slotRange,
                'slot_times'        => $slotTimes,
                'class_name'        => $labClass->name,
                'subject_name'      => $request->subject_name,
                'title'             => $request->title,
                'participant_count' => $request->participant_count,
                'total_slots'       => count($bookedSlots),
            ]);

        } catch (\Exception $e) {
            Log::warning('WA booking notification failed: ' . $e->getMessage());
        }
    }

    private function sendSundayBookingNotification(
        Request $request,
        string $teacherName,
        string $teacherPhone,
        SundayBooking $booking,
        \App\Models\LabClass $labClass,
        \App\Models\Resource $lab
    ): void {
        try {
            Http::timeout(3)->post(config('mikrotik.bot_url') . '/api/webhook/lab-session', [
                'event'             => 'booking_pending',
                'session_id'        => null,
                'booking_ids'       => [$booking->id],
                'teacher_name'      => $teacherName,
                'teacher_phone'     => $teacherPhone,
                'lab_name'          => $lab->name ?? '-',
                'booking_date'      => Carbon::parse($request->booking_date)->translatedFormat('l, d M Y'),
                'slot_range'        => 'Seharian',
                'slot_times'        => '07:00-12:45',
                'class_name'        => $labClass->name ?? '-',
                'subject_name'      => $request->subject_name,
                'title'             => $request->title,
                'participant_count' => $request->participant_count,
                'total_slots'       => 1,
                'is_sunday'         => true,
            ]);

        } catch (\Exception $e) {
            Log::warning('WA Sunday booking notification failed: ' . $e->getMessage());
        }
    }
}