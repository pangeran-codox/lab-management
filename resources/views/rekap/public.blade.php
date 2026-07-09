{{-- resources/views/rekap/public.blade.php --}}
@extends('layouts.public-schedule')

@section('title', 'Rekap Penggunaan Lab')

@section('vite')
@vite(['resources/css/rekap.css'])
@endsection

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="hero">
    <h1>📊 Rekap Penggunaan Laboratorium</h1>
    <p>{{ $months[$month] }} {{ $year }} · Jadwal tetap & booking digabung</p>
</div>

{{-- ═══ FILTER ═══ --}}
<div class="filter-bar">
    <form method="GET" action="/rekap" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <select name="month" class="inp">
            @foreach($months as $m => $mName)
            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $mName }}</option>
            @endforeach
        </select>
        <select name="year" class="inp">
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Tampilkan</button>
    </form>
    <div class="period-lbl">{{ $startDate->translatedFormat('d M') }} – {{ $endDate->translatedFormat('d M Y') }}</div>
</div>

{{-- ═══ MAIN WRAP ═══ --}}
<div class="wrap">

    {{-- SUMMARY --}}
    <div class="sum-grid">
        <div class="sum-card">
            <div class="sum-lbl">Total Kapasitas</div>
            <div class="sum-val" style="color:#6b7280">{{ number_format($summary['total_capacity']) }}</div>
            <div class="sum-sub">Slot tersedia bulan ini</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Jadwal Tetap</div>
            <div class="sum-val" style="color:#1A2517">{{ number_format($summary['total_scheduled']) }}</div>
            <div class="sum-sub">Slot terisi rutin</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Booking</div>
            <div class="sum-val" style="color:#2563eb">{{ number_format($summary['total_booking']) }}</div>
            <div class="sum-sub">Booking disetujui</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Tingkat Penggunaan</div>
            @php
                $pc       = $summary['total_pct'];
                $pcColor  = $pc >= 70 ? '#16a34a' : ($pc >= 40 ? '#d97706' : '#dc2626');
                $barColor = $pc >= 70 ? '#22c55e' : ($pc >= 40 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="sum-val" style="color:{{ $pcColor }}">{{ $pc }}%</div>
            <div class="sum-sub">{{ number_format($summary['total_used']) }} dari {{ number_format($summary['total_capacity']) }} slot</div>
            <div class="pbar-wrap">
                <div class="pbar" style="width:{{ $pc }}%;background:{{ $barColor }}"></div>
            </div>
        </div>
    </div>

    {{-- GRAFIK PEMAKAIAN LAB --}}
    <div class="overview-card" style="margin-top: 20px;">
        <div class="overview-title">📈 Grafik Pemakaian Bulan Ini</div>
        <div class="chart-wrap" style="height: 300px; padding: 16px;">
            <canvas id="usageChart"></canvas>
        </div>
    </div>

    {{-- RINGKASAN PENGGUNAAN LEMBAGA --}}
    @if(count($lembagaUsage) > 0)
    <div class="overview-card" style="margin-top: 20px;">
        <div class="overview-title">🏫 Ringkasan Penggunaan Lembaga</div>
        <table class="tbl" style="width: 100%;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Lembaga</th>
                    <th class="tc">Jadwal Tetap</th>
                    <th class="tc">Booking</th>
                    <th class="tc">Total Sesi</th>
                    <th>Pengajar Terbanyak</th>
                </tr>
            </thead>
            <tbody>
                @php $idx = 1; @endphp
                @foreach($lembagaUsage as $lembagaName => $data)
                @php
                    $topTeacher = array_key_first($data['teacherUsage'] ?? []);
                    $topCount   = $topTeacher ? $data['teacherUsage'][$topTeacher] : 0;
                @endphp
                <tr>
                    <td class="tc">{{ $idx++ }}</td>
                    <td><strong>{{ $lembagaName }}</strong></td>
                    <td class="tc">{{ number_format($data['scheduledSlots']) }}×</td>
                    <td class="tc">{{ number_format($data['bookingSlots']) }}×</td>
                    <td class="tc">
                        <span style="font-weight:700;color:#00693E">{{ number_format($data['sessionCount']) }} sesi</span>
                    </td>
                    <td>
                        @if($topTeacher)
                            <span style="font-weight:600">{{ $topTeacher }}</span>
                            <span style="color:#9ca3af;font-size:11px"> ({{ $topCount }} sesi)</span>
                        @else
                            <span style="color:#9ca3af">–</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- EXPORT BAR --}}
    <div class="export-bar">
        <span class="export-bar-label">Export:</span>
        <button class="btn-exp btn-exp-xl" onclick="exportExcel()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 17l1.5-2.5L8.5 12H10l.75 1.5L11.5 12H13l-1.5 2.5L13 17h-1.5l-.75-1.5-.75 1.5H8.5z"/></svg>
            Excel
        </button>
        <button class="btn-exp btn-exp-csv" onclick="exportCSV()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/></svg>
            CSV
        </button>
        <a id="btn-export-pdf" href="{{ route('rekap.public.pdf', ['month' => request('month'), 'year' => request('year')]) }}" class="btn-exp btn-exp-pdf">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editor & Cetak PDF
        </a>
        <button class="btn-exp btn-exp-print" onclick="window.print()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6v-8z"/></svg>
            Print
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         HEATMAP OVERVIEW SEMUA LAB
    ══════════════════════════════════════════════════════════ --}}
    <div class="overview-card">
        <div class="overview-title">Heatmap Penggunaan — Semua Lab (Per Hari dalam Seminggu)</div>
        <div class="overview-heatmap">
            <div class="ohm-header">
                <div class="ohm-lab-col"></div>
                @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d)
                    <div class="ohm-day {{ $d === 'Min' ? 'ohm-day-sun' : '' }}">{{ $d }}</div>
                @endforeach
            </div>
            @foreach($labData as $lab)
                @php
                    $dayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                    $schedByDay = collect($lab['scheduleDetails'])->groupBy('day_of_week');
                @endphp
                <div class="ohm-row">
                    <div class="ohm-lab">{{ $lab['resource']->name }}</div>
                    @foreach($dayNames as $dn)
                        @php
                            $cnt = $schedByDay->get($dn, collect())->count();
                            $pct = $totalSlotPerDay > 0 ? ($cnt / $totalSlotPerDay) * 100 : 0;
                            $lvl = $pct == 0 ? 0 : ($pct <= 25 ? 1 : ($pct <= 50 ? 2 : ($pct <= 75 ? 3 : 4)));
                        @endphp
                        <div class="ohm-cell heat-{{ $lvl }} {{ $dn === 'Sunday' ? 'heat-sun-cell' : '' }}"
                             title="{{ round($pct) }}% — {{ $cnt }} slot"></div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="heat-legend">
            <span class="hl-item"><span class="hl-dot heat-0"></span>Tidak ada</span>
            <span class="hl-item"><span class="hl-dot heat-1"></span>1–25%</span>
            <span class="hl-item"><span class="hl-dot heat-2"></span>26–50%</span>
            <span class="hl-item"><span class="hl-dot heat-3"></span>51–75%</span>
            <span class="hl-item"><span class="hl-dot heat-4"></span>76–100%</span>
            <span class="hl-item"><span class="hl-dot heat-sun-cell"></span>Minggu</span>
        </div>
    </div>

    {{-- TABS --}}
    <div class="tabs">
        @foreach($labData as $i => $lab)
        <button class="tab {{ $i === 0 ? 'on' : '' }}" 
                onclick="switchTab({{ $i }}, {{ $lab['resource']->id }})"
                data-resource-id="{{ $lab['resource']->id }}">
            <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ $lab['resource']->name }} @if($lab['resource']->organization) ({{ $lab['resource']->organization->name }}) @endif
            <span class="tab-pct">{{ $lab['percentage'] }}%</span>
        </button>
        @endforeach
    </div>

    {{-- PANELS --}}
    @foreach($labData as $i => $lab)
    @php
        $pct      = $lab['percentage'];
        $pctColor = $pct >= 70 ? '#86efac' : ($pct >= 40 ? '#fcd34d' : '#f87171');
        $ps       = $lab['totalCapacity'] > 0 ? ($lab['scheduledSlots'] / $lab['totalCapacity'] * 100) : 0;
        $pb       = $lab['totalCapacity'] > 0 ? ($lab['bookingSlots']   / $lab['totalCapacity'] * 100) : 0;

        // Top teachers dari booking
        $topTeachers = $lab['bookingDetails']
            ->groupBy('teacher_name')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(5);

        // Kepadatan per hari
        $dayLabels = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
        $schedByDay = collect($lab['scheduleDetails'])->groupBy('day_of_week');
        $bookByDay  = $lab['bookingDetails']->groupBy(fn($b) => \Carbon\Carbon::parse($b->booking_date)->format('l'));
        $dayDensity = [];
        foreach ($dayLabels as $dn => $dlabel) {
            $sc  = $schedByDay->get($dn, collect())->count();
            $bc  = $bookByDay->get($dn, collect())->count();
            $tot = $sc + $bc;
            $dayDensity[$dn] = [
                'label'    => $dlabel,
                'pct'      => $totalSlotPerDay > 0 ? round(($tot / $totalSlotPerDay) * 100) : 0,
                'isSunday' => $dn === 'Sunday',
            ];
        }
    @endphp
    <div class="panel {{ $i === 0 ? 'on' : '' }}" id="panel-{{ $i }}" 
         data-teacher-usage="{{ json_encode($lab['teacherUsage']) }}"
         data-total-used="{{ $lab['totalUsed'] }}">
        <div class="lab-card">

            {{-- Header --}}
            <div class="lab-hdr">
                <div>
                    <h2 style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:18px;color:#fff;margin:0">
                        🏫 {{ $lab['resource']->name }} @if($lab['resource']->organization) ({{ $lab['resource']->organization->name }}) @endif
                    </h2>
                    <p style="font-size:11px;color:rgba(172,200,162,.4);margin-top:4px">
                        {{ $lab['totalCapacity'] }} slot kapasitas · {{ $totalSlotPerDay }} slot/hari
                        @if($lab['resource']->building) · {{ $lab['resource']->building }} @endif
                    </p>
                </div>
                <div style="display:flex;align-items:center;gap:14px">
                    <div style="text-align:right">
                        <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:32px;font-weight:800;color:{{ $pctColor }};line-height:1">{{ $pct }}%</div>
                        <div style="font-size:10px;color:rgba(172,200,162,.35);margin-top:2px">Tingkat Penggunaan</div>
                    </div>
                    <svg viewBox="0 0 36 36" style="width:56px;height:56px;transform:rotate(-90deg)">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(172,200,162,.1)" stroke-width="3.5"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $pctColor }}" stroke-width="3.5"
                            stroke-dasharray="{{ $pct }} {{ 100-$pct }}" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            {{-- Stats --}}
            <div class="stat-row">
                <div class="stat-cell" style="background:rgba(172,200,162,.05)">
                    <div class="stat-val" style="color:#1A2517">{{ $lab['scheduledSlots'] }}</div>
                    <div class="stat-key">Jadwal Tetap</div>
                </div>
                <div class="stat-cell" style="background:#eff6ff">
                    <div class="stat-val" style="color:#2563eb">{{ $lab['bookingSlots'] }}</div>
                    <div class="stat-key">Booking</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-val" style="color:#1A2517">{{ $lab['totalUsed'] }}</div>
                    <div class="stat-key">Total Terpakai</div>
                </div>
                <div class="stat-cell" style="background:#f9fafb">
                    <div class="stat-val" style="color:#9ca3af">{{ $lab['totalFree'] }}</div>
                    <div class="stat-key">Slot Kosong</div>
                </div>
            </div>

            {{-- Progress --}}
            <div class="prog">
                <div class="prog-row">
                    <span class="prog-lbl">Jadwal Tetap</span>
                    <div class="prog-track">
                        <div class="prog-fill" style="width:{{ $ps }}%;background:linear-gradient(90deg,#ACC8A2,#3d5438)"></div>
                    </div>
                    <span class="prog-pct" style="color:#1A2517">{{ round($ps,1) }}%</span>
                </div>
                <div class="prog-row">
                    <span class="prog-lbl">Booking</span>
                    <div class="prog-track">
                        <div class="prog-fill" style="width:{{ $pb }}%;background:linear-gradient(90deg,#93c5fd,#2563eb)"></div>
                    </div>
                    <span class="prog-pct" style="color:#2563eb">{{ round($pb,1) }}%</span>
                </div>
            </div>

            {{-- Calendar --}}
            <div class="cal">
                <div class="sec-lbl">Kalender Penggunaan — {{ $months[$month] }} {{ $year }}</div>
                <div class="cal-grid">
                    @foreach($lab['dailyData'] as $day)
                    @php
                        $pct2 = $day['capacity'] > 0 ? ($day['total'] / $day['capacity']) * 100 : 0;
                        $dc   = 'dc-mt';
                        if ($day['isSunday'])                                  $dc = 'dc-sun';
                        elseif ($day['schedule'] > 0 && $day['booking'] > 0)  $dc = 'dc-both';
                        elseif ($day['schedule'] > 0)                          $dc = 'dc-sch';
                        elseif ($day['booking']  > 0)                          $dc = 'dc-book';
                    @endphp
                    <div class="dc {{ $dc }}"
                         style="{{ $day['isToday'] ? 'outline:2px solid #ACC8A2;outline-offset:1px;' : '' }}"
                         title="{{ $day['date']->translatedFormat('d M Y') }} — Jadwal: {{ $day['schedule'] }} | Booking: {{ $day['booking'] }} | Total: {{ $day['total'] }}/{{ $day['capacity'] }} slot ({{ round($pct2) }}%)">
                        <div>{{ $day['date']->format('d') }}</div>
                        @if(!$day['isSunday'] && $day['total'] > 0)
                        <div style="font-size:8px;margin-top:1px">{{ $day['total'] }}/{{ $day['capacity'] }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="legend">
                    @foreach([
                        ['dc-sch leg-dot', 'Jadwal Tetap'],
                        ['dc-book leg-dot', 'Booking'],
                        ['dc-both leg-dot', 'Keduanya'],
                        ['dc-mt leg-dot',  'Kosong'],
                        ['dc-sun leg-dot',  'Minggu'],
                    ] as $l)
                    <div class="leg">
                        <div class="leg-dot dc {{ $l[0] }}" style="padding:0;min-width:14px"></div>
                        {{ $l[1] }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ══ TOP TEACHERS + KEPADATAN PER HARI + DONUT ══ --}}
            <div class="insight-row">

                {{-- Top Pengajar --}}
                <div class="insight-card">
                    <div class="sec-lbl">Top Pengajar Bulan Ini</div>
                    @php
                        $topTeachers = array_slice($lab['teacherUsage'], 0, 5, true);
                        $totalUsed = $lab['totalUsed'];
                    @endphp
                    @forelse($topTeachers as $name => $count)
                        <div class="teacher-row">
                            <div class="teacher-rank">{{ $loop->iteration }}</div>
                            <div class="teacher-name">{{ $name }}</div>
                            <div class="teacher-count">
                                {{ $count }} sesi
                                <span style="color:#9ca3af;font-size:10px;margin-left:4px">
                                    ({{ $totalUsed > 0 ? round(($count / $totalUsed) * 100, 2) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="insight-empty">
                            Belum ada penggunaan bulan ini
                        </div>
                    @endforelse
                </div>

                {{-- Kepadatan per Hari --}}
                <div class="insight-card">
                    <div class="sec-lbl">Kepadatan per Hari</div>
                    @foreach($dayDensity as $dn => $d)
                        <div class="bar-row">
                            <div class="bar-label">{{ $d['label'] }}</div>
                            <div class="bar-track">
                                <div class="bar-fill {{ $d['isSunday'] ? 'bar-sun' : '' }}"
                                     style="width:{{ $d['pct'] }}%"></div>
                            </div>
                            <div class="bar-val {{ $d['isSunday'] ? 'bar-val-sun' : '' }}">{{ $d['pct'] }}%</div>
                        </div>
                    @endforeach
                </div>

                {{-- Donut Chart Pengajar --}}
                @if(!empty($lab['teacherUsage']))
                <div class="insight-card">
                    <div class="sec-lbl">Proporsi Pengajar</div>
                    <div class="donut-content">
                        <canvas class="donut-canvas"
                            data-teacher-usage="{{ json_encode($lab['teacherUsage']) }}"
                            data-total-used="{{ $lab['totalUsed'] }}"
                            width="160" height="160"
                            style="width:160px;height:160px;flex-shrink:0">
                        </canvas>
                        <div class="donut-legend"></div>
                    </div>
                </div>
                @endif

            </div>

            {{-- Detail --}}
            <div class="detail">

                @if($lab['scheduleDetails']->isNotEmpty())
                <div class="detail-heading" style="color:#3d5438">
                    <span>📅</span> Jadwal Tetap
                    <span style="font-size:11px;font-weight:400;color:#9ca3af">
                        ({{ $lab['scheduleDetails']->count() }} entri · {{ $lab['scheduledSlots'] }} slot/bulan)
                    </span>
                </div>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Slot Waktu</th>
                                <th>Guru / Pengajar</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th style="text-align:center">Frekuensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $dayColorIdx = ['Monday'=>0,'Tuesday'=>1,'Wednesday'=>2,'Thursday'=>3,'Friday'=>4,'Saturday'=>5,'Sunday'=>6]; @endphp
                            @foreach($lab['scheduleDetails'] as $sd)
                            <tr>
                                <td>
                                    <span class="badge badge-day-{{ $dayColorIdx[$sd->day_of_week] ?? 0 }}">
                                        {{ $sd->day_name_id }}
                                    </span>
                                </td>
                                <td style="color:#6b7280;font-weight:600;white-space:nowrap">
                                    {{ $sd->timeSlot?->name ?? '-' }}
                                    @if($sd->timeSlot)
                                    <div style="font-size:10px;color:#9ca3af">
                                        {{ \Carbon\Carbon::parse($sd->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($sd->timeSlot->end_time)->format('H:i') }}
                                    </div>
                                    @endif
                                </td>
                                <td style="font-weight:700;color:#1A2517">{{ $sd->teacher_name }}</td>
                                <td style="color:#374151">{{ $sd->labClass?->name ?? '-' }}</td>
                                <td style="color:#6b7280">{{ $sd->subject_name ?? '-' }}</td>
                                <td style="text-align:center">
                                    <span class="badge badge-freq">{{ $sd->occurrences }}×/bln</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($lab['bookingDetails']->isNotEmpty())
                <div class="detail-heading" style="color:#1d4ed8">
                    <span>📝</span> Booking Disetujui
                    <span style="font-size:11px;font-weight:400;color:#9ca3af">
                        ({{ $lab['bookingDetails']->count() }} booking)
                    </span>
                </div>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Slot Waktu</th>
                                <th>Pengajar</th>
                                <th>Kelas</th>
                                <th>Kegiatan / Mapel</th>
                                <th style="text-align:center">Peserta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lab['bookingDetails'] as $bd)
                            <tr>
                                <td style="white-space:nowrap">
                                    <div style="font-weight:700;color:#1A2517">
                                        {{ \Carbon\Carbon::parse($bd->booking_date)->translatedFormat('d M Y') }}
                                    </div>
                                    <div style="font-size:10px;color:#9ca3af">
                                        {{ \Carbon\Carbon::parse($bd->booking_date)->translatedFormat('l') }}
                                    </div>
                                </td>
                                <td style="color:#6b7280;font-weight:600;white-space:nowrap">
                                    {{ $bd->timeSlot?->name ?? '-' }}
                                    @if($bd->timeSlot)
                                    <div style="font-size:10px;color:#9ca3af">
                                        {{ \Carbon\Carbon::parse($bd->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($bd->timeSlot->end_time)->format('H:i') }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight:700;color:#1A2517">{{ $bd->teacher_name }}</div>
                                    @if($bd->teacher_phone)
                                    <div style="font-size:10px;color:#9ca3af">{{ $bd->teacher_phone }}</div>
                                    @endif
                                </td>
                                <td style="color:#374151">{{ $bd->class_name ?? '-' }}</td>
                                <td>
                                    <div style="font-weight:600;color:#1A2517">{{ $bd->title }}</div>
                                    @if($bd->subject_name)
                                    <div style="font-size:10px;color:#9ca3af">{{ $bd->subject_name }}</div>
                                    @endif
                                </td>
                                <td style="text-align:center">
                                    <span class="badge badge-book">{{ $bd->participant_count ?? '-' }} org</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($lab['scheduleDetails']->isEmpty() && $lab['bookingDetails']->isEmpty())
                <div class="empty">
                    <div style="font-size:36px;margin-bottom:10px">📭</div>
                    Tidak ada penggunaan pada bulan ini
                </div>
                @endif

            </div>{{-- /detail --}}
        </div>{{-- /lab-card --}}
    </div>{{-- /panel --}}
    @endforeach

    <p style="text-align:center;font-size:12px;color:#9ca3af;margin-top:8px">
        Data diperbarui otomatis · Lab Management Nuris Jember
    </p>

</div>{{-- /wrap --}}

@endsection

@section('scripts')
<script>
// Track the currently active resource ID
var activeResourceId = {{ $labData[0]['resource']->id ?? 'null' }};
var month = {{ request('month', $month) }};
var year = {{ request('year', $year) }};

// Data untuk Chart.js (untuk file rekap.js)
window.rekapChartData = {
    labData: @json($labData),
    months: @json($months),
    currentMonth: {{ $month }},
    currentYear: {{ $year }}
};

function switchTab(idx, resourceId) {
    document.querySelectorAll('.panel').forEach(function(p) { p.classList.remove('on'); });
    document.querySelectorAll('.tab').forEach(function(t)   { t.classList.remove('on'); });
    document.getElementById('panel-' + idx).classList.add('on');
    document.querySelectorAll('.tab')[idx].classList.add('on');
    
    // Update the active resource ID
    activeResourceId = resourceId;
    
    // Update the PDF button href
    updatePdfButton();
    
    window.scrollTo({ top: 120, behavior: 'smooth' });
}

function updatePdfButton() {
    var btn = document.getElementById('btn-export-pdf');
    var url = "{{ route('rekap.public.pdf') }}";
    var params = new URLSearchParams();
    params.append('month', month);
    params.append('year', year);
    if (activeResourceId) {
        params.append('resource_id', activeResourceId);
    }
    btn.href = url + '?' + params.toString();
}

function getActiveLabName() {
    var t = document.querySelector('.tab.on');
    return t ? t.textContent.trim().split('\n')[0].trim() : 'Rekap';
}
function getPeriod() {
    var l = document.querySelector('.period-lbl');
    return l ? l.textContent.trim() : '';
}
function getActiveTableData() {
    var panel = document.querySelector('.panel.on');
    if (!panel) return { jadwal: [], booking: [] };
    var jadwal = [], booking = [];
    panel.querySelectorAll('table.tbl').forEach(function(tbl) {
        var h = tbl.closest('.tbl-wrap').previousElementSibling;
        var isBook = h && h.textContent.includes('Booking');
        var target = isBook ? booking : jadwal;
        var headers = Array.from(tbl.querySelectorAll('thead th')).map(function(th) { return th.textContent.trim(); });
        if (target.length === 0) target.push(headers);
        tbl.querySelectorAll('tbody tr').forEach(function(row) {
            target.push(Array.from(row.querySelectorAll('td')).map(function(td) {
                return td.textContent.trim().replace(/\s+/g, ' ');
            }));
        });
    });
    return { jadwal: jadwal, booking: booking };
}
function getSummaryRows() {
    return Array.from(document.querySelectorAll('.sum-card')).map(function(c) {
        return [
            c.querySelector('.sum-lbl')?.textContent.trim() || '',
            c.querySelector('.sum-val')?.textContent.trim() || '',
            c.querySelector('.sum-sub')?.textContent.trim() || '',
        ];
    });
}

// ══════════════════════════════════════════════════════════════════
// SheetJS Lazy Loader untuk Rekap
// ══════════════════════════════════════════════════════════════════
let xlsxLoaded    = false;
let xlsxLoading   = false;
let xlsxCallbacks = [];

function loadXLSX(callback) {
    if (xlsxLoaded) { callback(); return; }

    xlsxCallbacks.push(callback);
    if (xlsxLoading) return;
    xlsxLoading = true;

    // Cari tombol Excel untuk tampilkan loading
    const btn = Array.from(document.querySelectorAll('.btn-exp')).find(b => b.textContent.includes('Excel'));
    const originalHTML = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = `<span class="xlsx-loading">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline-block;vertical-align:middle;margin-right:5px;animation:spin 1s linear infinite;">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
            </svg>
            Memuat...
        </span>`;
        btn.disabled = true;
    }

    const script  = document.createElement('script');
    script.src    = 'https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js';
    script.onload = function () {
        xlsxLoaded  = true;
        xlsxLoading = false;
        if (btn) {
            btn.innerHTML = originalHTML;
            btn.disabled  = false;
        }
        xlsxCallbacks.forEach(cb => cb());
        xlsxCallbacks = [];
    };
    script.onerror = function () {
        xlsxLoading   = false;
        xlsxCallbacks = [];
        if (btn) {
            btn.innerHTML = originalHTML;
            btn.disabled  = false;
        }
        alert('❌ Gagal memuat library Excel. Cek koneksi internet.');
    };
    document.head.appendChild(script);
}

function exportExcel() {
    loadXLSX(function() { doExportExcel(); });
}

function doExportExcel() {
    var lab = getActiveLabName(), period = getPeriod(), wb = XLSX.utils.book_new();

    // ── Sheet 1: Ringkasan Lembaga ─────────────────────────────
    var lembagaData = @json($lembagaUsage);
    var lembagaRows = [
        ['RINGKASAN PENGGUNAAN LEMBAGA'],
        ['Periode: ' + period],
        [],
        ['No', 'Lembaga', 'Jadwal Tetap', 'Booking', 'Total Sesi', 'Pengajar Terbanyak']
    ];
    var no = 1;
    for (var nama in lembagaData) {
        var d = lembagaData[nama];
        var topTeacher = Object.keys(d.teacherUsage || {})[0] || '-';
        var topCount   = d.teacherUsage ? (d.teacherUsage[topTeacher] || 0) : 0;
        lembagaRows.push([
            no++,
            nama,
            d.scheduledSlots,
            d.bookingSlots,
            d.sessionCount,
            topTeacher !== '-' ? topTeacher + ' (' + topCount + ' sesi)' : '-'
        ]);
    }
    var wsLembaga = XLSX.utils.aoa_to_sheet(lembagaRows);
    wsLembaga['!cols'] = [{ wch: 5 }, { wch: 28 }, { wch: 14 }, { wch: 10 }, { wch: 12 }, { wch: 30 }];
    XLSX.utils.book_append_sheet(wb, wsLembaga, 'Ringkasan Lembaga');

    // ── Sheet 2: Rekap Guru (per lab aktif) ────────────────────
    var panel = document.querySelector('.panel.on');
    var teacherUsage = JSON.parse(panel.dataset.teacherUsage || '{}');
    var totalUsed = parseInt(panel.dataset.totalUsed || '0');
    var sortedTeachers = Object.entries(teacherUsage).sort(function(a, b) { return b[1] - a[1]; });

    var teacherRows = [
        ['REKAP PENGGUNAAN GURU LAB'],
        ['Lab: ' + lab, 'Periode: ' + period],
        [],
        ['No', 'Nama Guru', 'Total Sesi', 'Persentase Penggunaan']
    ];
    var idx = 1;
    for (var i = 0; i < sortedTeachers.length; i++) {
        var name = sortedTeachers[i][0];
        var count = sortedTeachers[i][1];
        var percentage = totalUsed > 0 ? ((count / totalUsed) * 100).toFixed(2) + '%' : '0%';
        teacherRows.push([idx++, name, count, percentage]);
    }
    var wsGuru = XLSX.utils.aoa_to_sheet(teacherRows);
    wsGuru['!cols'] = [{ wch: 6 }, { wch: 30 }, { wch: 12 }, { wch: 20 }];
    XLSX.utils.book_append_sheet(wb, wsGuru, 'Rekap Guru');

    XLSX.writeFile(wb, 'Rekap_Lab_' + period.replace(/[^a-zA-Z0-9]/g, '_') + '.xlsx');
}

function exportCSV() {
    var period = getPeriod(), lab = getActiveLabName();
    var panel = document.querySelector('.panel.on');
    var teacherUsage = JSON.parse(panel.dataset.teacherUsage || '{}');
    var totalUsed = parseInt(panel.dataset.totalUsed || '0');
    var lembagaData = @json($lembagaUsage);

    var toCSV = function(rows) {
        return rows.map(function(r) {
            return r.map(function(c) { return '"' + String(c).replace(/"/g, '""') + '"'; }).join(',');
        }).join('\n');
    };

    // Bagian 1: Ringkasan Lembaga
    var csv = 'RINGKASAN PENGGUNAAN LEMBAGA\n';
    csv += 'Periode: ' + period + '\n\n';
    var lembagaRows = [['No', 'Lembaga', 'Jadwal Tetap', 'Booking', 'Total Sesi', 'Pengajar Terbanyak']];
    var no = 1;
    for (var nama in lembagaData) {
        var d = lembagaData[nama];
        var topTeacher = Object.keys(d.teacherUsage || {})[0] || '-';
        var topCount   = d.teacherUsage ? (d.teacherUsage[topTeacher] || 0) : 0;
        lembagaRows.push([no++, nama, d.scheduledSlots, d.bookingSlots, d.sessionCount,
            topTeacher !== '-' ? topTeacher + ' (' + topCount + ' sesi)' : '-']);
    }
    csv += toCSV(lembagaRows);

    // Bagian 2: Rekap Guru lab aktif
    csv += '\n\nREKAP GURU — ' + lab + '\n';
    csv += 'Periode: ' + period + '\n\n';
    var sortedTeachers = Object.entries(teacherUsage).sort(function(a, b) { return b[1] - a[1]; });
    var teacherRows = [['No', 'Nama Guru', 'Total Sesi', 'Persentase']];
    var idx = 1;
    for (var i = 0; i < sortedTeachers.length; i++) {
        var name = sortedTeachers[i][0], count = sortedTeachers[i][1];
        var pct = totalUsed > 0 ? ((count / totalUsed) * 100).toFixed(2) + '%' : '0%';
        teacherRows.push([idx++, name, count, pct]);
    }
    csv += toCSV(teacherRows);

    var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = 'Rekap_Lab_' + period.replace(/[^a-zA-Z0-9]/g, '_') + '.csv';
    a.click();
    URL.revokeObjectURL(url);
}

function exportPDF() {
    var lab   = getActiveLabName(), period = getPeriod();
    var panel = document.querySelector('.panel.on');
    if (!panel) return;
    var stats = Array.from(panel.querySelectorAll('.stat-cell')).map(function(c) {
        return { val: c.querySelector('.stat-val')?.textContent.trim() || '', key: c.querySelector('.stat-key')?.textContent.trim() || '' };
    });
    var data = getActiveTableData();
    
    // Ambil data top teachers dari panel dataset
    var teacherUsage = JSON.parse(panel.dataset.teacherUsage || '{}');
    var totalUsed = parseInt(panel.dataset.totalUsed || '0');
    var teachers = [];
    var idx = 1;
    for (var name in teacherUsage) {
        var count = teacherUsage[name];
        var percentage = totalUsed > 0 ? ((count / totalUsed) * 100).toFixed(2) + '%' : '0%';
        teachers.push([idx, name, count, percentage]);
        idx++;
    }
    
    var tblHTML = function(rows, title, color) {
        if (rows.length <= 1) return '';
        var headers = rows[0], body = rows.slice(1);
        return '<h3 style="color:' + color + ';font-size:13px;margin:18px 0 8px">' + title + '</h3>'
            + '<table><thead><tr>' + headers.map(function(h) { return '<th>' + h + '</th>'; }).join('') + '</tr></thead>'
            + '<tbody>' + body.map(function(r) { return '<tr>' + r.map(function(c) { return '<td>' + c + '</td>'; }).join('') + '</tr>'; }).join('') + '</tbody></table>';
    };
    
    var teachersHTML = '';
    if (teachers.length > 0) {
        teachersHTML = '<h3 style="color:#1A2517;font-size:13px;margin:18px 0 8px">🏆 Top Pengajar Bulan Ini</h3>'
            + '<table><thead><tr><th>No</th><th>Nama Guru</th><th>Total Sesi</th><th>Persentase Penggunaan</th></tr></thead>'
            + '<tbody>' + teachers.map(function(t) { return '<tr><td>' + t[0] + '</td><td>' + t[1] + '</td><td>' + t[2] + '</td><td>' + t[3] + '</td></tr>'; }).join('') + '</tbody></table>';
    }
    
    var html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Rekap ' + lab + '</title>'
        + '<style>body{font-family:Arial,sans-serif;font-size:11px;color:#1A2517;padding:20px}h1{font-size:17px;margin-bottom:3px}h2{font-size:13px;color:#6b7280;font-weight:400;margin-bottom:14px}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin:12px 0 18px}.stat{background:#f8faf7;border:1px solid #e8f0e6;border-radius:8px;padding:10px;text-align:center}.stat-v{font-size:20px;font-weight:800}.stat-k{font-size:10px;color:#9ca3af;margin-top:3px}table{width:100%;border-collapse:collapse;margin-bottom:16px}th{background:#1A2517;color:#ACC8A2;padding:7px 9px;text-align:left;font-size:10px}td{padding:6px 9px;border-bottom:1px solid #e8f0e6;font-size:11px}tr:nth-child(even) td{background:#f8faf7}.footer{margin-top:16px;font-size:10px;color:#9ca3af;text-align:center}</style>'
        + '</head><body>'
        + '<h1>📊 Rekap Penggunaan Laboratorium</h1>'
        + '<h2>' + lab + ' &nbsp;·&nbsp; ' + period + '</h2>'
        + '<div class="stats">' + stats.map(function(s) { return '<div class="stat"><div class="stat-v">' + s.val + '</div><div class="stat-k">' + s.key + '</div></div>'; }).join('') + '</div>'
        + teachersHTML
        + tblHTML(data.jadwal,  '📅 Jadwal Tetap',      '#3d5438')
        + tblHTML(data.booking, '📝 Booking Disetujui', '#1d4ed8')
        + '<div class="footer">Lab Management – Nuris Jember &nbsp;|&nbsp; Dicetak: ' + new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) + '</div>'
        + '</body></html>';
    var win = window.open('', '_blank');
    win.document.write(html);
    win.document.close();
    win.focus();
    setTimeout(function() { win.print(); }, 500);
}
</script>
@vite('resources/js/rekap.js')
@endsection