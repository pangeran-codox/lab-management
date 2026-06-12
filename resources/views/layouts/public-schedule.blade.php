<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lab Management') - Lab Management Nuris Jember</title>

    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --g9:    #003d24;
            --g8:    #00693E;
            --g7:    #00874f;
            --acc:   #B9D9EB;
            --acc2:  #8ec8e0;
            --white: #fff;
            --bg:    #f0f7fb;
            --border:#cce4f0;
            --text:  #0d2416;
            --muted: #6b8fa3;
            --sub:   #4a7a8a;
            --shadow: 0 2px 12px rgba(0,105,62,.08);
            --r: 14px;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ═══ NAVBAR ═══ */
        .pub-navbar {
            position: sticky; top: 0; z-index: 100;
            background: #fff;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 20px rgba(0,105,62,.07);
            animation: navSlideDown .4s cubic-bezier(.16,1,.3,1) both;
            width: 100%;
        }

        .pub-inner {
            max-width: 1280px; margin: 0 auto;
            padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            height: 62px; gap: 12px;
        }

        /* Brand */
        .pub-brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; flex-shrink: 0;
        }
        .pub-brand-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--g9), var(--g8));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,105,62,.25);
            transition: box-shadow .18s, transform .18s;
        }
        .pub-brand:hover .pub-brand-icon {
            box-shadow: 0 4px 14px rgba(0,105,62,.35);
            transform: translateY(-1px);
        }
        .pub-brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700; font-size: 14px;
            color: var(--text); line-height: 1.2;
        }
        .pub-brand-sub {
            font-size: 10px; color: var(--muted);
            font-weight: 500;
        }

        /* Divider */
        .pub-divider {
            width: 1px; height: 24px;
            background: var(--border);
            flex-shrink: 0;
        }

        /* Nav links */
        .pub-links {
            display: flex; align-items: center; gap: 2px;
            flex: 1;
            overflow-x: auto; -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .pub-links::-webkit-scrollbar { display: none; }

        .pub-link {
            font-size: 13px; font-weight: 600;
            color: var(--muted); text-decoration: none;
            padding: 7px 12px; border-radius: 9px;
            transition: color .15s, background .15s;
            white-space: nowrap; position: relative;
        }
        .pub-link:hover { color: var(--g8); background: rgba(0,105,62,.06); }
        .pub-link.on { color: var(--g8); }
        .pub-link.on::after {
            content: '';
            position: absolute; bottom: -1px; left: 12px; right: 12px;
            height: 2px; border-radius: 2px;
            background: var(--g8);
        }

        /* CTA Button */
        .pub-btn {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 700;
            padding: 8px 16px; border-radius: 10px;
            color: #fff;
            background: linear-gradient(135deg, var(--g9), var(--g8));
            border: none; text-decoration: none;
            box-shadow: 0 2px 10px rgba(0,105,62,.25);
            transition: box-shadow .15s, transform .15s, filter .15s;
            white-space: nowrap; flex-shrink: 0;
            cursor: pointer;
        }
        .pub-btn:hover {
            box-shadow: 0 4px 16px rgba(0,105,62,.35);
            transform: translateY(-1px);
            filter: brightness(1.06);
        }

        /* Right side actions */
        .pub-actions {
            display: flex; align-items: center; gap: 8px;
            flex-shrink: 0;
        }

        /* Mobile nav row 2 */
        .pub-nav-row2 { display: none; }

        footer {
            text-align: center; padding: 20px;
            font-size: 12px; color: var(--muted);
            border-top: 1px solid var(--border);
            margin-top: 2rem;
        }

        .page-trans {
            position: fixed; inset: 0; z-index: 9999;
            background: linear-gradient(135deg, var(--g9), var(--g8));
            opacity: 0; pointer-events: none; transition: opacity .22s ease;
        }
        .page-trans.go { opacity: 1; pointer-events: all; }

        @keyframes navSlideDown {
            from { transform: translateY(-64px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .pub-divider { display: none; }
        }

        @media (max-width: 640px) {
            .pub-inner { padding: 0 1rem; height: 56px; }
            .pub-brand-sub { display: none; }
            .pub-brand-name { font-size: 13px; }
            .pub-brand-icon { width: 32px; height: 32px; border-radius: 9px; }
        }

        @media (max-width: 600px) {
            .pub-link { display: none; }
            .pub-divider { display: none; }

            .pub-nav-row2 {
                display: flex; align-items: center; gap: 2px;
                padding: 0 12px 8px;
                overflow-x: auto; -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                border-top: 1px solid var(--border);
            }
            .pub-nav-row2::-webkit-scrollbar { display: none; }

            .pub-nav2-link {
                padding: 6px 12px; border-radius: 8px;
                font-size: 12px; font-weight: 600;
                color: var(--muted); text-decoration: none;
                white-space: nowrap;
                transition: color .15s, background .15s;
                position: relative;
            }
            .pub-nav2-link:hover { color: var(--g8); background: rgba(0,105,62,.06); }
            .pub-nav2-link.on { color: var(--g8); font-weight: 700; }
            .pub-nav2-link.on::after {
                content: '';
                position: absolute; bottom: -1px; left: 12px; right: 12px;
                height: 2px; border-radius: 2px;
                background: var(--g8);
            }
        }
    </style>

    @yield('vite')
</head>
<body>

{{-- ═══ NAVBAR ═══ --}}
<nav class="pub-navbar">
    <div class="pub-inner">

        {{-- Brand --}}
        <a href="{{ route('home') }}" class="pub-brand">
            <div class="pub-brand-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#B9D9EB" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="pub-brand-name">Lab Management</div>
                <div class="pub-brand-sub">Nuris Jember</div>
            </div>
        </a>

        <div class="pub-divider"></div>

        {{-- Nav links --}}
        <div class="pub-links">
            <a href="{{ route('home') }}"
               class="pub-link {{ request()->routeIs('home') ? 'on' : '' }}">Jadwal</a>
            <a href="{{ route('inventory.public') }}"
               class="pub-link {{ request()->routeIs('inventory.public') ? 'on' : '' }}">Inventaris</a>
            <a href="{{ route('rekap.public') }}"
               class="pub-link {{ request()->routeIs('rekap.public') ? 'on' : '' }}">Rekap</a>
            <a href="{{ route('assignment.public') }}"
               class="pub-link {{ request()->routeIs('assignment.public') ? 'on' : '' }}">Tugas</a>
        </div>

        {{-- CTA --}}
        <div class="pub-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="pub-btn">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Login
                </a>
            @endauth
        </div>

    </div>

    {{-- Mobile nav row 2 --}}
        <div class="pub-nav-row2">
            <a href="{{ route('home') }}"
               class="pub-nav2-link {{ request()->routeIs('home') ? 'on' : '' }}">Jadwal</a>
            <a href="{{ route('inventory.public') }}"
               class="pub-nav2-link {{ request()->routeIs('inventory.public') ? 'on' : '' }}">Inventaris</a>
            <a href="{{ route('rekap.public') }}"
               class="pub-nav2-link {{ request()->routeIs('rekap.public') ? 'on' : '' }}">Rekap</a>
            <a href="{{ route('assignment.public') }}"
               class="pub-nav2-link {{ request()->routeIs('assignment.public') ? 'on' : '' }}">Tugas</a>
        </div>
</nav>

{{-- ═══ KONTEN HALAMAN ═══ --}}
@yield('content')

<footer>© {{ date('Y') }} Lab Management System · Nuris Jember</footer>

<div class="page-trans" id="pt"></div>

@yield('scripts')

<script>
document.querySelectorAll('a.pub-link, a.pub-btn, a.pub-brand, a.pub-nav2-link').forEach(function(a) {
    var href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript') || a.getAttribute('target') === '_blank') return;
    a.addEventListener('click', function(e) {
        var current = window.location.pathname;
        try {
            var target = new URL(href, window.location.href).pathname;
            if (target === current) return;
        } catch(err) {}
        e.preventDefault();
        document.getElementById('pt').classList.add('go');
        setTimeout(function() { window.location.href = href; }, 220);
    });
});
window.addEventListener('pageshow', function() {
    document.getElementById('pt').classList.remove('go');
});
</script>

</body>
</html>