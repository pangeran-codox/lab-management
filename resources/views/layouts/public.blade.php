<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Lab - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --sage: #ACC8A2;
            --sage-light: #f0f7ee;
            --sage-mid: #d6ead2;
            --olive: #1A2517;
            --olive-mid: #2d3d29;
            --olive-light: #3d5438;
        }
        body { font-family: 'DM Sans', sans-serif; background: #f5f7f4; }
        .font-display { font-family: 'Outfit', sans-serif; }

        .hero-bg {
            background: linear-gradient(135deg, #1A2517 0%, #2d3d29 60%, #3d5438 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(172,200,162,0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-bg::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: 5%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(172,200,162,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Navbar terpisah dari hero-bg agar tidak double gradient */
        .navbar-bg {
            background: linear-gradient(135deg, #1A2517 0%, #2d3d29 100%);
            position: relative;
        }

        .input-modern {
            border: 1.5px solid #dce8d9;
            border-radius: 10px;
            padding: 10px 14px;
            width: 100%;
            font-size: 14px;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
            background: #fafcf9;
            color: #1A2517;
        }
        .input-modern:focus {
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(172,200,162,0.25);
            background: white;
        }
        .input-modern.error { border-color: #f87171; }
        .input-modern option { color: #1A2517; }

        .flash-toast {
            animation: toastIn 0.3s cubic-bezier(0.16,1,0.3,1);
        }
        @keyframes toastIn {
            from { opacity:0; transform: translateX(-50%) translateY(-14px); }
            to { opacity:1; transform: translateX(-50%) translateY(0); }
        }

        ::-webkit-scrollbar { height: 4px; width: 4px; }
        ::-webkit-scrollbar-track { background: #f0f7ee; }
        ::-webkit-scrollbar-thumb { background: #ACC8A2; border-radius: 4px; }

        .nav-login-btn {
            border: 1.5px solid rgba(172,200,162,0.35);
            color: #ACC8A2;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 10px;
            transition: all 0.2s;
        }
        .nav-login-btn:hover {
            background: rgba(172,200,162,0.1);
            border-color: rgba(172,200,162,0.6);
        }
    </style>
</head>
<body class="antialiased">

    {{-- NAVBAR --}}
    <nav class="navbar-bg sticky top-0 z-40 border-b border-white/5 shadow-md">
        <div class="max-w-screen-xl mx-auto px-4 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(172,200,162,0.15); border: 1.5px solid rgba(172,200,162,0.3);">
                    <svg class="w-5 h-5" style="color:#ACC8A2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-display font-bold text-white text-sm leading-tight tracking-tight">Lab Management</p>
                    <p class="text-xs" style="color:rgba(172,200,162,0.6)">Nuris Jember</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('journal.index') }}" class="nav-login-btn">Jurnal Lab</a>
                <a href="{{ route('assignment.public') }}" class="nav-login-btn">Tugas</a>
                <a href="{{ route('assignment.admin') }}" class="nav-login-btn">Panel Guru</a>
                <a href="{{ route('login') }}" class="nav-login-btn">Login Admin →</a>
            </div>
        </div>
    </nav>

    {{-- FLASH --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         x-transition:leave="transition duration-300" x-transition:leave-end="opacity-0"
         class="flash-toast fixed top-20 left-1/2 z-50 -translate-x-1/2 text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium flex items-center gap-2.5"
         style="background: linear-gradient(135deg, #2d3d29, #3d5438);">
        <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background:rgba(172,200,162,0.25)">
            <svg class="w-3 h-3" style="color:#ACC8A2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <span style="color:#ACC8A2">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flash-toast fixed top-20 left-1/2 z-50 -translate-x-1/2 bg-red-600 text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium">
        {{ session('error') }}
    </div>
    @endif

    <main>{{ $slot }}</main>

    <footer class="text-center text-xs py-8 mt-4 border-t border-gray-200" style="color:#7a9475">
        © {{ date('Y') }} Lab Management System &middot; Nuris Jember
    </footer>

    @livewireScripts
</body>
</html>