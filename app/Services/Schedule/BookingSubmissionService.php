<?php

namespace App\Services\Schedule;

use App\Models\Booking;
use App\Models\LabClass;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\SundayBooking;
use App\Models\Teacher;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingSubmissionService
{
    public function submitRegularBooking(Request $request): array
    {
        $dayEn        = Carbon::parse($request->booking_date)->format('l');
        $teacherName  = trim(ucwords(strtolower($request->teacher_name)));
        $teacherPhone = $this->normalizePhone($request->teacher_phone);
        $allSlotIds   = $this->resolveSlotIds($request);
        $sessionId    = (string) Str::uuid();

        return DB::transaction(function () use ($request, $dayEn, $teacherName, $teacherPhone, $allSlotIds, $sessionId) {

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

            if (count($bookedSlots) > 0) {
                $this->sendBookingNotification(
                    $request->resource_id,
                    $request->booking_date,
                    $teacherName,
                    $teacherPhone,
                    $labClass,
                    $bookedSlots,
                    $sessionId,
                    $request->subject_name,
                    $request->title,
                    $request->participant_count
                );
            }

            return [
                'bookedCount'  => count($bookedSlots),
                'skippedCount' => $skippedCount,
            ];
        });
    }

    public function submitSundayBooking(Request $request): void
    {
        $teacherName  = trim(ucwords(strtolower($request->teacher_name)));
        $teacherPhone = $this->normalizePhone($request->teacher_phone);

        DB::transaction(function () use ($request, $teacherName, $teacherPhone) {

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

            $booking = SundayBooking::create([
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

            $this->sendSundayBookingNotification(
                $request->resource_id,
                $request->booking_date,
                $teacherName,
                $teacherPhone,
                $booking,
                $labClass,
                $request->subject_name,
                $request->title,
                $request->participant_count
            );
        });
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
}
