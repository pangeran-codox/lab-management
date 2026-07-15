<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">
            📊 Dashboard Rekap Penggunaan Laboratorium
        </h2>
    </x-slot>

    @push('styles')
    @vite(['resources/css/rekap.css'])
    @endpush

    {{-- ═══ FILTER ═══ --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('reports.usage.index') }}" class="flex flex-wrap items-center gap-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium" style="color: #6b8fa3;">Bulan:</label>
                <select name="month" class="inp">
                    @foreach($months as $m => $mName)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium" style="color: #6b8fa3;">Tahun:</label>
                <select name="year" class="inp">
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">
                Tampilkan
            </button>
            <div class="period-lbl ml-auto">
                {{ $startDate->translatedFormat('d M') }} – {{ $endDate->translatedFormat('d M Y') }}
            </div>
        </form>
    </div>

    <div class="py-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- ═══ MAIN WRAP ═══ --}}
        <div>
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
                <a id="btn-export-pdf" href="{{ route('reports.usage.generate', ['month' => request('month'), 'year' => request('year')]) }}" class="btn-exp btn-exp-pdf">
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
                            <h2 style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:18px;margin:0">
                                🏫 {{ $lab['resource']->name }} @if($lab['resource']->organization) ({{ $lab['resource']->organization->name }}) @endif
                            </h2>
                            <p style="font-size:11px;margin-top:4px">
                                {{ $lab['totalCapacity'] }} slot kapasitas · {{ $totalSlotPerDay }} slot/hari
                                @if($lab['resource']->building) · {{ $lab['resource']->building }} @endif
                            </p>
                        </div>
                        <div style="display:flex;align-items:center;gap:14px">
                            <div style="text-align:right">
                                <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:32px;font-weight:800;color:{{ $pctColor }};line-height:1">{{ $pct }}%</div>
                                <div style="font-size:10px;margin-top:2px">Tingkat Penggunaan</div>
                            </div>
                            <svg viewBox="0 0 36 36" style="width:56px;height:56px;transform:rotate(-90deg)">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(255,255,255,.2)" stroke-width="3.5"/>
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $pctColor }}" stroke-width="3.5"
                                    stroke-dasharray="{{ $pct }} {{ 100-$pct }}" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="stat-row">
                        <div class="stat-cell">
                            <div class="stat-val" style="color:#1A2517">{{ $lab['scheduledSlots'] }}</div>
                            <div class="stat-key">Jadwal Tetap</div>
                        </div>
                        <div class="stat-cell">
                            <div class="stat-val" style="color:#2563eb">{{ $lab['bookingSlots'] }}</div>
                            <div class="stat-key">Booking</div>
                        </div>
                        <div class="stat-cell">
                            <div class="stat-val" style="color:#1A2517">{{ $lab['totalUsed'] }}</div>
                            <div class="stat-key">Total Terpakai</div>
                        </div>
                        <div class="stat-cell">
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

                    {{-- Donut Chart Guru --}}
                    <div class="donut-card">
                        <div class="sec-lbl">Distribusi Penggunaan per Guru</div>
                        <div class="donut-content">
                            <canvas class="donut-canvas" data-teacher-usage="{{ json_encode($lab['teacherUsage']) }}" data-total-used="{{ $lab['totalUsed'] }}"></canvas>
                            <div class="donut-legend"></div>
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

                    {{-- ══ TOP TEACHERS + KEPADATAN PER HARI ══ --}}
                    <div class="insight-row">
                        {{-- Top Pengajar --}}
                        <div class="insight-card">
                            <div class="sec-lbl">Top Pengajar Bulan Ini</div>
                            @php
                                // Ambil top 5 dari teacherUsage
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
                                        <th style="text-align:center">Absen</th>
                                        <th style="text-align:center">Aksi</th>
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
                                            @if($sd->absen_count > 0)
                                            <span class="badge" style="background:#fee2e2;color:#dc2626;margin-left:4px">-{{ $sd->absen_count }}</span>
                                            @endif
                                        </td>
                                        <td style="text-align:center">
                                            @if($sd->absences->isNotEmpty())
                                            <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:center">
                                                @foreach($sd->absences as $absen)
                                                <span class="badge" style="background:#fee2e2;color:#dc2626;font-size:10px">
                                                    {{ $absen->absent_date->translatedFormat('d M') }}
                                                    <form method="POST" action="{{ route('schedule-absences.destroy', $absen) }}" style="display:inline;margin-left:4px" onsubmit="return confirm('Hapus absen ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:10px;padding:0;margin:0;line-height:1">&times;</button>
                                                    </form>
                                                </span>
                                                @endforeach
                                            </div>
                                            @else
                                            <span style="color:#9ca3af;font-size:11px">–</span>
                                            @endif
                                        </td>
                                        <td style="text-align:center">
                                            <button onclick="openAddAbsenceModal({{ $sd->id }}, '{{ addslashes($sd->teacher_name) }}', '{{ $sd->day_of_week }}', {{ $month }}, {{ $year }})" class="btn-primary" style="padding:4px 10px;font-size:11px">
                                                + Tambah Absen
                                            </button>
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
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @push('modals')
    {{-- Modal Tambah Absen --}}
    <div id="add-absence-modal" class="modal-overlay" onclick="if(event.target===this)closeAddAbsenceModal()" hidden>
        <div class="modal-box">
            <div class="modal-hdr">
                <div class="modal-hdr-row">
                    <div>
                        <p class="modal-eyebrow">Absen Jadwal</p>
                        <h2 class="modal-title">Tambah Absen</h2>
                    </div>
                    <button class="modal-close" type="button" onclick="closeAddAbsenceModal()" aria-label="Tutup">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <form id="add-absence-form" method="POST" action="{{ route('schedule-absences.store') }}" class="modal-body">
                @csrf
                <input type="hidden" name="schedule_id" id="absence-schedule-id">
                <div>
                    <label class="field-label" for="absence-teacher">Guru</label>
                    <input id="absence-teacher" type="text" class="inp" readonly style="background:#f3f4f6">
                </div>
                <div style="margin-top:12px">
                    <label class="field-label" for="absence-date">Tanggal Absen *</label>
                    <input id="absence-date" name="absent_date" type="date" class="inp" required>
                </div>
                <div style="margin-top:12px">
                    <label class="field-label" for="absence-reason">Alasan</label>
                    <textarea id="absence-reason" name="reason" class="inp" rows="2" placeholder="Opsional: alasan guru tidak masuk"></textarea>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" onclick="closeAddAbsenceModal()" class="btn btn-ghost">Batal</button>
                <button type="submit" form="add-absence-form" class="btn btn-primary">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script>
        const dayMap = {
            'Sunday': 0,
            'Monday': 1,
            'Tuesday': 2,
            'Wednesday': 3,
            'Thursday': 4,
            'Friday': 5,
            'Saturday': 6
        };

        function openAddAbsenceModal(scheduleId, teacherName, dayOfWeek, month, year) {
            document.getElementById('absence-schedule-id').value = scheduleId;
            document.getElementById('absence-teacher').value = teacherName;
            
            const targetDay = dayMap[dayOfWeek];
            const dateInput = document.getElementById('absence-date');
            dateInput.dataset.targetDay = targetDay;
            
            // Set default date to first occurrence of the day in the month
            const firstDay = new Date(year, month - 1, 1);
            const diff = targetDay - firstDay.getDay();
            const firstOccurrence = new Date(firstDay);
            firstOccurrence.setDate(firstDay.getDate() + (diff >= 0 ? diff : diff + 7));
            
            dateInput.value = firstOccurrence.toISOString().split('T')[0];
            document.getElementById('add-absence-modal').hidden = false;
        }

        function closeAddAbsenceModal() {
            document.getElementById('add-absence-modal').hidden = true;
            document.getElementById('add-absence-form').reset();
        }

        // Validasi tanggal agar hanya sesuai dengan hari jadwal
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('absence-date');
            if (dateInput) {
                dateInput.addEventListener('change', function() {
                    const targetDay = parseInt(this.dataset.targetDay);
                    const selectedDate = new Date(this.value);
                    if (selectedDate.getDay() !== targetDay) {
                        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        alert('Silakan pilih tanggal yang sesuai dengan hari ' + dayNames[targetDay]);
                        this.value = '';
                    }
                });
            }
        });
    </script>
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
            var url = "{{ route('reports.usage.generate') }}";
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
            var currentMonthName = {!! json_encode($months) !!}[month];
            return currentMonthName + ' ' + year;
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

            // Dapatkan nama lembaga
            var resourceIndex = -1;
            var tabs = document.querySelectorAll('.tab');
            for (var i = 0; i < tabs.length; i++) {
                if (tabs[i].classList.contains('on')) {
                    resourceIndex = i;
                    break;
                }
            }
            var labDataFromView = @json($labData);
            var organizationName = 'Semua Lembaga';
            if (resourceIndex >= 0 && labDataFromView[resourceIndex]) {
                organizationName = labDataFromView[resourceIndex].resource.organization?.name || 'Semua Lembaga';
            }

            // Ambil data teacherUsage
            var panel = document.querySelector('.panel.on');
            var teacherUsage = JSON.parse(panel.dataset.teacherUsage || '{}');
            var totalUsed = parseInt(panel.dataset.totalUsed || '0');

            // Urutkan teacherUsage dari terbesar ke terkecil
            var sortedTeachers = Object.entries(teacherUsage).sort(function(a, b) {
                return b[1] - a[1];
            });

            // Buat sheet Top Pengajar
            var teacherRows = [
                ['REKAP PENGGUNAAN GURU LAB', '', ''],
                ['Lembaga: ' + organizationName, '', ''],
                ['Lab: ' + lab, 'Periode: ' + period, ''],
                [],
                ['No', 'Nama Guru', 'Total Sesi', 'Persentase Penggunaan']
            ];

            var idx = 1;
            for (var i = 0; i < sortedTeachers.length; i++) {
                var name = sortedTeachers[i][0];
                var count = sortedTeachers[i][1];
                var percentage = totalUsed > 0 ? ((count / totalUsed) * 100).toFixed(2) + '%' : '0%';
                teacherRows.push([idx, name, count, percentage]);
                idx++;
            }

            var ws = XLSX.utils.aoa_to_sheet(teacherRows);
            ws['!cols'] = [
                { wch: 6 },
                { wch: 30 },
                { wch: 12 },
                { wch: 20 }
            ];
            XLSX.utils.book_append_sheet(wb, ws, 'Rekap Guru');

            XLSX.writeFile(wb, 'Rekap_Guru_' + lab.replace(/\s+/g, '_') + '_' + period.replace(/[^a-zA-Z0-9]/g, '_') + '.xlsx');
        }

        function exportCSV() {
            var lab = getActiveLabName(), period = getPeriod();
            var panel = document.querySelector('.panel.on');
            var teacherUsage = JSON.parse(panel.dataset.teacherUsage || '{}');
            var totalUsed = parseInt(panel.dataset.totalUsed || '0');

            // Dapatkan nama lembaga
            var resourceIndex = -1;
            var tabs = document.querySelectorAll('.tab');
            for (var i = 0; i < tabs.length; i++) {
                if (tabs[i].classList.contains('on')) {
                    resourceIndex = i;
                    break;
                }
            }
            var labDataFromView = @json($labData);
            var organizationName = 'Semua Lembaga';
            if (resourceIndex >= 0 && labDataFromView[resourceIndex]) {
                organizationName = labDataFromView[resourceIndex].resource.organization?.name || 'Semua Lembaga';
            }

            var toCSV = function(rows) {
                return rows.map(function(r) {
                    return r.map(function(c) { return '"' + c.replace(/"/g, '""') + '"'; }).join(',');
                }).join('\n');
            };

            // Urutkan teacherUsage dari terbesar ke terkecil
            var sortedTeachers = Object.entries(teacherUsage).sort(function(a, b) {
                return b[1] - a[1];
            });

            // Buat CSV hanya untuk rekap guru
            var csv = 'REKAP PENGGUNAAN GURU LAB\n';
            csv += 'Lembaga: ' + organizationName + '\n';
            csv += 'Lab: ' + lab + '\n';
            csv += 'Periode: ' + period + '\n\n';

            var teacherRows = [
                ['No', 'Nama Guru', 'Total Sesi', 'Persentase Penggunaan']
            ];
            var idx = 1;
            for (var i = 0; i < sortedTeachers.length; i++) {
                var name = sortedTeachers[i][0];
                var count = sortedTeachers[i][1];
                var percentage = totalUsed > 0 ? ((count / totalUsed) * 100).toFixed(2) + '%' : '0%';
                teacherRows.push([idx, name, count, percentage]);
                idx++;
            }
            csv += toCSV(teacherRows);

            var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
            var url  = URL.createObjectURL(blob);
            var a    = document.createElement('a');
            a.href   = url;
            a.download = 'Rekap_Guru_' + lab.replace(/\s+/g, '_') + '_' + period.replace(/[^a-zA-Z0-9]/g, '_') + '.csv';
            a.click();
            URL.revokeObjectURL(url);
        }
    </script>
    @vite('resources/js/rekap.js')
    @endpush
</x-app-layout>
