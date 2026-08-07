<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Teacher extends Model
{
    protected $fillable = ['name', 'phone', 'token', 'is_active'];

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

