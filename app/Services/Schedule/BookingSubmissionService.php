<?php

namespace App\Services\Schedule;

use App\Events\BookingCreated;
use App\Events\ScheduleUpdated;
use App\Models\Booking;
use App\Models\LabClass;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\SundayBooking;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingSubmissionService
{
    public function __construct(
        private WhatsAppService $waService
    ) {}
    public function submitRegularBooking(Request $request): array
    {
        $dayEn        = Carbon::parse($request->booking_date)->format('l');
        $teacherName  = trim(ucwords(strtolower($request->teacher_name)));
        $teacherPhone = $this->normalizePhone($request->teacher_phone);
        $allSlotIds   = $this->resolveSlotIds($request);
        $sessionId    = (string) Str::uuid();

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

            Cache::forget('active_teachers');

            return [
                'bookedCount'  => count($bookedSlots),
                'skippedCount' => $skippedCount,
                'bookedSlots'  => $bookedSlots,
            ];
        });

        // Broadcast perubahan via Reverb DI LUAR transaction
        if (count($result['bookedSlots']) > 0) {
            $first = $result['bookedSlots'][0];
            $labClass = LabClass::where('name', $first->class_name)->first();
            $lab = Resource::find($request->resource_id);

            broadcast(new ScheduleUpdated('regular', 'created', [
                'resource_id'  => $first->resource_id,
                'booking_date' => $first->booking_date->toDateString(),
                'status'       => 'pending'
            ]));

            // Notifikasi Admin Real-time
            broadcast(new BookingCreated($first));

            // Kirim notifikasi ke teknisi untuk booking pending (hanya dari Laravel)
            $this->notifyTeknisiBookingPending(
                $request->resource_id,
                $request->booking_date,
                $teacherName,
                $lab?->name ?? 'Lab',
                $result['bookedSlots'],
                $request->subject_name,
                $request->title,
                $request->participant_count
            );
        }

        return [
            'bookedCount'  => $result['bookedCount'],
            'skippedCount' => $result['skippedCount'],
        ];
    }

    public function submitSundayBooking(Request $request): void
    {
        $teacherName  = trim(ucwords(strtolower($request->teacher_name)));
        $teacherPhone = $this->normalizePhone($request->teacher_phone);

        $booking = DB::transaction(function () use ($request, $teacherName, $teacherPhone) {

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

            Cache::forget('active_teachers');

            return SundayBooking::create([
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
            ]);
        });

        // Broadcast DI LUAR transaction
        broadcast(new ScheduleUpdated('sunday', 'created', [
            'resource_id'  => $booking->resource_id,
            'booking_date' => Carbon::parse($booking->booking_date)->toDateString(),
            'status'       => $booking->status
        ]));

        // Notifikasi Admin Real-time
        broadcast(new BookingCreated($booking));

        $labClass = LabClass::where('name', $booking->class_name)->first(); // Re-fetch for notification
        $lab = Resource::find($request->resource_id);

        // Kirim notifikasi ke teknisi untuk Sunday booking pending (hanya dari Laravel)
        $this->notifyTeknisiBookingPending(
            $request->resource_id,
            $request->booking_date,
            $teacherName,
            $lab?->name ?? 'Lab',
            [$booking],
            $request->subject_name,
            $request->title,
            $request->participant_count,
            true
        );
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        if (str_starts_with($phone, '0')) return '62' . substr($phone, 1);
        if (str_starts_with($phone, '+')) return ltrim($phone, '+');
        return $phone;
    }

    private function upsertTeacher(string $name, string $phone): Teacher
    {
        // First try to find by phone if provided
        if ($phone) {
            $teacher = Teacher::where('phone', $phone)->first();
            if ($teacher) {
                // Jika nama beda jauh, tolak
                if (strtolower(trim($teacher->name)) !== strtolower(trim($name))) {
                    throw new \RuntimeException(
                        "Nomor HP '{$phone}' sudah terdaftar untuk guru lain ({$teacher->name}). " .
                        "Silakan gunakan nama yang sama atau gunakan nomor HP lain."
                    );
                }
                return $teacher;
            }
        }

        // Then try to find by name
        $teacher = Teacher::where('name', $name)->first();
        if ($teacher) {
            // Update phone if needed
            if ($teacher->phone !== $phone && $phone) {
                try {
                    $teacher->update(['phone' => $phone]);
                } catch (\Exception $e) {
                    // If phone already exists, throw error
                    throw new \RuntimeException(
                        "Nomor HP '{$phone}' sudah terdaftar untuk guru lain. " .
                        "Silakan gunakan nomor HP lain."
                    );
                }
            }
            return $teacher;
        }

        // Otherwise create new teacher
        try {
            return Teacher::create([
                'name'      => $name,
                'phone'     => $phone,
                'token'     => Teacher::generateUniqueToken(),
                'is_active' => true,
            ]);
        } catch (\Exception $e) {
            // If create fails due to duplicate phone
            if ($phone && strpos($e->getMessage(), 'uk_teachers_phone') !== false) {
                $existing = Teacher::where('phone', $phone)->first();
                if ($existing) {
                    throw new \RuntimeException(
                        "Nomor HP '{$phone}' sudah terdaftar untuk guru lain ({$existing->name}). " .
                        "Silakan gunakan nama yang sama atau gunakan nomor HP lain."
                    );
                }
            }
            throw $e;
        }
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
        int $resourceId,
        string $bookingDate,
        string $teacherName,
        string $teacherPhone,
        LabClass $labClass,
        array $bookedSlots,
        string $sessionId,
        string $subjectName,
        string $title,
        int $participantCount
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

            $lab = Resource::find($resourceId);

            Http::timeout(3)->post(config('mikrotik.bot_url') . '/api/webhook/lab-session', [
                'event'             => 'booking_pending',
                'session_id'        => $sessionId,
                'booking_ids'       => $bookingIds,
                'teacher_name'      => $teacherName,
                'teacher_phone'     => $teacherPhone,
                'lab_name'          => $lab->name ?? '-',
                'booking_date'      => Carbon::parse($bookingDate)->translatedFormat('l, d M Y'),
                'slot_range'        => $slotRange,
                'slot_times'        => $slotTimes,
                'class_name'        => $labClass->name,
                'subject_name'      => $subjectName,
                'title'             => $title,
                'participant_count' => $participantCount,
                'total_slots'       => count($bookedSlots),
            ]);

        } catch (\Exception $e) {
            Log::warning('WA booking notification failed: ' . $e->getMessage());
        }
    }

    private function sendSundayBookingNotification(
        int $resourceId,
        string $bookingDate,
        string $teacherName,
        string $teacherPhone,
        SundayBooking $booking,
        LabClass $labClass,
        string $subjectName,
        string $title,
        int $participantCount
    ): void {
        try {
            $lab = Resource::find($resourceId);
            Http::timeout(3)->post(config('mikrotik.bot_url') . '/api/webhook/lab-session', [
                'event'             => 'booking_pending',
                'session_id'        => null,
                'booking_ids'       => [$booking->id],
                'teacher_name'      => $teacherName,
                'teacher_phone'     => $teacherPhone,
                'lab_name'          => $lab->name ?? '-',
                'booking_date'      => Carbon::parse($bookingDate)->translatedFormat('l, d M Y'),
                'slot_range'        => 'Seharian',
                'slot_times'        => '07:00-12:45',
                'class_name'        => $labClass->name ?? '-',
                'subject_name'      => $subjectName,
                'title'             => $title,
                'participant_count' => $participantCount,
                'total_slots'       => 1,
                'is_sunday'         => true,
            ]);

        } catch (\Exception $e) {
            Log::warning('WA Sunday booking notification failed: ' . $e->getMessage());
        }
    }

    private function notifyTeknisiBookingPending(
        int $resourceId,
        string $bookingDate,
        string $teacherName,
        string $labName,
        array $bookings,
        string $subjectName,
        string $title,
        int $participantCount,
        bool $isSunday = false
    ): void {
        try {
            // Cari semua teknisi yang ditugaskan ke lab ini
            $teknisiList = User::where('role', 'teknisi')
                ->whereHas('resources', fn($q) => $q->where('resource_id', $resourceId))
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();

            if ($teknisiList->isEmpty()) {
                Log::info('Tidak ada teknisi yang ditugaskan ke lab #' . $resourceId);
                return;
            }

            // Bangun pesan
            $message = $this->buildTeknisiPendingMessage(
                $bookingDate,
                $teacherName,
                $labName,
                $bookings,
                $subjectName,
                $title,
                $participantCount,
                $isSunday
            );

            // Kirim ke setiap teknisi
            foreach ($teknisiList as $teknisi) {
                $this->waService->send($teknisi->phone, $message);
                Log::info('Notifikasi booking pending dikirim ke teknisi ' . $teknisi->full_name . ' (' . $teknisi->phone . ')');
            }

        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi teknisi booking pending: ' . $e->getMessage(), [
                'resource_id' => $resourceId,
            ]);
        }
    }

    private function buildTeknisiPendingMessage(
        string $bookingDate,
        string $teacherName,
        string $labName,
        array $bookings,
        string $subjectName,
        string $title,
        int $participantCount,
        bool $isSunday
    ): string {
        $slotInfo = '';
        if (!$isSunday) {
            $slotIds = array_map(fn($b) => $b->time_slot_id, $bookings);
            $timeSlots = TimeSlot::whereIn('id', $slotIds)->orderBy('slot_order')->get();
            $slotNames = $timeSlots->map(fn($ts) => $ts->name)->join(', ');
            $slotTimes = $timeSlots->map(fn($ts) => substr($ts->start_time, 0, 5) . '-' . substr($ts->end_time, 0, 5))->join(', ');
            $slotInfo = "⏰ Waktu: {$slotNames} ({$slotTimes})";
        } else {
            $slotInfo = "⏰ Waktu: Hari Minggu (07:00-12:45)";
        }

        return implode("\n", [
            '🔔 *BOOKING LAB BARU MENUNGGU PERSETUJUAN*',
            '━━━━━━━━━━━━━━━━━━━━',
            "🏢 Lab: {$labName}",
            "👨‍🏫 Guru: {$teacherName}",
            "📅 Tanggal: " . Carbon::parse($bookingDate)->translatedFormat('l, d F Y'),
            $slotInfo,
            "📝 Kegiatan: {$title}",
            "📚 Mapel: {$subjectName}",
            "👥 Peserta: {$participantCount} orang",
            '━━━━━━━━━━━━━━━━━━━━',
            'Silakan periksa dan approve/reject booking ini!',
        ]);
    }
}