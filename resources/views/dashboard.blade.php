<x-app-layout>
<x-slot name="title">Dashboard</x-slot>

<style>
.stat-card { background:#fff;border-radius:14px;padding:20px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);transition:box-shadow .15s; }
.stat-card:hover { box-shadow:0 4px 14px rgba(26,37,23,.09); }
.badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700; }
.badge-pending  { background:#fef3c7;color:#92400e; }
.badge-approved { background:#dcfce7;color:#166534; }
.badge-rejected { background:#fee2e2;color:#991b1b; }
.quick-btn { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:12px;border:1.5px solid #e8f0e6;background:#fff;text-decoration:none;color:#1A2517;transition:all .15s;font-size:13px;font-weight:600; }
.quick-btn:hover { border-color:#ACC8A2;background:#f8faf7;transform:translateY(-1px);box-shadow:0 3px 10px rgba(26,37,23,.08); }
</style>

@php
// Tentukan lab yang boleh dilihat user ini
$authUser = auth()->user();
$allowedResources = null;
if (!in_array($authUser->role, ['admin', 'operator'])) {
    $meta = is_array($authUser->metadata) ? $authUser->metadata : json_decode($authUser->metadata, true);
    $allowedResources = $meta['allowed_resources'] ?? [];
}

// Base query helper
$bq = \App\Models\Booking::query();
if ($allowedResources !== null) $bq->whereIn('resource_id', $allowedResources);

$sq = \App\Models\Schedule::whereNull('deleted_at');
if ($allowedResources !== null) $sq->whereIn('resource_id', $allowedResources);

$totalLab      = $allowedResources !== null
    ? \App\Models\Resource::where('status','active')->whereIn('id', $allowedResources)->count()
    : \App\Models\Resource::where('status','active')->count();
$totalSchedule = (clone $sq)->where('status','active')->count();
$pendingBook   = (clone $bq)->where('status','pending')->count();
$todayBook     = (clone $bq)->whereDate('booking_date', today())->whereIn('status',['pending','approved'])->count();
$thisWeekBook  = (clone $bq)->whereBetween('booking_date',[now()->startOfWeek(),now()->endOfWeek()])->count();
$approvedToday = (clone $bq)->whereDate('updated_at', today())->where('status','approved')->count();

$totalBroken = \App\Models\LabInventory::where('quantity_broken', '>', 0)->count();
$totalLowStock = \App\Models\LabInventory::whereRaw('quantity <= 2')->where('quantity', '>', 0)->count();

$dayNames = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
$dayKeys  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
$bookingPerDay = [];
$maxDay = 1;
foreach ($dayKeys as $i => $dk) {
    $date = now()->startOfWeek()->addDays($i)->toDateString();
    $cnt  = (clone $bq)->whereDate('booking_date', $date)->whereIn('status',['pending','approved'])->count();
    $bookingPerDay[$dayNames[$i]] = $cnt;
    if ($cnt > $maxDay) $maxDay = $cnt;
}

$pendingBookings = (clone $bq)->with('resource','timeSlot')
    ->where('status','pending')->orderBy('created_at','desc')->take(5)->get();

$recentBookings = (clone $bq)->with('resource')
    ->orderBy('created_at','desc')->take(6)->get();

$labStats = (clone $bq)->whereBetween('booking_date',[now()->startOfWeek(),now()->endOfWeek()])
    ->selectRaw('resource_id, count(*) as cnt')
    ->groupBy('resource_id')->with('resource')
    ->orderByDesc('cnt')->take(5)->get();

// Dapatkan Status Lab Saat Ini (Real-time)
$allLabs = \App\Models\Resource::where('status','active')->orderBy('name')->get();
$currentDay = now()->format('l'); // Monday, Tuesday, dll (sesuai database schedules)
$currentTime = now()->format('H:i:s');

// Cari slot waktu sekarang
// Filter day_of_week dihapus karena time_slots biasanya sama setiap hari 
// dan kolom tersebut di database bertipe smallint (menyebabkan error jika diisi string)
$currentSlot = \App\Models\TimeSlot::where('start_time', '<=', $currentTime)
    ->where('end_time', '>=', $currentTime)
    ->where('is_active', true)
    ->first();

$labStatuses = [];
foreach ($allLabs as $lab) {
    $activity = null;
    $type = null;

    // 1. Cek Booking yang disetujui untuk hari ini & jam ini
    $booking = \App\Models\Booking::where('resource_id', $lab->id)
        ->whereDate('booking_date', today())
        ->where('status', 'approved')
        ->whereHas('timeSlot', function($q) use ($currentTime) {
            $q->where('start_time', '<=', $currentTime)->where('end_time', '>=', $currentTime);
        })->first();

    if ($booking) {
        $activity = $booking->title . ' (' . $booking->teacher_name . ')';
        $type = 'booking';
    } else if ($currentSlot) {
        // 2. Cek Jadwal Tetap (Gunakan currentDay format English)
        $schedule = \App\Models\Schedule::where('resource_id', $lab->id)
            ->where('day_of_week', $currentDay)
            ->where('time_slot_id', $currentSlot->id)
            ->where('status', 'active')
            ->first();
        if ($schedule) {
            $activity = $schedule->title . ' (' . $schedule->teacher_name . ')';
            $type = 'schedule';
        }
    }

    $labStatuses[] = [
        'lab' => $lab,
        'activity' => $activity,
        'type' => $type,
        'is_occupied' => !empty($activity)
    ];
}
@endphp

{{-- HEADER --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
    <div>
        <h1 style="font-family:Outfit,sans-serif;font-weight:800;font-size:22px;color:#1A2517;margin:0">
            Selamat datang, {{ auth()->user()->full_name ?? auth()->user()->username }} 👋
        </h1>
        <p style="font-size:13px;color:#9ca3af;margin:4px 0 0">{{ now()->translatedFormat('l, d F Y') }} · Lab Management Nuris Jember</p>
    </div>
    @if($pendingBook > 0)
    <a href="{{ route('booking.index') }}?status=pending"
       style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none">
        <span style="background:#f87171;color:#fff;border-radius:999px;padding:1px 8px;font-size:11px;font-weight:800">{{ $pendingBook }}</span>
        Booking Pending
    </a>
    @endif
</div>

{{-- ALERTS --}}
@if($totalBroken > 0 || $pendingBook > 0)
<div style="display:flex;gap:14px;margin-bottom:20px;flex-wrap:wrap">
    @if($pendingBook > 0)
    <div style="flex:1;min-width:300px;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:12px">
        <div style="width:36px;height:36px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;color:#d97706">
            <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div style="flex:1">
            <p style="margin:0;font-size:13px;font-weight:700;color:#92400e">Ada {{ $pendingBook }} Booking Menunggu</p>
            <p style="margin:2px 0 0;font-size:11px;color:#b45309">Segera periksa dan berikan persetujuan di menu Booking.</p>
        </div>
        <a href="{{ route('booking.index') }}?status=pending" style="font-size:11px;font-weight:700;color:#92400e;text-decoration:none;background:#fef3c7;padding:6px 12px;border-radius:8px">Periksa</a>
    </div>
    @endif

    @if($totalBroken > 0)
    <div style="flex:1;min-width:300px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:12px">
        <div style="width:36px;height:36px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:#dc2626">
            <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div style="flex:1">
            <p style="margin:0;font-size:13px;font-weight:700;color:#991b1b">Perhatian: {{ $totalBroken }} Barang Rusak</p>
            <p style="margin:2px 0 0;font-size:11px;color:#b91c1c">Ditemukan barang dengan kondisi rusak di inventaris lab.</p>
        </div>
        <a href="{{ route('inventory.admin') }}" style="font-size:11px;font-weight:700;color:#991b1b;text-decoration:none;background:#fee2e2;padding:6px 12px;border-radius:8px">Lihat Detail</a>
    </div>
    @endif
</div>
@endif

{{-- QUICK ACTIONS --}}
<div style="margin-bottom:24px">
    <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0 0 12px">⚡ Akses Cepat</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));gap:12px">
        <a href="{{ route('booking.index') }}" class="quick-btn">
            <div style="width:32px;height:32px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#16a34a">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            Manajemen Booking
        </a>
        <a href="{{ route('schedule.index') }}" class="quick-btn">
            <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#2563eb">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            Jadwal Rutin
        </a>
        <a href="{{ route('inventory.admin') }}" class="quick-btn">
            <div style="width:32px;height:32px;border-radius:8px;background:#fff7ed;display:flex;align-items:center;justify-content:center;color:#ea580c">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            Data Inventaris
        </a>
        <a href="{{ route('organization.index') }}" class="quick-btn">
            <div style="width:32px;height:32px;border-radius:8px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;color:#7c3aed">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            Sekolah & Kelas
        </a>
    </div>
</div>

{{-- STATS --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:20px">
    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em">Total Lab</span>
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(172,200,162,.15);display:flex;align-items:center;justify-content:center">
                <svg style="width:16px;height:16px;color:#3d5438" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <p style="font-size:32px;font-family:Outfit,sans-serif;font-weight:800;color:#1A2517;line-height:1">{{ $totalLab }}</p>
        <p style="font-size:11px;color:#9ca3af;margin-top:6px">Laboratorium aktif</p>
    </div>

    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em">Jadwal Tetap</span>
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(172,200,162,.15);display:flex;align-items:center;justify-content:center">
                <svg style="width:16px;height:16px;color:#3d5438" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <p style="font-size:32px;font-family:Outfit,sans-serif;font-weight:800;color:#1A2517;line-height:1">{{ $totalSchedule }}</p>
        <p style="font-size:11px;color:#9ca3af;margin-top:6px">Slot terjadwal aktif</p>
    </div>

    <div class="stat-card" style="{{ $pendingBook > 0 ? 'border-color:#fcd34d;' : '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em">Booking Pending</span>
            <div style="width:34px;height:34px;border-radius:10px;background:#fef9ec;display:flex;align-items:center;justify-content:center">
                <svg style="width:16px;height:16px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p style="font-size:32px;font-family:Outfit,sans-serif;font-weight:800;color:{{ $pendingBook > 0 ? '#d97706' : '#1A2517' }};line-height:1">{{ $pendingBook }}</p>
        <p style="font-size:11px;color:{{ $pendingBook > 0 ? '#d97706' : '#9ca3af' }};margin-top:6px">{{ $pendingBook > 0 ? 'Menunggu persetujuan' : 'Semua sudah diproses ✓' }}</p>
    </div>

    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em">Hari Ini</span>
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(172,200,162,.15);display:flex;align-items:center;justify-content:center">
                <svg style="width:16px;height:16px;color:#3d5438" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <p style="font-size:32px;font-family:Outfit,sans-serif;font-weight:800;color:#1A2517;line-height:1">{{ $todayBook }}</p>
        <p style="font-size:11px;color:#9ca3af;margin-top:6px">{{ $thisWeekBook }} booking minggu ini</p>
    </div>
</div>

{{-- STATUS LAB REAL-TIME --}}
<div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;padding:20px;box-shadow:0 1px 4px rgba(26,37,23,.05);margin-bottom:20px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:15px;margin:0">
            📡 Status Lab Saat Ini
            <span style="font-size:11px;font-weight:600;color:#9ca3af;margin-left:8px">{{ now()->format('H:i') }} WIB</span>
        </h2>
        <div style="display:flex;gap:12px">
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:#16a34a">
                <div style="width:8px;height:8px;border-radius:50%;background:#16a34a"></div> Tersedia
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:#ef4444">
                <div style="width:8px;height:8px;border-radius:50%;background:#ef4444"></div> Terpakai
            </div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(240px, 1fr));gap:12px">
        @foreach($labStatuses as $ls)
        <div style="padding:14px;border-radius:12px;border:1px solid #f0f4ee;background:{{ $ls['is_occupied'] ? '#fffcfc' : '#fcfdfb' }};display:flex;align-items:center;gap:12px;position:relative;overflow:hidden;transition:all .2s" onmouseover="this.style.borderColor='#ACC8A2';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#f0f4ee';this.style.transform='none'">
            <div style="position:absolute;top:0;left:0;bottom:0;width:4px;background:{{ $ls['is_occupied'] ? '#ef4444' : '#16a34a' }}"></div>
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $ls['is_occupied'] ? '#fef2f2' : '#f0fdf4' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:20px;height:20px;color:{{ $ls['is_occupied'] ? '#ef4444' : '#16a34a' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:6px">
                    <p style="font-size:13px;font-weight:800;color:#1A2517;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $ls['lab']->name }}</p>
                    @if(!$ls['is_occupied'])
                    <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;display:inline-block" title="Tersedia"></span>
                    @endif
                </div>
                <p style="font-size:11px;color:{{ $ls['is_occupied'] ? '#ef4444' : '#16a34a' }};font-weight:700;margin:2px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ $ls['activity'] ?? 'Tersedia Sekarang' }}
                </p>
                @if($ls['type'])
                <div style="display:flex;align-items:center;gap:4px;margin-top:2px">
                    <span style="font-size:9px;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;font-weight:800">
                        via {{ $ls['type'] === 'booking' ? 'Booking' : 'Jadwal' }}
                    </span>
                </div>
                @endif
            </div>
            @if(!$ls['is_occupied'])
            <a href="{{ route('booking.index') }}?resource_id={{ $ls['lab']->id }}" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);opacity:0;transition:opacity .2s;background:#1A2517;color:#fff;padding:4px 8px;border-radius:6px;font-size:10px;text-decoration:none;font-weight:700" class="hover-action">Booking</a>
            @endif
        </div>
        @endforeach
    </div>
</div>

<style>
.stat-card:hover .hover-action, div:hover .hover-action { opacity:1 !important; }
</style>

{{-- MAIN GRID --}}
<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start">

    <div style="display:flex;flex-direction:column;gap:16px">

        {{-- Pending Bookings --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);overflow:hidden">
            <div style="padding:14px 20px;border-bottom:1px solid #f0f4ee;display:flex;align-items:center;justify-content:space-between">
                <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0">
                    ⏳ Menunggu Persetujuan
                    @if($pendingBook > 0)
                    <span style="background:#fef3c7;color:#92400e;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;margin-left:6px">{{ $pendingBook }}</span>
                    @endif
                </h2>
                <a href="{{ route('booking.index') }}?status=pending" style="font-size:12px;color:#ACC8A2;font-weight:600;text-decoration:none">Lihat semua →</a>
            </div>
            @forelse($pendingBookings as $b)
            <div style="padding:13px 20px;border-top:1px solid #f9f9f9;display:flex;align-items:center;gap:12px">
                <div style="width:38px;height:38px;border-radius:10px;background:#fef9ec;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg style="width:16px;height:16px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div style="flex:1;min-width:0">
                    <p style="font-size:13px;font-weight:700;color:#1A2517;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $b->title }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin:3px 0 0">
                        {{ $b->teacher_name }} · {{ $b->resource->name ?? '-' }} ·
                        {{ \Carbon\Carbon::parse($b->booking_date)->translatedFormat('d M Y') }}
                        @if($b->timeSlot) · {{ $b->timeSlot->name }} @endif
                    </p>
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0">
                    <form method="POST" action="{{ route('booking.approve', $b->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" style="background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;border:none;border-radius:8px;padding:6px 12px;font-size:11px;font-weight:700;cursor:pointer">✓ Setuju</button>
                    </form>
                    <a href="{{ route('booking.show', $b->id) }}" style="background:#f3f4f6;color:#374151;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:600;text-decoration:none">Detail</a>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;color:#9ca3af;font-size:13px">
                <div style="font-size:28px;margin-bottom:8px">✅</div>
                Tidak ada booking yang menunggu
            </div>
            @endforelse
        </div>

        {{-- Booking per Hari --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;padding:20px;box-shadow:0 1px 4px rgba(26,37,23,.05)">
            <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0 0 16px">📊 Booking Minggu Ini per Hari</h2>
            @foreach($bookingPerDay as $day => $cnt)
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                <span style="font-size:11px;font-weight:700;color:#6b7280;width:48px;flex-shrink:0">{{ $day }}</span>
                <div style="flex:1;background:#f3f4f6;border-radius:999px;height:8px;overflow:hidden">
                    <div style="height:8px;border-radius:999px;background:linear-gradient(90deg,#ACC8A2,#3d5438);width:{{ $maxDay > 0 ? ($cnt/$maxDay*100) : 0 }}%;transition:width .5s ease"></div>
                </div>
                <span style="font-size:12px;font-weight:700;color:#1A2517;width:20px;text-align:right;flex-shrink:0">{{ $cnt }}</span>
            </div>
            @endforeach
            <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f0f4ee;display:flex;gap:20px">
                <div><p style="font-family:Outfit,sans-serif;font-size:20px;font-weight:800;color:#1A2517;margin:0">{{ $thisWeekBook }}</p><p style="font-size:10px;color:#9ca3af;margin:2px 0 0">Total minggu ini</p></div>
                <div><p style="font-family:Outfit,sans-serif;font-size:20px;font-weight:800;color:#16a34a;margin:0">{{ $approvedToday }}</p><p style="font-size:10px;color:#9ca3af;margin:2px 0 0">Disetujui hari ini</p></div>
                <div><p style="font-family:Outfit,sans-serif;font-size:20px;font-weight:800;color:#d97706;margin:0">{{ $pendingBook }}</p><p style="font-size:10px;color:#9ca3af;margin:2px 0 0">Masih pending</p></div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:16px">

        {{-- Quick Actions --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;padding:16px 18px;box-shadow:0 1px 4px rgba(26,37,23,.05)">
            <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0 0 12px">⚡ Aksi Cepat</h2>
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="{{ route('booking.index') }}" class="quick-btn">
                    <div style="width:32px;height:32px;border-radius:9px;background:#fef9ec;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:15px;height:15px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <span>Kelola Booking</span>
                    @if($pendingBook > 0)<span style="margin-left:auto;background:#fef3c7;color:#92400e;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px">{{ $pendingBook }}</span>@endif
                </a>
                <a href="{{ route('schedule.admin') }}" class="quick-btn">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(172,200,162,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:15px;height:15px;color:#3d5438" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span>Jadwal Tetap</span>
                </a>
                <a href="{{ route('teacher.index') }}" class="quick-btn">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(172,200,162,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:15px;height:15px;color:#3d5438" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <span>Data Guru</span>
                </a>
                <a href="{{ route('organization.index') }}" class="quick-btn">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(172,200,162,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:15px;height:15px;color:#3d5438" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span>Sekolah & Kelas</span>
                </a>
                <a href="{{ route('inventory.public') }}" class="quick-btn">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(172,200,162,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:15px;height:15px;color:#3d5438" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span>Lihat Inventaris</span>
                </a>
                <a href="{{ route('home') }}" class="quick-btn">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(172,200,162,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:15px;height:15px;color:#3d5438" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <span>Jadwal Publik</span>
                </a>
            </div>
        </div>

        {{-- Lab Paling Aktif --}}
        @if($labStats->isNotEmpty())
        <div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;padding:16px 18px;box-shadow:0 1px 4px rgba(26,37,23,.05)">
            <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0 0 14px">🏆 Lab Aktif Minggu Ini</h2>
            @foreach($labStats as $i => $ls)
            <div style="display:flex;align-items:center;gap:10px;{{ $loop->last ? '' : 'margin-bottom:10px' }}">
                <span style="font-family:Outfit,sans-serif;font-weight:800;font-size:13px;color:#ACC8A2;width:18px">{{ $i+1 }}</span>
                <p style="flex:1;font-size:12px;font-weight:700;color:#1A2517;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $ls->resource->name ?? '-' }}</p>
                <span style="background:#f0f4ee;color:#1A2517;font-size:11px;font-weight:700;padding:2px 9px;border-radius:999px">{{ $ls->cnt }}x</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Aktivitas Terbaru --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);overflow:hidden">
            <div style="padding:14px 18px;border-bottom:1px solid #f0f4ee">
                <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0">🕐 Aktivitas Terbaru</h2>
            </div>
            @foreach($recentBookings as $b)
            <div style="padding:10px 18px;{{ $loop->first ? '' : 'border-top:1px solid #f9f9f9' }};display:flex;align-items:center;gap:10px">
                <div style="width:7px;height:7px;border-radius:50%;flex-shrink:0;background:{{ $b->status==='pending'?'#f59e0b':($b->status==='approved'?'#22c55e':'#ef4444') }}"></div>
                <div style="flex:1;min-width:0">
                    <p style="font-size:12px;font-weight:600;color:#374151;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $b->teacher_name }} — {{ $b->resource->name ?? '-' }}</p>
                    <p style="font-size:10px;color:#9ca3af;margin:2px 0 0">{{ $b->created_at->diffForHumans() }}</p>
                </div>
                <span class="badge badge-{{ $b->status }}" style="font-size:9px;flex-shrink:0">{{ $b->status }}</span>
            </div>
            @endforeach
        </div>

    </div>
</div>

</x-app-layout>