<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\SundayBooking;
use App\Services\Booking\BookingAccessService;
use App\Services\Booking\BookingQueryService;
use App\Services\Booking\BookingApprovalService;
use App\Services\Booking\SundayBookingService;

class BookingController extends Controller
{
    public function __construct(
        private BookingAccessService  $access,
        private BookingQueryService   $query,
        private BookingApprovalService $approval,
        private SundayBookingService  $sunday,
    ) {}

    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $bookings       = $this->query->getBookings($request);
        $sundayBookings = $this->query->getSundayBookings($request);
        $resources      = $this->access->getAccessibleResources();
        $stats          = $this->query->getStats();

        return view('booking.index', compact('bookings', 'sundayBookings', 'resources', 'stats'));
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
    // DESTROY
    // ══════════════════════════════════════════════════════════════════

    public function destroy(Booking $booking)
    {
        if (!$this->access->checkResourceAccess($booking->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $title = $booking->title;
        $booking->delete();

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