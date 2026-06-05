{{-- resources/views/schedule/partials/hero.blade.php --}}
<div class="hero">
    <div class="hero-inner">
        <p class="hero-eyebrow">Sistem Informasi Laboratorium</p>
        <h1 class="hero-title">Jadwal Penggunaan Lab</h1>
        <p class="hero-desc">Pilih lab · Klik slot kosong untuk booking · Tanpa perlu login</p>

        <div class="period-badge">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:#E1F5EE;border:1px solid #5DCAA5"></div>
                <span class="legend-text">Jadwal Tetap</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#9FE1CB;border:1px solid #1D9E75"></div>
                <span class="legend-text">Disetujui</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#FAEEDA;border:1px solid #EF9F27"></div>
                <span class="legend-text">Pending</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:transparent;border:2px dashed #5DCAA5"></div>
                <span class="legend-text">Tersedia — klik booking</span>
            </div>
        </div>
    </div>
</div>
