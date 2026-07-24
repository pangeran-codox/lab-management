<x-app-layout>
<x-slot name="title">Manajemen Booking</x-slot>

@push('styles')
@vite('resources/css/booking.css')
@endpush

{{-- Data untuk JS --}}
<script>
    window.BOOKING_ROUTE_BASE   = '{{ url('/booking') }}';
    window.BOOKING_WEEKLY_URL   = '{{ route('booking.weekly-grid') }}';
    window.REVERB_APP_KEY       = '{{ config('broadcasting.connections.reverb.key') }}';
    window.REVERB_HOST          = '{{ config('broadcasting.connections.reverb.host') }}';
    window.REVERB_PORT          = {{ config('broadcasting.connections.reverb.port', 8084) }};
    window.REVERB_SCHEME        = '{{ config('broadcasting.connections.reverb.scheme', 'http') }}';
</script>

<div style="padding:24px">

{{-- ─── FLASH ─── --}}
@if(session('success'))
<div class="flash flash-ok">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;flex-shrink:0">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flash flash-err">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;flex-shrink:0">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- ─── STATS ─── --}}
<div class="stat-grid">
    @foreach([
        ['label'=>'Total Booking', 'value'=>$stats['regular']['total'] + $stats['sunday']['total'], 'color'=>'#6b7280'],
        ['label'=>'Pending',       'value'=>$stats['total_pending'],                                 'color'=>'#d97706'],
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
     TABEL MINGGUAN — rendered via partial
     (di-swap oleh JS saat navigasi minggu / WS event)
════════════════════════════════════════════════ --}}
<div class="weekly-section">
    <div id="bk-weekly-container">
        @include('booking.partials.weekly-table')
    </div>
</div>

{{-- ─── FILTER ─── --}}
@include('booking.partials.filter-bar')

{{-- ─── DAFTAR BOOKING + SUNDAY ─── --}}
@include('booking.partials.booking-list')

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
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span id="bk-add-b-lab">-</span>
                </span>
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span id="bk-add-b-date">-</span>
                </span>
                <span class="mbadge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px">
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
            <form id="bk-view-approve-form" method="POST" style="display:none">
                @csrf @method('PATCH')
                <button type="submit" class="btn-primary" onclick="return confirm('Setujui booking ini?')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Setujui
                </button>
            </form>
            <button id="bk-view-reject-btn" type="button" class="btn-danger" style="display:none">✗ Tolak</button>
            <a id="bk-view-detail-link" href="#" class="btn-ghost">Lihat Detail</a>
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
                <label class="field-label field-label-red">
                    Alasan Penolakan
                    <span style="font-weight:400;color:var(--muted)">(opsional)</span>
                </label>
                <textarea name="notes" rows="3" maxlength="500" class="inp-textarea"
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
