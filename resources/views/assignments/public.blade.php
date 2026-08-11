{{-- resources/views/assignments/public.blade.php --}}
@extends('layouts.public-schedule')

@section('title', 'Pengumpulan Tugas')

@section('vite')
@vite(['resources/css/assignment.css'])
@endsection

@section('content')

<style>
/* ── Hero ── */
.pub-hero {
    background: linear-gradient(135deg, #003d24 0%, #00693E 60%, #00874f 100%);
    padding: 32px 24px 28px; text-align: center; position: relative; overflow: hidden;
}
.pub-hero::before {
    content: ''; position: absolute; inset: 0; opacity: .04;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/svg%3E");
}
.pub-hero-eyebrow { font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: rgba(185,217,235,.5); margin-bottom: 6px; }
.pub-hero h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: clamp(1.4rem,4vw,2rem); color: #fff; margin-bottom: 6px; }
.pub-hero p  { font-size: 13px; color: rgba(185,217,235,.55); }

/* ── Wrap ── */
.pub-wrap { max-width: 860px; margin: 0 auto; padding: 28px 16px 56px; }

/* ── Class header bar ── */
.class-bar {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; flex-wrap: wrap;
    background: linear-gradient(135deg, #003d24, #00693E);
    border-radius: 16px; padding: 16px 20px; margin-bottom: 20px;
}
.class-bar-left { display: flex; align-items: center; gap: 12px; }
.class-bar-icon {
    width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
    background: rgba(185,217,235,.12); border: 1.5px solid rgba(185,217,235,.2);
    display: flex; align-items: center; justify-content: center;
}
.class-bar-name { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 16px; color: #fff; }
.class-bar-sub  { font-size: 12px; color: rgba(185,217,235,.5); margin-top: 2px; }
.btn-change-class {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 700; color: rgba(185,217,235,.7);
    background: rgba(185,217,235,.1); border: 1px solid rgba(185,217,235,.2);
    padding: 7px 14px; border-radius: 9px; cursor: pointer;
    font-family: 'Plus Jakarta Sans', sans-serif; transition: all .15s;
}
.btn-change-class:hover { background: rgba(185,217,235,.18); color: #B9D9EB; }

/* ── Task grid ── */
.task-grid { display: flex; flex-direction: column; gap: 16px; }

/* ── Task card ── */
.task-card {
    background: #fff; border-radius: 18px;
    border: 1.5px solid #cce4f0; overflow: hidden;
    box-shadow: 0 2px 16px rgba(0,105,62,.06);
    transition: transform .2s, box-shadow .2s;
    animation: cardIn .3s ease both;
}
.task-card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,105,62,.1); }
.task-card.closed { opacity: .65; border-style: dashed; }
@keyframes cardIn { from { opacity:0; transform: translateY(12px); } to { opacity:1; transform:none; } }

/* ─ Card top strip (warna berbeda sesuai status) ─ */
.task-card-strip { height: 5px; }
.strip-open    { background: linear-gradient(90deg, #00693E, #00874f); }
.strip-urgent  { background: linear-gradient(90deg, #dc2626, #ef4444); }
.strip-closed  { background: #d1d5db; }

/* ─ Card header ─ */
.task-card-head { padding: 18px 20px 14px; }
.task-card-top  { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 10px; }

.task-title { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 17px; color: #0d2416; line-height: 1.3; }
.task-subject-row { display: flex; align-items: center; gap: 8px; margin-top: 5px; flex-wrap: wrap; }
.task-subject { font-size: 12px; font-weight: 700; color: #00693E; background: #eaf5ee; padding: 3px 10px; border-radius: 999px; border: 1px solid #c3e6d0; }
.task-teacher { font-size: 12px; color: #6b8fa3; font-weight: 600; }

/* ─ Status badge (pojok kanan atas) ─ */
.task-status-badge { padding: 5px 13px; border-radius: 999px; font-size: 11px; font-weight: 800; flex-shrink: 0; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px; }
.status-open   { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.status-urgent { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.status-closed { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
.status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; animation: blink 1.4s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

/* ─ Deadline block (area besar, mudah dibaca) ─ */
.task-deadline-block {
    margin: 0 20px 14px;
    padding: 12px 16px; border-radius: 12px;
    display: flex; align-items: center; gap: 12px;
}
.deadline-open   { background: #f0fdf4; border: 1px solid #bbf7d0; }
.deadline-urgent { background: #fef2f2; border: 1px solid #fecaca; }
.deadline-closed { background: #f9fafb; border: 1px solid #e5e7eb; }

.deadline-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.deadline-open   .deadline-icon { background: #dcfce7; }
.deadline-urgent .deadline-icon { background: #fee2e2; }
.deadline-closed .deadline-icon { background: #f3f4f6; }

.deadline-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 2px; }
.deadline-open   .deadline-label { color: #16a34a; }
.deadline-urgent .deadline-label { color: #dc2626; }
.deadline-closed .deadline-label { color: #9ca3af; }

.deadline-value { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 800; line-height: 1.2; }
.deadline-open   .deadline-value { color: #0d2416; }
.deadline-urgent .deadline-value { color: #dc2626; }
.deadline-closed .deadline-value { color: #6b7280; }

.deadline-countdown { font-size: 11px; font-weight: 600; margin-top: 2px; }
.deadline-open   .deadline-countdown { color: #16a34a; }
.deadline-urgent .deadline-countdown { color: #dc2626; }
.deadline-closed .deadline-countdown { color: #9ca3af; }

/* ─ Meta info row ─ */
.task-meta-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; padding: 0 20px 14px; }
.task-meta-chip {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600; color: #6b8fa3;
    background: #f0f7fb; border: 1px solid #cce4f0;
    padding: 4px 10px; border-radius: 8px;
}
.task-meta-chip svg { width: 12px; height: 12px; flex-shrink: 0; }
.chip-series { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.chip-attachment { background: #f5f3ff; color: #7c3aed; border-color: #ddd6fe; }

/* ─ Card footer (actions) ─ */
.task-card-footer {
    padding: 14px 20px;
    border-top: 1px solid #f0f6fb;
    background: #fafcfe;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.btn-collect {
    flex: 1; min-width: 140px;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 12px 20px; border-radius: 12px;
    background: linear-gradient(135deg, #003d24, #00693E);
    color: #B9D9EB; font-size: 14px; font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
    text-decoration: none; border: none; cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 4px 14px rgba(0,61,36,.2);
}
.btn-collect:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(0,61,36,.28); }
.btn-collect svg { width: 16px; height: 16px; flex-shrink: 0; }

.btn-download-soal {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 18px; border-radius: 12px; font-size: 13px; font-weight: 700;
    background: #eff6ff; color: #1d4ed8; border: 1.5px solid #bfdbfe;
    text-decoration: none; transition: all .15s;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-download-soal:hover { background: #dbeafe; border-color: #93c5fd; }
.btn-download-soal svg { width: 14px; height: 14px; flex-shrink: 0; }

.btn-closed-label {
    flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 12px 20px; border-radius: 12px;
    background: #f9fafb; color: #9ca3af; font-size: 14px; font-weight: 700;
    border: 1.5px solid #e5e7eb;
}
.btn-closed-label svg { width: 16px; height: 16px; }

/* ── Empty state ── */
.task-empty {
    text-align: center; padding: 56px 24px;
    background: #fff; border-radius: 18px; border: 1.5px dashed #cce4f0;
}
.task-empty-icon { font-size: 48px; margin-bottom: 14px; opacity: .6; }
.task-empty h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px; font-weight: 800; color: #0d2416; margin-bottom: 6px; }
.task-empty p  { font-size: 13px; color: #6b8fa3; }

/* ── Flash ── */
.pub-flash { display: flex; align-items: center; gap: 8px; padding: 11px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
.pub-flash.ok  { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.pub-flash.err { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* ── PIN screen (tidak berubah dari sebelumnya) ── */
.pin-wrap { max-width: 420px; margin: 0 auto; }
</style>

{{-- ═══ HERO ═══ --}}
<div class="pub-hero">
    <p class="pub-hero-eyebrow">Sistem Pengumpulan Tugas</p>
    <h1>📋 Kumpulkan Tugasmu</h1>
    <p>{{ $activeClass ? 'Halo, kelas ' . $activeClass->name . '! Berikut daftar tugasmu.' : 'Masukkan PIN kelas untuk melihat tugasmu' }}</p>
</div>

{{-- ═══ WRAP ═══ --}}
<div class="pub-wrap">

    {{-- Flash --}}
    @if(session('success'))
    <div class="pub-flash ok">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->has('error'))
    <div class="pub-flash err">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ $errors->first('error') }}
    </div>
    @endif

    @if(!$activeClass)
    {{-- ─── PIN SCREEN ─── --}}
    <div class="pin-wrap">
        <div class="pin-screen">
            <div class="pin-icon">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#3d6b3d" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div class="pin-title">Masukkan PIN Kelas</div>
            <div class="pin-sub">PIN 6 digit diberikan oleh gurumu.<br>Tanyakan ke guru jika belum punya.</div>
            @if($errors->has('pin'))
            <div class="pin-error-msg">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                {{ $errors->first('pin') }}
            </div>
            @endif
            <div class="pin-boxes" id="pin-boxes" aria-live="polite">
                @for($i = 0; $i < 6; $i++)<div class="pin-box" id="pin-box-{{ $i }}"></div>@endfor
            </div>
            <form method="POST" action="{{ route('assignment.pin.verify') }}" id="pin-form">
                @csrf
                <input type="hidden" name="pin" id="pin-value">
                <div class="pin-keypad" role="group" aria-label="Keypad PIN">
                    @foreach([1,2,3,4,5,6,7,8,9] as $num)
                    <button type="button" class="pin-key" onclick="pinPress('{{ $num }}')" aria-label="Angka {{ $num }}">{{ $num }}</button>
                    @endforeach
                    <button type="button" class="pin-key" style="visibility:hidden" aria-hidden="true"></button>
                    <button type="button" class="pin-key pin-key-0" onclick="pinPress('0')" aria-label="Angka 0">0</button>
                    <button type="button" class="pin-key pin-key-del" onclick="pinDelete()" aria-label="Hapus">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/></svg>
                    </button>
                </div>
                <button type="submit" class="btn-pin-submit" id="btn-submit" disabled>Lihat Tugasku →</button>
            </form>
            <div class="pin-hint">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                PIN tersimpan di sesi browser ini
            </div>
        </div>
    </div>

    @else
    {{-- ─── DAFTAR TUGAS ─── --}}

    {{-- Class bar --}}
    <div class="class-bar">
        <div class="class-bar-left">
            <div class="class-bar-icon">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#B9D9EB" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="class-bar-name">{{ $activeClass->name }}</div>
                <div class="class-bar-sub">{{ $activeClass->organization->name ?? '-' }} · {{ $assignments->count() }} tugas aktif</div>
            </div>
        </div>
        <form method="POST" action="{{ route('assignment.pin.clear') }}">
            @csrf
            <button type="submit" class="btn-change-class">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Ganti Kelas
            </button>
        </form>
    </div>

    {{-- Task list --}}
    @if($assignments->isEmpty())
    <div class="task-empty">
        <div class="task-empty-icon">📭</div>
        <h3>Tidak ada tugas aktif</h3>
        <p>Belum ada tugas yang diberikan untuk kelas ini. Cek lagi nanti.</p>
    </div>
    @else
    <div class="task-grid" id="task-grid">
        @foreach($assignments as $idx => $a)
        @php
            $expired   = $a->isExpired();
            $diffHrs   = now()->diffInHours($a->deadline, false);
            $isUrgent  = !$expired && $diffHrs < 24;
            $isSeries  = !is_null($a->series_id);

            $stripClass   = $expired ? 'strip-closed' : ($isUrgent ? 'strip-urgent' : 'strip-open');
            $badgeClass   = $expired ? 'status-closed' : ($isUrgent ? 'status-urgent' : 'status-open');
            $badgeLabel   = $expired ? 'Ditutup' : ($isUrgent ? 'Mendesak' : 'Buka');
            $dlockClass   = $expired ? 'deadline-closed' : ($isUrgent ? 'deadline-urgent' : 'deadline-open');

            $deadlineDate = $a->deadline->translatedFormat('l, d M Y');
            $deadlineTime = $a->deadline->format('H:i') . ' WIB';
            $countdownTxt = $expired ? 'Deadline sudah lewat'
                : ($diffHrs < 1 ? 'Kurang dari 1 jam lagi!'
                : ($diffHrs < 24 ? 'Sisa ' . $diffHrs . ' jam lagi'
                : 'Sisa ' . $a->deadline->diffInDays(now()) . ' hari lagi'));
        @endphp
        <div class="task-card {{ $expired ? 'closed' : '' }}"
             id="acard-{{ $a->id }}"
             data-deadline="{{ $a->deadline->toISOString() }}"
             style="animation-delay: {{ $idx * 60 }}ms">

            {{-- Strip warna di atas ─ langsung beri tahu status ─ --}}
            <div class="task-card-strip {{ $stripClass }}"></div>

            {{-- Header --}}
            <div class="task-card-head">
                <div class="task-card-top">
                    <div style="flex:1;min-width:0">
                        <div class="task-title">{{ $a->title }}</div>
                        <div class="task-subject-row">
                            <span class="task-subject">{{ $a->subject_name }}</span>
                            <span class="task-teacher">👩‍🏫 {{ $a->teacher->name }}</span>
                        </div>
                    </div>
                    <span class="task-status-badge {{ $badgeClass }}">
                        @if(!$expired)<span class="status-dot"></span>@endif
                        {{ $badgeLabel }}
                    </span>
                </div>
            </div>

            {{-- Deadline block — info paling penting, paling besar ─ --}}
            <div class="task-deadline-block {{ $dlockClass }}">
                <div class="deadline-icon">
                    @if($expired)
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#9ca3af"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    @elseif($isUrgent)
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#dc2626"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    @else
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#16a34a"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div>
                    <div class="deadline-label">Batas Waktu Pengumpulan</div>
                    <div class="deadline-value">{{ $deadlineDate }}</div>
                    <div class="deadline-value" style="font-size:13px;font-weight:600">Pukul {{ $deadlineTime }}</div>
                    <div class="deadline-countdown countdown-item" data-time="{{ $a->deadline->toISOString() }}">{{ $countdownTxt }}</div>
                </div>
            </div>

            {{-- Meta chips --}}
            <div class="task-meta-row">
                <span class="task-meta-chip">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $a->submissions_count }} sudah kumpul
                </span>
                @if($isSeries)
                <span class="task-meta-chip chip-series">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Pertemuan {{ $a->session_number }} dari {{ $a->seriesSiblings()->count() }}
                </span>
                @endif
                @if($a->attachment_path)
                <span class="task-meta-chip chip-attachment">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Ada soal terlampir
                </span>
                @endif
                @if($a->description)
                <span class="task-meta-chip" style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $a->description }}">
                    💬 {{ Str::limit($a->description, 40) }}
                </span>
                @endif
            </div>

            {{-- Footer: action buttons --}}
            <div class="task-card-footer">
                @if($a->attachment_path)
                <a href="{{ route('assignment.download.attachment', $a) }}" class="btn-download-soal">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Soal
                </a>
                @endif

                @if(!$expired)
                <a href="{{ route('assignment.show', $a) }}" class="btn-collect">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Kumpulkan Sekarang
                </a>
                @else
                <span class="btn-closed-label">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Pengumpulan Ditutup
                </span>
                @endif
            </div>

        </div>
        @endforeach
    </div>
    @endif

    @endif {{-- end activeClass --}}
</div>

<script>
/* ── PIN Keypad ── */
var pinValue = '', MAX_PIN = 6;
function updateBoxes() {
    for (var i = 0; i < MAX_PIN; i++) {
        var b = document.getElementById('pin-box-' + i);
        if (!b) return;
        b.textContent = i < pinValue.length ? '●' : '';
        b.className   = 'pin-box' + (i < pinValue.length ? ' filled' : i === pinValue.length ? ' active' : '');
    }
    var btn = document.getElementById('btn-submit');
    if (btn) btn.disabled = pinValue.length < MAX_PIN;
    var inp = document.getElementById('pin-value');
    if (inp) inp.value = pinValue;
}
function pinPress(d) {
    if (pinValue.length >= MAX_PIN) return;
    pinValue += d; updateBoxes();
    if (pinValue.length === MAX_PIN) setTimeout(function(){ document.getElementById('pin-form').submit(); }, 120);
}
function pinDelete() { if (pinValue.length) { pinValue = pinValue.slice(0,-1); updateBoxes(); } }
document.addEventListener('keydown', function(e) {
    if (e.key >= '0' && e.key <= '9') pinPress(e.key);
    else if (e.key === 'Backspace')    pinDelete();
    else if (e.key === 'Enter' && pinValue.length === MAX_PIN) document.getElementById('pin-form')?.submit();
});
if (document.getElementById('pin-boxes')) updateBoxes();

/* ── Countdown realtime ── */
function updateCountdowns() {
    var now = new Date();
    document.querySelectorAll('.countdown-item').forEach(function(el) {
        var dl   = new Date(el.dataset.time);
        var diff = dl - now;
        if (diff <= 0) { el.textContent = 'Deadline sudah lewat'; return; }
        var h = Math.floor(diff/3600000), m = Math.floor((diff%3600000)/60000), s = Math.floor((diff%60000)/1000);
        if (h > 48) return;
        el.textContent = h > 0 ? 'Sisa ' + h + ' jam ' + m + ' menit lagi'
            : m > 0 ? 'Sisa ' + m + ' menit ' + s + ' detik lagi'
            : 'Sisa ' + s + ' detik lagi!';
    });
}
if (document.querySelector('.countdown-item')) { updateCountdowns(); setInterval(updateCountdowns, 1000); }

/* ── Realtime WebSocket ── */
@if($activeClass)
(function() {
    if (!window.Echo) return;
    const classSlug  = '{{ \Illuminate\Support\Str::slug($activeClass->name, "_") }}';
    const className  = '{{ $activeClass->name }}';
    const showRoute  = '{{ route("assignment.show", ":id") }}';
    const dlRoute    = '{{ route("assignment.download.attachment", ":id") }}';

    window.Echo.channel('class_assignments.' + classSlug)
        .listen('.assignment.updated', function(e) {
            if (e.action === 'access_changed') {
                if (e.is_active && e.class_name === className && !document.getElementById('acard-' + e.assignment_id)) {
                    injectCard(e);
                } else if (!e.is_active) {
                    var c = document.getElementById('acard-' + e.assignment_id);
                    if (c) { c.style.transition='opacity .4s,transform .4s'; c.style.opacity='0'; c.style.transform='scale(.96)'; setTimeout(()=>c.remove(),420); bumpCount(-1); }
                }
            }
            if (e.action === 'updated' && e.class_name === className) {
                var c = document.getElementById('acard-' + e.assignment_id);
                if (c) { var ci = c.querySelector('.countdown-item'); if (ci) ci.dataset.time = e.deadline; }
            }
        });

    function bumpCount(delta) {
        var s = document.querySelector('.class-bar-sub');
        if (!s) return;
        var m = s.textContent.match(/(\d+) tugas/);
        if (m) s.textContent = s.textContent.replace(/\d+ tugas/, (Math.max(0, +m[1]+delta)) + ' tugas');
    }

    function injectCard(e) {
        var grid = document.getElementById('task-grid');
        if (!grid) { document.querySelector('.task-empty')?.remove(); grid = Object.assign(document.createElement('div'),{id:'task-grid',className:'task-grid'}); document.querySelector('.pub-wrap').appendChild(grid); }
        var dl = new Date(e.deadline), now = new Date(), dh = (dl-now)/3600000;
        var isExp = dh<0, isUrg = !isExp&&dh<24;
        var strip = isExp?'strip-closed':isUrg?'strip-urgent':'strip-open';
        var bCls  = isExp?'status-closed':isUrg?'status-urgent':'status-open';
        var bLbl  = isExp?'Ditutup':isUrg?'Mendesak':'Buka';
        var dCls  = isExp?'deadline-closed':isUrg?'deadline-urgent':'deadline-open';
        var cnt   = isExp?'Deadline sudah lewat':dh<1?'Kurang dari 1 jam lagi!':dh<24?'Sisa '+Math.floor(dh)+' jam lagi':'Sisa '+Math.floor(dh/24)+' hari lagi';
        var showUrl = showRoute.replace(':id', e.assignment_id);
        var dlUrl   = dlRoute.replace(':id', e.assignment_id);
        var dlBtn   = e.has_attachment ? `<a href="${dlUrl}" class="btn-download-soal"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Unduh Soal</a>` : '';
        var actBtn  = isExp
            ? `<span class="btn-closed-label"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>Pengumpulan Ditutup</span>`
            : `<a href="${showUrl}" class="btn-collect"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>Kumpulkan Sekarang</a>`;
        var card = document.createElement('div');
        card.id = 'acard-' + e.assignment_id; card.className = 'task-card' + (isExp?' closed':'');
        card.style.animation = 'cardIn .35s ease both';
        card.innerHTML = `
            <div class="task-card-strip ${strip}"></div>
            <div class="task-card-head">
                <div class="task-card-top">
                    <div style="flex:1;min-width:0">
                        <div class="task-title">${esc(e.title)}</div>
                        <div class="task-subject-row">
                            <span class="task-subject">${esc(e.subject_name)}</span>
                            <span class="task-teacher">👩‍🏫 ${esc(e.teacher_name)}</span>
                        </div>
                    </div>
                    <span class="task-status-badge ${bCls}">${bLbl}</span>
                </div>
            </div>
            <div class="task-deadline-block ${dCls}">
                <div class="deadline-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;color:currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div>
                    <div class="deadline-label">Batas Waktu Pengumpulan</div>
                    <div class="deadline-value">${dl.toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric'})}</div>
                    <div class="deadline-value" style="font-size:13px;font-weight:600">Pukul ${dl.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})} WIB</div>
                    <div class="deadline-countdown countdown-item" data-time="${e.deadline}">${cnt}</div>
                </div>
            </div>
            <div class="task-meta-row">
                <span class="task-meta-chip"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>0 sudah kumpul</span>
                ${e.session_number ? `<span class="task-meta-chip chip-series">Pertemuan ${e.session_number}</span>` : ''}
                ${e.has_attachment ? `<span class="task-meta-chip chip-attachment">Ada soal terlampir</span>` : ''}
            </div>
            <div class="task-card-footer">${dlBtn}${actBtn}</div>
        `;
        grid.appendChild(card); bumpCount(1);
    }
    function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
})();
@endif
</script>

{{-- Vite Echo --}}
@vite(['resources/js/app.js'])

@endsection
