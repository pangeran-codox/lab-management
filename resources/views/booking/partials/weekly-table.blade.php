{{--
    Partial: booking/partials/weekly-table.blade.php
    Variabel: $resources, $weekDays, $timeSlots, $bookingGrid, $weeklyBookings,
              $weekStart, $weekEnd, $prevWeek, $nextWeek
    Data attribute: week saat ini disimpan di #bk-weekly-wrap agar JS bisa baca
--}}
<div id="bk-weekly-wrap"
     data-week-start="{{ $weekStart->toDateString() }}"
     data-prev-week="{{ $prevWeek }}"
     data-next-week="{{ $nextWeek }}">

    @include('booking.partials.weekly-header')

    {{-- Skeleton loader --}}
    <div class="bk-skeleton" id="bk-skeleton" style="display:none">
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
                    $panelCount   = $weeklyBookings->where('resource_id', $resource->id)->count();
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
                                @if($isToday)<div class="th-today-badge">Hari ini</div>@endif
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
                                $isToday   = $day['date']->format('Y-m-d') === $today;
                                $dateStr   = $day['date']->format('Y-m-d');
                                $key       = $resource->id . '_' . $dateStr . '_' . $slot->id;
                                $booking   = $bookingGrid->get($key);
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

</div>{{-- /#bk-weekly-wrap --}}
