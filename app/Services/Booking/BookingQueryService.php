<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\SundayBooking;
use Illuminate\Http\Request;

class BookingQueryService
{
    public function __construct(
        private BookingAccessService $access
    ) {}

    public function getBookings(Request $request)
    {
        $allowed = $this->access->getAllowedResources();

        $query = Booking::with(['resource', 'timeSlot'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'approved' THEN 2 WHEN 'rejected' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc');

        if ($allowed !== null)                               $query->whereIn('resource_id', $allowed);
        if ($request->status && $request->status !== 'all') $query->where('status', $request->status);
        if ($request->resource_id)                          $query->where('resource_id', $request->resource_id);
        if ($request->date)                                  $query->where('booking_date', $request->date);
        if ($request->search) {
            $query->where(fn($q) => $q
                ->where('teacher_name', 'like', '%' . $request->search . '%')
                ->orWhere('class_name',  'like', '%' . $request->search . '%')
                ->orWhere('title',       'like', '%' . $request->search . '%')
            );
        }

        return $query->paginate(15, ['*'], 'bookings_page')->withQueryString();
    }

    public function getSundayBookings(Request $request)
    {
        $allowed = $this->access->getAllowedResources();

        $query = SundayBooking::with(['resource', 'organization'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'approved' THEN 2 WHEN 'rejected' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc');

        if ($allowed !== null)                               $query->whereIn('resource_id', $allowed);
        if ($request->status && $request->status !== 'all') $query->where('status', $request->status);
        if ($request->resource_id)                          $query->where('resource_id', $request->resource_id);
        if ($request->date)                                  $query->where('booking_date', $request->date);
        if ($request->search) {
            $query->where(fn($q) => $q
                ->where('teacher_name', 'like', '%' . $request->search . '%')
                ->orWhere('class_name',  'like', '%' . $request->search . '%')
                ->orWhere('title',       'like', '%' . $request->search . '%')
            );
        }

        return $query->paginate(10, ['*'], 'sunday_page')->withQueryString();
    }

    public function getStats(): array
    {
        $allowed = $this->access->getAllowedResources();

        $bookingStats = Booking::query()
            ->when($allowed, fn($q) => $q->whereIn('resource_id', $allowed))
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
            ")
            ->first();

        $sundayStats = SundayBooking::query()
            ->when($allowed, fn($q) => $q->whereIn('resource_id', $allowed))
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
            ")
            ->first();

        return [
            'regular' => [
                'total'    => (int) ($bookingStats->total    ?? 0),
                'pending'  => (int) ($bookingStats->pending  ?? 0),
                'approved' => (int) ($bookingStats->approved ?? 0),
                'rejected' => (int) ($bookingStats->rejected ?? 0),
            ],
            'sunday' => [
                'total'    => (int) ($sundayStats->total    ?? 0),
                'pending'  => (int) ($sundayStats->pending  ?? 0),
                'approved' => (int) ($sundayStats->approved ?? 0),
                'rejected' => (int) ($sundayStats->rejected ?? 0),
            ],
            // Totals
            'total_pending' => (int) (($bookingStats->pending ?? 0) + ($sundayStats->pending ?? 0)),
        ];
    }
}