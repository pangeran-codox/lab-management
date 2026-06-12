<x-app-layout>
<x-slot name="title">Jadwal Penting</x-slot>

@vite(['resources/css/important.css'])

<div class="jp-wrap">

    {{-- FLASH --}}
    @if(session('success'))
    <div class="jp-flash jp-flash--ok">
        <i class="ti ti-circle-check"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="jp-flash jp-flash--err">
        <i class="ti ti-alert-triangle"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="jp-page-header">
        <div>
            <h1 class="jp-page-title">Jadwal Penting</h1>
            <p class="jp-page-sub">Event yang memblokir booking lab secara otomatis</p>
        </div>
        <a href="{{ route('important-schedule.create') }}" class="jp-btn-primary">
            <i class="ti ti-plus"></i>
            Tambah Event
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="jp-filter-bar">
        <form method="GET" class="jp-filter-form">
            <div class="jp-filter-group">
                <label class="jp-filter-label">
                    <i class="ti ti-building"></i>
                    Lab
                </label>
                <select name="resource_id" class="jp-select" onchange="this.form.submit()">
                    <option value="">Semua Lab</option>
                    @foreach($resources as $r)
                    <option value="{{ $r->id }}" {{ request('resource_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="jp-filter-group">
                <label class="jp-filter-label">
                    <i class="ti ti-calendar"></i>
                    Bulan
                </label>
                <input type="month" name="month" value="{{ request('month') }}" class="jp-input" onchange="this.form.submit()">
            </div>
            @if(request()->hasAny(['resource_id','month']))
            <a href="{{ route('important-schedule.index') }}" class="jp-btn-reset">
                <i class="ti ti-x"></i>
                Reset Filter
            </a>
            @endif
        </form>
    </div>

    {{-- TABLE CARD --}}
    <div class="jp-table-card">
        <div class="jp-table-header">
            <div class="jp-table-title">
                <i class="ti ti-calendar-event"></i>
                Daftar Event
                <span class="jp-count-badge">{{ $importantSchedules->total() }}</span>
            </div>
        </div>

        @forelse($importantSchedules as $item)
        <div class="jp-event-row">
            {{-- Color stripe --}}
            <div class="jp-event-stripe" style="background: {{ $item->color }}"></div>

            {{-- Color dot --}}
            <div class="jp-event-dot" style="background: {{ $item->color }}"></div>

            {{-- Main info --}}
            <div class="jp-event-info">
                <div class="jp-event-title-row">
                    <span class="jp-event-name">{{ $item->title }}</span>
                    <span class="jp-type-badge">{{ $item->type_label }}</span>
                    @if($item->is_full_day)
                    <span class="jp-fullday-badge">
                        <i class="ti ti-clock-24"></i>
                        Seharian
                    </span>
                    @endif
                </div>
                <div class="jp-event-meta">
                    <span><i class="ti ti-building"></i> {{ $item->resource->name ?? '-' }}</span>
                    <span class="jp-meta-sep">·</span>
                    <span><i class="ti ti-calendar"></i> {{ $item->date->translatedFormat('l, d F Y') }}</span>
                    @if(!$item->is_full_day && $item->startSlot && $item->endSlot)
                    <span class="jp-meta-sep">·</span>
                    <span><i class="ti ti-clock"></i> {{ $item->startSlot->start_time }} – {{ $item->endSlot->end_time }}</span>
                    @endif
                </div>
                @if($item->description)
                <div class="jp-event-desc">{{ Str::limit($item->description, 90) }}</div>
                @endif
            </div>

            {{-- Status --}}
            <div class="jp-event-status">
                @if($item->date->isPast())
                <span class="jp-status jp-status--past">
                    <i class="ti ti-clock"></i>
                    Lewat
                </span>
                @else
                <span class="jp-status jp-status--upcoming">
                    <i class="ti ti-hourglass"></i>
                    {{ $item->date->diffForHumans() }}
                </span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="jp-event-actions">
                <a href="{{ route('important-schedule.edit', $item) }}" class="jp-btn-edit">
                    <i class="ti ti-edit"></i>
                    Edit
                </a>
                <form method="POST" action="{{ route('important-schedule.destroy', $item) }}"
                      onsubmit="return confirm('Hapus jadwal penting ini?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="jp-btn-delete">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="jp-empty">
            <div class="jp-empty-icon">
                <i class="ti ti-calendar-off"></i>
            </div>
            <div class="jp-empty-title">Belum ada jadwal penting</div>
            <p class="jp-empty-sub">Tambahkan event seperti ujian atau olimpiade yang memblokir booking lab.</p>
            <a href="{{ route('important-schedule.create') }}" class="jp-btn-primary" style="margin-top:6px">
                <i class="ti ti-plus"></i>
                Tambah Sekarang
            </a>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($importantSchedules->hasPages())
        <div class="jp-pagination">
            {{ $importantSchedules->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>

@vite(['resources/js/important.js'])

</x-app-layout>