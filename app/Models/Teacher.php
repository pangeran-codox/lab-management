<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Teacher extends Model
{
    protected $fillable = ['name', 'phone', 'token', 'is_active', 'weekly_quota'];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function sundayBookings()
    {
        return $this->hasMany(SundayBooking::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Hitung kuota terpakai minggu ini
     * Minggu dimulai dari Senin, berakhir Minggu
     */
    public function getUsedQuotaThisWeek(): int
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        // Hitung booking biasa dan sunday booking yang status pending/approved
        $bookingsCount = $this->bookings()
            ->whereBetween('booking_date', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        $sundayBookingsCount = $this->sundayBookings()
            ->whereBetween('booking_date', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        return $bookingsCount + $sundayBookingsCount;
    }

    /**
     * Cek apakah masih ada kuota tersisa
     */
    public function hasRemainingQuota(int $additionalSlots = 1): bool
    {
        $used = $this->getUsedQuotaThisWeek();
        $quota = $this->weekly_quota ?? 5; // Default 5 jika tidak diset

        return ($used + $additionalSlots) <= $quota;
    }

    public static function generateUniqueToken(): string
    {
        $last = self::orderBy('id', 'desc')->first();
        $num  = $last ? (intval(substr($last->token, 4)) + 1) : 1;
        $token = 'GRU-' . str_pad($num, 3, '0', STR_PAD_LEFT);

        while (self::where('token', $token)->exists()) {
            $num++;
            $token = 'GRU-' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        return $token;
    }
}

