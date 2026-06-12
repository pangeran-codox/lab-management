<x-app-layout>
<x-slot name="title">Manajemen Jadwal</x-slot>

{{-- Link CSS --}}
@push('styles')
@vite('resources/css/jadwal.css')
@endpush

{{-- Pass data ke JS --}}
<script>
    window.TEACHERS           = @json($teachers);
    window.SCHEDULE_ROUTE_BASE = '{{ route('schedule.admin') }}';
    window.KELAS_API_URL       = '{{ url('/kelas') }}';
</script>

{{-- ═══════════════════════════════════════
     STATS
════════════════════════════════════════ --}}
<div class="stat-grid">
    <div class="stat-card" style="--stat-color:#003d24;--stat-accent:#c8e8d8;--stat-bg:#e8f4ee">
        <div class="stat-label">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Total Jadwal
        </div>
        <div class="stat-val">{{ $stats['total'] }}</div>
        <div class="stat-badge">Semua lab</div>
    </div>

    <div class="stat-card" style="--stat-color:#15803d;--stat-accent:#d1fae5;--stat-bg:#f0fdf4">
        <div class="stat-label">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Aktif
        </div>
        <div class="stat-val">{{ $stats['active'] }}</div>
        <div class="stat-badge" style="--stat-bg:#f0fdf4;--stat-color:#15803d">● Berjalan</div>
    </div>

    <div class="stat-card" style="--stat-color:#9ca3af;--stat-accent:#f3f4f6;--stat-bg:#f9fafb">
        <div class="stat-label">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="9"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/>
            </svg>
            Nonaktif
        </div>
        <div class="stat-val">{{ $stats['inactive'] }}</div>
        <div class="stat-badge">Tidak ada</div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     FLASH MESSAGES
════════════════════════════════════════ --}}
@if(session('success'))
<div class="flash success">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if($errors->has('error'))
<div class="flash error">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    {{ $errors->first('error') }}
</div>
@endif

{{-- ═══════════════════════════════════════
     LEGEND
════════════════════════════════════════ --}}
<div class="legend-bar">
    <div class="legend-item">
        <div class="legend-dot" style="background:#a8d9bf;border:1px solid #7fc4a8"></div>
        Jadwal aktif — klik untuk edit
    </div>
    <div class="legend-item">
        <div class="legend-dot" style="background:#dce8f0;border:1px dashed #a0b8c8"></div>
        Nonaktif
    </div>
    <div class="legend-item">
        <div class="legend-dot" style="background:transparent;border:1px dashed #b8d9c8"></div>
        Kosong — klik untuk tambah
    </div>
</div>

{{-- ═══════════════════════════════════════
     TABS
════════════════════════════════════════ --}}
<div class="lab-tabs">
    @foreach($resources as $i => $resource)
    <button
        onclick="switchTab({{ $resource->id }})"
        id="tab-{{ $resource->id }}"
        class="tab-btn {{ $i === 0 ? 'active' : '' }}"
    >
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        {{ $resource->name }}
    </button>
    @endforeach
</div>

{{-- ─── SKELETON LOADER ─── --}}
<div class="skeleton-wrap" id="skeleton">
    <div class="skel-hdr"></div>
    <div class="skel-body">
        <div class="skel-row">
            <div class="skel-cell sm"></div>
            @for($d = 0; $d < 7; $d++)<div class="skel-cell sm"></div>@endfor
        </div>
        @for($r = 0; $r < 5; $r++)
        <div class="skel-row">
            <div class="skel-cell" style="animation-delay:{{ $r * 40 }}ms"></div>
            @for($d = 0; $d < 7; $d++)
            <div class="skel-cell" style="animation-delay:{{ ($r * 7 + $d) * 25 }}ms"></div>
            @endfor
        </div>
        @endfor
    </div>
</div>

{{-- ═══════════════════════════════════════
     LAB PANELS
════════════════════════════════════════ --}}
<div id="panels-wrap">
@foreach($resources as $i => $resource)
<div id="panel-{{ $resource->id }}" class="lab-panel" style="{{ $i !== 0 ? 'display:none' : '' }}">
    <div class="panel-card">

        {{-- Panel Header --}}
        <div class="panel-hdr">
            <div class="panel-hdr-left">
                <div class="panel-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
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
            <div class="panel-actions">
                <button class="panel-action-btn" type="button" onclick="window.print()">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak
                </button>
                <a class="panel-action-btn" href="{{ route('schedule.admin.export', $resource->id) }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </a>
            </div>
        </div>

        {{-- Swipe hint (mobile) --}}
        <div class="swipe-hint">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Geser kiri/kanan untuk semua hari
        </div>

        {{-- Table --}}
        <div class="tbl-scroll">
            <table>
                <thead>
                    <tr>
                        <th class="th-time">Jam</th>
                        @foreach($days as $dayEn => $dayId)
                        @php $isSun = $dayId === 'Minggu'; @endphp
                        <th class="{{ $isSun ? 'th-sun' : '' }}">
                            @php $cls = ['Senin'=>'d-sen','Selasa'=>'d-sel','Rabu'=>'d-rab','Kamis'=>'d-kam','Jumat'=>'d-jum','Sabtu'=>'d-sab','Minggu'=>'d-min'][$dayId] ?? '' @endphp
                            <span class="day-badge {{ $cls }}">{{ $dayId }}</span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach($timeSlots as $slot)
                @php $isBreak = $slot->is_break ?? false; @endphp

                @if($isBreak)
                <tr class="break-row">
                    <td colspan="{{ count($days) + 1 }}">
                        ☕ ISTIRAHAT ·
                        {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                        @if($slot->end_time) – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }} @endif
                    </td>
                </tr>

                @else
                <tr>
                    <td class="td-time">
                        <div class="slot-name">{{ $slot->name }}</div>
                        <div class="slot-time">
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                            @if($slot->end_time)–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}@endif
                        </div>
                    </td>

                    @foreach($days as $dayEn => $dayId)
                    @php
                        $isSun = $dayId === 'Minggu';
                        $key   = $resource->id . '_' . $dayEn . '_' . $slot->id;
                        $sched = $scheduleGrid->get($key)?->first();
                        $startFmt = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                        $endFmt   = $slot->end_time ? '–' . \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '';
                        $slotTime = $startFmt . $endFmt;
                    @endphp
                    <td class="td-slot {{ $isSun ? 'sun' : '' }}">

                        @if($sched)
                        {{-- ─── Slot terisi ─── --}}
                        <div
                            class="sc {{ $sched->status === 'active' ? 'sc-active' : 'sc-inactive' }}"
                            onclick="openEdit(
                                {{ $sched->id }},
                                '{{ addslashes($sched->teacher_name) }}',
                                '{{ addslashes($sched->subject_name ?? '') }}',
                                '{{ addslashes($sched->notes ?? '') }}',
                                '{{ $sched->status }}',
                                '{{ addslashes($sched->labClass?->name ?? '-') }}',
                                '{{ $dayId }}',
                                '{{ addslashes($slot->name) }}',
                                '{{ $slotTime }}',
                                '{{ addslashes($resource->name) }}'
                            )"
                        >
                            <div class="sc-teacher">{{ $sched->teacher_name }}</div>
                            <div class="sc-class">{{ $sched->labClass?->name ?? '-' }}</div>
                            @if($sched->subject_name)
                            <div class="sc-subject">{{ $sched->subject_name }}</div>
                            @endif
                            <div class="sc-foot">
                                <span class="sc-status {{ $sched->status === 'active' ? 'on' : 'off' }}">
                                    <span class="sc-status-dot"></span>
                                    {{ $sched->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <button
                                    class="sc-del"
                                    type="button"
                                    onclick="event.stopPropagation(); confirmDelete({{ $sched->id }}, '{{ addslashes($sched->teacher_name) }}')"
                                    aria-label="Hapus jadwal"
                                >
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @else
                        {{-- ─── Slot kosong ─── --}}
                        <button
                            class="add-btn {{ $isSun ? 'sun' : '' }}"
                            type="button"
                            onclick="openAdd(
                                {{ $resource->id }},
                                '{{ addslashes($resource->name) }}',
                                {{ $slot->id }},
                                '{{ addslashes($slot->name) }}',
                                '{{ $slotTime }}',
                                '{{ $dayEn }}',
                                '{{ $dayId }}'
                            )"
                            aria-label="Tambah jadwal {{ $dayId }} {{ $slot->name }}"
                        >
                            <span class="add-icon">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                            </span>
                            <span class="add-text">Tambah</span>
                        </button>
                        @endif

                    </td>
                    @endforeach
                </tr>
                @endif
                @endforeach
                </tbody>
            </table>
        </div>{{-- /.tbl-scroll --}}

    </div>{{-- /.panel-card --}}
</div>{{-- /.lab-panel --}}
@endforeach
</div>{{-- /#panels-wrap --}}


{{-- ═══════════════════════════════════════
     MODAL: TAMBAH JADWAL
════════════════════════════════════════ --}}
<div id="add-modal" class="modal-overlay" onclick="if(event.target===this)closeAdd()">
    <div class="modal-box">
        <div class="modal-hdr">
            <div class="modal-hdr-row">
                <div>
                    <p class="modal-eyebrow">Jadwal Tetap</p>
                    <h2 class="modal-title">Tambah Jadwal</h2>
                </div>
                <button class="modal-close" type="button" onclick="closeAdd()" aria-label="Tutup">
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
                    <span id="add-b-lab">-</span>
                </span>
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span id="add-b-day">-</span>
                </span>
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span id="add-b-slot">-</span>
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('schedule.admin.store') }}" class="modal-body" id="add-form">
            @csrf
            <input type="hidden" name="resource_id"  id="add-f-rid">
            <input type="hidden" name="time_slot_id" id="add-f-sid">
            <input type="hidden" name="day_of_week"  id="add-f-day">

            <div>
                <label class="field-label" for="add-org">Unit Sekolah *</label>
                <select id="add-org" name="organization_id" class="inp" required onchange="loadKelasAdd(this.value)">
                    <option value="">— Pilih unit sekolah —</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label" for="add-class">Kelas *</label>
                <select id="add-class" name="class_id" class="inp" required disabled>
                    <option value="">— Pilih unit sekolah dulu —</option>
                </select>
            </div>

            <div>
                <label class="field-label" for="add-teacher-inp">Nama Guru *</label>
                <div style="position:relative">
                    <input
                        id="add-teacher-inp"
                        name="teacher_name"
                        type="text"
                        class="inp"
                        placeholder="Ketik nama guru..."
                        required
                        autocomplete="off"
                        oninput="filterTeacher('add-teacher-inp','add-teacher-sug',this.value)"
                    >
                    <div id="add-teacher-sug" class="suggest-box"></div>
                </div>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label" for="add-subject">Mata Pelajaran</label>
                    <input id="add-subject" name="subject_name" type="text" placeholder="Contoh: TIK" class="inp">
                </div>
                <div>
                    <label class="field-label" for="add-status">Status</label>
                    <select id="add-status" name="status" class="inp">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="field-label" for="add-notes">Catatan</label>
                <input id="add-notes" name="notes" type="text" placeholder="Opsional" class="inp">
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" onclick="closeAdd()" class="btn btn-ghost">Batal</button>
            <button type="submit" form="add-form" class="btn btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Simpan Jadwal
            </button>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════
     MODAL: EDIT JADWAL
════════════════════════════════════════ --}}
<div id="edit-modal" class="modal-overlay" onclick="if(event.target===this)closeEdit()">
    <div class="modal-box">
        <div class="modal-hdr">
            <div class="modal-hdr-row">
                <div>
                    <p class="modal-eyebrow">Edit Jadwal Tetap</p>
                    <h2 class="modal-title" id="edit-modal-title">Edit Jadwal</h2>
                </div>
                <button class="modal-close" type="button" onclick="closeEdit()" aria-label="Tutup">
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
                    <span id="edit-b-lab">-</span>
                </span>
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span id="edit-b-day">-</span>
                </span>
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span id="edit-b-slot">-</span>
                </span>
            </div>
        </div>

        <form id="edit-form" method="POST" class="modal-body">
            @csrf
            @method('PATCH')

            <div>
                <label class="field-label" for="edit-teacher">Nama Guru *</label>
                <div style="position:relative">
                    <input
                        id="edit-teacher"
                        name="teacher_name"
                        type="text"
                        class="inp"
                        required
                        autocomplete="off"
                        oninput="filterTeacher('edit-teacher','edit-teacher-sug',this.value)"
                    >
                    <div id="edit-teacher-sug" class="suggest-box"></div>
                </div>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label" for="edit-subject">Mata Pelajaran</label>
                    <input id="edit-subject" name="subject_name" type="text" class="inp">
                </div>
                <div>
                    <label class="field-label" for="edit-status">Status</label>
                    <select id="edit-status" name="status" class="inp">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="field-label" for="edit-notes">Catatan</label>
                <input id="edit-notes" name="notes" type="text" class="inp">
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" onclick="closeEdit()" class="btn btn-ghost">Batal</button>
            <button type="submit" form="edit-form" class="btn btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════
     MODAL: KONFIRMASI HAPUS
════════════════════════════════════════ --}}
<div id="delete-modal" class="modal-overlay" onclick="if(event.target===this)closeDelete()">
    <div class="del-modal-box">
        <div class="del-modal-hdr">
            <h3>Hapus Jadwal?</h3>
            <p id="delete-desc">Jadwal ini akan dihapus.</p>
        </div>
        <div class="del-modal-body">
            <p>Jadwal akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-footer" style="padding:0;border:none">
                    <button type="button" onclick="closeDelete()" class="btn btn-ghost">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Load JS --}}
@push('scripts')
@vite('resources/js/jadwal.js')
@endpush

</x-app-layout>