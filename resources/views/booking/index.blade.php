<x-app-layout>
<x-slot name="title">Manajemen Booking</x-slot>

@push('styles')
@vite('resources/css/booking.css')
@endpush

{{-- Pass route base ke JS --}}
<script>
    window.BOOKING_ROUTE_BASE = '{{ url('/booking') }}';
</script>

<div style="padding:24px">

{{-- ─── FLASH ─── --}}
@if(session('success'))
<div class="flash flash-ok">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash flash-err">⚠ {{ session('error') }}</div>
@endif

{{-- ─── STATS ─── --}}
<div class="stat-grid">
    @foreach([
        ['label'=>'Total Booking', 'value'=>$stats['regular']['total'] + $stats['sunday']['total'], 'color'=>'#6b7280'],
        ['label'=>'Pending',       'value'=>$stats['total_pending'], 'color'=>'#d97706'],
        ['label'=>'Disetujui',     'value'=>$stats['regular']['approved'] + $stats['sunday']['approved'], 'color'=>'#16a34a'],
        ['label'=>'Ditolak',       'value'=>$stats['regular']['rejected'] + $stats['sunday']['rejected'], 'color'=>'#dc2626'],
    ] as $i => $s)
    <div class="stat-card" style="animation-delay:{{ $i*60 }}ms">
        <div class="stat-label">{{ $s['label'] }}</div>
        <div class="stat-val" style="color:{{ $s['color'] }}">{{ $s['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- ════════════════════════════════════════════════
     TABEL MINGGUAN BOOKING
════════════════════════════════════════════════ --}}
<div class="weekly-section">

    {{-- Header + navigasi minggu --}}
    <div class="weekly-hdr">
        <div class="weekly-hdr-left">
            <div class="weekly-title">📅 Jadwal Booking Mingguan</div>
            <span class="weekly-range">
                {{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}
            </span>
        </div>

        <div class="week-nav">
            <button class="week-nav-btn" onclick="navigateWeek(-1)" title="Minggu lalu">‹</button>
            <button class="week-today-btn" onclick="goToday()">Minggu Ini</button>
            <form id="week-form" method="GET" action="{{ route('booking.index') }}" style="display:flex;align-items:center;gap:4px">
                @foreach(request()->except('week') as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <input
                    type="week"
                    id="week-picker"
                    name="week"
                    class="week-picker"
                    value="{{ $weekStart->format('Y') }}-W{{ $weekStart->format('W') }}"
                    onchange="submitWeekForm()"
                >
            </form>
            <button class="week-nav-btn" onclick="navigateWeek(1)" title="Minggu depan">›</button>
        </div>
    </div>

    {{-- Legend --}}
    <div class="bk-legend">
        <div class="bk-legend-item">
            <div class="bk-legend-dot" style="background:#ddf0e4;border:1px solid #a8d9b8"></div>
            Disetujui — klik untuk detail
        </div>
        <div class="bk-legend-item">
            <div class="bk-legend-dot" style="background:#fff8e6;border:1px solid #fcd34d"></div>
            Pending — menunggu persetujuan
        </div>
        <div class="bk-legend-item">
            <div class="bk-legend-dot" style="background:transparent;border:1px dashed #b8d9c8"></div>
            Kosong — klik untuk tambah
        </div>
    </div>

    {{-- Tabs lab --}}
    <div class="bk-lab-tabs">
        @foreach($resources as $i => $resource)
        <button
            onclick="bkSwitchTab({{ $resource->id }})"
            id="bk-tab-{{ $resource->id }}"
            class="bk-tab-btn {{ $i === 0 ? 'active' : '' }}"
        >
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ $resource->name }}
            @php
                $labCount = $weeklyBookings->where('resource_id', $resource->id)->count();
            @endphp
            @if($labCount > 0)
            <span style="background:rgba(172,200,162,.3);color:var(--g8);border-radius:999px;font-size:9px;font-weight:700;padding:1px 6px">{{ $labCount }}</span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- Skeleton loader --}}
    <div class="bk-skeleton" id="bk-skeleton">
        <div class="bk-skel-hdr"></div>
        <div class="bk-skel-body">
            <div class="bk-skel-row">
                <div class="bk-skel-cell sm"></div>
                @for($d = 0; $d < 6; $d++)<div class="bk-skel-cell sm"></div>@endfor
            </div>
            @for($r = 0; $r < 5; $r++)
            <div class="bk-skel-row">
                <div class="bk-skel-cell" style="animation-delay:{{ $r * 40 }}ms"></div>
                @for($d = 0; $d < 6; $d++)
                <div class="bk-skel-cell" style="animation-delay:{{ ($r * 6 + $d) * 25 }}ms"></div>
                @endfor
            </div>
            @endfor
        </div>
    </div>

    {{-- Lab Panels --}}
    @php $today = \Carbon\Carbon::today()->format('Y-m-d'); @endphp

    @foreach($resources as $i => $resource)
    <div id="bk-panel-{{ $resource->id }}" class="bk-panel" style="{{ $i !== 0 ? 'display:none' : '' }}">
        <div class="bk-panel-card">

            {{-- Panel header --}}
            <div class="bk-panel-hdr">
                <div class="bk-panel-hdr-left">
                    <div class="bk-panel-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="bk-panel-name">{{ $resource->name }}</div>
                        @if($resource->capacity)
                        <div class="bk-panel-cap">Kapasitas {{ $resource->capacity }} komputer</div>
                        @endif
                    </div>
                </div>
                @php
                    $panelCount = $weeklyBookings->where('resource_id', $resource->id)->count();
                    $pendingCount = $weeklyBookings->where('resource_id', $resource->id)->where('status', 'pending')->count();
                @endphp
                <div style="display:flex;gap:8px;align-items:center">
                    @if($pendingCount > 0)
                    <span class="badge" style="background:rgba(253,211,77,.15);color:#fcd34d;border:1px solid rgba(253,211,77,.3)">
                        ⏳ {{ $pendingCount }} pending
                    </span>
                    @endif
                    <span style="font-size:11px;color:rgba(172,200,162,.5)">{{ $panelCount }} booking minggu ini</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="bk-tbl-scroll">
                <table class="bk-table">
                    <thead>
                        <tr>
                            <th class="th-time">Jam</th>
                            @foreach($weekDays as $day)
                            @php $isToday = $day['date']->format('Y-m-d') === $today; @endphp
                            <th class="{{ $isToday ? 'th-today' : '' }}">
                                <div class="th-date-main">{{ $day['label'] }}</div>
                                <div class="th-date-sub">{{ $day['display'] }}</div>
                                @if($isToday)
                                <div class="th-today-badge">Hari ini</div>
                                @endif
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $slot)
                        @php
                            $startFmt = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                            $endFmt   = $slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : '';
                            $slotTime = $startFmt . $endFmt;
                        @endphp
                        <tr>
                            <td class="td-bk-time">
                                <div class="slot-name">{{ $slot->name }}</div>
                                <div class="slot-time">{{ $slotTime }}</div>
                            </td>

                            @foreach($weekDays as $day)
                            @php
                                $isToday  = $day['date']->format('Y-m-d') === $today;
                                $dateStr  = $day['date']->format('Y-m-d');
                                $key      = $resource->id . '_' . $dateStr . '_' . $slot->id;
                                $booking  = $bookingGrid->get($key);
                                $dateLabel = $day['full'] . ', ' . $day['date']->translatedFormat('d M Y');
                            @endphp
                            <td class="td-bk-slot {{ $isToday ? 'today' : '' }}">

                                @if($booking)
                                {{-- ─── Slot terisi ─── --}}
                                <div
                                    class="bk-card bk-{{ $booking->status }}"
                                    onclick="bkOpenView(
                                        {{ $booking->id }},
                                        '{{ addslashes($booking->teacher_name) }}',
                                        '{{ addslashes($booking->class_name ?? '-') }}',
                                        '{{ addslashes($booking->subject_name ?? '') }}',
                                        '{{ $booking->status }}',
                                        '{{ addslashes($slot->name) }}',
                                        '{{ $slotTime }}',
                                        '{{ addslashes($dateLabel) }}',
                                        '{{ addslashes($resource->name) }}',
                                        '{{ addslashes($booking->title ?? $booking->teacher_name) }}'
                                    )"
                                >
                                    <div class="bk-card-teacher">{{ $booking->teacher_name }}</div>
                                    <div class="bk-card-class">{{ $booking->class_name ?? '-' }}</div>
                                    @if($booking->subject_name)
                                    <div class="bk-card-subject">{{ $booking->subject_name }}</div>
                                    @endif
                                    <div class="bk-card-foot">
                                        <span class="bk-card-status {{ $booking->status }}">
                                            <span class="bk-card-dot {{ $booking->status === 'pending' ? 'pulse' : '' }}"></span>
                                            {{ $booking->status === 'approved' ? 'Disetujui' : ($booking->status === 'pending' ? 'Pending' : 'Ditolak') }}
                                        </span>
                                        <form method="POST" action="{{ route('booking.destroy', $booking->id) }}"
                                              onsubmit="event.stopPropagation(); return confirm('Hapus booking ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bk-card-del" onclick="event.stopPropagation()" aria-label="Hapus">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @else
                                {{-- ─── Slot kosong ─── --}}
                                <button
                                    class="bk-add-btn"
                                    type="button"
                                    onclick="bkOpenAdd(
                                        {{ $resource->id }},
                                        '{{ addslashes($resource->name) }}',
                                        {{ $slot->id }},
                                        '{{ addslashes($slot->name) }}',
                                        '{{ $slotTime }}',
                                        '{{ $dateStr }}',
                                        '{{ addslashes($dateLabel) }}'
                                    )"
                                    aria-label="Tambah booking {{ $dateLabel }} {{ $slot->name }}"
                                >
                                    <span class="bk-add-icon">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </span>
                                    <span class="bk-add-text">Tambah</span>
                                </button>
                                @endif

                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    @endforeach

</div>{{-- /.weekly-section --}}


{{-- ─── FILTER ─── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('booking.index') }}"
          style="display:flex;flex-wrap:wrap;gap:9px;width:100%;align-items:center">

        @if(request('week'))
        <input type="hidden" name="week" value="{{ request('week') }}">
        @endif

        <div class="search-container">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="🔍 Cari nama, kelas, judul..."
                   class="filter-inp" id="booking-search">
            @if(request('search'))
            <span class="search-clear" onclick="document.getElementById('booking-search').value='';this.closest('form').submit()">×</span>
            @endif
        </div>

        <div style="flex:0 0 auto">
            <select name="status" class="filter-inp" style="width:auto">
                <option value="all" {{ request('status','all')==='all'?'selected':'' }}>Semua Status</option>
                <option value="pending"  {{ request('status')==='pending' ?'selected':'' }}>⏳ Pending</option>
                <option value="approved" {{ request('status')==='approved'?'selected':'' }}>✓ Disetujui</option>
                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>✗ Ditolak</option>
            </select>
        </div>

        <div style="flex:0 0 auto">
            <select name="resource_id" class="filter-inp" style="width:auto">
                <option value="">Semua Lab</option>
                @foreach($resources as $r)
                <option value="{{ $r->id }}" {{ request('resource_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex:0 0 auto">
            <input type="date" name="date" value="{{ request('date') }}" class="filter-inp" style="width:auto">
        </div>

        <button type="submit" class="btn-filter">Filter</button>

        @if(request()->hasAny(['search','status','resource_id','date']))
        <a href="{{ route('booking.index') }}"
           style="font-size:12px;color:var(--muted);text-decoration:none;padding:4px 8px;font-weight:600">Reset</a>
        @endif
    </form>
</div>

{{-- ─── SUNDAY BOOKINGS ─── --}}
@if($sundayBookings->total() > 0)
<div class="table-card" style="margin-bottom:28px;border:2px solid #3b82f6;box-shadow:0 10px 25px rgba(59,130,246,.12)">
    <div class="table-head-bar" style="background:#3b82f6;color:#fff;padding:12px 20px">
        <div>
            <span class="table-title" style="color:#fff">📅 Permintaan Booking Minggu (Full Day)</span>
            <span class="table-count" style="color:rgba(255,255,255,.8)">&nbsp;({{ $sundayBookings->total() }} data)</span>
        </div>
        <span class="badge" style="background:rgba(255,255,255,.2);color:#fff">KHUSUS MINGGU</span>
    </div>
    <div class="tbl-scroll">
        <table>
            <thead>
                <tr>
                    <th style="background:#f8fafb">Tanggal & Lab</th>
                    <th style="background:#f8fafb">Pemohon</th>
                    <th style="background:#f8fafb">Kegiatan</th>
                    <th style="background:#f8fafb" class="th-center">Status</th>
                    <th style="background:#f8fafb" class="th-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sundayBookings as $sb)
                <tr class="{{ $sb->status === 'pending' ? 'row-pending' : ($sb->status === 'approved' ? 'row-approved' : 'row-rejected') }}">
                    <td>
                        <div class="cell-primary">{{ $sb->booking_date->translatedFormat('d M Y') }}</div>
                        <div class="cell-accent" style="color:#2563eb">🖥 {{ $sb->resource->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="cell-primary">{{ $sb->teacher_name }}</div>
                        <div class="cell-secondary">{{ $sb->organization->name ?? '-' }}</div>
                        @if($sb->teacher_phone)
                        <div class="cell-secondary" style="display:flex;align-items:center;gap:8px">
                            📱 {{ substr(preg_replace('/\D/', '', $sb->teacher_phone), 0, 3) }}****{{ substr(preg_replace('/\D/', '', $sb->teacher_phone), -2) }}
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $sb->teacher_phone) }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;background:#25d366;color:#fff;border-radius:6px;text-decoration:none;font-weight:700;font-size:11px">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.52 3.48a11.85 11.85 0 00-17 0 11.89 11.89 0 00-2.74 8.46 11.93 11.93 0 003.56 8.29l.17.18-.4 1.46-1.47.4.18.18a11.92 11.92 0 008.68 3.04h.01a11.94 11.94 0 008.46-3.05 11.89 11.89 0 003.05-8.46 11.85 11.85 0 00-3.48-8.46zM17.86 15.9c-.33.93-1.91 1.78-2.65 1.8-.68.02-1.55.04-2.51-.16-.58-.12-1.32-.42-2.27-.88-3.96-1.93-6.53-6.3-6.7-6.6-.17-.3-1.4-2.33 1.41-4.48 1.25-.97 2.5-1.13 3.04-1.13.47 0 1.09-.18 1.68.9.59 1.08.79 1.87 1.01 2.35.22.48.11.9-.06 1.28-.18.38-.5.61-.93.97-.43.36-.75.54-1.07.72-.32.18-.07.86.43 1.89.5 1.03 1.03 1.69 1.86 2.24 1.22.8 2.22.7 2.89.62.67-.08 2.08-.85 2.38-1.68.3-.83.3-1.54.21-1.54-.08-.13-.28-.2-.6-.32-.32-.16-1.9-.93-2.19-1.04-.29-.11-.5-.16-.72.11-.22.27-.84.98-1.03 1.18-.19.2-.38.22-.7.08-.32-.14-1.34-.49-2.55-1.57-.94-.84-1.57-1.88-1.76-2.2-.19-.32-.02-.49.14-.64.14-.14.33-.36.5-.54.17-.18.27-.3.4-.5.13-.2.06-.37-.03-.52-.09-.15-.79-1.9-1.08-2.58-.29-.68-.58-.58-.72-.59-.12 0-.26 0-.4.06-.14.06-.36.14-.55.42-.19.28-.73.71-.73 1.73 0 1.01.75 1.99.86 2.13.11.14 1.58 2.42 3.82 3.41.54.24 1.04.37 1.49.48.7.17 1.34.14 1.84.09.59-.06 1.9-.78 2.17-1.54.27-.76.27-1.41.19-1.54-.08-.13-.28-.2-.6-.32z"/>
                                </svg>
                                Chat
                            </a>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="cell-primary">{{ $sb->class_name }}</div>
                        <div class="cell-secondary">{{ $sb->title }}</div>
                    </td>
                    <td class="td-center">
                        <span class="badge badge-{{ $sb->status }}">
                            @if($sb->status==='pending')<span class="dot"></span>@endif
                            {{ ucfirst($sb->status) }}
                        </span>
                    </td>
                    <td class="td-center">
                        <div class="act-wrap">
                            @if($sb->status === 'pending')
                                <form method="POST" action="{{ route('booking.approve.sunday', $sb->id) }}"
                                      onsubmit="return confirm('Setujui booking minggu ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-approve-single" style="background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff">✓ Setujui</button>
                                </form>
                                <button class="btn-reject-act"
                                    onclick="openReject({{ $sb->id }}, '{{ addslashes($sb->title) }}', '{{ addslashes($sb->teacher_name) }}', 'sunday')">
                                    ✗ Tolak
                                </button>
                            @endif
                            <form method="POST" action="{{ route('booking.destroy.sunday', $sb->id) }}"
                                  onsubmit="return confirm('Hapus booking minggu ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del" title="Hapus">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($sundayBookings->hasPages())
    <div class="pagi-wrap" style="background:#fff">{{ $sundayBookings->links() }}</div>
    @endif
</div>
@endif

{{-- ─── DAFTAR BOOKING (tabel list) ─── --}}
<div class="table-card">
    <div class="table-head-bar">
        <div>
            <span class="table-title">Daftar Semua Booking</span>
            @if($bookings->total() > 0)
            <span class="table-count">&nbsp;({{ $bookings->total() }} data)</span>
            @endif
        </div>
        @if($stats['regular']['pending'] > 0)
        <span class="badge badge-pending">
            <span class="dot"></span>
            {{ $stats['regular']['pending'] }} menunggu persetujuan
        </span>
        @endif
    </div>

    @if($bookings->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        Belum ada booking ditemukan
    </div>
    @else
    <div class="tbl-scroll">
        <table>
            <thead>
                <tr>
                    <th>Tanggal & Lab</th>
                    <th>Pemohon</th>
                    <th>Kegiatan</th>
                    <th>Slot Waktu</th>
                    <th class="th-center">Status</th>
                    <th class="th-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                @php
                    $rowClass = match($b->status) {
                        'pending'  => 'row-pending',
                        'approved' => 'row-approved',
                        default    => 'row-rejected',
                    };
                    $groupCount = $b->status === 'pending'
                        ? $bookings->where('teacher_name', $b->teacher_name)
                            ->where('resource_id', $b->resource_id)
                            ->where('booking_date', $b->booking_date)
                            ->where('status', 'pending')->count()
                        : 0;
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>
                        <div class="cell-primary">{{ \Carbon\Carbon::parse($b->booking_date)->translatedFormat('d M Y') }}</div>
                        <div class="cell-accent">🖥 {{ $b->resource->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="cell-primary">{{ $b->teacher_name }}</div>
                        <div class="cell-secondary">{{ $b->class_name }}</div>
                        @if($b->teacher_phone)
                        <div class="cell-secondary" style="display:flex;align-items:center;gap:8px">
                            📱 {{ substr(preg_replace('/\D/', '', $b->teacher_phone), 0, 3) }}****{{ substr(preg_replace('/\D/', '', $b->teacher_phone), -2) }}
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $b->teacher_phone) }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;background:#25d366;color:#fff;border-radius:6px;text-decoration:none;font-weight:700;font-size:11px">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.52 3.48a11.85 11.85 0 00-17 0 11.89 11.89 0 00-2.74 8.46 11.93 11.93 0 003.56 8.29l.17.18-.4 1.46-1.47.4.18.18a11.92 11.92 0 008.68 3.04h.01a11.94 11.94 0 008.46-3.05 11.89 11.89 0 003.05-8.46 11.85 11.85 0 00-3.48-8.46zM17.86 15.9c-.33.93-1.91 1.78-2.65 1.8-.68.02-1.55.04-2.51-.16-.58-.12-1.32-.42-2.27-.88-3.96-1.93-6.53-6.3-6.7-6.6-.17-.3-1.4-2.33 1.41-4.48 1.25-.97 2.5-1.13 3.04-1.13.47 0 1.09-.18 1.68.9.59 1.08.79 1.87 1.01 2.35.22.48.11.9-.06 1.28-.18.38-.5.61-.93.97-.43.36-.75.54-1.07.72-.32.18-.07.86.43 1.89.5 1.03 1.03 1.69 1.86 2.24 1.22.8 2.22.7 2.89.62.67-.08 2.08-.85 2.38-1.68.3-.83.3-1.54.21-1.54-.08-.13-.28-.2-.6-.32-.32-.16-1.9-.93-2.19-1.04-.29-.11-.5-.16-.72.11-.22.27-.84.98-1.03 1.18-.19.2-.38.22-.7.08-.32-.14-1.34-.49-2.55-1.57-.94-.84-1.57-1.88-1.76-2.2-.19-.32-.02-.49.14-.64.14-.14.33-.36.5-.54.17-.18.27-.3.4-.5.13-.2.06-.37-.03-.52-.09-.15-.79-1.9-1.08-2.58-.29-.68-.58-.58-.72-.59-.12 0-.26 0-.4.06-.14.06-.36.14-.55.42-.19.28-.73.71-.73 1.73 0 1.01.75 1.99.86 2.13.11.14 1.58 2.42 3.82 3.41.54.24 1.04.37 1.49.48.7.17 1.34.14 1.84.09.59-.06 1.9-.78 2.17-1.54.27-.76.27-1.41.19-1.54-.08-.13-.28-.2-.6-.32z"/>
                                </svg>
                                Chat
                            </a>
                        </div>
                        @endif
                    </td>
                    <td style="max-width:190px">
                        <div class="cell-primary" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $b->title }}</div>
                        <div class="cell-secondary">
                            {{ $b->subject_name ?? '-' }}
                            @if($b->participant_count)
                            · <span style="font-weight:600;color:#6b7280">{{ $b->participant_count }} peserta</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($b->timeSlot)
                        <div class="cell-slot">{{ $b->timeSlot->name }}</div>
                        <div class="cell-time">{{ \Carbon\Carbon::parse($b->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($b->timeSlot->end_time)->format('H:i') }}</div>
                        @else
                        <span style="color:#d1d5db">—</span>
                        @endif
                    </td>
                    <td class="td-center">
                        @if($b->status === 'pending')
                            <span class="badge badge-pending"><span class="dot"></span>Pending</span>
                        @elseif($b->status === 'approved')
                            <span class="badge badge-approved">✓ Disetujui</span>
                            @if($b->approved_at)
                            <div style="font-size:10px;color:var(--muted);margin-top:3px">{{ \Carbon\Carbon::parse($b->approved_at)->format('d/m H:i') }}</div>
                            @endif
                        @else
                            <span class="badge badge-rejected">✗ Ditolak</span>
                        @endif
                    </td>
                    <td class="td-center">
                        <div class="act-wrap">
                            @if($b->status === 'pending')
                                @if($groupCount > 1)
                                <form method="POST" action="{{ route('booking.approve.group') }}"
                                      onsubmit="return confirm('Setujui semua {{ $groupCount }} slot booking {{ $b->teacher_name }} sekaligus?')">
                                    @csrf
                                    <input type="hidden" name="teacher_name" value="{{ $b->teacher_name }}">
                                    <input type="hidden" name="resource_id"  value="{{ $b->resource_id }}">
                                    <input type="hidden" name="booking_date" value="{{ $b->booking_date }}">
                                    <button type="submit" class="btn-approve-group">✓ {{ $groupCount }} Slot</button>
                                </form>
                                <button class="btn-reject-act"
                                    onclick="openRejectGroup('{{ addslashes($b->teacher_name) }}', {{ $b->resource_id }}, '{{ $b->booking_date }}', {{ $groupCount }})">
                                    ✗ {{ $groupCount }} Slot
                                </button>
                                @else
                                <form method="POST" action="{{ route('booking.approve', $b->id) }}"
                                      onsubmit="return confirm('Setujui booking ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-approve-single">✓ Setujui</button>
                                </form>
                                <button class="btn-reject-act"
                                    onclick="openReject({{ $b->id }}, '{{ addslashes($b->title) }}', '{{ addslashes($b->teacher_name) }}')">
                                    ✗ Tolak
                                </button>
                                @endif
                            @else
                                <a href="{{ route('booking.show', $b->id) }}" class="btn-detail">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            @endif
                            <form method="POST" action="{{ route('booking.destroy', $b->id) }}"
                                  onsubmit="return confirm('Hapus booking ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del" title="Hapus">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="pagi-wrap">{{ $bookings->links() }}</div>
    @endif
    @endif
</div>

</div>{{-- /padding wrap --}}


{{-- ════════════════════════════════════════════════
     MODAL: TAMBAH BOOKING (dari tabel mingguan)
════════════════════════════════════════════════ --}}
<div id="bk-add-modal" class="modal-overlay" onclick="if(event.target===this)bkCloseAdd()">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-head-row">
                <div>
                    <p class="modal-eyebrow">Booking Lab</p>
                    <h2 class="modal-head-title">Tambah Booking</h2>
                </div>
                <button class="modal-close" type="button" onclick="bkCloseAdd()" aria-label="Tutup">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-badges">
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span id="bk-add-b-lab">-</span>
                </span>
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span id="bk-add-b-date">-</span>
                </span>
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span id="bk-add-b-slot">-</span>
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('booking.store') }}" class="modal-body" id="bk-add-form">
            @csrf
            <input type="hidden" name="resource_id"  id="bk-add-rid">
            <input type="hidden" name="time_slot_id" id="bk-add-sid">
            <input type="hidden" name="booking_date" id="bk-add-date">
            <input type="hidden" name="status" value="approved">

            <div>
                <label class="field-label" for="bk-add-teacher">Nama Guru *</label>
                <input id="bk-add-teacher" name="teacher_name" type="text" class="inp" required placeholder="Nama guru...">
            </div>

            <div>
                <label class="field-label" for="bk-add-title">Judul Kegiatan *</label>
                <input id="bk-add-title" name="title" type="text" class="inp" required placeholder="Contoh: Pembelajaran TIK Kelas 8">
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label" for="bk-add-class">Kelas</label>
                    <input id="bk-add-class" name="class_name" type="text" class="inp" placeholder="Contoh: 8A">
                </div>
                <div>
                    <label class="field-label" for="bk-add-subject">Mata Pelajaran</label>
                    <input id="bk-add-subject" name="subject_name" type="text" class="inp" placeholder="Contoh: TIK">
                </div>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label" for="bk-add-phone">No HP</label>
                    <input id="bk-add-phone" name="teacher_phone" type="text" class="inp" placeholder="08...">
                </div>
                <div>
                    <label class="field-label" for="bk-add-count">Jumlah Peserta</label>
                    <input id="bk-add-count" name="participant_count" type="number" min="1" class="inp" placeholder="30">
                </div>
            </div>

            <div>
                <label class="field-label" for="bk-add-notes">Catatan</label>
                <input id="bk-add-notes" name="notes" type="text" class="inp" placeholder="Opsional">
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" onclick="bkCloseAdd()" class="btn-ghost">Batal</button>
            <button type="submit" form="bk-add-form" class="btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Simpan Booking
            </button>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════════
     MODAL: VIEW/APPROVE/REJECT BOOKING
════════════════════════════════════════════════ --}}
<div id="bk-view-modal" class="modal-overlay" onclick="if(event.target===this)bkCloseView()">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-head-row">
                <div>
                    <p class="modal-eyebrow">Detail Booking</p>
                    <h2 class="modal-head-title" id="bk-view-title">-</h2>
                </div>
                <button class="modal-close" type="button" onclick="bkCloseView()" aria-label="Tutup">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-badges">
                <span class="mbadge"><span id="bk-view-b-lab">-</span></span>
                <span class="mbadge"><span id="bk-view-b-date">-</span></span>
                <span class="mbadge"><span id="bk-view-b-slot">-</span></span>
            </div>
        </div>

        <div class="modal-body">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#f8fcf7;border-radius:10px;border:1px solid var(--border)">
                <div>
                    <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px">Status</div>
                    <span id="bk-view-status" class="badge">-</span>
                </div>
                <div style="text-align:right">
                    <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px">Guru</div>
                    <div id="bk-view-teacher" style="font-weight:700;color:var(--g9);font-size:13px">-</div>
                </div>
            </div>

            <div class="field-row">
                <div>
                    <div class="field-label">Kelas</div>
                    <div id="bk-view-class" style="font-weight:600;color:var(--text);font-size:13px">-</div>
                </div>
                <div>
                    <div class="field-label">Mata Pelajaran</div>
                    <div id="bk-view-subject" style="font-weight:600;color:var(--text);font-size:13px">-</div>
                </div>
            </div>
        </div>

        <div class="modal-footer" style="flex-wrap:wrap">
            {{-- Approve (hanya pending) --}}
            <form id="bk-view-approve-form" method="POST" style="display:none">
                @csrf @method('PATCH')
                <button type="submit" class="btn-primary" onclick="return confirm('Setujui booking ini?')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Setujui
                </button>
            </form>

            {{-- Reject (hanya pending) --}}
            <button id="bk-view-reject-btn" type="button" class="btn-danger" style="display:none">
                ✗ Tolak
            </button>

            {{-- Detail --}}
            <a id="bk-view-detail-link" href="#" class="btn-ghost">
                Lihat Detail
            </a>

            {{-- Delete --}}
            <form id="bk-view-delete-form" method="POST" onsubmit="return confirm('Hapus booking ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger" style="background:#dc2626">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </form>

            <button type="button" onclick="bkCloseView()" class="btn-ghost">Tutup</button>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════════
     MODAL: REJECT
════════════════════════════════════════════════ --}}
<div id="reject-modal" class="modal-overlay" onclick="if(event.target===this)closeReject()">
    <div class="modal-box" style="max-width:440px">
        <div class="modal-head modal-head-red">
            <h3 class="modal-head-title">Tolak Booking</h3>
            <p id="reject-subtitle" class="modal-head-sub"></p>
        </div>
        <div class="modal-body">
            <form id="reject-form" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="type" id="reject-type" value="regular">
                <label class="field-label field-label-red">Alasan Penolakan *</label>
                <textarea name="notes" rows="3" required class="inp-textarea"
                    placeholder="Contoh: Slot sudah terpakai untuk kegiatan lain..."></textarea>
                <div class="modal-footer" style="padding:16px 0 0;border:none">
                    <button type="button" onclick="closeReject()" class="btn-cancel">Batal</button>
                    <button type="submit" id="reject-submit-btn" class="btn-reject-submit">✗ Tolak Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@vite('resources/js/booking.js')
@endpush

</x-app-layout>