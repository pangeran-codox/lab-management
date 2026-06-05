@props(['stats' => ['labs' => 0, 'pending' => 0, 'today' => 0]])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Login</title>

    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    @vite(['resources/css/app.css', 'resources/css/login.css'])
</head>
<body>

    {{-- Blobs --}}
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="login-wrapper">

        {{-- Tombol kembali --}}
        <a href="{{ url('/') }}" class="back-link">
            <svg viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Jadwal
        </a>

        {{-- Card split panel --}}
        <div class="login-card" id="login-card">

            {{-- ── LEFT PANEL ── --}}
            <div class="left-panel">

                {{-- Brand --}}
                <div class="brand">
                    <div class="brand-icon">
                        <svg stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="brand-name">Lab Management</div>
                        <div class="brand-sub">Nuris Jember</div>
                    </div>
                </div>

                {{-- Hero --}}
                <div class="left-hero">
                    <h1>Selamat<br>Datang <span>Kembali</span></h1>
                    <p>Platform manajemen laboratorium komputer — jadwal, booking, inventaris dalam satu sistem.</p>
                </div>

                {{-- Stats --}}
                <div class="left-stats">
                    <div class="left-stat">
                        <div class="left-stat-num" data-target="{{ $stats['labs'] }}">0</div>
                        <div class="left-stat-lbl">Lab</div>
                    </div>
                    <div class="left-stat">
                        <div class="left-stat-num" data-target="{{ $stats['pending'] }}">0</div>
                        <div class="left-stat-lbl">Pending</div>
                    </div>
                    <div class="left-stat">
                        <div class="left-stat-num" data-target="{{ $stats['today'] }}">0</div>
                        <div class="left-stat-lbl">Hari Ini</div>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT PANEL ── --}}
            <div class="right-panel">
                <div class="panel-eyebrow">Panel Admin</div>
                <div class="panel-title">Masuk ke<br>Sistem</div>
                <p class="panel-sub">Kelola laboratorium dengan mudah</p>

                {{-- slot: konten dari login.blade.php --}}
                {{ $slot }}

                <div class="panel-footer">
                    &copy; {{ date('Y') }} Lab Management &middot; Nuris Jember
                </div>
            </div>

        </div>
    </div>

    <script>
        // Count-up stats
        document.querySelectorAll('.left-stat-num[data-target]').forEach(el => {
            const target = parseInt(el.dataset.target, 10) || 0;
            if (target === 0) { el.textContent = '0'; return; }
            const duration = 900;
            const step = Math.ceil(duration / target);
            let current = 0;
            const timer = setInterval(() => {
                current = Math.min(current + 1, target);
                el.textContent = current;
                if (current >= target) clearInterval(timer);
            }, step);
        });

        // Shake saat error
        const errorBox = document.querySelector('.error-box');
        if (errorBox) {
            const card = document.getElementById('login-card');
            card?.classList.add('shake');
            setTimeout(() => card?.classList.remove('shake'), 500);
        }

        // Loading state
        document.querySelector('form')?.addEventListener('submit', function () {
            const btn = this.querySelector('.btn-login');
            if (btn) {
                btn.classList.add('loading');
                const text = btn.querySelector('.btn-text');
                if (text) text.textContent = 'Memproses...';
            }
        });

        // Caps Lock warning
        const passInput = document.getElementById('password');
        const capsWarn  = document.getElementById('caps-warning');
        if (passInput && capsWarn) {
            passInput.addEventListener('keyup', e => {
                capsWarn.classList.toggle('show', !!e.getModifierState?.('CapsLock'));
            });
            passInput.addEventListener('blur', () => capsWarn.classList.remove('show'));
        }
    </script>

</body>
</html>