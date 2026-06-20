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
    /* ── Reset & Variables ── */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        --primary: #00693E;
        --primary-dark: #003d24;
        --primary-light: #eaf4f0;
        --accent: #B9D9EB;
        --danger: #c0392b;
        --warning: #d97706;
        --success: #16a34a;
        --text-main: #0d2416;
        --text-muted: #6b8fa3;
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --radius-xl: 20px;
        --radius-lg: 16px;
        --radius-md: 12px;
        --font-head: 'Outfit', sans-serif;
        --font-main: 'DM Sans', sans-serif;
    }

    body {
        font-family: var(--font-main);
        background: var(--bg-body);
        color: var(--text-main);
        min-height: 100vh;
    }

    /* ── Layout ── */
    .app-shell {
        display: flex;
        min-height: 100vh;
        width: 100%;
    }

    .sidebar {
        width: 280px;
        background: #fff;
        border-right: 1px solid var(--border-color);
        padding: 24px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 50;
        display: flex;
        flex-direction: column;
    }

    .main-content {
        flex: 1;
        padding: 40px;
        margin-left: 280px; /* Lebar sidebar */
        width: calc(100% - 280px);
        min-height: 100vh;
    }

    @media (max-width: 1024px) {
        .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
        .sidebar.show { transform: translateX(0); }
        .main-content { margin-left: 0; width: 100%; padding: 20px; }
    }

    /* ── Sidebar Elements ── */
    .sb-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 40px;
    }
    .sb-logo {
        width: 40px;
        height: 40px;
        background: var(--primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }
    .sb-menu { list-style: none; }
    .sb-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        color: var(--text-muted);
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .sb-item:hover { background: var(--primary-light); color: var(--primary); }
    .sb-item.active { background: var(--primary); color: #fff; }

    /* ── Dashboard Header ── */
    .dash-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }
    .dash-title h1 { font-family: var(--font-head); font-weight: 800; font-size: 24px; margin: 0; }
    .dash-title p { color: var(--text-muted); font-size: 14px; margin-top: 4px; }

    /* ── Stats Overview ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    .stat-card {
        background: #fff;
        padding: 24px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .stat-label { font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-family: var(--font-head); font-size: 28px; font-weight: 800; color: var(--text-main); }
    .stat-trend { font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 6px; width: fit-content; }
    .trend-up { background: #dcfce7; color: #166534; }

    /* ── Task Grid ── */
    .task-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 24px;
    }
    .task-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .task-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.05);
        border-color: var(--primary);
    }
    .task-badge {
        position: absolute;
        top: 24px;
        right: 24px;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-expired { background: #fee2e2; color: #991b1b; }

    .task-subject { font-size: 12px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 8px; }
    .task-title { font-family: var(--font-head); font-size: 18px; font-weight: 800; margin-bottom: 12px; line-height: 1.3; }
    
    .task-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
    }
    .meta-row { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); font-weight: 600; }
    .meta-row svg { width: 14px; opacity: 0.6; }

    .task-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }
    .task-progress { flex: 1; margin-right: 16px; }
    .progress-bar { height: 6px; background: #f1f5f9; border-radius: 99px; overflow: hidden; margin-top: 6px; }
    .progress-fill { height: 100%; background: var(--primary); border-radius: 99px; }

    .btn-manage {
        background: var(--primary);
        color: #fff;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-manage:hover { background: var(--primary-dark); transform: scale(1.02); }

    /* ── Modal ── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(13, 36, 22, 0.4);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }
    .modal-content {
        background: #fff;
        width: 100%;
        max-width: 1000px;
        max-height: 90vh;
        border-radius: var(--radius-xl);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        animation: modalFadeUp 0.3s ease-out;
    }
    @keyframes modalFadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }

    .modal-header { padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #fff; }
    .modal-body { padding: 0; overflow-y: auto; flex: 1; }
    
    .table-container { width: 100%; overflow-x: auto; }
    .modern-table { width: 100%; border-collapse: collapse; min-width: 800px; }
    .modern-table th { padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; background: #f8fafc; border-bottom: 2px solid var(--border-color); }
    .modern-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .modern-table tr:hover { background: #f8fafc; }

    /* ── Utils ── */
    .btn-create-large {
        background: var(--primary);
        color: #fff;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 105, 62, 0.2);
    }

    .btn-create-large:hover { background: var(--primary-dark); transform: translateY(-2px); }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .empty-icon { font-size: 48px; margin-bottom: 16px; opacity: 0.5; }

    /* ── Mobile Toggle ── */
    .mobile-nav {
        display: none;
        background: #fff;
        padding: 12px 20px;
        border-bottom: 1px solid var(--border-color);
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 60;
    }

    /* ── Form Fields ── */
    .field {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 16px;
    }
    .field-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
    }
    .inp {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        font-family: var(--font-main);
        font-size: 14px;
        transition: all 0.2s;
        background: #fff;
    }
    .inp:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 105, 62, 0.1);
    }
    .field-row-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    @media (max-width: 1024px) {
        .field-row-3 { grid-template-columns: 1fr; }
        .mobile-nav { display: flex; }
    }
</style>
</head>
<body>

<div class="mobile-nav">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div class="sb-logo" style="width: 32px; height: 32px;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <span style="font-weight: 800; font-size: 14px;">EduZone Lab</span>
    </div>
    <button onclick="toggleSidebar()" style="background: var(--primary-light); color: var(--primary); border: none; padding: 8px; border-radius: 8px; cursor: pointer;">
        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
    </button>
</div>

<div class="app-shell">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sb-brand">
            <div class="sb-logo">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 16px;">EduZone Lab</div>
                <div style="font-size: 11px; color: var(--text-muted);">Panel Manajemen Guru</div>
            </div>
        </div>

        <nav class="sb-menu">
            <div class="sb-item active">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard Tugas
            </div>
            <div class="sb-item" onclick="location.href='{{ route('assignment.public') }}'">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Lihat Halaman Publik
            </div>
            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'staff', 'technician']))
            <div class="sb-item" onclick="location.href='{{ route('dashboard') }}'">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                Kembali ke Dashboard
            </div>
            @endif
        </nav>

        <div style="margin-top: auto; padding-top: 24px; border-top: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary); font-weight: 800; font-size: 12px;">{{ substr($teacher->name ?? 'G', 0, 1) }}</div>
                <div>
                    <div style="font-weight: 700; font-size: 13px;">{{ $teacher->name ?? 'Guru' }}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">{{ $teacher->token ?? '-' }}</div>
                </div>
            </div>
            <a href="{{ route('assignment.admin.logout') }}" style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 10px; color: var(--text-muted); font-size: 13px; font-weight: 700; transition: all 0.2s; background: rgba(0, 105, 62, 0.05);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="dash-header">
            <div class="dash-title">
                <h1>Halo, {{ explode(' ', $teacher->name ?? 'Guru')[0] }}! 👋</h1>
                <p>Pantau dan kelola pengumpulan tugas siswa Anda di sini.</p>
            </div>
            <button class="btn-create-large" onclick="openCreateModal()">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tugas Baru
            </button>
        </header>

        <!-- STATS -->
        <section class="stats-grid">
            <div class="stat-card">
                <span class="stat-label">Total Tugas</span>
                <span class="stat-value">{{ $assignments->count() }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Menunggu Dinilai</span>
                <span class="stat-value" style="color: var(--warning)">{{ $assignments->sum(fn($a) => $a->submissions->where('status','submitted')->count()) }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Sudah Dinilai</span>
                <span class="stat-value" style="color: var(--success)">{{ $assignments->sum(fn($a) => $a->submissions->where('status','graded')->count()) }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Aktif Saat Ini</span>
                <span class="stat-value">{{ $assignments->filter(fn($a) => !$a->isExpired())->count() }}</span>
            </div>
        </section>

        <!-- TASK GRID -->
        <section class="task-grid">
            @forelse($assignments as $a)
            @php
                $expired = $a->isExpired();
                $subs = $a->submissions;
                $total = $subs->count();
                $graded = $subs->where('status','graded')->count();
                $progress = $total > 0 ? min(($total / 36) * 100, 100) : 0;
            @endphp
            <article class="task-card">
                <span class="task-badge {{ $expired ? 'badge-expired' : 'badge-active' }}">
                    {{ $expired ? 'Arsip' : 'Aktif' }}
                </span>
                
                <div class="task-subject">{{ $a->subject_name }}</div>
                <h3 class="task-title">{{ $a->title }}</h3>

                <div class="task-meta">
                    <div class="meta-row">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Kelas: {{ $a->class_name }}
                    </div>
                    <div class="meta-row">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Deadline: {{ $a->deadline->translatedFormat('d M, H:i') }}
                    </div>
                </div>

                <div class="task-footer">
                    <div class="task-progress">
                        <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 800; color: var(--text-muted);">
                            <span>PENGUMPULAN</span>
                            <span>{{ $total }} SISWA</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                    <button class="btn-manage" onclick="openSubmissionsModal('{{ $a->id }}')">Kelola</button>
                </div>
            </article>
            @empty
            <div class="empty-state" style="grid-column: 1/-1;">
                <div class="empty-icon">📋</div>
                <h3>Belum ada tugas</h3>
                <p>Mulai dengan membuat tugas baru untuk kelas Anda.</p>
            </div>
            @endforelse
        </section>
    </main>
</div>

<!-- MODAL: CREATE TASK -->
<div id="modal-create" class="modal-overlay">
    <div class="modal-content" style="max-width: 650px;">
        <div class="modal-header">
            <h2 style="font-family: var(--font-head); font-weight: 800; font-size: 20px;">Buat Tugas Baru</h2>
            <button onclick="closeCreateModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted);"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="modal-body" style="padding: 32px;">
            <form method="POST" action="{{ route('assignment.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="teacher_token" value="{{ $teacher->token ?? '' }}">
                
                <div class="field">
                    <label class="field-label">Judul Tugas</label>
                    <input name="title" type="text" class="inp" required placeholder="Contoh: Laporan Praktikum Jaringan">
                </div>

                <div class="field-row-3">
                    <div class="field">
                        <label class="field-label">Lembaga</label>
                        <select name="organization_id" class="inp" required onchange="loadAdmKelas(this.value)">
                            <option value="">Pilih Lembaga</option>
                            @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" style="grid-column: span 2">
                        <label class="field-label">Pilih Kelas</label>
                        <div id="kelas_container" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(100px, 1fr));gap:8px;padding:12px;background:var(--bg-body);border-radius:12px;border:1.5px solid var(--border-color);max-height:120px;overflow-y:auto">
                            <span style="font-size:12px;color:var(--text-muted);grid-column:1/-1">Pilih lembaga dulu...</span>
                        </div>
                    </div>
                </div>

                <div class="field-row-3">
                    <div class="field">
                        <label class="field-label">Mata Pelajaran</label>
                        <input name="subject_name" type="text" class="inp" required placeholder="Produktif TKJ">
                    </div>
                    <div class="field" style="grid-column: span 2">
                        <label class="field-label">Deadline</label>
                        <input name="deadline" type="datetime-local" class="inp" required>
                    </div>
                </div>

                <div class="field">
                    <label class="field-label">Keterangan / Lampiran</label>
                    <textarea name="description" class="inp" rows="2" placeholder="Instruksi tambahan..."></textarea>
                    <input name="attachment" type="file" class="inp" style="margin-top: 8px;">
                </div>

                <div style="margin-top: 24px; display: flex; gap: 12px;">
                    <button type="submit" class="btn-create-large" style="flex: 1; justify-content: center;">Terbitkan Tugas</button>
                    <button type="button" onclick="closeCreateModal()" class="btn-manage" style="background: var(--bg-body); color: var(--text-main);">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: SUBMISSIONS -->
@foreach($assignments as $a)
<div id="modal-subs-{{ $a->id }}" class="modal-overlay assignment-modal">
    <div class="modal-content">
        <div class="modal-header">
            <div>
                <h2 style="font-family: var(--font-head); font-weight: 800; font-size: 20px;">{{ $a->title }}</h2>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">{{ $a->subject_name }} • {{ $a->class_name }}</p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <form method="POST" action="{{ route('assignment.destroy', $a) }}" onsubmit="return confirm('Hapus tugas ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--danger);"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </form>
                <button onclick="closeSubmissionsModal('{{ $a->id }}')" style="background: none; border: none; cursor: pointer; color: var(--text-muted);"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        </div>
        <div class="modal-body">
            @if($a->submissions->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>Belum ada pengumpulan.</p>
            </div>
            @else
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Waktu</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($a->submissions->sortByDesc('submitted_at') as $sub)
                        <tr>
                            <td>
                                <div style="font-weight: 700;">{{ $sub->student_name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $sub->student_class }}</div>
                            </td>
                            <td>{{ $sub->submitted_at->translatedFormat('d M, H:i') }}</td>
                            <td>
                                <a href="{{ route('assignment.download', $sub) }}" style="display: flex; align-items: center; gap: 8px; color: var(--primary); font-weight: 700;">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh
                                </a>
                            </td>
                            <td>
                                <span class="badge-status {{ $sub->status === 'graded' ? 'badge-graded' : 'badge-submitted' }}" style="padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 800; {{ $sub->status === 'graded' ? 'background: #dcfce7; color: #166534;' : 'background: #fffbeb; color: #d97706;' }}">
                                    {{ $sub->status === 'graded' ? 'Dinilai' : 'Menunggu' }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('assignment.grade', $sub) }}" style="display: flex; gap: 8px;">
                                    @csrf
                                    <input type="number" name="grade" value="{{ $sub->grade }}" class="inp" style="width: 60px; padding: 6px;" min="0" max="100">
                                    <button type="submit" class="btn-manage" style="padding: 6px 12px;">Simpan</button>
                                </form>
                            </td>
                            <td>
                                <!-- Additional actions if needed -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endforeach

<script>
    const CLASSES = @json($classes->groupBy('organization_id'));

    function loadAdmKelas(orgId) {
        const container = document.getElementById('kelas_container');
        container.innerHTML = '';
        if (orgId && CLASSES[orgId]) {
            CLASSES[orgId].forEach(k => {
                container.innerHTML += `
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:12px; font-weight:700;">
                        <input type="checkbox" name="class_names[]" value="${k.name}" style="accent-color: var(--primary)">
                        ${k.name}
                    </label>
                `;
            });
        } else {
            container.innerHTML = '<span style="font-size:12px;color:var(--text-muted);grid-column:1/-1">Pilih lembaga dulu...</span>';
        }
    }

    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('show');
    }

    function openCreateModal() { document.getElementById('modal-create').style.display = 'flex'; }
    function closeCreateModal() { document.getElementById('modal-create').style.display = 'none'; }

    function openSubmissionsModal(id) { document.getElementById(`modal-subs-${id}`).style.display = 'flex'; }
    function closeSubmissionsModal(id) { document.getElementById(`modal-subs-${id}`).style.display = 'none'; }

    // Close modals on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCreateModal();
            document.querySelectorAll('.assignment-modal').forEach(m => m.style.display = 'none');
        }
    });

    // Close modal on outside click
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.style.display = 'none';
        }
    }
</script>

</body>
</html>