<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Organization;
use App\Models\LabClass;
use App\Models\Teacher;
use App\Services\Booking\BookingAccessService;

class ScheduleAdminController extends Controller
{
    public function __construct(
        private BookingAccessService $accessService
    ) {}

    private $days = [
        'Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
        'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu',
    ];

    public function index(Request $request)
    {
        $allowed = $this->accessService->getAllowedResources();

        // ─── OPTIMASI: Ambil SEMUA jadwal sekaligus (Active & Inactive) ───────
        $allSchedules = Schedule::with(['resource', 'timeSlot', 'labClass'])
            ->when($allowed, fn($q) => $q->whereIn('resource_id', $allowed))
            ->get();

        // Hitung Stats dari koleksi di memori (0 Query tambahan)
        $stats = [
            'total'    => $allSchedules->count(),
            'active'   => $allSchedules->where('status', 'active')->count(),
            'inactive' => $allSchedules->where('status', 'inactive')->count(),
        ];

        // Buat Grid dari koleksi di memori (0 Query tambahan)
        $scheduleGrid = $allSchedules->where('status', 'active')
            ->groupBy(fn($s) => $s->resource_id . '_' . $s->day_of_week . '_' . $s->time_slot_id);

        // ─── DATA PENDUKUNG ──────────────────────────────────
        $resources = Resource::where('status', 'active')
            ->when($allowed, fn($q) => $q->whereIn('id', $allowed))
            ->orderBy('name')
            ->get();

        $timeSlots = TimeSlot::where('is_active', 1)
            ->orderBy('slot_order')
            ->get();
        
        $timeSlotsForm = $timeSlots->where('is_break', false);

        $organizations = Organization::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $teachers = Teacher::where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        return view('schedule.admin', compact(
            'scheduleGrid', 'resources', 'timeSlots', 'timeSlotsForm',
            'organizations', 'teachers', 'stats'
        ))->with('days', $this->days);
    }

    public function store(Request $request)
    {
        if (!$this->accessService->checkResourceAccess((int)$request->resource_id)) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses ke lab ini.'])->withInput();
        }

        $request->validate([
            'resource_id'  => 'required|exists:resources,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'class_id'     => 'required|exists:classes,id',
            'day_of_week'  => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'teacher_name' => 'required|string|max:255',
            'subject_name' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $exists = Schedule::where('resource_id', $request->resource_id)
            ->where('time_slot_id', $request->time_slot_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('status', 'active')
            ->whereNull('deleted_at')->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Slot ini sudah ada jadwal tetap yang aktif.'])->withInput();
        }

        Schedule::create([
            'resource_id'  => $request->resource_id,
            'time_slot_id' => $request->time_slot_id,
            'class_id'     => $request->class_id,
            'day_of_week'  => $request->day_of_week,
            'teacher_name' => $request->teacher_name,
            'subject_name' => $request->subject_name,
            'notes'        => $request->notes,
            'status'       => 'active',
            'user_id'      => auth()->id(),
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        if (!$this->accessService->checkResourceAccess($schedule->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $request->validate([
            'teacher_name' => 'required|string|max:255',
            'subject_name' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:active,inactive',
        ]);

        $schedule->update([
            'teacher_name' => $request->teacher_name,
            'subject_name' => $request->subject_name,
            'notes'        => $request->notes,
            'status'       => $request->status,
        ]);

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        if (!$this->accessService->checkResourceAccess($schedule->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        $schedule->delete(); // ← ganti ini
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    public function getClassesByOrg(Request $request)
    {
        $classes = LabClass::where('organization_id', $request->organization_id)
            ->where('is_active', 1)->whereNull('deleted_at')
            ->orderBy('name')->get(['id', 'name']);
        return response()->json($classes);
    }
}