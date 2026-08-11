@extends('layouts.public-schedule')

@section('title', $assignment->title . ' – Kumpul Tugas')

@section('vite')
@vite(['resources/css/assignment.css'])
@endsection

@section('content')

@php
    $expired  = $assignment->isExpired();
    $diffHrs  = now()->diffInHours($assignment->deadline, false);
    $deadlineBadgeClass = $expired
        ? 'badge-closed'
        : ($diffHrs < 24 ? 'badge-urgent' : 'badge-open');
    $deadlineLabel = $expired
        ? 'Deadline terlewat'
        : ($diffHrs < 1
            ? 'Kurang dari 1 jam!'
            : ($diffHrs < 24
                ? "Sisa {$diffHrs} jam"
                : $assignment->deadline->translatedFormat('d M Y, H:i')));

    $isSeries = !is_null($assignment->series_id);
@endphp

{{-- ═══ HERO ═══ --}}
<div class="hero show-hero">
    <div class="show-hero-inner">
        <a href="{{ route('assignment.public') }}" class="show-back-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke daftar tugas
        </a>
        @if($isSeries)
        <div class="series-badge series-badge-hero">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            Pertemuan {{ $assignment->session_number }} dari {{ $seriesSiblings->count() }} · Materi Berkelanjutan
        </div>
        @endif
        <div class="show-hero-title">{{ $assignment->title }}</div>
        <div class="show-hero-meta">
            <span class="hero-badge">📚 {{ $assignment->subject_name }}</span>
            <span class="hero-badge">👥 {{ $assignment->class_name }}</span>
            <span class="hero-badge">👩‍🏫 {{ $assignment->teacher->name }}</span>
            <span class="hero-badge {{ $deadlineBadgeClass }}">🕐 {{ $deadlineLabel }}</span>
        </div>
    </div>
</div>

{{-- ═══ MAIN WRAP ═══ --}}
<div class="wrap show-wrap">

    {{-- KIRI: Form + Submissions --}}
    <div>

        @if(session('success'))
        <div class="flash flash-ok">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="flash flash-err">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ $errors->first() }}
        </div>
        @endif

        @if($expired)
        {{-- ─── Expired ─── --}}
        <div class="expired-banner">
            <div class="expired-icon">🔒</div>
            <div class="expired-title">Pengumpulan Ditutup</div>
            <div class="expired-sub">Deadline tugas ini sudah terlewat. Hubungi gurumu jika ada kendala.</div>
        </div>

        @else
        {{-- ─── Form Pengumpulan ─── --}}
        <div class="show-card">
            <div class="show-card-head">
                <div class="show-card-title">📤 Form Pengumpulan Tugas</div>
                <div class="show-card-sub">Isi data diri dan upload file tugasmu</div>
            </div>
            <div class="show-card-body">
                <form method="POST" action="{{ route('assignment.submit', $assignment) }}"
                      enctype="multipart/form-data" id="submit-form">
                    @csrf

                    <div class="show-field">
                        <label class="show-field-label" for="student-name">Nama Lengkap *</label>
                        <input id="student-name" name="student_name" type="text"
                               class="show-inp" placeholder="Nama lengkapmu"
                               required value="{{ old('student_name') }}">
                    </div>

                    <div class="show-field">
                        <label class="show-field-label">Kelas (otomatis)</label>
                        <input type="text" class="show-inp show-inp-disabled"
                               value="{{ $activeClass->name }}" disabled>
                    </div>

                    <div class="show-field">
                        <label class="show-field-label" for="file-input">File Tugas *</label>
                        <div class="file-drop" id="file-drop">
                            <input type="file" name="file" id="file-input"
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                   required onchange="previewFile(this)">
                            <div class="file-drop-icon">📎</div>
                            <div class="file-drop-text">Klik atau drag file ke sini</div>
                            <div class="file-drop-sub">PDF, Word, PPT, Excel, ZIP, RAR · Maks 5MB</div>
                        </div>
                        <div class="file-preview" id="file-preview">
                            <span style="font-size:18px">📄</span>
                            <span class="file-preview-name" id="file-preview-name"></span>
                            <span class="file-preview-size" id="file-preview-size"></span>
                        </div>
                    </div>

                    <button type="submit" class="btn-pin-submit btn-submit-form" id="btn-submit">
                        ✓ Kumpulkan Tugas
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- ─── Daftar Submission ─── --}}
        @if($submissions->isNotEmpty())
        <div class="show-card" style="margin-top:16px">
            <div class="show-subs-head">
                <span class="show-subs-title">📋 Yang Sudah Mengumpulkan</span>
                <span class="show-subs-count">{{ $submissions->count() }} siswa</span>
            </div>

            {{-- Banner download — tampil hanya kalau guru sudah buka akses --}}
            @if($assignment->allow_student_download)
            <div style="display:flex;align-items:center;gap:8px;padding:10px 18px;background:#f0fdf4;border-bottom:1px solid #bbf7d0;font-size:12px;font-weight:600;color:#166534">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Guru membuka akses download — kamu bisa download ulang file tugasmu
            </div>
            @endif

            <div id="submission-list">
            @foreach($submissions as $sub)
            @php
                $ext = strtolower($sub->file_ext ?? 'other');
                $extClass = match($ext) {
                    'pdf'        => 'ext-pdf',
                    'doc','docx' => 'ext-doc',
                    'ppt','pptx' => 'ext-ppt',
                    'xls','xlsx' => 'ext-xls',
                    'zip','rar'  => 'ext-zip',
                    default      => 'ext-other'
                };
            @endphp
            <div class="show-sub-row">
                <div class="show-sub-ext {{ $extClass }}">{{ strtoupper($ext) }}</div>
                <div style="flex:1;min-width:0">
                    <div class="show-sub-name">
                        {{ $sub->student_name }}
                        @if($isSeries && $sub->continued_from_previous)
                        <span class="continued-badge" title="Siswa ini juga mengumpulkan tugas di pertemuan sebelumnya">
                            ✓ Lanjutan
                        </span>
                        @endif
                    </div>
                    <div class="show-sub-class">{{ $sub->student_class }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                    <div class="show-sub-time">{{ $sub->submitted_at->translatedFormat('d M, H:i') }}</div>
                    @if($sub->grade !== null)
                    <div class="show-sub-grade">{{ $sub->grade }}</div>
                    @endif
                    {{-- Tombol download — hanya tampil kalau guru sudah buka akses --}}
                    @if($assignment->allow_student_download)
                    <a href="{{ route('assignment.submission.download-own', [$assignment, $sub]) }}"
                       class="btn-dl-own"
                       style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;color:#0284c7;background:#e0f2fe;border:1px solid #bae6fd;padding:3px 9px;border-radius:6px;text-decoration:none;transition:background .15s"
                       onmouseover="this.style.background='#bae6fd'" onmouseout="this.style.background='#e0f2fe'"
                       title="Download file tugasmu">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
            </div>{{-- /#submission-list --}}
        </div>
        @else
        <div class="show-card" style="margin-top:16px">
            <div class="show-subs-head">
                <span class="show-subs-title">📋 Yang Sudah Mengumpulkan</span>
                <span class="show-subs-count">0 siswa</span>
            </div>
            <div id="submission-list">
                <div class="show-history-empty" style="padding:32px 18px;text-align:center;color:var(--muted);font-size:13px">Belum ada yang mengumpulkan.</div>
            </div>
        </div>
        @endif

    </div>{{-- /kiri --}}

    {{-- KANAN: Info Tugas --}}
    <div>
        <div class="show-card show-info-card">
            <div class="show-card-head">
                <div class="show-card-title">ℹ️ Detail Tugas</div>
            </div>
            <div class="show-info-body">

                <div class="show-info-row">
                    <span class="show-info-icon">📝</span>
                    <div>
                        <div class="show-info-key">Judul</div>
                        <div class="show-info-val">{{ $assignment->title }}</div>
                    </div>
                </div>

                <div class="show-info-row">
                    <span class="show-info-icon">📚</span>
                    <div>
                        <div class="show-info-key">Mata Pelajaran</div>
                        <div class="show-info-val">{{ $assignment->subject_name }}</div>
                    </div>
                </div>

                <div class="show-info-row">
                    <span class="show-info-icon">👥</span>
                    <div>
                        <div class="show-info-key">Kelas</div>
                        <div class="show-info-val">{{ $assignment->class_name }}</div>
                    </div>
                </div>

                <div class="show-info-row">
                    <span class="show-info-icon">👩‍🏫</span>
                    <div>
                        <div class="show-info-key">Guru</div>
                        <div class="show-info-val">{{ $assignment->teacher->name }}</div>
                    </div>
                </div>

                <div class="show-info-row">
                    <span class="show-info-icon">🕐</span>
                    <div>
                        <div class="show-info-key">Deadline</div>
                        <div class="show-info-val" style="color:{{ $expired ? '#dc2626' : ($diffHrs < 24 ? '#d97706' : 'inherit') }}">
                            {{ $assignment->deadline->translatedFormat('l, d M Y') }}<br>
                            <span style="font-size:12px;font-weight:400">Pukul {{ $assignment->deadline->format('H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                @if($assignment->description)
                <div class="show-info-row">
                    <span class="show-info-icon">💬</span>
                    <div>
                        <div class="show-info-key">Keterangan</div>
                        <div class="show-info-val" style="font-weight:400;font-size:12px;line-height:1.6">{{ $assignment->description }}</div>
                    </div>
                </div>
                @endif

                @if($assignment->attachment_path)
                <a href="{{ route('assignment.download.attachment', $assignment) }}" class="btn-unduh" style="display:flex;justify-content:center;margin-top:4px">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Soal
                </a>
                @endif

            </div>
        </div>

        {{-- ─── Rangkaian Materi (kalau bagian dari series) ─── --}}
        @if($isSeries)
        <div class="show-card show-info-card" style="margin-top:16px">
            <div class="show-card-head">
                <div class="show-card-title">🔗 Rangkaian Materi</div>
                <div class="show-card-sub">Tugas ini bagian dari {{ $seriesSiblings->count() }} pertemuan berkelanjutan</div>
            </div>
            <div class="show-info-body" style="padding-top:4px">
                @foreach($seriesSiblings as $sibling)
                @php
                    $isCurrent = $sibling->id === $assignment->id;
                    $siblingStatus = !$sibling->is_active
                        ? 'Belum dibuka'
                        : ($sibling->deadline->isPast() ? 'Ditutup' : 'Terbuka');
                    $siblingStatusColor = match($siblingStatus) {
                        'Terbuka'      => '#00693E',
                        'Ditutup'      => '#9ca3af',
                        default        => '#d97706',
                    };
                @endphp
                <div class="series-item {{ $isCurrent ? 'series-item-current' : '' }}">
                    <span class="series-item-num">{{ $sibling->session_number }}</span>
                    <div style="flex:1;min-width:0">
                        <div class="series-item-title">
                            {{ $sibling->title }}
                            @if($isCurrent) <span class="series-item-you">(sedang dilihat)</span> @endif
                        </div>
                        <div class="series-item-status" style="color:{{ $siblingStatusColor }}">{{ $siblingStatus }}</div>
                    </div>
                    @if(!$isCurrent && $sibling->is_active)
                    <a href="{{ route('assignment.show', $sibling) }}" class="series-item-link">Buka →</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>{{-- /kanan --}}

</div>{{-- /show-wrap --}}

{{-- CSS tambahan untuk series --}}
<style>
.series-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10.5px;
    font-weight: 700;
    color: #00693E;
    background: #eaf5ee;
    border: 1px solid #c9e6d3;
    border-radius: 999px;
    padding: 3px 9px;
    margin-bottom: 8px;
    letter-spacing: .01em;
}
.series-badge-hero {
    color: #B9D9EB;
    background: rgba(185,217,235,.12);
    border-color: rgba(185,217,235,.25);
}
.continued-badge {
    display: inline-block;
    font-size: 10px;
    font-weight: 700;
    color: #00693E;
    background: #eaf5ee;
    border-radius: 999px;
    padding: 1px 7px;
    margin-left: 6px;
    vertical-align: middle;
}
.series-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 0;
    border-bottom: 1px solid #f1f5f9;
}
.series-item:last-child { border-bottom: none; }
.series-item-current { background: #f8fafc; margin: 0 -8px; padding: 9px 8px; border-radius: 8px; }
.series-item-num {
    flex-shrink: 0;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #00693E;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}
.series-item-title { font-size: 12.5px; font-weight: 600; color: #0d2416; }
.series-item-you { font-weight: 400; color: #6b8fa3; font-size: 11px; }
.series-item-status { font-size: 11px; font-weight: 600; margin-top: 1px; }
.series-item-link { font-size: 11.5px; font-weight: 700; color: #00693E; flex-shrink: 0; }
</style>

{{-- JS --}}
<script>
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('file-preview-name').textContent = file.name;
    document.getElementById('file-preview-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
    document.getElementById('file-preview').classList.add('show');
}

const drop = document.getElementById('file-drop');
if (drop) {
    drop.addEventListener('dragover',  e => { e.preventDefault(); drop.classList.add('drag'); });
    drop.addEventListener('dragleave', () => drop.classList.remove('drag'));
    drop.addEventListener('drop',      () => drop.classList.remove('drag'));
}

const form = document.getElementById('submit-form');
if (form) {
    form.addEventListener('submit', () => {
        const btn = document.getElementById('btn-submit');
        btn.disabled    = true;
        btn.textContent = '⏳ Mengupload...';
    });
}

/* ── Realtime: Reverb WebSocket ── */
(function initAssignmentRealtime() {
    if (!window.Echo) return;

    const assignmentId = {{ $assignment->id }};

    window.Echo.channel('assignments.' + assignmentId)

        // Siswa lain baru kumpul → tambah ke daftar tanpa reload
        .listen('.submission.created', function(e) {
            addSubmissionToList(e);
            updateSubmissionCount(1);
        })

        // Guru update tugas (nilai, akses, deadline)
        .listen('.assignment.updated', function(e) {
            if (e.action === 'access_changed') {
                handleAccessChange(e.is_active);
            }
            if (e.action === 'updated') {
                // Deadline berubah — update countdown
                handleDeadlineChange(e.deadline);
            }
            if (e.action === 'graded') {
                updateGradeInList(e.data);
            }
            // Toggle download siswa
            if (e.allow_student_download !== undefined) {
                handleDownloadToggle(e.allow_student_download);
            }
        });

    function addSubmissionToList(e) {
        const listWrap = document.getElementById('submission-list');
        if (!listWrap) return;

        // Hapus empty state
        const empty = listWrap.querySelector('.show-history-empty');
        if (empty) empty.remove();

        const extClass = {
            'pdf': 'ext-pdf', 'doc': 'ext-doc', 'docx': 'ext-doc',
            'ppt': 'ext-ppt', 'pptx': 'ext-ppt',
            'xls': 'ext-xls', 'xlsx': 'ext-xls',
            'zip': 'ext-zip', 'rar': 'ext-zip',
        }[e.file_ext?.toLowerCase()] || 'ext-other';

        const row = document.createElement('div');
        row.className = 'show-sub-row';
        row.style.animation = 'fadeIn .35s ease both';
        row.innerHTML = `
            <div class="show-sub-ext ${extClass}">${(e.file_ext || '?').toUpperCase()}</div>
            <div style="flex:1;min-width:0">
                <div class="show-sub-name">${escHtml(e.student_name)}</div>
                <div class="show-sub-class">${escHtml(e.student_class)}</div>
            </div>
            <div style="text-align:right;flex-shrink:0">
                <div class="show-sub-time">${escHtml(e.submitted_at)}</div>
            </div>
        `;

        // Prepend ke atas list
        listWrap.prepend(row);
    }

    function updateSubmissionCount(delta) {
        const countEl = document.querySelector('.show-subs-count');
        if (!countEl) return;
        const current = parseInt(countEl.textContent) || 0;
        countEl.textContent = (current + delta) + ' siswa';
    }

    function updateGradeInList(data) {
        if (!data?.grade) return;
        // Cari nama siswa di list dan tambahkan badge nilai
        document.querySelectorAll('.show-sub-name').forEach(function(el) {
            if (el.textContent.trim().toLowerCase() === (data.student_name || '').trim().toLowerCase()) {
                const timeEl = el.closest('.show-sub-row')?.querySelector('.show-sub-time');
                if (timeEl && !timeEl.nextElementSibling) {
                    const badge = document.createElement('div');
                    badge.className = 'show-sub-grade';
                    badge.style.marginTop = '3px';
                    badge.textContent = data.grade;
                    timeEl.after(badge);
                }
            }
        });
    }

    function handleAccessChange(isActive) {
        const formCard = document.querySelector('.show-card:not(.show-info-card)');
        if (!formCard) return;

        if (!isActive) {
            // Sembunyikan form, tampilkan banner
            const form = document.getElementById('submit-form');
            if (form) form.style.display = 'none';
            showAccessBanner('🔒 Akses tugas ini baru saja ditutup oleh guru.', 'warning');
        } else {
            // Buka kembali — reload agar form fresh
            window.location.reload();
        }
    }

    function handleDeadlineChange(newDeadlineIso) {
        if (!newDeadlineIso) return;
        // Update semua elemen countdown dengan deadline baru
        document.querySelectorAll('.countdown-item').forEach(function(el) {
            el.dataset.time = newDeadlineIso;
        });
        showAccessBanner('📅 Deadline tugas telah diperbarui oleh guru.', 'info');
    }

    function handleDownloadToggle(isAllowed) {
        // Update atau hapus banner download
        const existingBanner = document.getElementById('download-banner');
        if (isAllowed) {
            if (!existingBanner) {
                const subHead = document.querySelector('.show-subs-head');
                if (!subHead) return;
                const banner = document.createElement('div');
                banner.id = 'download-banner';
                banner.style.cssText = 'display:flex;align-items:center;gap:8px;padding:10px 18px;background:#f0fdf4;border-bottom:1px solid #bbf7d0;font-size:12px;font-weight:600;color:#166534';
                banner.innerHTML = '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> Guru membuka akses download — kamu bisa download ulang file tugasmu';
                subHead.after(banner);
            }
            // Tampilkan semua tombol download yang tersembunyi
            document.querySelectorAll('.btn-dl-own').forEach(b => b.style.display = 'inline-flex');
        } else {
            if (existingBanner) existingBanner.remove();
            document.querySelectorAll('.btn-dl-own').forEach(b => b.style.display = 'none');
        }
        showAccessBanner(
            isAllowed ? '📥 Guru membuka akses download file tugas.' : '🔒 Akses download file tugas ditutup.',
            isAllowed ? 'ok' : 'warning'
        );
    }

    function showAccessBanner(msg, type) {
        const existing = document.getElementById('access-banner');
        if (existing) existing.remove();

        const banner = document.createElement('div');
        banner.id = 'access-banner';
        banner.className = 'flash ' + (type === 'warning' ? 'flash-err' : 'flash-ok');
        banner.style.marginBottom = '12px';
        banner.innerHTML = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg> ${escHtml(msg)}`;

        const wrap = document.querySelector('.show-wrap > div');
        if (wrap) wrap.prepend(banner);
    }

    function escHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
})();
</script>

{{-- Vite — load Echo + bootstrap untuk WebSocket --}}
@vite(['resources/js/app.js'])

@endsection