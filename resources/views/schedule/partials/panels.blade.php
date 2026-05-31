{{-- resources/views/schedule/partials/panels.blade.php --}}
{{--
    Variabel pre-computed dari controller (FIX #1 & #4):
    - $slotMeta[$slot->id]  = ['start', 'end', 'time']
    - $dateMeta[$day]       = ['date', 'isToday', 'isPast', 'formatted', 'dm']
    - $takenSlotsMap[$rid.'_'.$date] = [slotId, ...]  ← tidak ada lagi flatten O(n²)
    - $slotPastMap[$slot->id] = bool
    - $firstNonBreakId      = id slot non-break pertama
    - $sunRowspan           = total timeslots
    - $importantSchedules   = grouped by 'resourceId_date'
--}}
<div id="panels-wrap">
@foreach($resources as $i => $resource)
<div id="panel-{{ $resource->id }}" class="lab-panel" style="{{ $i !== 0 ? 'display:none' : '' }}">
    <div class="panel-card">

        <div class="panel-header">
            <div class="panel-icon">
                <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="1.8">
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

        <div class="swipe-hint">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Geser kiri/kanan untuk semua hari
        </div>

        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="col-time">JAM</th>
                        @foreach($days as $day)
                        @php
                            $dm = $dateMeta[$day];
                            $date = $dm['date'];
                            // Cek apakah hari ini ada jadwal penting untuk resource ini
                            $dayImpEvents = ($day !== 'Minggu')
                                ? ($importantSchedules->get($resource->id . '_' . $date) ?? collect())
                                : collect();
                            $hasImpEvent = $dayImpEvents->isNotEmpty();
                            $firstImpEvent = $dayImpEvents->first();
                        @endphp
                        <th class="{{ $dm['isToday'] ? 'th-today' : '' }} {{ $day === 'Minggu' ? 'th-sun' : '' }} {{ $hasImpEvent ? 'th-important' : '' }}"
                            style="{{ $hasImpEvent ? 'border-bottom: 2.5px solid ' . ($firstImpEvent->color ?? '#f97316') . ';' : '' }}">
                            @if($hasImpEvent)
                                <div style="display:flex;flex-direction:column;align-items:center;gap:2px">
                                    <span style="font-size:9px;font-weight:700;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;border-radius:999px;padding:1px 7px;white-space:nowrap;max-width:90%;overflow:hidden;text-overflow:ellipsis">
                                        📌 {{ Str::limit($firstImpEvent->title, 12) }}
                                    </span>
                                    <div>{{ $day }}</div>
                                    <div style="margin-top:1px;font-weight:400">
                                        <span style="font-size:10px;color:{{ $dm['isToday'] ? '#c2410c' : '#fb923c' }}">
                                            {{ $dm['dm'] }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div>{{ $day }}</div>
                                <div style="margin-top:2px;font-weight:400">
                                    @if($dm['isToday'])
                                        <span style="background:var(--g9);color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:999px">
                                            {{ $dm['dm'] }}
                                        </span>
                                    @else
                                        <span style="font-size:10px;color:{{ $day === 'Minggu' ? '#fca5a5' : 'var(--muted)' }}">
                                            {{ $dm['dm'] }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- ── BANNER ROW jadwal penting ── --}}
                    @php
                        // Cek apakah ada hari yang punya jadwal penting minggu ini
                        $hasBannerRow = false;
                        foreach ($days as $_bday) {
                            if ($_bday === 'Minggu') continue;
                            $_bdate = $dateMeta[$_bday]['date'];
                            if ($importantSchedules->has($resource->id . '_' . $_bdate)) {
                                $hasBannerRow = true;
                                break;
                            }
                        }
                    @endphp
                    @if($hasBannerRow)
                    <tr style="background:#fffbf5">
                        <td class="col-time" style="background:#fffbf5;border-right:1px solid #f0f0f0">
                            <div style="font-size:9px;font-weight:700;color:#c2410c;text-transform:uppercase;letter-spacing:.06em">Event</div>
                        </td>
                        @foreach($days as $day)
                        @php
                            $dm = $dateMeta[$day];
                            $date = $dm['date'];
                            $dayImpEvents = ($day !== 'Minggu')
                                ? ($importantSchedules->get($resource->id . '_' . $date) ?? collect())
                                : collect();
                            $hasImpEvent = $dayImpEvents->isNotEmpty();
                            $firstImpEvent = $dayImpEvents->first();
                        @endphp
                        @if($day === 'Minggu')
                            <td style="background:rgba(254,226,226,.1);border-right:1px solid #f5f5f5"></td>
                        @elseif($hasImpEvent)
                            <td style="padding:4px 3px;background:#fff7ed;border-left:1.5px solid {{ $firstImpEvent->color ?? '#f97316' }};border-right:1.5px solid {{ $firstImpEvent->color ?? '#f97316' }}">
                                <div style="display:flex;align-items:center;gap:5px;background:#ffedd5;border:1px solid #fed7aa;border-radius:8px;padding:5px 7px">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#c2410c" stroke-width="2" style="flex-shrink:0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    </svg>
                                    <div style="min-width:0">
                                        <div style="font-size:10px;font-weight:700;color:#7c2d12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                            {{ $firstImpEvent->title }}
                                        </div>
                                        <div style="font-size:9px;color:#c2410c;margin-top:1px">
                                            {{ $firstImpEvent->type_label }}
                                            @if(!$firstImpEvent->is_full_day && $firstImpEvent->startSlot && $firstImpEvent->endSlot)
                                                · {{ $firstImpEvent->startSlot->name }} – {{ $firstImpEvent->endSlot->name }}
                                            @else
                                                · Seharian
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- Jika ada lebih dari 1 event di hari yang sama --}}
                                @foreach($dayImpEvents->skip(1) as $_extraEv)
                                <div style="display:flex;align-items:center;gap:5px;background:#ffedd5;border:1px solid #fed7aa;border-radius:8px;padding:4px 7px;margin-top:3px">
                                    <div style="font-size:9px;font-weight:700;color:#7c2d12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                        {{ $_extraEv->title }}
                                    </div>
                                </div>
                                @endforeach
                            </td>
                        @else
                            <td style="background:#fffbf5;border-right:1px solid #f5f5f5"></td>
                        @endif
                        @endforeach
                    </tr>
                    @endif

                    {{-- ── SLOT ROWS ── --}}
                    @foreach($timeSlots as $slot)
                    @php $isBreak = $slot->is_break ?? false; @endphp

                    @if($isBreak)
                    <tr class="break-row">
                        <td colspan="{{ count($days) + 1 }}">
                            ☕ ISTIRAHAT · {{ $slotMeta[$slot->id]['start'] }}
                            @if($slotMeta[$slot->id]['end']) – {{ $slotMeta[$slot->id]['end'] }}@endif
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td class="col-time">
                            <div class="slot-label">{{ $slot->name }}</div>
                            <div class="slot-time">{{ $slotMeta[$slot->id]['time'] }}</div>
                        </td>

                        @foreach($days as $day)
                        @php
                            $dm           = $dateMeta[$day];
                            $date         = $dm['date'];
                            $dayEn        = $dayMapReverse[$day];
                            $isSun        = $day === 'Minggu';
                            $sk           = $resource->id . '_' . $dayEn . '_' . $slot->id;
                            $bk           = $resource->id . '_' . $date . '_' . $slot->id;
                            $sched        = $schedules->get($sk)?->first();
                            $book         = $bookings->get($bk)?->first();
                            $isSlotPast   = $dm['isToday'] && ($slotPastMap[$slot->id] ?? false);
                            $sunKey       = $resource->id . '_' . $date;
                            $sunBook      = $sundayBookings->get($sunKey)?->first();
                            $takenSlotIds = $takenSlotsMap[$sunKey] ?? [];

                            // Cari jadwal penting yang mencakup slot ini
                            $impEvents = ($isSun) ? collect() : ($importantSchedules->get($resource->id . '_' . $date) ?? collect());
                            $impEvent  = null;
                            foreach ($impEvents as $_ev) {
                                if ($_ev->is_full_day) {
                                    $impEvent = $_ev; break;
                                }
                                $_startOrder = $_ev->startSlot?->slot_order ?? 0;
                                $_endOrder   = $_ev->endSlot?->slot_order   ?? 0;
                                if ($slot->slot_order >= $_startOrder && $slot->slot_order <= $_endOrder) {
                                    $impEvent = $_ev; break;
                                }
                            }
                            // Apakah hari ini ada event (untuk border kolom)
                            $dayHasEvent = $impEvents->isNotEmpty();
                            $dayFirstEvent = $impEvents->first();
                            $eventColor = $dayFirstEvent?->color ?? '#f97316';
                        @endphp

                        @if($isSun)
                            @if($slot->id === $firstNonBreakId)
                            <td class="slot-td td-sun" rowspan="{{ $sunRowspan }}" style="vertical-align:top;padding:6px;height:100%;">

                                @if($sunBook && $sunBook->status === 'approved')
                                    @php $detailSun = json_encode(['type'=>'approved','teacher'=>$sunBook->teacher_name,'class_name'=>$sunBook->class_name??'','subject'=>$sunBook->subject_name??'','slot'=>'Seharian','time'=>'07:00–12:45','day'=>'Minggu','date'=>$dm['formatted'],'lab'=>$resource->name,'phone'=>$sunBook->teacher_phone??'','title'=>$sunBook->title??'','desc'=>$sunBook->description??'','participants'=>(string)($sunBook->participant_count??'')], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS); @endphp
                                    <div class="sc sc-approved" style="cursor:pointer;min-height:80px;display:flex;flex-direction:column;justify-content:flex-start;align-items:flex-start;" role="button" tabindex="0" data-detail='{{ $detailSun }}' onclick="showDetail(JSON.parse(this.dataset.detail))" onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                        <div class="sc-name">{{ $sunBook->teacher_name }}</div>
                                        <div class="sc-class">{{ $sunBook->class_name }}</div>
                                        @if($sunBook->subject_name)<div class="sc-subject">{{ $sunBook->subject_name }}</div>@endif
                                        <div class="sc-status">✓ Disetujui · Seharian</div>
                                    </div>

                                @elseif($sunBook && $sunBook->status === 'pending')
                                    @php $detailSun = json_encode(['type'=>'pending','teacher'=>$sunBook->teacher_name,'class_name'=>$sunBook->class_name??'','subject'=>$sunBook->subject_name??'','slot'=>'Seharian','time'=>'07:00–12:45','day'=>'Minggu','date'=>$dm['formatted'],'lab'=>$resource->name,'phone'=>$sunBook->teacher_phone??'','title'=>$sunBook->title??'','desc'=>$sunBook->description??'','participants'=>(string)($sunBook->participant_count??'')], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS); @endphp
                                    <div class="sc sc-pending" style="cursor:pointer;min-height:80px;display:flex;flex-direction:column;justify-content:flex-start;align-items:flex-start;" role="button" tabindex="0" data-detail='{{ $detailSun }}' onclick="showDetail(JSON.parse(this.dataset.detail))" onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                        <div class="sc-name">{{ $sunBook->teacher_name }}</div>
                                        <div class="sc-class">{{ $sunBook->class_name }}</div>
                                        <div class="sc-status">⏳ Pending · Seharian</div>
                                    </div>

                                @elseif($dm['isPast'])
                                    <div class="slot-past" style="padding:30px 0;">Lewat</div>

                                @else
                                    <button class="bk-btn bk-btn-sun" style="height:100%;min-height:400px;width:100%;box-sizing:border-box;"
                                        onclick="openSundayBooking({{ $resource->id }},'{{ e($resource->name) }}','{{ $date }}')">
                                        <svg class="bk-icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        <span class="bk-text">Booking</span>
                                        <span style="font-size:9px;color:#fca5a5;margin-top:2px">Seharian</span>
                                    </button>
                                @endif
                            </td>
                            @endif

                        @else
                        {{-- ─── HARI BIASA ─── --}}
                        <td class="slot-td {{ $dm['isToday'] ? 'td-today' : '' }}"
                            style="{{ $dayHasEvent ? 'background:rgba(249,115,22,.04);border-left:1.5px solid ' . $eventColor . ';border-right:1.5px solid ' . $eventColor . ';' : '' }}">

                            @if($impEvent)
                                {{-- Jadwal Penting — slot terblokir --}}
                                @php
                                    $impRangeLabel = $impEvent->is_full_day
                                        ? 'Seharian'
                                        : (($impEvent->startSlot->name ?? '') . ($impEvent->endSlot && $impEvent->endSlot->id !== $impEvent->startSlot->id ? ' – ' . $impEvent->endSlot->name : ''));
                                    $detailImp = json_encode([
                                        'type'         => 'important',
                                        'teacher'      => '',
                                        'class_name'   => '',
                                        'subject'      => '',
                                        'slot'         => $impRangeLabel,
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
                                <button style="width:100%;border-radius:9px;padding:10px 3px;background:rgba(249,115,22,.08);border:1.5px dashed #fb923c;display:flex;flex-direction:column;align-items:center;gap:3px;cursor:pointer"
                                        data-detail='{{ $detailImp }}'
                                        onclick="showDetail(JSON.parse(this.dataset.detail))">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#fb923c" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span style="font-size:9px;color:#c2410c;font-weight:700">Terblokir</span>
                                </button>

                            @elseif($sched)
                                @php $detailTetap = json_encode(['type'=>'tetap','teacher'=>$sched->teacher_name,'class_name'=>$sched->labClass?->name??'-','subject'=>$sched->subject_name??'','slot'=>$slot->name,'time'=>$slotMeta[$slot->id]['time'],'day'=>$day,'date'=>$dm['formatted'],'lab'=>$resource->name,'phone'=>'','title'=>'','desc'=>'','participants'=>''], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS); @endphp
                                <div class="sc sc-tetap" style="cursor:pointer" role="button" tabindex="0" data-detail='{{ $detailTetap }}' onclick="showDetail(JSON.parse(this.dataset.detail))" onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                    <div class="sc-name">{{ $sched->teacher_name }}</div>
                                    <div class="sc-class">{{ $sched->labClass?->name ?? '-' }}</div>
                                    @if($sched->subject_name)<div class="sc-subject">{{ $sched->subject_name }}</div>@endif
                                </div>

                            @elseif($book && $book->status === 'approved')
                                @php $detailApproved = json_encode(['type'=>'approved','teacher'=>$book->teacher_name,'class_name'=>$book->class_name??'','subject'=>$book->subject_name??'','slot'=>$slot->name,'time'=>$slotMeta[$slot->id]['time'],'day'=>$day,'date'=>$dm['formatted'],'lab'=>$resource->name,'phone'=>$book->teacher_phone??'','title'=>$book->title??'','desc'=>$book->description??'','participants'=>(string)($book->participant_count??'')], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS); @endphp
                                <div class="sc sc-approved" style="cursor:pointer" role="button" tabindex="0" data-detail='{{ $detailApproved }}' onclick="showDetail(JSON.parse(this.dataset.detail))" onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                    <div class="sc-name">{{ $book->teacher_name }}</div>
                                    <div class="sc-class">{{ $book->class_name }}</div>
                                    <div class="sc-status">✓ Disetujui</div>
                                </div>

                            @elseif($book && $book->status === 'pending')
                                @php $detailPending = json_encode(['type'=>'pending','teacher'=>$book->teacher_name,'class_name'=>$book->class_name??'','subject'=>$book->subject_name??'','slot'=>$slot->name,'time'=>$slotMeta[$slot->id]['time'],'day'=>$day,'date'=>$dm['formatted'],'lab'=>$resource->name,'phone'=>$book->teacher_phone??'','title'=>$book->title??'','desc'=>$book->description??'','participants'=>(string)($book->participant_count??'')], JSON_HEX_TAG|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_APOS); @endphp
                                <div class="sc sc-pending" style="cursor:pointer" role="button" tabindex="0" data-detail='{{ $detailPending }}' onclick="showDetail(JSON.parse(this.dataset.detail))" onkeydown="if(event.key==='Enter'||event.key===' ')showDetail(JSON.parse(this.dataset.detail))">
                                    <div class="sc-name">{{ $book->teacher_name }}</div>
                                    <div class="sc-class">{{ $book->class_name }}</div>
                                    <div class="sc-status">⏳ Pending</div>
                                </div>

                            @elseif($dm['isPast'] || $isSlotPast)
                                <div class="slot-past">Lewat</div>

                            @else
                                <button class="bk-btn"
                                    onclick="openBooking({{ $resource->id }},'{{ e($resource->name) }}',{{ $slot->id }},'{{ e($slot->name) }}','{{ $slotMeta[$slot->id]['time'] }}','{{ $dayEn }}','{{ $day }}','{{ $date }}',{{ json_encode($takenSlotIds) }})">
                                    <svg class="bk-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span class="bk-text">Booking</span>
                                </button>
                            @endif
                        </td>
                        @endif

                        @endforeach
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
</div>