<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\TimeSlot;
use App\Models\Booking;
use App\Models\SundayBooking;
use App\Services\Booking\BookingAccessService;
use App\Services\Booking\BookingQueryService;
use App\Services\Booking\BookingApprovalService;
use App\Services\Booking\SundayBookingService;
use App\Services\Booking\ConflictCheckerService;

class BookingController extends Controller
{
    public function __construct(
        private BookingAccessService   $access,
        private BookingQueryService    $query,
        private BookingApprovalService $approval,
        private SundayBookingService   $sunday,
        private ConflictCheckerService $conflict,  // ← TAMBAH
    ) {}

    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $bookings = $this->query->getBookings($request);
        $sundayBookings = $this->query->getSundayBookings($request);
        $resources = $this->access->getAccessibleResources();
        $stats = $this->query->getStats();

        // ─── WEEKLY GRID OPTIMIZATION ──────────────────────────
        $weekDate = $request->get('week');
        $weekStart = $weekDate ? Carbon::parse($weekDate)->startOfWeek(Carbon::SUNDAY) : now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SATURDAY);

        $resourceIds = $resources->pluck('id');

        // Gunakan BookingQueryService untuk weekly bookings
        $weeklyBookings = $this->query->getWeeklyBookings($weekStart, $weekEnd, $resourceIds);

        $bookingGrid = $weeklyBookings->groupBy(function ($b) {
            return $b->resource_id . '_' . $b->booking_date->toDateString() . '_' . $b->time_slot_id;
        })->map(fn($group) => $group->first());

        $timeSlots = TimeSlot::where('is_break', false)->orderBy('start_time')->get();

        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $weekDays[] = [
                'date'    => $date,
                'label'   => $date->translatedFormat('D'),
                'display' => $date->translatedFormat('d M'),
                'full'    => $date->translatedFormat('l'),
            ];
        }

        return view('booking.index', compact(
            'bookings', 'sundayBookings', 'resources', 'stats',
            'weekStart', 'weekEnd', 'weekDays', 'timeSlots', 'bookingGrid', 'weeklyBookings'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    // SHOW
    // ══════════════════════════════════════════════════════════════════

    public function show(Booking $booking)
    {
        if (!$this->access->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $booking->load(['resource', 'timeSlot']);

        return view('booking.show', compact('booking'));
    }

    // ══════════════════════════════════════════════════════════════════
    // APPROVE
    // ══════════════════════════════════════════════════════════════════

    public function approve(Request $request, Booking $booking)
    {
        if (!$this->access->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        try {
            $this->approval->approve($booking, $request);
        } catch (\Exception $e) {
            Log::error('approve failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui booking. Silakan coba lagi.');
        }

        return back()->with('success', 'Booking "' . $booking->title . '" berhasil disetujui.');
    }

    // ══════════════════════════════════════════════════════════════════
    // APPROVE GROUP
    // ══════════════════════════════════════════════════════════════════

    public function approveGroup(Request $request)
    {
        $request->validate([
            'teacher_name' => 'required|string',
            'resource_id'  => 'required|integer|exists:resources,id',
            'booking_date' => 'required|date',
        ]);

        if (!$this->access->checkResourceAccess((int) $request->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        try {
            $count = $this->approval->approveGroup($request);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('approveGroup failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui booking. Silakan coba lagi.');
        }

        return back()->with('success', "{$count} slot booking berhasil disetujui sekaligus.");
    }

    // ══════════════════════════════════════════════════════════════════
    // REJECT
    // ══════════════════════════════════════════════════════════════════

    public function reject(Request $request, $id)
    {
        $type = $request->input('type', 'regular');

        $booking = $type === 'sunday'
            ? SundayBooking::findOrFail($id)
            : Booking::findOrFail($id);

        if (!$this->access->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'notes' => 'required|string|min:5|max:500',
        ], [
            'notes.required' => 'Alasan penolakan wajib diisi.',
            'notes.min'      => 'Alasan minimal 5 karakter.',
        ]);

        if ($type === 'sunday') {
            $this->sunday->reject($booking, $request->notes);
        } else {
            $this->approval->reject($booking, $request->notes);
        }

        return back()->with('success', 'Booking "' . $booking->title . '" telah ditolak.');
    }

    // ══════════════════════════════════════════════════════════════════
    // REJECT GROUP
    // ══════════════════════════════════════════════════════════════════

    public function rejectGroup(Request $request)
    {
        if (!$this->access->checkResourceAccess((int) $request->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        try {
            $count = $this->approval->rejectGroup($request);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('rejectGroup failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menolak booking. Silakan coba lagi.');
        }

        return back()->with('success', "{$count} slot booking berhasil ditolak sekaligus.");
    }

    // ══════════════════════════════════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════════════════════════════════

    public function destroy(Booking $booking)
    {
        if (!$this->access->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $title = $this->approval->destroy($booking);

        return back()->with('success', 'Booking "' . $title . '" berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════
    // DESTROY SUNDAY
    // ══════════════════════════════════════════════════════════════════

    public function destroySunday($id)
    {
        $booking = SundayBooking::findOrFail($id);

        if (!$this->access->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $title = $this->sunday->destroy($booking);

        return back()->with('success', 'Booking Minggu "' . $title . '" berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════
    // APPROVE SUNDAY
    // ══════════════════════════════════════════════════════════════════

    public function approveSunday(Request $request, $id)
    {
        $booking = SundayBooking::findOrFail($id);

        if (!$this->access->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        try {
            $this->sunday->approve($booking, $request);
        } catch (\Exception $e) {
            Log::error('approveSunday failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui booking. Silakan coba lagi.');
        }

        return back()->with('success', 'Booking Minggu "' . $booking->title . '" berhasil disetujui.');
    }
}