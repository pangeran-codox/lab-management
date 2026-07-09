<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Keuangan Tim')</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --g1: #071f14;
            --g2: #0d3322;
            --g3: #134d32;
            --accent: #00e87a;
            --accent2: #00c468;
            --accent-glow: rgba(0,232,122,0.15);
            --red: #ff4d6d;
            --red-dim: rgba(255,77,109,0.1);
            --amber: #ffb703;
            --surface: #ffffff;
            --bg: #f0ede8;
            --border: #e2ddd8;
            --text: #16140f;
            --muted: #706b64;
            --sidebar-w: 256px;
            --radius: 14px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.04);
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ═══════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 300;
            display: flex;
            flex-direction: column;
            background: var(--g1);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        /* Subtle noise texture overlay */
        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            opacity: 0.4;
        }

        /* Glow blobs */
        .sidebar-blob1 {
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,232,122,0.12) 0%, transparent 70%);
            top: -60px; right: -60px;
            pointer-events: none;
        }
        .sidebar-blob2 {
            position: absolute;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,232,122,0.06) 0%, transparent 70%);
            bottom: 80px; left: -40px;
            pointer-events: none;
        }

        .sidebar-scroll {
            display: flex; flex-direction: column;
            height: 100%; overflow-y: auto;
            scrollbar-width: none;
            position: relative; z-index: 1;
        }
        .sidebar-scroll::-webkit-scrollbar { display: none; }

        /* Brand */
        .brand {
            padding: 22px 18px 18px;
            display: flex; align-items: center; gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .brand-mark {
            width: 38px; height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.1), 0 4px 14px rgba(0,232,122,0.3);
            position: relative;
        }
        .brand-mark svg { width: 20px; height: 20px; color: var(--g1); }
        .brand-name { font-size: 13.5px; font-weight: 700; color: white; letter-spacing: -0.3px; }
        .brand-role { font-size: 10px; color: rgba(255,255,255,0.3); margin-top: 1px; letter-spacing: 0.3px; }

        /* Nav */
        .nav { padding: 14px 10px; flex: 1; }
        .nav-label {
            font-size: 9px; font-weight: 700;
            color: rgba(255,255,255,0.2);
            text-transform: uppercase; letter-spacing: 1.5px;
            padding: 10px 10px 5px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 11px; border-radius: 10px;
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
            margin-bottom: 2px;
            position: relative;
        }
        .nav-item svg { width: 15px; height: 15px; flex-shrink: 0; transition: transform 0.2s; }
        .nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.85);
            transform: translateX(2px);
        }
        .nav-item:hover svg { transform: scale(1.1); }
        .nav-item.active {
            background: rgba(0,232,122,0.1);
            color: var(--accent);
            font-weight: 600;
        }
        .nav-item.active::after {
            content: '';
            position: absolute;
            right: 10px; top: 50%;
            transform: translateY(-50%);
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
        }

        /* User Footer */
        .sidebar-foot {
            padding: 12px 10px 14px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .user-pill {
            display: flex; align-items: center; gap: 9px;
            padding: 9px 10px; border-radius: 10px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 6px;
        }
        .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: var(--g1);
            flex-shrink: 0;
            box-shadow: 0 0 0 2px rgba(0,232,122,0.3);
        }
        .user-nm { font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.85); }
        .user-em { font-size: 10px; color: rgba(255,255,255,0.3); margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px; }
        .btn-logout {
            width: 100%; display: flex; align-items: center; gap: 8px;
            padding: 8px 11px; border-radius: 10px;
            font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 500;
            color: rgba(255,255,255,0.3); background: none; border: none; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-logout svg { width: 13px; height: 13px; }
        .btn-logout:hover { background: rgba(255,77,109,0.1); color: #fca5a5; }

        /* ═══════════════════════════════════════
           MAIN
        ═══════════════════════════════════════ */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1; display: flex; flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.35s cubic-bezier(0.4,0,0.2,1);
        }

        .topbar {
            height: 58px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 26px; gap: 14px;
            position: sticky; top: 0; z-index: 100;
        }
        .menu-btn {
            display: none; background: none; border: none;
            cursor: pointer; color: var(--muted); padding: 5px;
            border-radius: 8px; transition: background 0.15s;
        }
        .menu-btn:hover { background: var(--border); }
        .menu-btn svg { width: 20px; height: 20px; display: block; }
        .topbar-title { font-size: 15px; font-weight: 700; flex: 1; color: var(--text); }
        .topbar-date {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px; color: var(--muted);
            background: var(--bg); padding: 5px 11px;
            border-radius: 7px; border: 1px solid var(--border);
        }

        .content { padding: 22px 26px; flex: 1; }

        /* Flash */
        .flash {
            padding: 12px 15px; border-radius: 10px;
            font-size: 13px; margin-bottom: 18px;
            display: flex; align-items: center; gap: 9px;
            animation: slideIn 0.35s cubic-bezier(0.4,0,0.2,1);
        }
        .flash svg { width: 15px; height: 15px; flex-shrink: 0; }
        .flash-success { background: #f0fdf4; border: 1px solid #86efac; color: #15803d; }
        .flash-error   { background: #fff1f2; border: 1px solid #fda4af; color: #be123c; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Overlay */
        .overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(7,31,20,0.6);
            backdrop-filter: blur(3px);
            z-index: 250; opacity: 0;
            transition: opacity 0.35s;
        }
        .overlay.show { opacity: 1; pointer-events: all; }

        /* ═══════════════════════════════════════
           SHARED COMPONENTS
        ═══════════════════════════════════════ */
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .card-head {
            padding: 15px 18px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 13px; font-weight: 700; color: var(--text); }
        .card-body  { padding: 18px; }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 9px;
            font-family: 'Sora', sans-serif; font-size: 12.5px; font-weight: 600;
            cursor: pointer; border: none; text-decoration: none;
            transition: all 0.2s; white-space: nowrap;
        }
        .btn svg { width: 13px; height: 13px; }
        .btn-primary {
            background: var(--g1); color: white;
            box-shadow: 0 2px 8px rgba(7,31,20,0.2);
        }
        .btn-primary:hover {
            background: var(--g2);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(7,31,20,0.25);
        }
        .btn-secondary {
            background: var(--bg); color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: var(--border); }
        .btn-danger { background: var(--red-dim); color: var(--red); border: 1px solid rgba(255,77,109,0.2); }
        .btn-danger:hover { background: rgba(255,77,109,0.15); }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { border-bottom: 2px solid var(--border); }
        thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.8px; }
        tbody tr { border-bottom: 1px solid var(--border); transition: background 0.12s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--bg); }
        tbody td { padding: 11px 14px; }

        /* Badge */
        .badge { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 10.5px; font-weight: 700; letter-spacing: 0.2px; }
        .badge-income    { background: #dcfce7; color: #16a34a; }
        .badge-expense   { background: #fff1f2; color: #be123c; }
        .badge-admin     { background: #ede9fe; color: #7c3aed; }
        .badge-bendahara { background: #dbeafe; color: #1d4ed8; }

        /* Form */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 11px; font-weight: 700;
            color: var(--text); text-transform: uppercase;
            letter-spacing: 0.6px; margin-bottom: 6px;
        }
        .form-control {
            width: 100%; padding: 9px 12px;
            border: 1.5px solid var(--border); border-radius: 9px;
            font-family: 'Sora', sans-serif; font-size: 13px;
            color: var(--text); background: white; outline: none;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0,232,122,0.1);
        }
        .form-control.is-invalid { border-color: var(--red); }
        .invalid-feedback { font-size: 12px; color: var(--red); margin-top: 4px; }
        .form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-hint { font-size: 11px; color: var(--muted); margin-top: 4px; }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .overlay { display: block; pointer-events: none; }
            .main { margin-left: 0; }
            .menu-btn { display: block; }
            .content { padding: 14px 16px; }
            .topbar { padding: 0 16px; }
            .topbar-date { display: none; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-blob1"></div>
    <div class="sidebar-blob2"></div>
    <div class="sidebar-scroll">
        <div class="brand">
            <div class="brand-mark">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="brand-name">Keuangan Tim</div>
                <div class="brand-role">{{ auth('finance')->user()->role_label }}</div>
            </div>
        </div>

        <nav class="nav">
            <a href="{{ route('finance.dashboard') }}" class="nav-item {{ request()->routeIs('finance.dashboard*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM14 11a1 1 0 011-1h4a1 1 0 011 1v8a1 1 0 01-1 1h-4a1 1 0 01-1-1v-8z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('finance.transactions.index') }}" class="nav-item {{ request()->routeIs('finance.transactions*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Transaksi
            </a>
            <a href="{{ route('finance.budgets.index') }}" class="nav-item {{ request()->routeIs('finance.budgets*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                Anggaran
            </a>
            <a href="{{ route('finance.laporan.index') }}" class="nav-item {{ request()->routeIs('finance.laporan*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Bulanan
            </a>

            @if(auth('finance')->user()->isAdmin())
                <div class="nav-label">Admin</div>
                <a href="{{ route('finance.users.index') }}" class="nav-item {{ request()->routeIs('finance.users*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kelola User
                </a>
                <a href="{{ route('finance.wa-settings.index') }}" class="nav-item {{ request()->routeIs('finance.wa-settings*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Pengaturan WA
                </a>
            @endif
        </nav>

        <div class="sidebar-foot">
            <div class="user-pill">
                <div class="avatar">{{ strtoupper(substr(auth('finance')->user()->name, 0, 1)) }}</div>
                <div style="min-width:0">
                    <div class="user-nm">{{ auth('finance')->user()->name }}</div>
                    <div class="user-em">{{ auth('finance')->user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('finance.logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="main" id="mainWrap">
    <div class="topbar">
        <button class="menu-btn" onclick="toggleSidebar()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        <span class="topbar-date" id="liveClock">{{ now()->isoFormat('ddd, D MMM Y') }}</span>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="flash flash-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
}
// Live clock
(function tick() {
    const el = document.getElementById('liveClock');
    if (el) {
        const now = new Date();
        const days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        const hh = String(now.getHours()).padStart(2,'0');
        const mm = String(now.getMinutes()).padStart(2,'0');
        const ss = String(now.getSeconds()).padStart(2,'0');
        el.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} — ${hh}:${mm}:${ss}`;
    }
    setTimeout(tick, 1000);
})();
</script>
@stack('scripts')
</body>
</html>