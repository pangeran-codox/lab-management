<x-app-layout>
<x-slot name="title">Dashboard</x-slot>

@push('styles')
    @vite('resources/css/dashboard.css')
@endpush

@php
// Helper untuk persentase
$totalStatus = array_sum($statusDistribution->toArray()) ?: 1;
$approvedPct = round(($statusDistribution['approved'] ?? 0) / $totalStatus * 100);
$pendingPct  = round(($statusDistribution['pending'] ?? 0) / $totalStatus * 100);
$rejectedPct = round(($statusDistribution['rejected'] ?? 0) / $totalStatus * 100);

$pendingBook = $stats->pending_count;
$todayBook   = $stats->today_count;
$approvedToday = $stats->approved_today_count;
$thisMonthBook = $stats->this_month_count;
$lastMonthBook = $stats->last_month_count;
$monthGrowth   = $lastMonthBook > 0 ? round((($thisMonthBook - $lastMonthBook) / $lastMonthBook) * 100) : 0;

// Data untuk bar chart
$maxDay = count($bookingPerDay) > 0 ? max($bookingPerDay) : 1;
$thisWeekBook = array_sum($bookingPerDay);
@endphp

<div class="db-wrap">

    {{-- ── HEADER ─────────────────────────────────────────── --}}
    <div class="db-header">
        <div>
            <h1 class="db-greeting">
                Selamat datang, {{ auth()->user()->full_name ?? auth()->user()->username }} 👋
            </h1>
            <p class="db-subline">Lab Management Nuris Jember</p>
        </div>

        <div class="db-header-right">
            <div class="clock-card">
                <div class="clock-face">
                    <svg class="clock-svg" viewBox="0 0 56 56" aria-hidden="true">
                        <circle cx="28" cy="28" r="26" fill="none" stroke="#EAF3DE" stroke-width="2"/>
                        <circle cx="28" cy="28" r="26" fill="none" stroke="#639922" stroke-width="2"
                            stroke-dasharray="163.4" stroke-dashoffset="163.4" id="db-sec-ring"
                            stroke-linecap="round" transform="rotate(-90 28 28)"/>
                        <line id="db-h-hand" x1="28" y1="28" x2="28" y2="14" stroke="#27500A" stroke-width="2.5" stroke-linecap="round"/>
                        <line id="db-m-hand" x1="28" y1="28" x2="28" y2="10" stroke="#3B6D11" stroke-width="1.8" stroke-linecap="round"/>
                        <line id="db-s-hand" x1="28" y1="30" x2="28" y2="8"  stroke="#BA7517" stroke-width="1.2" stroke-linecap="round"/>
                        <circle cx="28" cy="28" r="2.5" fill="#639922"/>
                        <circle cx="28" cy="28" r="1.2" fill="#fff"/>
                    </svg>
                </div>
                <div class="clock-text">
                    <div style="display:flex;align-items:baseline;gap:3px">
                        <span class="clock-hm" id="db-clock-hm">--:--</span>
                        <span class="clock-ss" id="db-clock-ss">--</span>
                    </div>
                    <div class="clock-date" id="db-clock-date">-- --- ----</div>
                    <div class="clock-tz">
                        <span class="clock-tz-dot"></span> WIB &middot; UTC+7
                    </div>
                </div>
            </div>

            @if($pendingBook > 0 && in_array(auth()->user()->role, ['admin', 'staff', 'operator', 'teknisi']))
            <a href="{{ route('booking.index') }}?status=pending" class="btn-pending">
                <span class="badge-count">{{ $pendingBook }}</span>
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Booking Pending
            </a>
            @endif
        </div>
    </div>

    {{-- ── ALERTS ──────────────────────────────────────────── --}}
    @if($totalBroken > 0 || ($pendingBook > 0 && in_array(auth()->user()->role, ['admin', 'staff', 'operator', 'teknisi'])))
    <div class="alert-row">
        @if($pendingBook > 0 && in_array(auth()->user()->role, ['admin', 'staff', 'operator', 'teknisi']))
        <div class="alert-box warn">
            <div class="alert-icon">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div style="flex:1">
                <p class="alert-title">{{ $pendingBook }} Booking Menunggu Persetujuan</p>
                <p class="alert-sub">Segera periksa dan berikan keputusan.</p>
            </div>
            <a href="{{ route('booking.index') }}?status=pending" class="alert-cta">Periksa</a>
        </div>
        @endif
        @if($totalBroken > 0)
        <div class="alert-box danger">
            <div class="alert-icon">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div style="flex:1">
                <p class="alert-title">{{ $totalBroken }} Item Inventaris Rusak</p>
                <p class="alert-sub">Ditemukan kerusakan di inventaris lab.</p>
            </div>
            <a href="{{ route('inventory.admin') }}" class="alert-cta">Lihat Detail</a>
        </div>
        @endif
    </div>
    @endif

    {{-- ── STAT CARDS ──────────────────────────────────────── --}}
    <div class="stat-grid">

        <div class="stat-card">
            <div class="stat-stripe" style="background:#639922"></div>
            <div class="stat-icon" style="background:#EAF3DE;color:#3B6D11">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div class="stat-card-label">Total Lab</div>
            <div class="stat-card-val">{{ $totalLab }}</div>
            <div class="stat-card-sub muted">Laboratorium aktif</div>
            <div style="height:38px;margin-top:8px"><canvas id="sp1"></canvas></div>
        </div>

        <div class="stat-card">
            <div class="stat-stripe" style="background:#639922"></div>
            <div class="stat-icon" style="background:#EAF3DE;color:#3B6D11">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="stat-card-label">Jadwal Tetap</div>
            <div class="stat-card-val">{{ $totalSchedule }}</div>
            <div class="stat-card-sub up">
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                Slot terjadwal aktif
            </div>
            <div style="height:38px;margin-top:8px"><canvas id="sp2"></canvas></div>
        </div>

        <div class="stat-card" style="{{ $pendingBook > 0 ? 'border-color:#FAC775' : '' }}">
            <div class="stat-stripe" style="background:#BA7517"></div>
            <div class="stat-icon" style="background:#FAEEDA;color:#854F0B">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-card-label" style="{{ $pendingBook > 0 ? 'color:#854F0B' : '' }}">Booking Pending</div>
            <div class="stat-card-val {{ $pendingBook > 0 ? 'amber' : '' }}">{{ $pendingBook }}</div>
            <div class="stat-card-sub {{ $pendingBook > 0 ? 'warn' : 'muted' }}">
                @if($pendingBook > 0)
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"/></svg>
                    Menunggu persetujuan
                @else
                    Semua sudah diproses ✓
                @endif
            </div>
            <div style="height:38px;margin-top:8px"><canvas id="sp3"></canvas></div>
        </div>

        <div class="stat-card">
            <div class="stat-stripe" style="background:#639922"></div>
            <div class="stat-icon" style="background:#EAF3DE;color:#3B6D11">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="stat-card-label">Booking Hari Ini</div>
            <div class="stat-card-val">{{ $todayBook }}</div>
            <div class="stat-card-sub {{ $monthGrowth >= 0 ? 'up' : 'down' }}">
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $monthGrowth >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                </svg>
                {{ $monthGrowth >= 0 ? '+' : '' }}{{ $monthGrowth }}% vs bulan lalu
            </div>
            <div style="height:38px;margin-top:8px"><canvas id="sp4"></canvas></div>
        </div>

    </div>

    {{-- ── MAIN GRID ───────────────────────────────────────── --}}
    <div class="db-main">

        {{-- ── KOLOM KIRI ──────────────────────────────────── --}}
        <div class="db-left">

            {{-- Grafik Penggunaan Lab Bulan Ini --}}
            <div class="card">
                <div class="card-head">
                    <h2 class="card-head-title">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#3B6D11" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Penggunaan Lab — {{ now()->translatedFormat('F Y') }}
                    </h2>
                    <span class="card-head-sub">booking per hari</span>
                </div>
                <div class="card-body">
                    <div class="chart-wrap" style="height:160px">
                        <canvas id="barChart"></canvas>
                    </div>
                    <div class="stat-mini-row">
                        <div class="stat-mini-item">
                            <p class="sum-num">{{ $thisMonthBook }}</p>
                            <p class="sum-label">Total bulan ini</p>
                        </div>
                        <div class="stat-mini-item">
                            <p class="sum-num" style="color:#3B6D11">{{ $approvedPct }}%</p>
                            <p class="sum-label">Konfirmasi rate</p>
                        </div>
                        <div class="stat-mini-item">
                            <p class="sum-num" style="color:#854F0B">{{ $pendingBook }}</p>
                            <p class="sum-label">Masih pending</p>
                        </div>
                        <div class="stat-mini-item">
                            <p class="sum-num" style="color:{{ $monthGrowth >= 0 ? '#3B6D11' : '#A32D2D' }}">
                                {{ $monthGrowth >= 0 ? '+' : '' }}{{ $monthGrowth }}%
                            </p>
                            <p class="sum-label">vs bulan lalu</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Distribusi status --}}
            <div class="section-pair">
                <div class="card" style="flex:1">
                    <div class="card-head">
                        <h2 class="card-head-title">Distribusi status</h2>
                        <span class="card-head-sub">{{ now()->translatedFormat('F') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="dist-row">
                            <div class="dist-dot" style="background:#639922"></div>
                            <span class="dist-label">Disetujui</span>
                            <div class="dist-bar"><div class="dist-fill" style="width:{{ $approvedPct }}%;background:#639922"></div></div>
                            <span class="dist-val">{{ $statusDistribution['approved'] ?? 0 }}</span>
                        </div>
                        <div class="dist-row">
                            <div class="dist-dot" style="background:#BA7517"></div>
                            <span class="dist-label">Pending</span>
                            <div class="dist-bar"><div class="dist-fill" style="width:{{ $pendingPct }}%;background:#BA7517"></div></div>
                            <span class="dist-val">{{ $statusDistribution['pending'] ?? 0 }}</span>
                        </div>
                        <div class="dist-row">
                            <div class="dist-dot" style="background:#E24B4A"></div>
                            <span class="dist-label">Ditolak</span>
                            <div class="dist-bar"><div class="dist-fill" style="width:{{ $rejectedPct }}%;background:#E24B4A"></div></div>
                            <span class="dist-val">{{ $statusDistribution['rejected'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Booking per hari minggu ini --}}
            <div class="card">
                <div class="card-head">
                    <h2 class="card-head-title">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#3B6D11" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2M3 20h18"/></svg>
                        Booking Minggu Ini
                    </h2>
                    <span class="card-head-sub">Total: {{ $thisWeekBook }}</span>
                </div>
                <div class="bar-chart-wrap">
                    @foreach($bookingPerDay as $day => $cnt)
                    <div class="bar-row">
                        <span class="bar-day">{{ $day }}</span>
                        <div class="bar-bg">
                            <div class="bar-fill" style="width:{{ $maxDay > 0 ? round($cnt/$maxDay*100) : 0 }}%"></div>
                        </div>
                        <span class="bar-cnt">{{ $cnt }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pending Bookings --}}
            @if(in_array(auth()->user()->role, ['admin', 'staff', 'operator', 'teknisi']))
            <div class="card">
                <div class="card-head">
                    <h2 class="card-head-title">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#854F0B" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Menunggu Persetujuan
                        @if($pendingBook > 0)
                        <span class="badge badge-pending">{{ $pendingBook }}</span>
                        @endif
                    </h2>
                    <a href="{{ route('booking.index') }}?status=pending" class="card-head-link">Lihat semua →</a>
                </div>
                @forelse($pendingBookings as $b)
                <div class="pbook-item">
                    <div class="pbook-icon">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <p class="pbook-title">{{ $b->title }}</p>
                        <p class="pbook-meta">
                            {{ $b->teacher_name }} · {{ $b->resource->name ?? '-' }} ·
                            {{ \Carbon\Carbon::parse($b->booking_date)->translatedFormat('d M Y') }}
                            @if($b->timeSlot) · {{ $b->timeSlot->name }}@endif
                        </p>
                    </div>
                    <div class="pbook-actions">
                        <form method="POST" action="{{ route('booking.approve', $b->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-approve">✓ Setuju</button>
                        </form>
                        <a href="{{ route('booking.show', $b->id) }}" class="btn-detail">Detail</a>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-state-icon">✅</div>
                    Tidak ada booking yang menunggu
                </div>
                @endforelse
            </div>
            @endif

        </div>{{-- end db-left --}}

        {{-- ── KOLOM KANAN ─────────────────────────────────── --}}
        <div class="db-right">

            {{-- Status Lab Realtime --}}
            <div class="card">
                <div class="card-head">
                    <h2 class="card-head-title">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#3B6D11" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                        Status Lab
                    </h2>
                    <span class="card-head-sub" id="status-lab-time">{{ now()->format('H:i') }} WIB</span>
                </div>
                @foreach($labStatuses as $ls)
                <div class="lab-status-row">
                    <div class="lab-status-dot" style="background:{{ $ls['is_occupied'] ? '#E24B4A' : '#639922' }}"></div>
                    <div style="flex:1;min-width:0">
                        <p class="lab-status-name">{{ $ls['lab']->name }}</p>
                        <p class="lab-status-sub" style="color:{{ $ls['is_occupied'] ? '#A32D2D' : '#3B6D11' }}">
                            {{ $ls['activity'] ?? 'Tersedia' }}
                        </p>
                        @if($ls['type'])
                        <span class="lab-via">via {{ $ls['type'] === 'booking' ? 'Booking' : 'Jadwal' }}</span>
                        @endif
                    </div>
                    <span class="lab-pill" style="background:{{ $ls['is_occupied'] ? '#FCEBEB' : '#EAF3DE' }};color:{{ $ls['is_occupied'] ? '#A32D2D' : '#3B6D11' }}">
                        {{ $ls['is_occupied'] ? 'Terpakai' : 'Bebas' }}
                    </span>
                </div>
                @endforeach
            </div>

            {{-- Jadwal Penting Aktif --}}
            @if($importantSchedules->isNotEmpty())
            <div class="card">
                <div class="card-head">
                    <h2 class="card-head-title">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#854F0B" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        Jadwal Penting Aktif
                    </h2>
                </div>
                @foreach($importantSchedules as $is)
                <div class="sched-item">
                    <div class="sched-dot" style="background:#BA7517;margin-top:4px"></div>
                    <div style="flex:1;min-width:0">
                        <p class="sched-name">{{ $is->title }}</p>
                        <p class="sched-sub">
                            {{ \Carbon\Carbon::parse($is->start_date)->translatedFormat('d M') }}
                            @if($is->start_date->toDateString() !== $is->end_date->toDateString())
                                – {{ \Carbon\Carbon::parse($is->end_date)->translatedFormat('d M Y') }}
                            @endif
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Aktivitas Terbaru --}}
            <div class="card">
                <div class="card-head">
                    <h2 class="card-head-title">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#3B6D11" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Aktivitas Terbaru
                    </h2>
                </div>
                @foreach($recentBookings as $b)
                <div class="act-item">
                    <div class="act-dot" style="background:{{ $b->status==='pending'?'#f59e0b':($b->status==='approved'?'#22c55e':'#ef4444') }}"></div>
                    <div style="flex:1;min-width:0">
                        <p class="act-name">{{ $b->teacher_name }} — {{ $b->resource->name ?? '-' }}</p>
                        <p class="act-time">{{ $b->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>

        </div>{{-- end db-right --}}

    </div>{{-- end db-main --}}

</div>{{-- end db-wrap --}}

@push('scripts')
<script>
window.dashboardData = {
    totalLab:       {{ $totalLab }},
    totalSchedule:  {{ $totalSchedule }},
    pendingBook:    {{ $pendingBook }},
    todayBook:      {{ $todayBook }},
    monthLabels:    @json($monthlyLabels),
    dailyBookings:  @json($monthlyBookings),
    dailySchedules: @json($monthlySchedules),
    statusApproved: {{ $statusDistribution['approved'] ?? 0 }},
    statusPending:  {{ $statusDistribution['pending'] ?? 0 }},
    statusRejected: {{ $statusDistribution['rejected'] ?? 0 }},
};

// Analog Clock
(function () {
    var days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        var now = new Date();
        var h = now.getHours(), m = now.getMinutes(), s = now.getSeconds(), ms = now.getMilliseconds();
        var elHM   = document.getElementById('db-clock-hm');
        var elSS   = document.getElementById('db-clock-ss');
        var elDate = document.getElementById('db-clock-date');
        var elSR   = document.getElementById('db-sec-ring');
        var elHH   = document.getElementById('db-h-hand');
        var elMH   = document.getElementById('db-m-hand');
        var elSH   = document.getElementById('db-s-hand');
        if (!elHM) return;
        elHM.textContent   = pad(h) + ':' + pad(m);
        elSS.textContent   = pad(s);
        elDate.textContent = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
        var sDeg = (s + ms / 1000) * 6;
        var mDeg = (m + s / 60) * 6;
        var hDeg = ((h % 12) + m / 60) * 30;
        elHH.setAttribute('transform', 'rotate(' + hDeg + ' 28 28)');
        elMH.setAttribute('transform', 'rotate(' + mDeg + ' 28 28)');
        elSH.setAttribute('transform', 'rotate(' + sDeg + ' 28 28)');
        elSR.setAttribute('stroke-dashoffset', Math.round((163.4 - s / 60 * 163.4) * 1000) / 1000);
    }
    tick();
    setInterval(tick, 200);
})();
</script>
@vite('resources/js/dashboard.js')
@endpush

</x-app-layout>
