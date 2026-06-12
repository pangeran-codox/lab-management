<x-app-layout>
<x-slot name="title">Manajemen Sekolah & Kelas</x-slot>

@vite(['resources/js/sekolah.js'])

<style>
    /* ── Variables & Utility ────────────────────────────────── */
    :root {
        --primary: var(--g8, #00693E);
        --primary-dark: var(--g9, #003d24);
        --primary-light: var(--sk-surface-3, #eaf4f0);
        --secondary: #0369A1;
        --secondary-light: #E0F2FE;
        --accent: #92400E;
        --accent-light: #FEF3C7;
        --danger: #c0392b;
        --text-main: var(--text, #0d2416);
        --text-muted: var(--muted, #6b8fa3);
        --bg-card: #ffffff;
        --border-color: var(--border, #cce4f0);
        --radius-lg: var(--r, 14px);
        --radius-md: 10px;
        --shadow-sm: var(--shadow, 0 1px 4px rgba(0,105,62,.07));
        --shadow-md: 0 6px 28px rgba(0,105,62,.14);
    }

    /* ── Layout ────────────────────────────────────────────── */
    .sk-container {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
        animation: skFadeUp 0.4s ease-out;
    }

    @keyframes skFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Header & Toolbar ───────────────────────────────────── */
    .sk-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .sk-title-section h1 {
        font-size: 1.875rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.025em;
    }

    .sk-title-section p {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 0.25rem;
    }

    .sk-controls {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-grow: 1;
        justify-content: flex-end;
    }

    .sk-search-wrapper {
        position: relative;
        flex-grow: 1;
        max-width: 450px;
    }

    .sk-search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        width: 1.25rem;
        height: 1.25rem;
    }

    .sk-search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        background: white;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .sk-search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-light);
    }

    /* ── Filters ────────────────────────────────────────────── */
    .sk-filters {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        scrollbar-width: none;
    }

    .sk-filter-pill {
        padding: 0.5rem 1rem;
        border-radius: 999px;
        background: white;
        border: 1px solid var(--border-color);
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .sk-filter-pill:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .sk-filter-pill.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* ── Add Card ───────────────────────────────────────────── */
    .sk-add-trigger {
        background: var(--primary);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .sk-add-trigger:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 105, 62, 0.2);
    }

    .sk-add-form-container {
        display: none;
        margin-bottom: 2rem;
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        animation: skSlideDown 0.3s ease-out;
    }

    @keyframes skSlideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Grid & Cards ───────────────────────────────────────── */
    .sk-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
    }

    .sk-org-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .sk-org-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -8px rgba(0,0,0,0.1);
        border-color: var(--primary);
    }

    .sk-org-type-badge {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-smk { background: var(--primary-light); color: var(--primary); }
    .badge-sma { background: var(--secondary-light); color: var(--secondary); }
    .badge-ma  { background: var(--accent-light); color: var(--accent); }
    .badge-smp { background: #F3E8FF; color: #6B21A8; }
    .badge-mts { background: #DCFCE7; color: #166534; }
    .badge-other { background: #F3F4F6; color: #374151; }

    .sk-org-info {
        margin-bottom: 1.5rem;
    }

    .sk-org-name {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        padding-right: 3rem; /* Space for badge */
        line-height: 1.2;
    }

    .sk-org-contact {
        display: grid;
        gap: 0.5rem;
    }

    .sk-contact-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .sk-contact-item svg {
        width: 1rem;
        height: 1rem;
        flex-shrink: 0;
        opacity: 0.6;
    }

    .sk-card-divider {
        height: 1px;
        background: var(--border-color);
        margin: 1.25rem 0;
    }

    .sk-classes-preview {
        flex-grow: 1;
    }

    .sk-classes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .sk-classes-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }

    .sk-classes-count {
        background: var(--primary-light);
        color: var(--primary);
        padding: 0.125rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .sk-classes-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        max-height: 120px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .sk-classes-list::-webkit-scrollbar { width: 4px; }
    .sk-classes-list::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

    .sk-class-tag {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 0.25rem 0.625rem;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #475569;
        transition: all 0.2s;
    }

    .sk-class-tag:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .sk-card-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .sk-action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 700;
        transition: all 0.2s;
        cursor: pointer;
        border: 1px solid var(--border-color);
        background: white;
        color: var(--text-main);
    }

    .sk-action-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .sk-action-btn--primary {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .sk-action-btn--primary:hover {
        background: var(--primary-dark);
    }

    .sk-icon-btn {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: white;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
    }

    .sk-icon-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .sk-icon-btn--danger:hover {
        border-color: #fecaca;
        color: var(--danger);
        background: #fef2f2;
    }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 768px) {
        .sk-header { flex-direction: column; align-items: stretch; }
        .sk-controls { flex-direction: column; align-items: stretch; }
        .sk-search-wrapper { max-width: none; }
        .sk-grid { grid-template-columns: 1fr; }
    }
    .sk-edit-panel {
        display: none;
        position: absolute;
        inset: 0;
        background: white;
        z-index: 20;
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        animation: skFadeIn 0.2s ease-out;
        flex-direction: column;
    }

    @keyframes skFadeIn {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<div class="sk-container">
    {{-- ── FLASH MESSAGE ────────────────────────────────────── --}}
    @if (session('success'))
    <div class="sk-flash sk-flash--success" id="sk-flash" style="margin-bottom: 2rem; background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 1rem; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem;">
        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span style="font-weight: 600;">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ── HEADER ───────────────────────────────────────────── --}}
    <header class="sk-header">
        <div class="sk-title-section">
            <h1>Sekolah & Lembaga</h1>
            <p>Kelola data instansi dan pembagian kelas laboratorium.</p>
        </div>
        
        <div class="sk-controls">
            <div class="sk-search-wrapper">
                <svg class="sk-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="sk-search" type="text" class="sk-search-input" placeholder="Cari nama sekolah atau alamat...">
            </div>
            <button class="sk-add-trigger" id="btn-toggle-add">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Sekolah
            </button>
        </div>
    </header>

    {{-- ── FILTERS ──────────────────────────────────────────── --}}
    <div class="sk-filters">
        <button class="sk-filter-pill active" data-filter="all">Semua</button>
        @foreach (['SMK','SMA','MA','SMP','MTs','Lainnya'] as $t)
            <button class="sk-filter-pill" data-filter="{{ $t }}">{{ $t }}</button>
        @endforeach
    </div>

    {{-- ── ADD FORM (Hidden by default) ──────────────────────── --}}
    <div class="sk-add-form-container" id="add-school-form">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); background: #f9fafb; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-weight: 800; font-size: 1.1rem; color: var(--text-main);">Tambah Sekolah Baru</h2>
            <button type="button" id="btn-close-add" style="background: none; border: none; cursor: pointer; color: var(--text-muted);"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('organization.store') }}" style="padding: 1.5rem;">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div class="sk-field">
                    <label class="sk-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Nama Sekolah <span style="color: var(--danger)">*</span></label>
                    <input name="name" type="text" class="sk-search-input" style="padding-left: 1rem;" placeholder="Contoh: SMK Nuris Jember" required>
                </div>
                <div class="sk-field">
                    <label class="sk-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Tipe Instansi <span style="color: var(--danger)">*</span></label>
                    <select name="type" class="sk-search-input" style="padding-left: 1rem;" required>
                        @foreach (['SMK','SMA','MA','SMP','MTs','Lainnya'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sk-field"><label class="sk-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Email</label><input name="email" type="email" class="sk-search-input" style="padding-left: 1rem;" placeholder="info@sekolah.sch.id"></div>
                <div class="sk-field"><label class="sk-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Telepon</label><input name="phone" type="text" class="sk-search-input" style="padding-left: 1rem;" placeholder="0331-xxxxxx"></div>
                <div class="sk-field" style="grid-column: 1 / -1;"><label class="sk-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Alamat Lengkap</label><input name="address" type="text" class="sk-search-input" style="padding-left: 1rem;" placeholder="Jl. Kalimantan No. 123..."></div>
            </div>
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" class="sk-action-btn" id="btn-cancel-add">Batal</button>
                <button type="submit" class="sk-add-trigger" style="padding: 0.75rem 2rem;">Simpan Data</button>
            </div>
        </form>
    </div>

    {{-- ── GRID DAFTAR SEKOLAH ──────────────────────────────── --}}
    <div id="sk-org-list" class="sk-grid">
        @forelse ($organizations as $org)
        @php
            $type = strtoupper($org->type ?? 'LAINNYA');
            $badgeClass = match($type) {
                'SMK'  => 'badge-smk', 'SMA'  => 'badge-sma',
                'MA'   => 'badge-ma', 'SMP'  => 'badge-smp',
                'MTS'  => 'badge-mts', default => 'badge-other',
            };
        @endphp

        <div class="sk-org-card" data-type="{{ $type }}" data-search="{{ strtolower($org->name . ' ' . $org->address) }}">
            <span class="sk-org-type-badge {{ $badgeClass }}">{{ $org->type }}</span>
            
            <div class="sk-org-info">
                <h3 class="sk-org-name">{{ $org->name }}</h3>
                <div class="sk-org-contact">
                    <div class="sk-contact-item" title="Alamat">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $org->address ?: 'Alamat belum diatur' }}
                    </div>
                    @if($org->phone)
                    <div class="sk-contact-item" title="Telepon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $org->phone }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="sk-card-divider"></div>

            <div class="sk-classes-preview">
                <div class="sk-classes-header">
                    <span class="sk-classes-title">Daftar Kelas</span>
                    <span class="sk-classes-count">{{ $org->classes->count() }} Kelas</span>
                </div>
                <div class="sk-classes-list">
                    @forelse ($org->classes as $class)
                        <span class="sk-class-tag">{{ $class->grade_level }} {{ $class->name }}</span>
                    @empty
                        <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">Belum ada kelas terdaftar.</span>
                    @endforelse
                </div>
            </div>

            <div class="sk-card-actions">
                <a href="{{ route('class.index') }}?org_id={{ $org->id }}" class="sk-action-btn sk-action-btn--primary">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Kelola Kelas
                </a>
                <button type="button" class="sk-icon-btn" data-edit-org="{{ $org->id }}" title="Edit Sekolah">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form method="POST" action="{{ route('organization.destroy', $org) }}" onsubmit="return confirm('Hapus {{ $org->name }} dan semua data kelasnya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="sk-icon-btn sk-icon-btn--danger" title="Hapus Sekolah">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>

            {{-- Edit Panel (Hidden) --}}
            <div id="sk-edit-org-{{ $org->id }}" class="sk-edit-panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="font-weight: 800; color: var(--text-main);">Edit Instansi</h4>
                    <button type="button" data-cancel-edit="{{ $org->id }}" style="background: none; border: none; cursor: pointer;"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form method="POST" action="{{ route('organization.update', $org) }}">
                    @csrf @method('PATCH')
                    <div style="display: grid; gap: 0.75rem;">
                        <input type="text" name="name" value="{{ $org->name }}" class="sk-search-input" style="padding-left: 1rem;" required placeholder="Nama Sekolah">
                        <select name="type" class="sk-search-input" style="padding-left: 1rem;">
                            @foreach (['SMK','SMA','MA','SMP','MTs','Lainnya'] as $t)
                                <option value="{{ $t }}" {{ $org->type == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="address" value="{{ $org->address }}" class="sk-search-input" style="padding-left: 1rem;" placeholder="Alamat">
                        <input type="email" name="email" value="{{ $org->email }}" class="sk-search-input" style="padding-left: 1rem;" placeholder="Email">
                        <input type="text" name="phone" value="{{ $org->phone }}" class="sk-search-input" style="padding-left: 1rem;" placeholder="Telepon">
                    </div>
                    <div style="margin-top: 1.25rem; display: flex; gap: 0.5rem;">
                        <button type="submit" class="sk-add-trigger" style="flex: 1; justify-content: center;">Update</button>
                        <button type="button" class="sk-action-btn" data-cancel-edit="{{ $org->id }}" style="flex: 1;">Batal</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div id="sk-empty" style="grid-column: 1/-1; text-align: center; padding: 5rem 2rem; background: white; border-radius: var(--radius-lg); border: 1px dashed var(--border-color);">
            <div style="background: var(--primary-light); width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <svg style="width: 32px; height: 32px; color: var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 style="font-weight: 800; font-size: 1.25rem; color: var(--text-main);">Belum ada sekolah</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Mulai dengan menambahkan sekolah atau lembaga baru.</p>
        </div>
        @endforelse
    </div>
</div>

</x-app-layout>
