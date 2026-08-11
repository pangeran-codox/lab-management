<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Tugas – {{ $teacher->name ?? 'Panel Guru' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
    --primary:#00693E;--primary-dark:#003d24;--primary-light:#eaf4f0;
    --accent:#B9D9EB;--danger:#c0392b;--warning:#d97706;--success:#16a34a;
    --text-main:#0d2416;--text-muted:#6b8fa3;--bg-body:#f8fafc;--bg-card:#fff;
    --border:#e2e8f0;--r-xl:20px;--r-lg:16px;--r-md:12px;
    --font-head:'Outfit',sans-serif;--font-main:'DM Sans',sans-serif;
}
body{font-family:var(--font-main);background:var(--bg-body);color:var(--text-main);min-height:100vh}
.app-shell{display:flex;min-height:100vh}
.sidebar{width:260px;background:#fff;border-right:1px solid var(--border);padding:24px;height:100vh;position:fixed;left:0;top:0;z-index:50;display:flex;flex-direction:column}
.main-content{flex:1;padding:36px;margin-left:260px;min-height:100vh}
.sb-brand{display:flex;align-items:center;gap:12px;margin-bottom:32px}
.sb-logo{width:40px;height:40px;background:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0}
.sb-menu{list-style:none}
.sb-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;color:var(--text-muted);font-weight:700;font-size:13px;margin-bottom:6px;cursor:pointer;transition:all .2s;text-decoration:none}
.sb-item:hover{background:var(--primary-light);color:var(--primary)}
.sb-item.active{background:var(--primary);color:#fff}
.sb-item svg{flex-shrink:0}
</style>
<style>
/* ── Stats ── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:28px}
.stat-card{background:#fff;padding:20px;border-radius:var(--r-lg);border:1px solid var(--border);display:flex;flex-direction:column;gap:6px}
.stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em}
.stat-value{font-family:var(--font-head);font-size:26px;font-weight:800}

/* ── Task Grid ── */
.task-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px}
.task-card{background:#fff;border-radius:var(--r-lg);border:1px solid var(--border);padding:20px;display:flex;flex-direction:column;transition:all .25s;position:relative}
.task-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.07);border-color:var(--primary)}
.task-card.inactive{opacity:.72;border-style:dashed}
.task-badge{position:absolute;top:16px;right:16px;padding:3px 10px;border-radius:99px;font-size:10px;font-weight:800;text-transform:uppercase}
.badge-active{background:#dcfce7;color:#166534}
.badge-expired{background:#fee2e2;color:#991b1b}
.badge-inactive{background:#f1f5f9;color:#64748b}
.badge-series{background:#eff6ff;color:#1d4ed8;margin-left:6px}
.task-subject{font-size:11px;font-weight:800;color:var(--primary);text-transform:uppercase;margin-bottom:6px}
.task-title{font-family:var(--font-head);font-size:17px;font-weight:800;margin-bottom:10px;line-height:1.3;padding-right:72px}
.task-meta{display:flex;flex-direction:column;gap:6px;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border)}
.meta-row{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--text-muted);font-weight:600}
.meta-row svg{width:13px;opacity:.6;flex-shrink:0}
.task-footer{display:flex;justify-content:space-between;align-items:center;margin-top:auto}
.task-progress{flex:1;margin-right:14px}
.progress-label{display:flex;justify-content:space-between;font-size:10px;font-weight:800;color:var(--text-muted);margin-bottom:5px}
.progress-bar{height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden}
.progress-fill{height:100%;background:var(--primary);border-radius:99px}

/* ── Buttons ── */
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .2s;font-family:var(--font-main);text-decoration:none;white-space:nowrap}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{background:var(--primary-dark);transform:translateY(-1px)}
.btn-secondary{background:var(--bg-body);color:var(--text-main);border:1px solid var(--border)}
.btn-secondary:hover{border-color:var(--primary);color:var(--primary)}
.btn-danger{background:#fef2f2;color:var(--danger);border:1px solid #fecaca}
.btn-danger:hover{background:#fee2e2}
.btn-warning{background:#fffbeb;color:var(--warning);border:1px solid #fde68a}
.btn-warning:hover{background:#fef3c7}
.btn-info{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe}
.btn-info:hover{background:#dbeafe}
.btn-sm{padding:6px 12px;font-size:12px;border-radius:8px}
.btn-xs{padding:4px 9px;font-size:11px;border-radius:7px}
.btn-icon{padding:7px;border-radius:8px}
.btn-manage{background:var(--primary);color:#fff;padding:9px 16px;border-radius:10px;font-size:13px;font-weight:700;border:none;cursor:pointer}
.btn-manage:hover{background:var(--primary-dark)}
.btn-create-large{background:var(--primary);color:#fff;padding:11px 22px;border-radius:12px;font-weight:700;display:inline-flex;align-items:center;gap:9px;border:none;cursor:pointer;font-size:13px}
.btn-create-large:hover{background:var(--primary-dark)}
</style>
<style>
/* ── Modal ── */
.modal-overlay{position:fixed;inset:0;background:rgba(13,36,22,.45);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;z-index:1000;padding:16px}
.modal-overlay.open{display:flex}
.modal-box{background:#fff;width:100%;max-width:1100px;max-height:92vh;border-radius:var(--r-xl);overflow:hidden;display:flex;flex-direction:column;animation:modalUp .25s ease-out}
.modal-box.sm{max-width:560px}
.modal-box.md{max-width:700px}
@keyframes modalUp{from{opacity:0;transform:translateY(18px) scale(.97)}to{opacity:1;transform:none}}
.modal-head{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:flex-start;flex-shrink:0;background:#fff}
.modal-head-green{background:linear-gradient(135deg,var(--primary-dark),var(--primary));padding:20px 24px;border-bottom:none}
.modal-head-green .modal-title{color:#fff;font-family:var(--font-head);font-size:18px;font-weight:800}
.modal-head-green .modal-subtitle{color:rgba(185,217,235,.6);font-size:12px;margin-top:3px}
.modal-head-green .btn-close{background:rgba(255,255,255,.12);color:#fff;border:none;width:32px;height:32px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center}
.modal-head-green .btn-close:hover{background:rgba(255,255,255,.22)}
.modal-title{font-family:var(--font-head);font-weight:800;font-size:18px}
.modal-subtitle{font-size:12px;color:var(--text-muted);margin-top:3px}
.btn-close{background:none;border:none;cursor:pointer;color:var(--text-muted);padding:4px;border-radius:8px;display:flex;align-items:center}
.btn-close:hover{background:var(--bg-body)}
.modal-body{padding:0;overflow-y:auto;flex:1}
.modal-body::-webkit-scrollbar{width:4px}
.modal-body::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:10px;flex-shrink:0;background:#fff}

/* ── Tabs dalam modal ── */
.modal-tabs{display:flex;gap:0;border-bottom:1px solid var(--border);flex-shrink:0;background:#fafafa}
.modal-tab{padding:12px 20px;font-size:13px;font-weight:700;color:var(--text-muted);cursor:pointer;border-bottom:2px solid transparent;transition:all .2s;background:none;border-top:none;border-left:none;border-right:none;font-family:var(--font-main)}
.modal-tab:hover{color:var(--primary);background:var(--primary-light)}
.modal-tab.active{color:var(--primary);border-bottom-color:var(--primary);background:#fff}
.tab-pane{display:none;padding:20px 24px}
.tab-pane.active{display:block}

/* ── Action toolbar di modal ── */
.modal-actions-bar{display:flex;gap:8px;flex-wrap:wrap;padding:14px 24px;background:#f8fafc;border-bottom:1px solid var(--border)}

/* ── Table ── */
.tbl-wrap{width:100%;overflow-x:auto}
.adm-table{width:100%;border-collapse:collapse;min-width:700px;font-size:13px}
.adm-table th{padding:11px 16px;text-align:left;font-size:10px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;background:#f8fafc;border-bottom:1.5px solid var(--border)}
.adm-table td{padding:12px 16px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.adm-table tr:hover td{background:#f8fafc}

/* ── Form ── */
.field{display:flex;flex-direction:column;gap:7px;margin-bottom:14px}
.field-label{font-size:12px;font-weight:700;color:var(--text-main)}
.field-hint{font-size:11px;color:var(--text-muted)}
.inp{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:var(--font-main);font-size:13px;transition:all .2s;background:#fff;color:var(--text-main)}
.inp:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,105,62,.1)}
.field-row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.field-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
@media(max-width:600px){.field-row-2,.field-row-3{grid-template-columns:1fr}}

/* ── Flash ── */
.flash{padding:11px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.flash.ok{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.flash.err{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}

/* ── Series badge ── */
.series-info{display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-top:4px}
.chip{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700}
.chip-blue{background:#eff6ff;color:#1d4ed8}
.chip-green{background:#dcfce7;color:#166534}
.chip-gray{background:#f1f5f9;color:#475569}
.chip-orange{background:#fff7ed;color:#c2410c}

@media(max-width:1024px){
    .sidebar{transform:translateX(-100%);transition:transform .3s}
    .sidebar.show{transform:translateX(0)}
    .main-content{margin-left:0;padding:20px}
    .mobile-nav{display:flex!important}
}
.mobile-nav{display:none;background:#fff;padding:12px 18px;border-bottom:1px solid var(--border);justify-content:space-between;align-items:center;position:sticky;top:0;z-index:60}
.empty-state{text-align:center;padding:48px 20px;color:var(--text-muted)}
.empty-icon{font-size:40px;margin-bottom:12px;opacity:.5}

@keyframes highlightNew {
    0%   { background: #dcfce7; }
    100% { background: transparent; }
}
</style>

{{-- Vite — load Echo + bootstrap untuk WebSocket --}}
@vite(['resources/js/app.js'])
</head>
<body>

{{-- Mobile nav --}}
<div class="mobile-nav" style="display:none">
    <div style="display:flex;align-items:center;gap:10px">
        <div class="sb-logo" style="width:32px;height:32px">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
        </div>
        <span style="font-weight:800;font-size:14px">Admin Tugas</span>
    </div>
    <button onclick="document.querySelector('.sidebar').classList.toggle('show')" style="background:var(--primary-light);color:var(--primary);border:none;padding:8px;border-radius:8px;cursor:pointer">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
    </button>
</div>

<div class="app-shell">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-logo">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
        </div>
        <div>
            <div style="font-weight:800;font-size:15px">EduZone Lab</div>
            <div style="font-size:11px;color:var(--text-muted)">Panel Manajemen Guru</div>
        </div>
    </div>

    <nav class="sb-menu">
        <div class="sb-item active">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard Tugas
        </div>
        <div class="sb-item" onclick="location.href='{{ route('assignment.public') }}'">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Lihat Halaman Publik
        </div>
        @if(Auth::check() && in_array(Auth::user()->role, ['admin','staff','technician','teknisi']))
        <div class="sb-item" onclick="location.href='{{ route('dashboard') }}'">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
            Kembali ke Dashboard
        </div>
        @endif
    </nav>

    <div style="margin-top:auto;padding-top:20px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
            <div style="width:34px;height:34px;border-radius:50%;background:var(--primary-light);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:800;font-size:13px;flex-shrink:0">{{ substr($teacher->name ?? 'G',0,1) }}</div>
            <div>
                <div style="font-weight:700;font-size:13px">{{ $teacher->name ?? 'Guru' }}</div>
                <div style="font-size:11px;color:var(--text-muted)">{{ $teacher->token ?? '-' }}</div>
            </div>
        </div>
        <a href="{{ route('assignment.admin.logout') }}" class="sb-item" style="color:var(--text-muted)">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Logout
        </a>
    </div>
</aside>

{{-- ═══ MAIN ═══ --}}
<main class="main-content">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flash ok">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error') || $errors->any())
    <div class="flash err">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') ?? $errors->first() }}
    </div>
    @endif

    <header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-family:var(--font-head);font-weight:800;font-size:22px">Halo, {{ explode(' ',$teacher->name ?? 'Guru')[0] }}! 👋</h1>
            <p style="color:var(--text-muted);font-size:13px;margin-top:3px">Pantau dan kelola pengumpulan tugas siswa</p>
        </div>
        <button class="btn-create-large" onclick="openModal('modal-create')">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tugas Baru
        </button>
    </header>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Tugas</span>
            <span class="stat-value">{{ $assignments->count() }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Belum Dinilai</span>
            <span class="stat-value" style="color:var(--warning)">{{ $assignments->sum(fn($a)=>$a->submissions->where('status','submitted')->count()) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Sudah Dinilai</span>
            <span class="stat-value" style="color:var(--success)">{{ $assignments->sum(fn($a)=>$a->submissions->where('status','graded')->count()) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Aktif Sekarang</span>
            <span class="stat-value">{{ $assignments->filter(fn($a)=>$a->is_active && !$a->isExpired())->count() }}</span>
        </div>
    </div>

    {{-- Task Grid --}}
    <div class="task-grid">
        @forelse($assignments as $a)
        @php
            $expired  = $a->isExpired();
            $active   = $a->is_active;
            $subs     = $a->submissions;
            $total    = $subs->count();
            $graded   = $subs->where('status','graded')->count();
            $progress = $total > 0 ? min(($total / 36) * 100, 100) : 0;
            $badgeClass = !$active ? 'badge-inactive' : ($expired ? 'badge-expired' : 'badge-active');
            $badgeLabel = !$active ? 'Tertutup' : ($expired ? 'Arsip' : 'Aktif');
        @endphp
        <article class="task-card {{ !$active ? 'inactive' : '' }}">
            <span class="task-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>

            <div class="task-subject">{{ $a->subject_name }}</div>
            <h3 class="task-title">{{ $a->title }}</h3>

            @if($a->isPartOfSeries())
            <div class="series-info" style="margin-bottom:8px">
                <span class="chip chip-blue">
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Pertemuan {{ $a->session_number }}
                </span>
            </div>
            @endif

            <div class="task-meta">
                <div class="meta-row">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    Kelas: {{ $a->class_name }}
                </div>
                <div class="meta-row">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Deadline: {{ $a->deadline->translatedFormat('d M Y, H:i') }}
                </div>
                @if($a->attachment_name)
                <div class="meta-row">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Ada soal: {{ $a->attachment_name }}
                </div>
                @endif
            </div>

            <div class="task-footer">
                <div class="task-progress">
                    <div class="progress-label">
                        <span>PENGUMPULAN</span>
                        <span>{{ $total }} SISWA{{ $graded > 0 ? " · {$graded} dinilai" : '' }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:{{ $progress }}%"></div>
                    </div>
                </div>
                <button class="btn-manage" onclick="openModal('modal-subs-{{ $a->id }}')">Kelola</button>
            </div>
        </article>
        @empty
        <div class="empty-state" style="grid-column:1/-1">
            <div class="empty-icon">📋</div>
            <h3 style="font-family:var(--font-head);margin-bottom:6px">Belum ada tugas</h3>
            <p style="font-size:13px">Mulai dengan membuat tugas baru untuk kelas Anda.</p>
        </div>
        @endforelse
    </div>

</main>
</div>{{-- /app-shell --}}

{{-- ═══════════════════════════════════════════
     MODAL: BUAT TUGAS BARU
════════════════════════════════════════════ --}}
<div id="modal-create" class="modal-overlay">
    <div class="modal-box md">
        <div class="modal-head modal-head-green">
            <div>
                <div class="modal-title">Buat Tugas Baru</div>
                <div class="modal-subtitle">Isi detail tugas untuk diterbitkan ke siswa</div>
            </div>
            <button class="btn-close" onclick="closeModal('modal-create')">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('assignment.store') }}" enctype="multipart/form-data" style="padding:20px 24px">
                @csrf
                <input type="hidden" name="teacher_token" value="{{ $teacher->token ?? '' }}">

                <div class="field">
                    <label class="field-label">Judul Tugas *</label>
                    <input name="title" type="text" class="inp" required placeholder="Contoh: Laporan Praktikum Jaringan">
                </div>

                <div class="field-row-2">
                    <div class="field">
                        <label class="field-label">Lembaga *</label>
                        <select name="organization_id" class="inp" required onchange="loadKelas(this.value,'kelas_container')">
                            <option value="">Pilih Lembaga</option>
                            @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label class="field-label">Mata Pelajaran *</label>
                        <input name="subject_name" type="text" class="inp" required placeholder="Produktif TKJ">
                    </div>
                </div>

                <div class="field">
                    <label class="field-label">Pilih Kelas *</label>
                    <div id="kelas_container" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:7px;padding:12px;background:var(--bg-body);border-radius:10px;border:1.5px solid var(--border);max-height:110px;overflow-y:auto">
                        <span style="font-size:12px;color:var(--text-muted);grid-column:1/-1">Pilih lembaga dulu...</span>
                    </div>
                </div>

                <div class="field-row-2">
                    <div class="field">
                        <label class="field-label">Deadline *</label>
                        <input name="deadline" type="datetime-local" class="inp" required>
                    </div>
                    <div class="field">
                        <label class="field-label">Soal / Lampiran</label>
                        <input name="attachment" type="file" class="inp">
                    </div>
                </div>

                <div class="field">
                    <label class="field-label">Keterangan</label>
                    <textarea name="description" class="inp" rows="2" placeholder="Instruksi tambahan..."></textarea>
                </div>

                <div style="display:flex;gap:10px;margin-top:6px">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Terbitkan Tugas</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-create')">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL: KELOLA PER TUGAS (submissions + actions)
════════════════════════════════════════════ --}}
@foreach($assignments as $a)
@php
    $aExpired = $a->isExpired();
    $aActive  = $a->is_active;
    $aSubs    = $a->submissions->sortByDesc('submitted_at');
    $siblings = $a->isPartOfSeries() ? $a->seriesSiblings() : collect();
@endphp
<div id="modal-subs-{{ $a->id }}" class="modal-overlay">
    <div class="modal-box">

        {{-- Header --}}
        <div class="modal-head modal-head-green">
            <div style="min-width:0">
                <div class="modal-title" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    {{ $a->title }}
                    @if($a->isPartOfSeries())
                    <span class="chip chip-blue" style="font-size:11px">Pertemuan {{ $a->session_number }}</span>
                    @endif
                    @if(!$aActive)
                    <span class="chip chip-gray" style="font-size:11px">Tertutup</span>
                    @elseif($aExpired)
                    <span class="chip chip-orange" style="font-size:11px">Expired</span>
                    @else
                    <span class="chip chip-green" style="font-size:11px">Aktif</span>
                    @endif
                </div>
                <div class="modal-subtitle">{{ $a->subject_name }} · {{ $a->class_name }} · Deadline: {{ $a->deadline->translatedFormat('d M Y, H:i') }}</div>
            </div>
            <button class="btn-close" onclick="closeModal('modal-subs-{{ $a->id }}')">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Tabs --}}
        <div class="modal-tabs">
            <button class="modal-tab active" onclick="switchTab(this,'tab-subs-{{ $a->id }}','tabs-{{ $a->id }}')">
                📋 Pengumpulan ({{ $aSubs->count() }})
            </button>
            <button class="modal-tab" onclick="switchTab(this,'tab-actions-{{ $a->id }}','tabs-{{ $a->id }}')">
                ⚙️ Aksi Tugas
            </button>
            <button class="modal-tab" onclick="switchTab(this,'tab-edit-{{ $a->id }}','tabs-{{ $a->id }}')">
                ✏️ Edit Tugas
            </button>
        </div>

        <div class="modal-body" id="tabs-{{ $a->id }}">

            {{-- ─── TAB 1: SUBMISSIONS ─── --}}
            <div id="tab-subs-{{ $a->id }}" class="tab-pane active" style="padding:0">

                {{-- Action bar submissions --}}
                <div class="modal-actions-bar">
                    @if($aSubs->count() > 0)
                    <a href="{{ route('assignment.download-zip', $a) }}" class="btn btn-info btn-sm">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download ZIP Semua
                    </a>
                    <a href="{{ route('assignment.export-excel', $a) }}" class="btn btn-sm" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel
                    </a>
                    @endif
                    @if($a->attachment_path)
                    <a href="{{ route('assignment.download.attachment', $a) }}" class="btn btn-secondary btn-sm">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L20.5 13"/></svg>
                        Unduh Soal
                    </a>
                    @endif
                    <span style="margin-left:auto;font-size:12px;color:var(--text-muted)">
                        {{ $aSubs->where('status','graded')->count() }}/{{ $aSubs->count() }} dinilai
                    </span>
                </div>

                @if($aSubs->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <p>Belum ada pengumpulan.</p>
                </div>
                @else
                <div class="tbl-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Waktu</th>
                                <th>File</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aSubs as $sub)
                            <tr>
                                <td>
                                    <div style="font-weight:700">{{ $sub->student_name }}</div>
                                    <div style="font-size:11px;color:var(--text-muted)">{{ $sub->student_class }}</div>
                                    @if($sub->status === 'graded')
                                    <span class="chip chip-green" style="margin-top:3px">Dinilai</span>
                                    @else
                                    <span class="chip chip-orange" style="margin-top:3px">Menunggu</span>
                                    @endif
                                </td>
                                <td style="font-size:12px;color:var(--text-muted)">{{ $sub->submitted_at->translatedFormat('d M, H:i') }}</td>
                                <td>
                                    <a href="{{ route('assignment.download', $sub) }}" class="btn btn-secondary btn-xs">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        {{ $sub->file_ext ? strtoupper($sub->file_ext) : 'Unduh' }}
                                    </a>
                                    <div style="font-size:10px;color:var(--text-muted);margin-top:2px">{{ $sub->file_size ?? '' }}</div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('assignment.grade', $sub) }}" style="display:flex;gap:6px;align-items:center">
                                        @csrf
                                        <input type="number" name="grade" value="{{ $sub->grade }}" class="inp" style="width:58px;padding:5px 8px;text-align:center" min="0" max="100" placeholder="0">
                                        <button type="submit" class="btn btn-primary btn-xs">Simpan</button>
                                    </form>
                                    @if($sub->feedback)
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:3px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $sub->feedback }}">{{ $sub->feedback }}</div>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('assignment.submission.destroy', $sub) }}" onsubmit="return confirm('Hapus pengumpulan {{ addslashes($sub->student_name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" title="Hapus pengumpulan ini">
                                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- ─── TAB 2: AKSI TUGAS ─── --}}
            <div id="tab-actions-{{ $a->id }}" class="tab-pane" style="display:none">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:20px 24px">

                    {{-- Toggle Akses --}}
                    <div style="background:var(--bg-body);border-radius:12px;border:1px solid var(--border);padding:18px">
                        <div style="font-weight:700;font-size:14px;margin-bottom:6px">
                            {{ $aActive ? '🔓 Tutup Akses Siswa' : '🔑 Buka Akses Siswa' }}
                        </div>
                        <p style="font-size:12px;color:var(--text-muted);margin-bottom:14px">
                            {{ $aActive ? 'Tugas sedang aktif, siswa bisa melihat dan mengumpulkan.' : 'Tugas sedang tertutup, siswa tidak bisa melihat tugas ini.' }}
                        </p>
                        <form method="POST" action="{{ route('assignment.toggle-access', $a) }}">
                            @csrf
                            <button type="submit" class="btn {{ $aActive ? 'btn-warning' : 'btn-primary' }} btn-sm" style="width:100%;justify-content:center">
                                {{ $aActive ? 'Tutup Akses' : 'Buka Akses' }}
                            </button>
                        </form>
                    </div>

                    {{-- Toggle Download Siswa --}}
                    @php $aAllowDl = $a->allow_student_download ?? false; @endphp
                    <div style="background:{{ $aAllowDl ? '#f0fdf4' : 'var(--bg-body)' }};border-radius:12px;border:1.5px solid {{ $aAllowDl ? '#bbf7d0' : 'var(--border)' }};padding:18px">
                        <div style="font-weight:700;font-size:14px;margin-bottom:6px;color:{{ $aAllowDl ? '#166534' : 'var(--text-main)' }}">
                            {{ $aAllowDl ? '📥 Download Siswa: Aktif' : '📥 Download Siswa: Nonaktif' }}
                        </div>
                        <p style="font-size:12px;color:var(--text-muted);margin-bottom:14px">
                            @if($aAllowDl)
                                Siswa bisa download kembali file yang sudah mereka kumpulkan dari halaman tugas.
                            @else
                                Siswa tidak bisa download file tugasnya sendiri. Aktifkan agar siswa bisa re-download.
                            @endif
                        </p>
                        <form method="POST" action="{{ route('assignment.toggle-student-download', $a) }}">
                            @csrf
                            <button type="submit" class="btn {{ $aAllowDl ? 'btn-warning' : 'btn-info' }} btn-sm" style="width:100%;justify-content:center">
                                {{ $aAllowDl ? '🔒 Tutup Akses Download' : '🔓 Buka Akses Download Siswa' }}
                            </button>
                        </form>
                    </div>

                    {{-- Perpanjang / Buka Ulang Deadline --}}
                    <div style="background:var(--bg-body);border-radius:12px;border:1px solid var(--border);padding:18px">
                        <div style="font-weight:700;font-size:14px;margin-bottom:6px">📅 Perpanjang Deadline</div>
                        <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px">
                            Buka kembali pengumpulan dengan deadline baru. Deadline saat ini: <strong>{{ $a->deadline->translatedFormat('d M Y, H:i') }}</strong>
                        </p>
                        <form method="POST" action="{{ route('assignment.reopen', $a) }}">
                            @csrf
                            <input type="datetime-local" name="deadline" class="inp" style="margin-bottom:8px" required min="{{ now()->format('Y-m-d\TH:i') }}">
                            <label style="display:flex;align-items:center;gap:7px;font-size:12px;font-weight:600;margin-bottom:12px;cursor:pointer">
                                <input type="checkbox" name="allow_resubmit" value="1" style="accent-color:var(--primary)">
                                Izinkan siswa yang sudah submit untuk submit ulang
                            </label>
                            <button type="submit" class="btn btn-info btn-sm" style="width:100%;justify-content:center">
                                Buka Ulang Pengumpulan
                            </button>
                        </form>
                    </div>

                    {{-- Tugas Lanjutan --}}
                    <div style="background:var(--bg-body);border-radius:12px;border:1px solid var(--border);padding:18px">
                        <div style="font-weight:700;font-size:14px;margin-bottom:6px">⚡ Buat Tugas Lanjutan</div>
                        <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px">
                            Buat tugas pertemuan berikutnya dalam satu rangkaian materi. Tugas baru otomatis <strong>tertutup</strong> — buka akses manual saat siap.
                        </p>
                        <form method="POST" action="{{ route('assignment.continue', $a) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="field" style="margin-bottom:8px">
                                <label class="field-label">Judul Tugas Lanjutan *</label>
                                <input name="title" type="text" class="inp" required placeholder="Contoh: Laporan Praktikum Pertemuan 2">
                            </div>
                            <div class="field" style="margin-bottom:8px">
                                <label class="field-label">Deadline *</label>
                                <input name="deadline" type="datetime-local" class="inp" required min="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="field" style="margin-bottom:8px">
                                <label class="field-label">Keterangan</label>
                                <textarea name="description" class="inp" rows="2" placeholder="Instruksi..."></textarea>
                            </div>
                            <div class="field" style="margin-bottom:12px">
                                <label class="field-label">Soal / Lampiran (opsional)</label>
                                <input name="attachment" type="file" class="inp">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center">
                                Buat Tugas Lanjutan
                            </button>
                        </form>
                    </div>

                    {{-- Hapus Tugas --}}
                    <div style="background:#fff5f5;border-radius:12px;border:1px solid #fecaca;padding:18px">
                        <div style="font-weight:700;font-size:14px;color:var(--danger);margin-bottom:6px">🗑️ Hapus Tugas</div>
                        <p style="font-size:12px;color:var(--text-muted);margin-bottom:14px">
                            Menghapus tugas dan <strong>semua {{ $aSubs->count() }} file pengumpulan</strong> secara permanen. Tindakan ini tidak bisa dibatalkan.
                        </p>
                        @if($siblings->count() > 1)
                        <div style="font-size:11px;color:var(--warning);background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:8px 10px;margin-bottom:12px">
                            ⚠️ Tugas ini bagian dari rangkaian {{ $siblings->count() }} pertemuan. Hanya pertemuan ini yang dihapus.
                        </div>
                        @endif
                        <form method="POST" action="{{ route('assignment.destroy', $a) }}" onsubmit="return confirm('Hapus tugas ini beserta {{ $aSubs->count() }} file pengumpulan? Tidak bisa dibatalkan.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center">Hapus Permanen</button>
                        </form>
                    </div>

                </div>

                {{-- Series navigator --}}
                @if($siblings->count() > 1)
                <div style="padding:0 24px 20px">
                    <div style="background:var(--bg-body);border-radius:12px;border:1px solid var(--border);padding:16px">
                        <div style="font-size:12px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px">Rangkaian Materi</div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px">
                            @foreach($siblings as $sib)
                            <div style="display:flex;align-items:center;gap:6px;padding:7px 12px;border-radius:8px;border:1px solid {{ $sib->id === $a->id ? 'var(--primary)' : 'var(--border)' }};background:{{ $sib->id === $a->id ? 'var(--primary-light)' : '#fff' }};font-size:12px;font-weight:700">
                                <span style="color:{{ $sib->id === $a->id ? 'var(--primary)' : 'var(--text-muted)' }}">P{{ $sib->session_number }}</span>
                                <span style="color:{{ $sib->id === $a->id ? 'var(--primary-dark)' : 'var(--text-main)' }}">{{ Str::limit($sib->title, 22) }}</span>
                                @if(!$sib->is_active)
                                <span class="chip chip-gray" style="font-size:9px">Tertutup</span>
                                @elseif($sib->deadline->isPast())
                                <span class="chip chip-orange" style="font-size:9px">Arsip</span>
                                @else
                                <span class="chip chip-green" style="font-size:9px">Aktif</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- ─── TAB 3: EDIT TUGAS ─── --}}
            <div id="tab-edit-{{ $a->id }}" class="tab-pane" style="display:none">
                <form method="POST" action="{{ route('assignment.update', $a) }}" enctype="multipart/form-data" style="padding:20px 24px">
                    @csrf @method('PATCH')

                    <div class="field">
                        <label class="field-label">Judul Tugas *</label>
                        <input name="title" type="text" class="inp" required value="{{ old('title', $a->title) }}">
                    </div>

                    <div class="field-row-2">
                        <div class="field">
                            <label class="field-label">Mata Pelajaran *</label>
                            <input name="subject_name" type="text" class="inp" required value="{{ old('subject_name', $a->subject_name) }}">
                        </div>
                        <div class="field">
                            <label class="field-label">Deadline *</label>
                            <input name="deadline" type="datetime-local" class="inp" required value="{{ old('deadline', $a->deadline->format('Y-m-d\TH:i')) }}">
                            <span class="field-hint">Mengubah deadline ke masa depan akan otomatis membuka akses tugas.</span>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">Keterangan</label>
                        <textarea name="description" class="inp" rows="3">{{ old('description', $a->description) }}</textarea>
                    </div>

                    <div class="field">
                        <label class="field-label">Ganti Soal / Lampiran</label>
                        @if($a->attachment_name)
                        <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:var(--bg-body);border-radius:8px;border:1px solid var(--border);margin-bottom:8px">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:var(--primary)"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L20.5 13"/></svg>
                            <span style="font-size:12px;font-weight:600">{{ $a->attachment_name }}</span>
                            <span style="font-size:11px;color:var(--text-muted)">({{ $a->attachment_size }})</span>
                        </div>
                        @endif
                        <input name="attachment" type="file" class="inp">
                        <span class="field-hint">Kosongkan jika tidak ingin mengganti file soal.</span>
                    </div>

                    <div style="display:flex;gap:10px;margin-top:6px">
                        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="switchTab(document.querySelector('#tabs-{{ $a->id }} .modal-tab'), 'tab-subs-{{ $a->id }}', 'tabs-{{ $a->id }}')">Batal</button>
                    </div>
                </form>
            </div>

        </div>{{-- /modal-body --}}
    </div>{{-- /modal-box --}}
</div>{{-- /modal-overlay --}}
@endforeach

<script>
const CLASSES = @json($classes->groupBy('organization_id'));

/* ── Modal helpers ── */
function openModal(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
}

// Tutup saat klik backdrop
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('open');
        document.body.style.overflow = '';
    }
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(m => {
            m.classList.remove('open');
        });
        document.body.style.overflow = '';
    }
});

/* ── Tab switcher ── */
function switchTab(clickedBtn, targetId, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Deactivate all tabs & panes
    container.closest('.modal-box').querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
    container.querySelectorAll('.tab-pane').forEach(p => {
        p.style.display = 'none';
        p.classList.remove('active');
    });

    // Activate clicked
    clickedBtn.classList.add('active');
    const pane = document.getElementById(targetId);
    if (pane) { pane.style.display = 'block'; pane.classList.add('active'); }
}

// Bind tab clicks on load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal-tab').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('.modal-box');
            const tabs      = container.querySelectorAll('.modal-tab');
            const panes     = container.querySelectorAll('.tab-pane');
            const idx       = Array.from(tabs).indexOf(this);

            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => { p.style.display = 'none'; p.classList.remove('active'); });

            this.classList.add('active');
            if (panes[idx]) { panes[idx].style.display = 'block'; panes[idx].classList.add('active'); }
        });
    });
});

/* ── Kelas loader ── */
function loadKelas(orgId, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';
    if (orgId && CLASSES[orgId]) {
        CLASSES[orgId].forEach(k => {
            container.innerHTML += `
                <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:12px;font-weight:700;padding:4px 0">
                    <input type="checkbox" name="class_names[]" value="${k.name}" style="accent-color:var(--primary);width:14px;height:14px">
                    ${k.name}
                </label>
            `;
        });
    } else {
        container.innerHTML = '<span style="font-size:12px;color:var(--text-muted);grid-column:1/-1">Pilih lembaga dulu...</span>';
    }
}

/* ── Sidebar mobile toggle ── */
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
}

/* ── Auto-dismiss flash ── */
document.querySelectorAll('.flash').forEach(function(el) {
    setTimeout(function() {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 420);
    }, 5000);
});

/* ── Realtime: Reverb WebSocket ── */
(function initRealtime() {
    if (!window.Echo) return;

    // Subscribe ke setiap channel tugas yang tampil di halaman ini
    const assignmentIds = @json($assignments->pluck('id'));

    assignmentIds.forEach(function(aId) {
        window.Echo.channel('assignments.' + aId)

            // Siswa baru kumpul → update counter + tambah baris ke tabel
            .listen('.submission.created', function(e) {
                // Update counter di kartu tugas
                updateSubmissionCount(aId, 1);

                // Jika modal tugas ini sedang terbuka, tambah baris baru
                const modal = document.getElementById('modal-subs-' + aId);
                if (modal && modal.classList.contains('open')) {
                    addSubmissionRow(aId, e);
                }

                // Toast notifikasi
                if (window.showGlobalNotification) {
                    window.showGlobalNotification(
                        '📥 ' + e.student_name + ' mengumpulkan tugas (' + e.student_class + ')',
                        'ok'
                    );
                }
            })

            // Guru update tugas (nilai, akses berubah, dll) → refresh halaman setelah jeda
            .listen('.assignment.updated', function(e) {
                if (e.action === 'graded') {
                    // Update badge nilai di baris siswa jika modal terbuka
                    updateGradeBadge(aId, e.data);
                }
                // Untuk aksi lain (updated, access_changed) — schedule soft reload
                if (['updated', 'access_changed'].includes(e.action)) {
                    scheduleReload();
                }
            });
    });

    function updateSubmissionCount(aId, delta) {
        // Cari elemen progress label di kartu tugas
        const card = document.querySelector(`[onclick="openModal('modal-subs-${aId}')"]`);
        if (!card) return;
        const label = card.closest('.task-card')?.querySelector('.progress-label span:last-child');
        if (!label) return;
        const current = parseInt(label.textContent) || 0;
        label.textContent = (current + delta) + ' SISWA';
    }

    function addSubmissionRow(aId, e) {
        const tbody = document.querySelector('#modal-subs-' + aId + ' .adm-table tbody');
        if (!tbody) return;

        // Hapus empty state kalau ada
        const emptyState = document.querySelector('#modal-subs-' + aId + ' .empty-state');
        if (emptyState) emptyState.remove();

        const tr = document.createElement('tr');
        tr.dataset.submissionId = e.submission_id;
        tr.style.animation = 'highlightNew .8s ease';
        tr.innerHTML = `
            <td>
                <div style="font-weight:700">${escHtml(e.student_name)}</div>
                <div style="font-size:11px;color:var(--text-muted)">${escHtml(e.student_class)}</div>
                <span class="chip chip-orange" style="margin-top:3px">Menunggu</span>
            </td>
            <td style="font-size:12px;color:var(--text-muted)">${escHtml(e.submitted_at)}</td>
            <td>
                <span class="chip chip-gray">${escHtml((e.file_ext || '').toUpperCase() || 'File')}</span>
                <div style="font-size:10px;color:var(--text-muted);margin-top:2px">${escHtml(e.file_size || '')}</div>
            </td>
            <td><span style="font-size:12px;color:var(--text-muted)">—</span></td>
            <td><span style="font-size:11px;color:var(--text-muted)">Muat ulang untuk aksi</span></td>
        `;
        tbody.prepend(tr);
    }

    function updateGradeBadge(aId, data) {
        if (!data || !data.submission_id) return;
        const row = document.querySelector(`[data-submission-id="${data.submission_id}"]`);
        if (!row) return;
        const chip = row.querySelector('.chip-orange');
        if (chip) {
            chip.className = 'chip chip-green';
            chip.textContent = 'Dinilai';
        }
    }

    let _reloadTimer = null;
    function scheduleReload() {
        clearTimeout(_reloadTimer);
        _reloadTimer = setTimeout(function() {
            window.location.reload();
        }, 2000);
    }

    function escHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
})();
</script>
</body>
</html>
