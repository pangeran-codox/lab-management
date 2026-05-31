<?php

namespace App\Http\Controllers;

use App\Models\ImportantSchedule;
use App\Models\Resource;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportantScheduleController extends Controller
{
    public function __construct()
    {
        // Hanya admin yang bisa akses semua method
        $this->middleware(['auth']);
    }

    // ── Index: daftar semua jadwal penting ──────────────────
    public function index(Request $request)
    {
        $query = ImportantSchedule::with(['resource', 'startSlot', 'endSlot'])
            ->orderBy('date', 'desc');

        if ($request->filled('resource_id')) {
            $query->where('resource_id', $request->resource_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', now()->parse($request->month)->month)
                  ->whereYear('date', now()->parse($request->month)->year);
        }

        $importantSchedules = $query->paginate(15)->withQueryString();
        $resources = Resource::where('status', 'active')->orderBy('name')->get();

        return view('important-schedule.index', compact('importantSchedules', 'resources'));
    }

    // ── Create form ─────────────────────────────────────────
    public function create()
    {
        $resources  = Resource::where('status', 'active')->orderBy('name')->get();
        $timeSlots  = TimeSlot::where('is_active', true)->where('is_break', false)->orderBy('slot_order')->get();
        $typeLabels = ImportantSchedule::$typeLabels;

        return view('important-schedule.create', compact('resources', 'timeSlots', 'typeLabels'));
    }

    // ── Store ────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'resource_id'   => 'required|exists:resources,id',
            'title'         => 'required|string|max:255',
            'type'          => 'required|in:exam,olympiad,event,other',
            'date'          => 'required|date|after_or_equal:today',
            'is_full_day'   => 'boolean',
            'start_slot_id' => 'nullable|required_if:is_full_day,false|exists:time_slots,id',
            'end_slot_id'   => 'nullable|required_if:is_full_day,false|exists:time_slots,id|gte:start_slot_id',
            'description'   => 'nullable|string|max:1000',
            'color'         => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'start_slot_id.required_if' => 'Slot mulai wajib diisi jika tidak full day.',
            'end_slot_id.required_if'   => 'Slot selesai wajib diisi jika tidak full day.',
            'end_slot_id.gte'           => 'Slot selesai harus lebih besar atau sama dengan slot mulai.',
        ]);

        if ($request->boolean('is_full_day')) {
            $validated['start_slot_id'] = null;
            $validated['end_slot_id']   = null;
        }

        $validated['created_by'] = Auth::id();

        ImportantSchedule::create($validated);

        return redirect()
            ->route('important-schedule.index')
            ->with('success', 'Jadwal penting berhasil ditambahkan.');
    }

    // ── Edit form ────────────────────────────────────────────
    public function edit(ImportantSchedule $importantSchedule)
    {
        $resources  = Resource::where('status', 'active')->orderBy('name')->get();
        $timeSlots  = TimeSlot::where('is_active', true)->where('is_break', false)->orderBy('slot_order')->get();
        $typeLabels = ImportantSchedule::$typeLabels;

        return view('important-schedule.edit', compact('importantSchedule', 'resources', 'timeSlots', 'typeLabels'));
    }

    // ── Update ───────────────────────────────────────────────
    public function update(Request $request, ImportantSchedule $importantSchedule)
    {
        $validated = $request->validate([
            'resource_id'   => 'required|exists:resources,id',
            'title'         => 'required|string|max:255',
            'type'          => 'required|in:exam,olympiad,event,other',
            'date'          => 'required|date',
            'is_full_day'   => 'boolean',
            'start_slot_id' => 'nullable|required_if:is_full_day,false|exists:time_slots,id',
            'end_slot_id'   => 'nullable|required_if:is_full_day,false|exists:time_slots,id|gte:start_slot_id',
            'description'   => 'nullable|string|max:1000',
            'color'         => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($request->boolean('is_full_day')) {
            $validated['start_slot_id'] = null;
            $validated['end_slot_id']   = null;
        }

        $importantSchedule->update($validated);

        return redirect()
            ->route('important-schedule.index')
            ->with('success', 'Jadwal penting berhasil diperbarui.');
    }

    // ── Destroy ──────────────────────────────────────────────
    public function destroy(ImportantSchedule $importantSchedule)
    {
        $importantSchedule->delete();

        return redirect()
            ->route('important-schedule.index')
            ->with('success', 'Jadwal penting berhasil dihapus.');
    }

    // ── API: cek slot yang terblokir (untuk kalender/booking) ─
    public function blockedSlots(Request $request)
    {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'date'        => 'required|date',
        ]);

        $schedules = ImportantSchedule::forResourceOnDate(
            $request->resource_id,
            $request->date
        )->with(['startSlot', 'endSlot'])->get();

        if ($schedules->isEmpty()) {
            return response()->json(['blocked' => [], 'events' => []]);
        }

        $blockedSlotIds = [];
        $allSlots = TimeSlot::where('is_active', true)->where('is_break', false)
                            ->orderBy('slot_order')->get();

        foreach ($schedules as $schedule) {
            if ($schedule->is_full_day) {
                $blockedSlotIds = $allSlots->pluck('id')->toArray();
                break;
            }
            // Blokir semua slot dari start sampai end (berdasarkan slot_order)
            $startOrder = $schedule->startSlot?->slot_order ?? 0;
            $endOrder   = $schedule->endSlot?->slot_order   ?? 0;

            foreach ($allSlots as $slot) {
                if ($slot->slot_order >= $startOrder && $slot->slot_order <= $endOrder) {
                    $blockedSlotIds[] = $slot->id;
                }
            }
        }

        return response()->json([
            'blocked' => array_unique($blockedSlotIds),
            'events'  => $schedules->map(fn($s) => [
                'id'          => $s->id,
                'title'       => $s->title,
                'type'        => $s->type_label,
                'is_full_day' => $s->is_full_day,
                'color'       => $s->color,
                'start_slot'  => $s->startSlot?->name,
                'end_slot'    => $s->endSlot?->name,
            ]),
        ]);
    }
}