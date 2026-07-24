{{--
    Partial: booking/partials/weekly-header.blade.php
    Variabel: $weekStart, $weekEnd, $prevWeek, $nextWeek, $resources, $weeklyBookings
--}}
<div class="weekly-hdr">
    <div class="weekly-hdr-left">
        <div class="weekly-title">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Jadwal Booking Mingguan
        </div>
        <span class="weekly-range" id="weekly-range-label">
            {{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}
        </span>
    </div>

    <div class="week-nav" id="week-nav">
        <button
            class="week-nav-btn"
            onclick="bkNavigateWeek('{{ $prevWeek }}')"
            title="Minggu lalu"
            aria-label="Minggu lalu"
        >
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <button
            class="week-today-btn"
            onclick="bkGoToday()"
            title="Ke minggu ini"
        >Minggu Ini</button>

        <span class="week-nav-label">
            {{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}
        </span>

        <button
            class="week-nav-btn"
            onclick="bkNavigateWeek('{{ $nextWeek }}')"
            title="Minggu depan"
            aria-label="Minggu depan"
        >
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
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
<div class="bk-lab-tabs" id="bk-lab-tabs">
    @foreach($resources as $i => $resource)
    <button
        onclick="bkSwitchTab({{ $resource->id }})"
        id="bk-tab-{{ $resource->id }}"
        class="bk-tab-btn {{ $i === 0 ? 'active' : '' }}"
    >
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:13px;height:13px">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        {{ $resource->name }}
        @php $labCount = $weeklyBookings->where('resource_id', $resource->id)->count(); @endphp
        @if($labCount > 0)
        <span class="bk-tab-count">{{ $labCount }}</span>
        @endif
    </button>
    @endforeach
</div>
