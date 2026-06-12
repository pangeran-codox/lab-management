{{-- resources/views/schedule/partials/panels.blade.php --}}
{{--
    Variabel dari controller (sama persis, tidak ada perubahan controller):
    - $slotMeta[$slot->id]  = ['start', 'end', 'time']
    - $dateMeta[$day]       = ['date', 'isToday', 'isPast', 'formatted', 'dm']
    - $takenSlotsMap[$rid.'_'.$date] = [slotId, ...]
    - $slotPastMap[$slot->id] = bool
    - $firstNonBreakId      = id slot non-break pertama
    - $sunRowspan           = total timeslots
    - $importantSchedules   = grouped by 'resourceId_date'
--}}

<div id="panels-wrap">
@foreach($resources as $i => $resource)
<div id="panel-{{ $resource->id }}" class="lab-panel" style="{{ $i !== 0 ? 'display:none' : '' }}">

    {{-- Panel header: nama lab + kapasitas --}}
    <div class="panel-header">
        <div class="panel-icon">
            <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="#0F6E56" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <div class="panel-name">{{ $resource->name }}</div>
            @if($resource->capacity)
            <div class="panel-cap">Kapasitas {{ $resource->capacity }} komputer</div>
            @endif
        </div>
    </div>

    {{-- ═══ GRID SENIN–SABTU (6 kolom sejajar) ═══ --}}
    @php
        $weekdays = array_filter($days, fn($d) => $d !== 'Minggu');
    @endphp

    <div class="day-grid-outer">
    <div class="day-grid">
        @foreach($weekdays as $day)
        @php
            $dm       = $dateMeta[$day];
            $date     = $dm['date'];
            $dayEn    = $dayMapReverse[$day];
            $impEvents= $importantSchedules->get($resource->id . '_' . $date) ?? collect();
            $hasImp   = $impEvents->isNotEmpty();
            $firstImp = $impEvents->first();

            // Ambil ketersediaan dari data yang sudah di-precompute
            $availCount = $availCounts[$resource->id . '_' . $date] ?? 0;
        @endphp

        <div class="day-card {{ $dm['isToday'] ? 'day-card--today' : '' }}">

            {{-- Card head: nama hari + tanggal --}}
            <div class="dc-head">
                <div class="dc-day-name">{{ $day }}</div>
                <div class="dc-date-row">
                    <div class="dc-date {{ $dm['isToday'] ? 'dc-date--today' : '' }}">
                        {{ \Carbon\Carbon::parse($date)->format('d') }}
                    </div>
                    @if($dm['isToday'])
                        <span class="dc-today-badge">Hari ini</span>
                    @endif
                </div>
                {{-- Event penting banner di dalam card --}}
                @if($hasImp)
                <div class="dc-event-banner" style="border-color:{{ $firstImp->color ?? '#f97316' }}">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="{{ $firstImp->color ?? '#f97316' }}" stroke-width="2.5" style="flex-shrink:0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <span>{{ Str::limit($firstImp->title, 18) }}</span>
                </div>
                @endif
            </div>

            {{-- Slot rows --}}
            <div class="dc-slots">
                @foreach($timeSlots as $slot)
                @php $isBreak = $slot->is_break ?? false; @endphp

                @if($isBreak)
                <div class="dc-break">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                    Istirahat · {{ $slotMeta[$slot->id]['start'] }}@if($slotMeta[$slot->id]['end'])–{{ $slotMeta[$slot->id]['end'] }}@endif
                </div>
                @else
                @php
                    $sk          = $resource->id . '_' . $dayEn . '_' . $slot->id;
                    $bk          = $resource->id . '_' . $date . '_' . $slot->id;
                    $sched       = $schedules->get($sk)?->first();
                    $book        = $bookings->get($bk)?->first();
                    $isSlotPast  = $dm['isToday'] && ($slotPastMap[$slot->id] ?? false);
                    $takenSlotIds= $takenSlotsMap[$resource->id . '_' . $date] ?? [];

                    $impEvent = null;
                    foreach ($impEvents as $_ev) {
                        if ($_ev->is_full_day) { $impEvent = $_ev; break; }
                        if ($slot->slot_order >= ($_ev->startSlot?->slot_order ?? 0) &&
                            $slot->slot_order <= ($_ev->endSlot?->slot_order   ?? 0)) {
                            $impEvent = $_ev; break;
                        }
                    }
                @endphp

                <div class="dc-slot-row">
                    <div class="dc-slot-time">{{ $slotMeta[$slot->id]['start'] }}</div>

                    {{-- ── State: Terblokir (event penting) ── --}}
                    @if($impEvent)
                    @php
                        $detailImp = json_encode([
                            'type'         => 'important',
                            'teacher'      => '',
                            'class_name'   => '',
                            'subject'      => '',
                            'slot'         => $impEvent->is_full_day ? 'Seharian' : (($impEvent->startSlot->name ?? '') . ($impEvent->endSlot && $impEvent->endSlot->id !== $impEvent->startSlot->id ? ' – ' . $impEvent->endSlot->name : '')),
                            'time'         => $slotMeta[$slot->id]['time'],
                            'day'          => $day,
                            'date'         => $dm['formatted'],
                            'lab'          => $resource->name,
                            'phone'        => '',
                            'title'        => $impEvent->title ?? 'Jadwal Penting',
                            'desc'         => $impEvent->description ?? '',
                            'participants' => '',
                        ], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS);
                    @endphp
                    <button class="dc-slot-bar dc-slot-blocked"
                        data-detail='{{ $detailImp }}'
                        onclick="showDetail(JSON.parse(this.dataset.detail))">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Terblokir
                    </button>

                    {{-- ── State: Jadwal Tetap ── --}}
                    @elseif($sched)
                    @php
                        $detailTetap = json_encode([
                            'type'       => 'tetap',
                            'teacher'    => $sched->teacher_name,
                            'class_name' => $sched->labClass?->name ?? '-',
                            'subject'    => $sched->subject_name ?? '',
                            'slot'       => $slot->name,
                            'time'       => $slotMeta[$slot->id]['time'],
                            'day'        => $day,
                            'date'       => $dm['formatted'],
                            'lab'        => $resource->name,
                            'phone'      => '',
                            'title'      => '',
                            'desc'       => '',
                            'participants' => '',
                        ], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS);
                    @endphp
                    <button class="dc-slot-bar dc-slot-tetap"
                        data-detail='{{ $detailTetap }}'
                        onclick="showDetail(JSON.parse(this.dataset.detail))">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                        <span class="dc-slot-name">{{ Str::limit($sched->teacher_name, 14) }}</span>
                    </button>

                    {{-- ── State: Disetujui ── --}}
                    @elseif($book && $book->status === 'approved')
                    @php
                        $detailApproved = json_encode([
                            'type'         => 'approved',
                            'teacher'      => $book->teacher_name,
                            'class_name'   => $book->class_name ?? '',
                            'subject'      => $book->subject_name ?? '',
                            'slot'         => $slot->name,
                            'time'         => $slotMeta[$slot->id]['time'],
                            'day'          => $day,
                            'date'         => $dm['formatted'],
                            'lab'          => $resource->name,
                            'phone'        => $book->teacher_phone ?? '',
                            'title'        => $book->title ?? '',
                            'desc'         => $book->description ?? '',
                            'participants' => (string)($book->participant_count ?? ''),
                        ], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS);
                    @endphp
                    <button class="dc-slot-bar dc-slot-approved"
                        data-detail='{{ $detailApproved }}'
                        onclick="showDetail(JSON.parse(this.dataset.detail))">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="dc-slot-name">{{ Str::limit($book->teacher_name, 14) }}</span>
                    </button>

                    {{-- ── State: Pending ── --}}
                    @elseif($book && $book->status === 'pending')
                    @php
                        $detailPending = json_encode([
                            'type'         => 'pending',
                            'teacher'      => $book->teacher_name,
                            'class_name'   => $book->class_name ?? '',
                            'subject'      => $book->subject_name ?? '',
                            'slot'         => $slot->name,
                            'time'         => $slotMeta[$slot->id]['time'],
                            'day'          => $day,
                            'date'         => $dm['formatted'],
                            'lab'          => $resource->name,
                            'phone'        => $book->teacher_phone ?? '',
                            'title'        => $book->title ?? '',
                            'desc'         => $book->description ?? '',
                            'participants' => (string)($book->participant_count ?? ''),
                        ], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS);
                    @endphp
                    <button class="dc-slot-bar dc-slot-pending"
                        data-detail='{{ $detailPending }}'
                        onclick="showDetail(JSON.parse(this.dataset.detail))">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="dc-slot-name">{{ Str::limit($book->teacher_name, 14) }}</span>
                    </button>

                    {{-- ── State: Lewat ── --}}
                    @elseif($dm['isPast'] || $isSlotPast)
                    <div class="dc-slot-bar dc-slot-past">Lewat</div>

                    {{-- ── State: Tersedia (Booking) ── --}}
                    @else
                    <button class="dc-slot-bar dc-slot-booking"
                        onclick="openBooking({{ $resource->id }},'{{ e($resource->name) }}',{{ $slot->id }},'{{ e($slot->name) }}','{{ $slotMeta[$slot->id]['time'] }}','{{ $dayEn }}','{{ $day }}','{{ $date }}',{{ json_encode($takenSlotIds) }})">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Booking
                    </button>
                    @endif
                </div>
                @endif
                @endforeach
            </div>

            {{-- Card footer: jumlah slot tersedia --}}
            <div class="dc-footer">
                @if($availCount > 0)
                    <span class="dc-avail-count">{{ $availCount }} slot tersedia</span>
                @else
                    <span class="dc-avail-none">Penuh</span>
                @endif
            </div>

        </div>
        @endforeach
    </div>{{-- end .day-grid --}}
    </div>{{-- end .day-grid-outer --}}


    {{-- ═══ CARD MINGGU (baris sendiri di bawah) ═══ --}}
    @php
        $sunDay    = 'Minggu';
        $sunDm     = $dateMeta[$sunDay];
        $sunDate   = $sunDm['date'];
        $sunKey    = $resource->id . '_' . $sunDate;
        $sunBook   = $sundayBookings->get($sunKey)?->first();
    @endphp

    <div class="sunday-row">
        <div class="day-card day-card--sunday {{ $sunDm['isToday'] ? 'day-card--today' : '' }}">

            <div class="dc-head">
                <div class="dc-day-name dc-day-name--sunday">Minggu</div>
                <div class="dc-date-row">
                    <div class="dc-date dc-date--sunday">
                        {{ \Carbon\Carbon::parse($sunDate)->format('d') }}
                    </div>
                    @if($sunDm['isToday'])
                        <span class="dc-today-badge">Hari ini</span>
                    @endif
                    <span class="dc-sunday-badge">Booking seharian</span>
                </div>
            </div>

            <div class="dc-sunday-body">
                @if($sunBook && $sunBook->status === 'approved')
                @php
                    $detailSun = json_encode([
                        'type'         => 'approved',
                        'teacher'      => $sunBook->teacher_name,
                        'class_name'   => $sunBook->class_name ?? '',
                        'subject'      => $sunBook->subject_name ?? '',
                        'slot'         => 'Seharian',
                        'time'         => '07:00–12:45',
                        'day'          => 'Minggu',
                        'date'         => $sunDm['formatted'],
                        'lab'          => $resource->name,
                        'phone'        => $sunBook->teacher_phone ?? '',
                        'title'        => $sunBook->title ?? '',
                        'desc'         => $sunBook->description ?? '',
                        'participants' => (string)($sunBook->participant_count ?? ''),
                    ], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS);
                @endphp
                <button class="dc-sunday-booking dc-sunday-approved"
                    data-detail='{{ $detailSun }}'
                    onclick="showDetail(JSON.parse(this.dataset.detail))">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div>
                        <div class="dc-sun-name">{{ $sunBook->teacher_name }}</div>
                        <div class="dc-sun-sub">{{ $sunBook->class_name }} · Disetujui</div>
                    </div>
                </button>

                @elseif($sunBook && $sunBook->status === 'pending')
                @php
                    $detailSun = json_encode([
                        'type'         => 'pending',
                        'teacher'      => $sunBook->teacher_name,
                        'class_name'   => $sunBook->class_name ?? '',
                        'subject'      => $sunBook->subject_name ?? '',
                        'slot'         => 'Seharian',
                        'time'         => '07:00–12:45',
                        'day'          => 'Minggu',
                        'date'         => $sunDm['formatted'],
                        'lab'          => $resource->name,
                        'phone'        => $sunBook->teacher_phone ?? '',
                        'title'        => $sunBook->title ?? '',
                        'desc'         => $sunBook->description ?? '',
                        'participants' => (string)($sunBook->participant_count ?? ''),
                    ], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS);
                @endphp
                <button class="dc-sunday-booking dc-sunday-pending"
                    data-detail='{{ $detailSun }}'
                    onclick="showDetail(JSON.parse(this.dataset.detail))">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <div class="dc-sun-name">{{ $sunBook->teacher_name }}</div>
                        <div class="dc-sun-sub">{{ $sunBook->class_name }} · Pending</div>
                    </div>
                </button>

                @elseif($sunDm['isPast'])
                <div class="dc-sunday-past">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sudah lewat
                </div>

                @else
                <button class="dc-sunday-booking dc-sunday-open"
                    onclick="openSundayBooking({{ $resource->id }},'{{ e($resource->name) }}','{{ $sunDate }}')">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <div>
                        <div class="dc-sun-name">Tersedia untuk booking</div>
                        <div class="dc-sun-sub">07:00 – 12:45 · Seharian penuh</div>
                    </div>
                </button>
                @endif
            </div>

        </div>
    </div>{{-- end .sunday-row --}}

</div>{{-- end .lab-panel --}}
@endforeach
</div>{{-- end #panels-wrap --}}
