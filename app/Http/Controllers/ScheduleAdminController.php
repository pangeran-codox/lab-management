<?php

namespace App\Http\Controllers;

use App\Events\ScheduleUpdated;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Organization;
use App\Models\LabClass;
use App\Models\Teacher;
use App\Services\Booking\BookingAccessService;
use Illuminate\Support\Facades\Cache;

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

        // ─── DATA PENDUKUNG — di-cache karena jarang berubah ──────────
        $resourceCacheKey = $allowed
            ? 'schedule_admin_resources_' . md5(implode(',', $allowed))
            : 'schedule_admin_resources_all';

        $resources = Cache::remember($resourceCacheKey, 300, fn() =>
            Resource::where('status', 'active')
                ->when($allowed, fn($q) => $q->whereIn('id', $allowed))
                ->orderBy('name')
                ->get()
        );

        $timeSlots = Cache::remember('schedule_admin_timeslots', 3600, fn() =>
            TimeSlot::where('is_active', 1)
                ->orderBy('slot_order')
                ->get()
        );

        $timeSlotsForm = $timeSlots->where('is_break', false);

        $organizations = Cache::remember('schedule_admin_organizations', 3600, fn() =>
            Organization::where('is_active', 1)
                ->orderBy('name')
                ->get()
        );

        $teachers = Cache::remember('schedule_admin_teachers', 300, fn() =>
            Teacher::where('is_active', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'phone'])
        );

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

        $this->forgetScheduleCache();
        
        broadcast(new ScheduleUpdated('regular', 'created', [
            'resource_id'  => $request->resource_id,
            'day_of_week'  => $request->day_of_week
        ]));

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

        $this->forgetScheduleCache();
        
        broadcast(new ScheduleUpdated('regular', 'updated', [
            'resource_id'  => $schedule->resource_id,
            'day_of_week'  => $schedule->day_of_week
        ]));

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        if (!$this->accessService->checkResourceAccess($schedule->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }
        $schedule->delete();
        $this->forgetScheduleCache();
        
        broadcast(new ScheduleUpdated('regular', 'deleted', [
            'resource_id'  => $schedule->resource_id,
            'day_of_week'  => $schedule->day_of_week
        ]));

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Hapus semua cache yang berkaitan dengan jadwal.
     * Dipanggil setelah store/update/destroy.
     */
    private function forgetScheduleCache(): void
    {
        // Cache jadwal di ScheduleQueryService (berdasarkan hash resource IDs)
        // Karena hash-nya dinamis, forget key statis yang bisa di-predict
        Cache::forget('schedule_admin_timeslots');
        Cache::forget('schedule_admin_organizations');
        Cache::forget('schedule_admin_teachers');
        // Jadwal aktif di ScheduleQueryService di-cache dengan hash resourceIds.
        // Cara paling aman: forget semua rekap bulan ini juga.
        Cache::forget('rekap_monthly_' . now()->month . '_' . now()->year);
    }

    public function export($resource)
{
    if (!$this->accessService->checkResourceAccess((int) $resource)) {
        abort(403, 'Anda tidak memiliki akses ke lab ini.');
    }

    $labResource = Resource::findOrFail($resource);

    $schedules = Schedule::with(['timeSlot', 'labClass'])
        ->where('resource_id', $labResource->id)
        ->where('status', 'active')
        ->whereNull('deleted_at')
        ->get();

    $timeSlots = TimeSlot::where('is_active', 1)
        ->orderBy('slot_order')
        ->get();

    $scheduleGrid = $schedules->groupBy(
        fn($s) => $s->day_of_week . '_' . $s->time_slot_id
    );

    return view('schedule.reports.editor', [
        'resource'       => $labResource,
        'timeSlots'      => $timeSlots,
        'scheduleGrid'   => $scheduleGrid,
        'days'           => $this->days,
        'date'           => now()->translatedFormat('d F Y'),
        'logo'           => \App\Models\Setting::get(\App\Models\Setting::SITE_LOGO),
        'siteName'       => \App\Models\Setting::get(\App\Models\Setting::SITE_NAME, config('app.name')),
        'siteAddress'    => \App\Models\Setting::get(\App\Models\Setting::SITE_ADDRESS, ''),
        'sitePhone'      => \App\Models\Setting::get(\App\Models\Setting::SITE_PHONE, ''),
        'siteHeadName'   => \App\Models\Setting::get(\App\Models\Setting::SITE_HEAD_NAME, ''),
        'reportFooter'   => \App\Models\Setting::get(\App\Models\Setting::REPORT_FOOTER, ''),
        'kopNameSize'    => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_NAME_SIZE, 20),
        'kopAddressSize' => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_ADDRESS_SIZE, 13),
        'kopPhoneSize'   => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_PHONE_SIZE, 12),
    ]);
}

    public function getClassesByOrg(Request $request)
    {
        $classes = LabClass::where('organization_id', $request->organization_id)
            ->where('is_active', 1)->whereNull('deleted_at')
            ->orderBy('name')->get(['id', 'name']);
        return response()->json($classes);
    }
}