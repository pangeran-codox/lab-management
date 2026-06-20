<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - {{ $title ?? 'Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Tambahkan ini untuk page-specific CSS --}}
    @stack('styles')

    <style>
        body { font-family:'DM Sans',sans-serif; }
        .font-display { font-family:'Outfit',sans-serif; }
        /* Sidebar nav active */
        .nav-active { background:linear-gradient(135deg,rgba(185,217,235,.18),rgba(185,217,235,.08)) !important; color:#B9D9EB !important; border-left:3px solid #B9D9EB; }
        .nav-item { border-left:3px solid transparent; }
        .nav-item:hover { background:rgba(185,217,235,.08) !important; color:rgba(185,217,235,.8) !important; }

        /* Loading Screen */
        .loading-screen {
            position: fixed; inset: 0; z-index: 9999;
            background: linear-gradient(135deg, #003d24, #00331e);
            display: flex; align-items: center; justify-content: center;
            flex-direction: column;
            opacity: 1; pointer-events: all; transition: opacity 0.5s ease;
        }
        .loading-screen.hidden {
            opacity: 0; pointer-events: none;
        }
        .loading-logo {
            display: flex; align-items: center; gap: 16px; margin-bottom: 32px;
            opacity: 0; transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }
        .loading-logo-icon {
            width: 64px; height: 64px; border-radius: 16px;
            background: rgba(185,217,235,0.15);
            border: 2px solid rgba(185,217,235,0.3);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
            50% { transform: scale(1.05); box-shadow: 0 15px 50px rgba(0,0,0,0.3); }
        }
        .loading-logo-text {
            text-align: left;
        }
        .loading-logo-text h1 {
            color: #fff; font-size: 24px; font-weight: 700; font-family: 'Outfit', sans-serif;
            margin: 0; line-height: 1.1;
        }
        .loading-logo-text p {
            color: #B9D9EB; font-size: 12px; font-weight: 500; margin: 2px 0 0 0;
        }
        .loading-spinner-container {
            display: flex; align-items: center; gap: 12px;
        }
        .loading-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: #B9D9EB;
            animation: bounce 1.4s ease-in-out infinite;
        }
        .loading-dot:nth-child(1) { animation-delay: -0.32s; }
        .loading-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Global Toast */
        #global-toast-wrap { position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
        .g-toast { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:11px; font-size:13px; font-weight:500; box-shadow:0 8px 32px rgba(0,61,36,.2); pointer-events:all; animation:g-toastIn .3s cubic-bezier(.16,1,.3,1); min-width:260px; max-width:340px; }
        @keyframes g-toastIn { from{opacity:0;transform:translateY(14px) scale(.95)} to{opacity:1;transform:none} }
        .g-toast.out { animation:g-toastOut .3s ease forwards; }
        @keyframes g-toastOut { to{opacity:0;transform:translateY(8px) scale(.96)} }
        .g-toast.t-ok { background:#003d24; color:#B9D9EB; border:1px solid rgba(185,217,235,.2); }
        .g-toast.t-err { background:#7f1d1d; color:#fecaca; border:1px solid #991b1b; }
        .g-toast-ic { font-size:15px; flex-shrink:0; }
        .g-toast-close { margin-left:auto; background:none; border:none; color:inherit; opacity:.6; cursor:pointer; font-size:16px; line-height:1; padding:0 2px; }
        .g-toast-close:hover { opacity:1; }

        @media (max-width: 640px) {
            #global-toast-wrap { bottom: 16px; right: 16px; left: 16px; align-items: center; }
            .g-toast { min-width: 100%; width: 100%; max-width: none; }
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">

<!-- Loading Screen -->
<div class="loading-screen" id="loading-screen">
    <div class="loading-logo">
        <div class="loading-logo-icon">
            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#B9D9EB" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="loading-logo-text">
            <h1>Lab Management</h1>
            <p>Nuris Jember</p>
        </div>
    </div>
    <div class="loading-spinner-container">
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
    </div>
</div>

<div id="global-toast-wrap"></div>

<script>
    // Hide loading screen when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                loadingScreen.classList.add('hidden');
            }
        }, 300); // Minimum delay so it doesn't flash
    });

    window.showGlobalNotification = function(msg, type = 'ok') {
        const wrap = document.getElementById('global-toast-wrap');
        if (!wrap) return;
        const t = document.createElement('div');
        t.className = `g-toast t-${type}`;
        t.innerHTML = `<span class="g-toast-ic">${type === 'ok' ? '✓' : '⚠'}</span>
                       <span>${msg}</span>
                       <button class="g-toast-close" aria-label="Tutup">×</button>`;
        t.querySelector('.g-toast-close').addEventListener('click', () => {
            t.classList.add('out');
            setTimeout(() => t.remove(), 300);
        });
        wrap.appendChild(t);
        
        // Play notification sound if you want, but simple toast for now
        if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
            new Notification('Lab Management', { body: msg });
        }

        setTimeout(() => {
            if (t.parentElement) {
                t.classList.add('out');
                setTimeout(() => t.remove(), 300);
            }
        }, 5000);
    };

    // Request notification permission
    if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Pass allowed resources to JS for Reverb filtering
    window.userConfig = {
        role: "{{ auth()->user()->role }}",
        allowedResources: @json(auth()->user()->getAssignedLabIds())
    };
</script>

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-60 flex-shrink-0 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
           style="background:linear-gradient(180deg,#003d24 0%,#00331e 100%)">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5" style="border-bottom:1px solid rgba(185,217,235,.1)">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(185,217,235,.12);border:1.5px solid rgba(185,217,235,.25)">
                <svg class="w-5 h-5" style="color:#B9D9EB" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p style="font-family:Outfit,sans-serif;font-weight:700;color:#fff;font-size:14px;line-height:1.2">Lab Management</p>
                <p style="font-size:11px;color:rgba(185,217,235,.45)">Nuris Jember</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            @php
            $navItems = [
                ['route'=>'dashboard',              'label'=>'Dashboard',      'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route'=>'schedule.admin',         'label'=>'Jadwal Lab',     'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route'=>'booking.index',          'label'=>'Booking Lab',    'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2','badge'=>true],
                ['route'=>'inventory.admin',        'label'=>'Inventaris',     'icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['route'=>'reports.usage.index',    'label'=>'Laporan Penggunaan','icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['route'=>'teacher.index',          'label'=>'Data Guru',      'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'roles' => ['admin', 'staff']],
                ['route'=>'organization.index',     'label'=>'Sekolah & Kelas','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'roles' => ['admin', 'staff']],
                ['route'=>'assignment.admin',       'label'=>'Tugas',          'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                ['route'=>'important-schedule.index','label'=>'Jadwal Penting','icon'=>'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z', 'roles' => ['admin', 'staff']],
                ['route'=>'users.index',            'label'=>'Pengelolaan Pengguna','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'roles' => ['admin', 'super_admin']],
            ];
            @endphp

            @foreach($navItems as $item)
            @if(!isset($item['roles']) || in_array(auth()->user()->role, $item['roles']))
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               @click="sidebarOpen = false"
               class="nav-item {{ $active ? 'nav-active' : '' }} group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
               style="color:{{ $active ? '#B9D9EB' : 'rgba(185,217,235,.45)' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
                @if(!empty($item['badge']))
                    @php
                        $user = auth()->user();
                        $allowed = null;
                        if (!in_array($user->role, ['admin', 'operator'])) {
                            $allowed = $user->metadata['allowed_resources'] ?? [];
                        }
                        $pc = \App\Models\Booking::where('status','pending')
                            ->when($allowed, fn($q) => $q->whereIn('resource_id', $allowed))
                            ->count();
                    @endphp
                    <span class="notification-badge ml-auto text-white text-xs font-bold px-1.5 py-0.5 rounded-full" style="background:#ef4444;font-size:10px;{{ $pc >0 ? '' : 'display:none;' }}">{{ $pc }}</span>
                @endif
            </a>
            @endif
            @endforeach

        </nav>

        {{-- User info --}}
        <div class="px-3 py-4" style="border-top:1px solid rgba(185,217,235,.1)">
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background:linear-gradient(135deg,#B9D9EB,#8ec8e0)">
                    <span style="color:#003d24;font-size:12px;font-weight:800">{{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username, 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p style="color:#fff;font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth()->user()->full_name ?? auth()->user()->username }}</p>
                    <p style="color:rgba(185,217,235,.4);font-size:11px;text-transform:capitalize">{{ str_replace('_',' ',auth()->user()->role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                        style="background:none;border:none;cursor:pointer;color:rgba(185,217,235,.35);padding:4px;line-height:1;transition:color .15s"
                        onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='rgba(185,217,235,.35)'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══ MAIN ═══ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="flex-shrink-0 bg-white px-4 lg:px-6 h-16 flex items-center gap-4"
                style="border-bottom:1px solid #cce4f0;box-shadow:0 1px 4px rgba(0,105,62,.06)">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex-1">
                <h1 style="font-family:Outfit,sans-serif;font-weight:700;color:#0d2416;font-size:17px">@yield('title', 'Dashboard')</h1>
                <p style="font-size:11px;color:#6b8fa3" class="hidden sm:block">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative" style="color:#6b8fa3;background:none;border:none;cursor:pointer;padding:4px"
                    onmouseover="this.style.color='#0d2416'" onmouseout="this.style.color='#6b8fa3'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @php
                        $user = auth()->user();
                        $allowed = null;
                        if (!in_array($user->role, ['admin', 'operator'])) {
                            $allowed = $user->metadata['allowed_resources'] ?? [];
                        }
                        $pendingCount = \App\Models\Booking::where('status','pending')
                            ->when($allowed, fn($q) => $q->whereIn('resource_id', $allowed))
                            ->count();
                    @endphp
                    <span class="notification-badge" style="position:absolute;top:-2px;right:-2px;width:15px;height:15px;background:#ef4444;border-radius:50%;display:{{ $pendingCount > 0 ? 'flex' : 'none' }};align-items:center;justify-content:center;color:white;font-size:9px;font-weight:bold;border:2px solid white">
                        {{ $pendingCount }}
                    </span>
                </button>
                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                     style="background:linear-gradient(135deg,#003d24,#00693E)">
                    <span style="color:#B9D9EB;font-size:11px;font-weight:800">{{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username, 0, 2)) }}</span>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{ show:true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 text-sm px-4 py-3 rounded-xl"
             style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534">
            <svg class="w-4 h-4 flex-shrink-0" style="color:#22c55e" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
            <button @click="show=false" class="ml-auto" style="color:#16a34a;background:none;border:none;cursor:pointer">✕</button>
        </div>
        @endif
        @if(session('error'))
        <div x-data="{ show:true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
             class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 text-sm px-4 py-3 rounded-xl"
             style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
            <svg class="w-4 h-4 flex-shrink-0" style="color:#ef4444" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
            <button @click="show=false" class="ml-auto" style="color:#dc2626;background:none;border:none;cursor:pointer">✕</button>
        </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')     {{-- ← tambah ini --}}

<script>
    // Fallback untuk menutup sidebar ketika mengklik link navigasi
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarLinks = document.querySelectorAll('.nav-item');
        sidebarLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                // Cari elemen dengan Alpine.js dan tutup sidebar
                const layout = document.querySelector('[x-data]');
                if (layout && layout.__x) {
                    layout.__x.$data.sidebarOpen = false;
                }
            });
        });
    });
</script>

</body>
</html>