{{--
    resources/views/kelas/index.blade.php

    Entry point Vite: resources/js/kelas.js
    CSS diimport di dalam kelas.js — Vite proses otomatis.
    Font (DM Sans, Outfit) diload via app.css (local woff2).

    vite.config.js → input: ['resources/js/app.js', 'resources/js/kelas.js']
--}}
<x-app-layout>
<x-slot name="title">Manajemen Kelas</x-slot>

@vite(['resources/js/kelas.js'])

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Show toast after page loaded
            setTimeout(() => {
                const el = document.getElementById('kls-toast');
                if (el) {
                    el.textContent = '{{ session('success') }}';
                    el.classList.add('kls-toast--show');
                    setTimeout(() => el.classList.remove('kls-toast--show'), 2400);
                }
            }, 100);
        });
    </script>
@endif

<div class="kls-wrap">

    {{-- ── PAGE HEADER ──────────────────────────────────────── --}}
    <div class="kls-page-header">
        <div>
            <h1 class="kls-page-title">Manajemen Kelas</h1>
            <p class="kls-page-subtitle">Kelola kelas dan PIN akses siswa</p>
        </div>
    </div>

    {{-- ── TOOLBAR ──────────────────────────────────────────── --}}
    <div class="kls-toolbar">
        <div class="kls-search">
            <svg class="kls-search__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                id="kls-search"
                type="text"
                class="kls-search__input"
                placeholder="Cari nama kelas atau jurusan…"
                autocomplete="off"
            >
        </div>

        <select id="kls-school-filter" class="kls-filter">
            <option value="">Semua Sekolah</option>
            @foreach ($organizations as $org)
                <option value="{{ $org->id }}" {{ (isset($orgId) && $orgId == $org->id) ? 'selected' : '' }}>{{ $org->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── FORM TAMBAH ──────────────────────────────────────── --}}
    <div class="kls-card kls-add-card">

        <div
            class="kls-add-head"
            id="kls-add-head"
            role="button"
            tabindex="0"
            aria-expanded="false"
            aria-controls="kls-add-body"
        >
            <div class="kls-add-head__left">
                <svg class="kls-add-head__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kelas Baru
            </div>
            <svg class="kls-add-chevron" id="kls-add-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <div class="kls-add-body" id="kls-add-body">
            <form method="POST" action="{{ route('class.store') }}{{ isset($orgId) && $orgId ? '?org_id=' . $orgId : '' }}">
                @csrf

                <div class="kls-form-grid">

                    <div class="kls-field">
                        <label class="kls-label" for="f-org">Sekolah / Lembaga *</label>
                        <select
                            id="f-org"
                            name="organization_id"
                            class="kls-select {{ $errors->has('organization_id') ? 'kls-select--error' : '' }}"
                            required
                        >
                            <option value="">Pilih Sekolah</option>
                            @foreach ($organizations as $org)
                                <option value="{{ $org->id }}" {{ (old('organization_id') == $org->id || (isset($orgId) && $orgId == $org->id)) ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('organization_id')
                            <span class="kls-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="kls-field">
                        <label class="kls-label" for="f-grade">Tingkat *</label>
                        <input
                            id="f-grade"
                            name="grade_level"
                            type="text"
                            class="kls-input {{ $errors->has('grade_level') ? 'kls-input--error' : '' }}"
                            placeholder="Contoh: X, XI, XII atau 7, 8, 9"
                            value="{{ old('grade_level') }}"
                            required
                        >
                        @error('grade_level')
                            <span class="kls-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="kls-field">
                        <label class="kls-label" for="f-name">Nama Kelas *</label>
                        <input
                            id="f-name"
                            name="name"
                            type="text"
                            class="kls-input {{ $errors->has('name') ? 'kls-input--error' : '' }}"
                            placeholder="Contoh: TKJ 1, RPL 2"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')
                            <span class="kls-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="kls-field">
                        <label class="kls-label" for="f-major">Jurusan</label>
                        <input
                            id="f-major"
                            name="major"
                            type="text"
                            class="kls-input"
                            placeholder="Contoh: Teknik Komputer dan Jaringan"
                            value="{{ old('major') }}"
                        >
                    </div>

                    <div class="kls-field">
                        <label class="kls-label" for="f-count">Jumlah Siswa</label>
                        <input
                            id="f-count"
                            name="student_count"
                            type="number"
                            min="0"
                            class="kls-input"
                            placeholder="36"
                            value="{{ old('student_count') }}"
                        >
                    </div>

                    <div class="kls-field">
                        <label class="kls-label" for="f-year">Tahun Akademik *</label>
                        <input
                            id="f-year"
                            name="academic_year"
                            type="text"
                            class="kls-input {{ $errors->has('academic_year') ? 'kls-input--error' : '' }}"
                            placeholder="2025/2026"
                            value="{{ old('academic_year', date('Y') . '/' . (date('Y') + 1)) }}"
                            required
                        >
                        @error('academic_year')
                            <span class="kls-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>{{-- /kls-form-grid --}}

                <div class="kls-pin-notice">
                    <svg class="kls-pin-notice__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    PIN kelas akan digenerate otomatis setelah kelas berhasil ditambahkan.
                </div>

                <div class="kls-form-actions">
                    <button type="submit" class="kls-btn kls-btn--primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Tambah Kelas
                    </button>
                </div>

            </form>
        </div>{{-- /kls-add-body --}}
    </div>{{-- /kls-add-card --}}


    {{-- ── TABEL KELAS ──────────────────────────────────────── --}}
    <div class="kls-card">

        <div class="kls-tbl-head">
            <span class="kls-tbl-title">
                <svg class="kls-tbl-title__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Daftar Kelas
            </span>
            <span class="kls-tbl-hint">PIN digunakan siswa untuk mengakses halaman tugas</span>
        </div>

        <div class="kls-tbl-wrap">
            <table class="kls-table">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Sekolah</th>
                        <th>Kelas</th>
                        <th>Tingkat / Jurusan</th>
                        <th>Siswa</th>
                        <th>PIN Tugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                @forelse ($classes as $idx => $cls)
                <tr
                    class="kls-row"
                    data-school-id="{{ $cls->organization_id }}"
                    data-search="{{ strtolower($cls->name . ' ' . $cls->major . ' ' . ($cls->organization->name ?? '')) }}"
                >
                    {{-- No --}}
                    <td class="kls-no">{{ $idx + 1 }}</td>

                    {{-- Sekolah --}}
                    <td>
                        <span class="kls-school-badge">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"/>
                            </svg>
                            {{ $cls->organization->name ?? '—' }}
                        </span>
                    </td>

                    {{-- Nama Kelas + Edit Form Inline --}}
                    <td>
                        <div class="kls-class-name">{{ $cls->name }}</div>

                        <div id="kls-edit-form-{{ $cls->id }}" class="kls-edit-form">
                            <form method="POST" action="{{ route('class.update', $cls) }}{{ isset($orgId) && $orgId ? '?org_id=' . $orgId : '' }}" style="display:contents">
                                @csrf @method('PATCH')

                                <select name="organization_id" class="kls-input-sm" required style="width:150px">
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}" {{ $cls->organization_id == $org->id ? 'selected' : '' }}>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text"   name="name"          class="kls-input-sm" value="{{ $cls->name }}"          style="width:100px" required>
                                <input type="text"   name="grade_level"   class="kls-input-sm" value="{{ $cls->grade_level }}"   style="width:58px"  required>
                                <input type="text"   name="major"         class="kls-input-sm" value="{{ $cls->major }}"         style="width:110px" placeholder="Jurusan">
                                <input type="number" name="student_count" class="kls-input-sm" value="{{ $cls->student_count }}" style="width:62px"  min="0">
                                <input type="text"   name="academic_year" class="kls-input-sm" value="{{ $cls->academic_year }}" style="width:86px"  required>

                                <button type="submit" class="kls-btn kls-btn--primary kls-btn--sm">Simpan</button>
                                <button type="button" class="kls-btn kls-btn--ghost kls-btn--sm" data-cancel-id="{{ $cls->id }}">Batal</button>
                            </form>
                        </div>
                    </td>

                    {{-- Tingkat / Jurusan --}}
                    <td>
                        <div class="kls-grade-wrap">
                            @if ($cls->grade_level)
                                <span class="kls-grade-tag">{{ $cls->grade_level }}</span>
                            @endif
                            <span class="kls-major">{{ $cls->major ?: '—' }}</span>
                        </div>
                    </td>

                    {{-- Siswa --}}
                    <td>
                        <span class="kls-student-count">{{ $cls->student_count ?? 0 }}</span>
                        <span class="kls-student-label">siswa</span>
                    </td>

                    {{-- PIN --}}
                    <td>
                        @if ($cls->pin)
                            <div class="kls-pin-wrap">
                                <span class="kls-pin-badge">{{ $cls->pin }}</span>

                                <button
                                    type="button"
                                    class="kls-btn--icon"
                                    data-copy-pin="{{ $cls->pin }}"
                                    title="Salin PIN"
                                    aria-label="Salin PIN {{ $cls->pin }}"
                                >
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>

                                <form
                    method="POST"
                    action="{{ route('class.reset-pin', $cls) }}{{ isset($orgId) && $orgId ? '?org_id=' . $orgId : '' }}"
                    data-confirm-reset="Reset PIN kelas {{ $cls->name }}? PIN lama tidak bisa dipakai lagi."
                >
                                    @csrf @method('PATCH')
                                    <button type="submit" class="kls-btn--reset">↺ Reset</button>
                                </form>
                            </div>
                        @else
                            <div class="kls-pin-wrap">
                                <span class="kls-pin-none">Belum ada PIN</span>
                                <form method="POST" action="{{ route('class.reset-pin', $cls) }}{{ isset($orgId) && $orgId ? '?org_id=' . $orgId : '' }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="kls-btn--reset">⚿ Generate</button>
                                </form>
                            </div>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td>
                        <div class="kls-actions">
                            <button
                                type="button"
                                class="kls-btn kls-btn--ghost kls-btn--sm"
                                data-edit-id="{{ $cls->id }}"
                                aria-label="Edit kelas {{ $cls->name }}"
                            >
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>

                            <form
                                method="POST"
                                action="{{ route('class.destroy', $cls) }}{{ isset($orgId) && $orgId ? '?org_id=' . $orgId : '' }}"
                                data-confirm="Hapus kelas {{ $cls->name }}? Tindakan ini tidak dapat dibatalkan."
                            >
                                @csrf @method('DELETE')
                                <button
                                    type="submit"
                                    class="kls-btn kls-btn--danger kls-btn--sm"
                                    aria-label="Hapus kelas {{ $cls->name }}"
                                >
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="kls-empty">
                            <svg class="kls-empty__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                            <p class="kls-empty__text">Belum ada data kelas</p>
                        </div>
                    </td>
                </tr>
                @endforelse

                {{-- Row muncul saat hasil search kosong --}}
                <tr id="kls-empty-search" style="display:none">
                    <td colspan="7">
                        <div class="kls-empty">
                            <svg class="kls-empty__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <p class="kls-empty__text">Tidak ada kelas yang cocok</p>
                        </div>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>{{-- /kls-tbl-wrap --}}

    </div>{{-- /kls-card --}}

</div>{{-- /kls-wrap --}}

{{-- Toast notifikasi --}}
<div class="kls-toast" id="kls-toast" role="status" aria-live="polite"></div>

</x-app-layout>