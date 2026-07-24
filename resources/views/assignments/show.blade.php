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
                    <div class="show-sub-name">{{ $sub->student_name }}</div>
                    <div class="show-sub-class">{{ $sub->student_class }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0">
                    <div class="show-sub-time">{{ $sub->submitted_at->translatedFormat('d M, H:i') }}</div>
                    @if($sub->grade !== null)
                    <div class="show-sub-grade" style="margin-top:3px">{{ $sub->grade }}</div>
                    @endif
                </div>
            </div>
            @endforeach
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
    </div>{{-- /kanan --}}

</div>{{-- /show-wrap --}}

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
</script>

@endsection
