<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $assignment->title }} – Kumpul Tugas</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f0f4ef;color:#1A2517;min-height:100vh}
a{text-decoration:none}
@keyframes navSlideDown{from{transform:translateY(-64px);opacity:0}to{transform:none;opacity:1}}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.pub-navbar{position:sticky;top:0;z-index:100;background:linear-gradient(135deg,#1A2517,#2a3826);box-shadow:0 2px 16px rgba(0,0,0,.28);animation:navSlideDown .4s cubic-bezier(.16,1,.3,1) both}
.pub-inner{max-width:1280px;margin:0 auto;padding:0 1.5rem;display:flex;align-items:center;justify-content:space-between;height:60px}
.pub-brand{display:flex;align-items:center;gap:10px;text-decoration:none;flex-shrink:0}
.pub-brand-icon{width:34px;height:34px;border-radius:9px;background:rgba(172,200,162,.12);border:1.5px solid rgba(172,200,162,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .18s}
.pub-brand-name{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff;line-height:1.2}
.pub-brand-sub{font-size:10px;color:rgba(172,200,162,.4)}
.pub-links{display:flex;align-items:center;gap:4px}
.pub-link{font-size:13px;font-weight:600;color:rgba(172,200,162,.55);text-decoration:none;padding:7px 13px;border-radius:8px;transition:color .15s,background .15s;white-space:nowrap}
.pub-link:hover,.pub-link.on{color:#ACC8A2;background:rgba(172,200,162,.1)}
.pub-btn{font-size:12px;font-weight:700;padding:7px 15px;border-radius:8px;color:#ACC8A2;border:1.5px solid rgba(172,200,162,.3);text-decoration:none;margin-left:6px;transition:background .15s,transform .15s;white-space:nowrap}
.pub-btn:hover{background:rgba(172,200,162,.08);transform:translateY(-1px)}
@media(max-width:600px){.pub-inner{padding:0 1rem}.pub-brand-sub{display:none}.pub-link{padding:6px 9px;font-size:12px}.pub-btn{padding:6px 11px;font-size:12px;margin-left:3px}}
.page-trans{position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#1A2517,#2d3d29);opacity:0;pointer-events:none;transition:opacity .22s ease}
.page-trans.go{opacity:1;pointer-events:all}

/* HERO */
.hero{background:linear-gradient(135deg,#1A2517 0%,#2a3826 60%,#3d5438 100%);padding:28px 24px 24px}
.hero-inner{max-width:900px;margin:0 auto}
.back-link{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:rgba(172,200,162,.55);margin-bottom:12px;transition:color .15s}
.back-link:hover{color:#ACC8A2}
.hero-title{font-family:'Outfit',sans-serif;font-weight:800;font-size:clamp(1.2rem,3vw,1.7rem);color:#fff;margin-bottom:5px}
.hero-meta{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
.hero-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:999px;font-size:11px;font-weight:600;background:rgba(172,200,162,.1);color:#ACC8A2;border:1px solid rgba(172,200,162,.2)}
.badge-urgent{background:rgba(248,113,113,.1);color:#f87171;border-color:rgba(248,113,113,.25)}
.badge-soon{background:rgba(245,158,11,.1);color:#f59e0b;border-color:rgba(245,158,11,.25)}

/* WRAP */
.wrap{max-width:900px;margin:0 auto;padding:24px 18px 48px;display:grid;grid-template-columns:1fr 360px;gap:20px;animation:fadeUp .4s ease both}
@media(max-width:720px){.wrap{grid-template-columns:1fr}}

/* FORM CARD */
.form-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden}
.form-head{padding:16px 20px;background:linear-gradient(135deg,#1A2517,#2a3826)}
.form-head-title{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff}
.form-head-sub{font-size:11px;color:rgba(172,200,162,.4);margin-top:2px}
.form-body{padding:20px}
.field{margin-bottom:16px}
.field-label{display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.inp{width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;font-size:13px;font-family:inherit;background:#fafcf9;outline:none;transition:border-color .15s,box-shadow .15s}
.inp:focus{border-color:#ACC8A2;box-shadow:0 0 0 3px rgba(172,200,162,.1)}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:480px){.field-row{grid-template-columns:1fr}}

/* FILE DROP */
.file-drop{border:2px dashed #c8d9c5;border-radius:12px;padding:28px 20px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;background:#fafcf9;position:relative}
.file-drop:hover,.file-drop.drag{border-color:#ACC8A2;background:rgba(172,200,162,.05)}
.file-drop input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.file-drop-icon{font-size:28px;margin-bottom:8px}
.file-drop-text{font-size:13px;font-weight:600;color:#374151}
.file-drop-sub{font-size:11px;color:#9ca3af;margin-top:3px}
.file-preview{display:none;align-items:center;gap:10px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;margin-top:10px}
.file-preview.show{display:flex}
.file-preview-name{font-size:12px;font-weight:600;color:#166534;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.file-preview-size{font-size:11px;color:#9ca3af;flex-shrink:0}

/* SUBMIT BTN */
.btn-submit{width:100%;padding:13px;border-radius:11px;border:none;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;transition:transform .15s,box-shadow .15s,filter .15s;margin-top:6px}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(26,37,23,.3);filter:brightness(1.08)}
.btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none}

/* INFO CARD */
.info-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden;height:fit-content}
.info-head{padding:14px 18px;background:linear-gradient(135deg,#1A2517,#2a3826)}
.info-head-title{font-family:'Outfit',sans-serif;font-weight:700;font-size:14px;color:#fff}
.info-body{padding:16px 18px;display:flex;flex-direction:column;gap:12px}
.info-row{display:flex;gap:10px;align-items:flex-start}
.info-icon{font-size:14px;flex-shrink:0;margin-top:1px}
.info-key{font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em}
.info-val{font-size:13px;font-weight:600;color:#1A2517;margin-top:1px}

/* SUBMISSIONS */
.subs-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden;margin-top:20px}
.subs-head{padding:14px 18px;background:#f8faf7;border-bottom:1px solid #e8f0e6;display:flex;align-items:center;justify-content:space-between}
.subs-head-title{font-size:13px;font-weight:700;color:#1A2517}
.subs-count{font-size:11px;color:#9ca3af}
.sub-row{display:flex;align-items:center;gap:10px;padding:11px 18px;border-bottom:1px solid #f5f5f5}
.sub-row:last-child{border-bottom:none}
.sub-ext{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0}
.ext-pdf{background:#fef2f2;color:#ef4444}
.ext-doc{background:#eff6ff;color:#2563eb}
.ext-ppt{background:#fff7ed;color:#ea580c}
.ext-xls{background:#f0fdf4;color:#16a34a}
.ext-zip{background:#faf5ff;color:#7c3aed}
.ext-other{background:#f9fafb;color:#6b7280}
.sub-name{font-size:12px;font-weight:600;color:#1A2517;flex:1}
.sub-class{font-size:10px;color:#9ca3af}
.sub-time{font-size:10px;color:#9ca3af;white-space:nowrap}
.sub-grade{font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;background:#f0fdf4;color:#16a34a}

/* FLASH */
.flash{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px}
.flash-ok{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.flash-err{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}

/* EXPIRED */
.expired-banner{background:linear-gradient(135deg,#7f1d1d,#991b1b);border-radius:12px;padding:20px;text-align:center;color:#fca5a5}
.expired-banner h3{font-family:'Outfit',sans-serif;font-weight:700;font-size:16px;color:#fff;margin-bottom:6px}
</style>
</head>
<body>

<nav class="pub-navbar">
    <div class="pub-inner">
        <a href="{{ route('home') }}" class="pub-brand">
            <div class="pub-brand-icon">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="pub-brand-name">Lab Management</div>
                <div class="pub-brand-sub">Nuris Jember</div>
            </div>
        </a>
        <div class="pub-links">
            <a href="{{ route('home') }}" class="pub-link">Jadwal</a>
            <a href="{{ route('inventory.public') }}" class="pub-link">Inventaris</a>
            <a href="{{ route('rekap.public') }}" class="pub-link">Rekap</a>
            <a href="{{ route('assignment.public') }}" class="pub-link on">Tugas</a>
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="pub-btn">Login →</a>
            @endauth
        </div>
    </div>
</nav>

@php
    $expired  = $assignment->isExpired();
    $diffHrs  = now()->diffInHours($assignment->deadline, false);
    $deadlineBadgeClass = $expired ? 'badge-urgent' : ($diffHrs < 24 ? 'badge-soon' : '');
    $deadlineLabel = $expired
        ? 'Deadline terlewat'
        : ($diffHrs < 1 ? 'Kurang dari 1 jam!' : ($diffHrs < 24 ? "Sisa {$diffHrs} jam" : $assignment->deadline->translatedFormat('d M Y, H:i')));
@endphp

<div class="hero">
    <div class="hero-inner">
        <a href="{{ route('assignment.public') }}" class="back-link">
            ← Kembali ke daftar tugas
        </a>
        <div class="hero-title">{{ $assignment->title }}</div>
        <div class="hero-meta">
            <span class="hero-badge">📚 {{ $assignment->subject_name }}</span>
            <span class="hero-badge">👥 {{ $assignment->class_name }}</span>
            <span class="hero-badge">👩‍🏫 {{ $assignment->teacher->name }}</span>
            <span class="hero-badge {{ $deadlineBadgeClass }}">🕐 {{ $deadlineLabel }}</span>
        </div>
    </div>
</div>

<div class="wrap">

    {{-- KIRI: Form --}}
    <div>
        @if(session('success'))
            <div class="flash flash-ok">✓ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="flash flash-err">⚠ {{ $errors->first() }}</div>
        @endif

        @if($expired)
            <div class="expired-banner">
                <h3>🔒 Pengumpulan Ditutup</h3>
                <p>Deadline tugas ini sudah terlewat. Hubungi gurumu jika ada kendala.</p>
            </div>
        @else
            <div class="form-card">
                <div class="form-head">
                    <div class="form-head-title">📤 Form Pengumpulan Tugas</div>
                    <div class="form-head-sub">Isi data diri dan upload file tugasmu</div>
                </div>
                <div class="form-body">
                    <form method="POST" action="{{ route('assignment.submit', $assignment) }}" enctype="multipart/form-data" id="submit-form">
                        @csrf
                        <div class="field">
                            <label class="field-label">Nama Lengkap *</label>
                            <input name="student_name" type="text" class="inp" placeholder="Nama lengkapmu" required value="{{ old('student_name') }}">
                        </div>
                        <div class="field">
                            <label class="field-label">Kelas (otomatis)</label>
                            <input type="text" class="inp" value="{{ $activeClass->name }}" disabled>
                        </div>

                        <div class="field">
                            <label class="field-label">File Tugas *</label>
                            <div class="file-drop" id="file-drop">
                                <input type="file" name="file" id="file-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar" required onchange="previewFile(this)">
                                <div class="file-drop-icon">📎</div>
                                <div class="file-drop-text">Klik atau drag file ke sini</div>
                                <div class="file-drop-sub">PDF, Word, PPT, Excel, ZIP, RAR · Maks 10MB</div>
                            </div>
                            <div class="file-preview" id="file-preview">
                                <span style="font-size:18px">📄</span>
                                <span class="file-preview-name" id="file-preview-name"></span>
                                <span class="file-preview-size" id="file-preview-size"></span>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit" id="btn-submit">
                            ✓ Kumpulkan Tugas
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Daftar Submission --}}
        @if($submissions->isNotEmpty())
        <div class="subs-card">
            <div class="subs-head">
                <span class="subs-head-title">📋 Yang Sudah Mengumpulkan</span>
                <span class="subs-count">{{ $submissions->count() }} siswa</span>
            </div>
            @foreach($submissions as $sub)
            @php
                $ext = strtolower($sub->file_ext ?? 'other');
                $extClass = match($ext) {
                    'pdf'          => 'ext-pdf',
                    'doc','docx'   => 'ext-doc',
                    'ppt','pptx'   => 'ext-ppt',
                    'xls','xlsx'   => 'ext-xls',
                    'zip','rar'    => 'ext-zip',
                    default        => 'ext-other'
                };
            @endphp
            <div class="sub-row">
                <div class="sub-ext {{ $extClass }}">{{ strtoupper($ext) }}</div>
                <div style="flex:1;min-width:0">
                    <div class="sub-name">{{ $sub->student_name }}</div>
                    <div class="sub-class">{{ $sub->student_class }}</div>
                </div>
                <div style="text-align:right">
                    <div class="sub-time">{{ $sub->submitted_at->translatedFormat('d M, H:i') }}</div>
                    @if($sub->grade !== null)
                    <div class="sub-grade" style="margin-top:3px">{{ $sub->grade }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- KANAN: Info Tugas --}}
    <div>
        <div class="info-card">
            <div class="info-head">
                <div class="info-head-title">ℹ️ Detail Tugas</div>
            </div>
            <div class="info-body">
                <div class="info-row">
                    <span class="info-icon">📝</span>
                    <div>
                        <div class="info-key">Judul</div>
                        <div class="info-val">{{ $assignment->title }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-icon">📚</span>
                    <div>
                        <div class="info-key">Mata Pelajaran</div>
                        <div class="info-val">{{ $assignment->subject_name }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-icon">👥</span>
                    <div>
                        <div class="info-key">Kelas</div>
                        <div class="info-val">{{ $assignment->class_name }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-icon">👩‍🏫</span>
                    <div>
                        <div class="info-key">Guru</div>
                        <div class="info-val">{{ $assignment->teacher->name }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-icon">🕐</span>
                    <div>
                        <div class="info-key">Deadline</div>
                        <div class="info-val {{ $expired ? 'deadline-urgent' : '' }}" style="color:{{ $expired ? '#ef4444' : ($diffHrs < 24 ? '#f59e0b' : '#1A2517') }}">
                            {{ $assignment->deadline->translatedFormat('l, d M Y') }}<br>
                            <span style="font-size:12px">Pukul {{ $assignment->deadline->format('H:i') }} WIB</span>
                        </div>
                    </div>
                </div>
                @if($assignment->description)
                <div class="info-row">
                    <span class="info-icon">💬</span>
                    <div>
                        <div class="info-key">Keterangan</div>
                        <div class="info-val" style="font-weight:400;font-size:12px;line-height:1.6">{{ $assignment->description }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

<div class="page-trans" id="pt"></div>
<script>
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    const preview = document.getElementById('file-preview');
    document.getElementById('file-preview-name').textContent = file.name;
    document.getElementById('file-preview-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
    preview.classList.add('show');
}

// Drag & drop visual
const drop = document.getElementById('file-drop');
if (drop) {
    drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('drag'); });
    drop.addEventListener('dragleave', () => drop.classList.remove('drag'));
    drop.addEventListener('drop', () => drop.classList.remove('drag'));
}

// Loading state on submit
const form = document.getElementById('submit-form');
if (form) {
    form.addEventListener('submit', () => {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.textContent = '⏳ Mengupload...';
    });
}

document.querySelectorAll('a.pub-link, a.pub-btn, a.pub-brand, a.back-link').forEach(a => {
    const href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript') || a.getAttribute('target') === '_blank') return;
    a.addEventListener('click', function(e) {
        const current = window.location.pathname;
        try {
            const target = new URL(href, window.location.href).pathname;
            if (target === current) return;
        } catch(err) {}
        e.preventDefault();
        document.getElementById('pt').classList.add('go');
        setTimeout(() => { window.location.href = href; }, 220);
    });
});
window.addEventListener('pageshow', () => {
    document.getElementById('pt').classList.remove('go');
});
</script>
</body>
</html>