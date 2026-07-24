<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\LabJournal;
use App\Models\LabJournalPhoto;
use App\Models\Schedule;
use App\Services\Journal\JournalAvailabilityService;
use App\Services\Journal\JournalQueryService;
use App\Services\Schedule\ScheduleQueryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JournalController extends Controller
{
    public function __construct(
        private ScheduleQueryService $scheduleQuery,
        private JournalQueryService $journalQuery,
        private JournalAvailabilityService $availability,
        private \App\Services\Booking\BookingAccessService $access
    ) {}

    public function index(Request $request)
    {
        $dateParam = $request->get('date');
        $date = $dateParam ? Carbon::parse($dateParam) : Carbon::today();

        // Tampilkan SEMUA resource lab (untuk publik)
        $resources = $this->scheduleQuery->getActiveResources();
        $timeSlots = $this->scheduleQuery->getActiveTimeSlots()->where('is_break', false)->values();
        $resourceIds = $resources->pluck('id');

        $schedules = $this->journalQuery->getSchedulesForDay($date, $resourceIds);
        $bookings  = $this->journalQuery->getBookingsForDay($date, $resourceIds);
        $journals  = $this->journalQuery->getJournalsForDay($date, $resourceIds);
        $eligibility = $this->availability->getSlotEligibilityMap($timeSlots, $date);
        [$resourceRows, $journalGroups] = $this->buildJournalRows(
            $resources,
            $timeSlots,
            $schedules,
            $bookings,
            $journals,
            $eligibility
        );

        $prevDate = $date->copy()->subDay()->toDateString();
        $nextDate = $date->copy()->addDay()->toDateString();

        return view('journal.index', compact(
            'resources', 'timeSlots', 'schedules', 'bookings', 'journals',
            'eligibility', 'date', 'prevDate', 'nextDate', 'resourceRows', 'journalGroups'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_type'   => 'required|in:schedule,booking',
            'source_ids'    => 'required|array|min:1',
            'source_ids.*'  => 'required|integer',
            'resource_id'   => 'required|exists:resources,id',
            'time_slot_ids' => 'required|array|min:1',
            'time_slot_ids.*' => 'required|exists:time_slots,id',
            'journal_date'  => 'required|date',
            'notes'         => 'nullable|string|max:1000',
            'photos'        => 'required|array|min:1|max:5',
            'photos.*'      => 'image|max:5120', // 5MB per foto
        ]);

        $sourceIds = collect($request->input('source_ids', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
        $timeSlotIds = collect($request->input('time_slot_ids', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        abort_if(
            $sourceIds->count() !== $timeSlotIds->count(),
            422,
            'Jumlah sumber jurnal dan slot waktu tidak cocok.'
        );

        $sources = $request->source_type === 'schedule'
            ? Schedule::with('labClass')->whereIn('id', $sourceIds)->get()->keyBy('id')
            : Booking::whereIn('id', $sourceIds)->get()->keyBy('id');

        abort_if(
            $sources->count() !== $sourceIds->count(),
            422,
            'Sebagian data sesi jurnal tidak ditemukan.'
        );

        $orderedSources = $sourceIds->map(function (int $sourceId, int $index) use ($sources, $timeSlotIds, $request) {
            $source = $sources->get($sourceId);

            abort_if(
                (int) $source->resource_id !== (int) $request->resource_id,
                422,
                'Sumber jurnal tidak sesuai dengan laboratorium yang dipilih.'
            );

            abort_if(
                (int) $source->time_slot_id !== (int) $timeSlotIds[$index],
                422,
                'Data slot waktu jurnal tidak valid.'
            );

            return $source;
        });

        $photoDirectory = 'lab_journals/' . Carbon::parse($request->journal_date)->format('Y/m/d') . '/' . Str::uuid();

        // Kumpulkan path foto yang berhasil di-upload agar bisa di-rollback manual
        // jika terjadi error setelah penyimpanan file ke storage.
        $uploadedPaths = [];

        try {
            // Upload foto ke storage terlebih dahulu (di luar transaksi DB karena
            // Storage::disk tidak bisa di-rollback, jadi kita kelola cleanup manual).
            foreach ($request->file('photos', []) as $index => $photo) {
                $path = $photo->store($photoDirectory, 'public');

                if ($path === false) {
                    throw new \RuntimeException('Gagal menyimpan foto ke storage.');
                }

                $uploadedPaths[] = ['photo_path' => $path, 'sort_order' => $index];
            }

            $savedCount = 0;

            DB::transaction(function () use ($orderedSources, $request, $uploadedPaths, &$savedCount) {
                foreach ($orderedSources as $source) {
                    $activity = $request->source_type === 'booking'
                        ? trim($source->title . ($source->description ? " — {$source->description}" : ''))
                        : ($source->notes ?: $source->subject_name);

                    $journal = LabJournal::updateOrCreate(
                        [
                            'source_type'  => $request->source_type,
                            'source_id'    => $source->id,
                            'journal_date' => $request->journal_date,
                        ],
                        [
                            'resource_id'  => $request->resource_id,
                            'time_slot_id' => $source->time_slot_id,
                            'teacher_id'   => $source->teacher_id ?? null,
                            'teacher_name' => $source->teacher_name,
                            'subject_name' => $source->subject_name,
                            'class_name'   => $request->source_type === 'schedule'
                                ? $source->labClass?->name
                                : $source->class_name,
                            'activity'     => $activity,
                            'notes'        => $request->notes,
                            'filled_at'    => now(),
                        ]
                    );

                    // Hapus foto lama dari storage sebelum mengganti dengan yang baru
                    $journal->photos->each(function ($photo) {
                        Storage::disk('public')->delete($photo->photo_path);
                    });
                    $journal->photos()->delete();

                    foreach ($uploadedPaths as $photoData) {
                        LabJournalPhoto::create([
                            'lab_journal_id' => $journal->id,
                            'photo_path'     => $photoData['photo_path'],
                            'sort_order'     => $photoData['sort_order'],
                        ]);
                    }

                    $savedCount++;
                }
            });
        } catch (\Throwable $e) {
            // Bersihkan file yang sudah terlanjur di-upload ke storage
            foreach ($uploadedPaths as $photoData) {
                Storage::disk('public')->delete($photoData['photo_path']);
            }

            throw $e;
        }

        $slotLabel = $savedCount > 1 ? $savedCount . ' jam pelajaran' : '1 jam pelajaran';

        return redirect()->back()->with('success', 'Jurnal berhasil disimpan untuk ' . $slotLabel . '.');
    }

    private function buildJournalRows(
        Collection $resources,
        Collection $timeSlots,
        Collection $schedules,
        Collection $bookings,
        Collection $journals,
        Collection $eligibility
    ): array {
        $rowsByResource = [];
        $groups = [];
        $orderedSlots = $timeSlots->values();

        foreach ($resources as $resource) {
            $rows = [];
            $index = 0;

            while ($index < $orderedSlots->count()) {
                $slot = $orderedSlots[$index];
                $entry = $this->resolveJournalEntry(
                    $resource->id,
                    $slot,
                    $schedules,
                    $bookings,
                    $journals,
                    $eligibility
                );

                if ($entry === null) {
                    $rows[] = [
                        'type' => 'empty',
                        'slot_name' => $slot->name,
                        'slot_time' => $this->formatSlotTime($slot),
                    ];
                    $index++;
                    continue;
                }

                $members = [$entry];
                $nextIndex = $index + 1;

                while ($nextIndex < $orderedSlots->count()) {
                    $nextSlot = $orderedSlots[$nextIndex];
                    $nextEntry = $this->resolveJournalEntry(
                        $resource->id,
                        $nextSlot,
                        $schedules,
                        $bookings,
                        $journals,
                        $eligibility
                    );

                    if (!$this->canGroupEntries(end($members), $nextEntry)) {
                        break;
                    }

                    $members[] = $nextEntry;
                    $nextIndex++;
                }

                $group = $this->buildGroupPayload($resource->id, $members);
                $rows[] = [
                    'type' => 'group',
                    'group' => $group,
                ];
                $groups[$group['id']] = $group;
                $index = $nextIndex;
            }

            $rowsByResource[$resource->id] = $rows;
        }

        return [$rowsByResource, $groups];
    }

    private function resolveJournalEntry(
        int $resourceId,
        object $slot,
        Collection $schedules,
        Collection $bookings,
        Collection $journals,
        Collection $eligibility
    ): ?array {
        $key = $resourceId . '_' . $slot->id;
        $schedule = $schedules->get($key)?->first();
        $booking = $bookings->get($key)?->first();
        $journal = $journals->get($key)?->first();

        if (!$schedule && !$booking && !$journal) {
            return null;
        }

        $source = $schedule ?? $booking ?? $journal;
        $sourceType = $schedule ? 'schedule' : ($booking ? 'booking' : $journal->source_type);
        $className = $schedule
            ? $schedule->labClass?->name
            : ($booking ? $booking->class_name : $journal?->class_name);
        $subjectName = $source->subject_name ?? null;
        $activity = $schedule
            ? ($schedule->notes ?: $schedule->subject_name)
            : ($booking
                ? trim($booking->title . ($booking->description ? " — {$booking->description}" : ''))
                : $journal?->activity);

        return [
            'slot_id' => $slot->id,
            'slot_order' => (int) $slot->slot_order,
            'slot_name' => $slot->name,
            'slot_time' => $this->formatSlotTime($slot),
            'start_time' => Carbon::parse($slot->start_time)->format('H:i'),
            'end_time' => $slot->end_time ? Carbon::parse($slot->end_time)->format('H:i') : null,
            'source_type' => $sourceType,
            'source_id' => (int) ($source->id ?? $journal?->source_id),
            'teacher_name' => $source->teacher_name ?? $journal?->teacher_name,
            'class_name' => $className,
            'subject_name' => $subjectName,
            'activity' => $activity,
            'signature' => $this->buildEntrySignature($sourceType, $source, $className, $subjectName),
            'is_eligible' => (bool) ($eligibility[$slot->id] ?? false),
            'journal' => $journal ? $this->serializeJournal($journal) : null,
        ];
    }

    private function canGroupEntries(array $currentEntry, ?array $nextEntry): bool
    {
        if ($nextEntry === null) {
            return false;
        }

        return $currentEntry['source_type'] === $nextEntry['source_type']
            && $currentEntry['signature'] === $nextEntry['signature']
            && ($nextEntry['slot_order'] - $currentEntry['slot_order']) === 1;
    }

    private function buildGroupPayload(int $resourceId, array $members): array
    {
        $first = $members[0];
        $last = $members[count($members) - 1];
        $journal = collect($members)
            ->pluck('journal')
            ->first(fn ($item) => $item !== null);
        $slotCount = count($members);

        return [
            'id' => 'group_' . $resourceId . '_' . $first['source_type'] . '_' . $first['slot_id'] . '_' . $last['slot_id'],
            'resource_id' => $resourceId,
            'source_type' => $first['source_type'],
            'source_ids' => array_values(array_map(fn ($member) => $member['source_id'], $members)),
            'time_slot_ids' => array_values(array_map(fn ($member) => $member['slot_id'], $members)),
            'teacher_name' => $first['teacher_name'],
            'class_name' => $first['class_name'],
            'subject_name' => $first['subject_name'],
            'activity' => $first['activity'],
            'slot_name' => $slotCount > 1
                ? $first['slot_name'] . ' - ' . $last['slot_name']
                : $first['slot_name'],
            'slot_time' => $first['start_time'] . ($last['end_time'] ? '–' . $last['end_time'] : ''),
            'slot_count' => $slotCount,
            'is_eligible' => collect($members)->every(fn ($member) => $member['is_eligible']),
            'journal' => $journal,
        ];
    }

    private function buildEntrySignature(
        string $sourceType,
        object $source,
        ?string $className,
        ?string $subjectName
    ): string {
        if ($sourceType === 'booking' && !empty($source->session_id)) {
            return 'booking|session|' . $source->session_id;
        }

        return implode('|', [
            $sourceType,
            $this->normalizeSignatureValue($source->teacher_name ?? null),
            $this->normalizeSignatureValue($className),
            $this->normalizeSignatureValue($subjectName),
        ]);
    }

    private function normalizeSignatureValue(?string $value): string
    {
        return Str::of($value ?? '')
            ->lower()
            ->squish()
            ->value();
    }

    private function serializeJournal(LabJournal $journal): array
    {
        return [
            'teacher_name' => $journal->teacher_name,
            'class_name' => $journal->class_name,
            'subject_name' => $journal->subject_name,
            'activity' => $journal->activity,
            'notes' => $journal->notes,
            'photos' => $journal->photos->map(fn ($photo) => [
                'url' => $photo->url,
            ])->values()->all(),
        ];
    }

    private function formatSlotTime(object $slot): string
    {
        $start = Carbon::parse($slot->start_time)->format('H:i');
        $end = $slot->end_time ? Carbon::parse($slot->end_time)->format('H:i') : null;

        return $end ? $start . '–' . $end : $start;
    }
}
