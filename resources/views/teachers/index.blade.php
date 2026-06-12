<x-app-layout>
<x-slot name="title">Manajemen Guru</x-slot>

@vite(['resources/css/teacher.css'])

<div class="mg-wrap">

    {{-- FLASH --}}
    @if(session('success'))
        <div class="mg-flash mg-flash--ok">
            <i class="ti ti-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mg-flash mg-flash--err">
            <i class="ti ti-alert-triangle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="mg-page-header">
        <div>
            <h1 class="mg-page-title">Manajemen Guru</h1>
            <p class="mg-page-sub">Kelola data guru, token akses, dan status aktif</p>
        </div>
        <button class="mg-btn-primary" onclick="toggleAdd()">
            <i class="ti ti-plus"></i>
            Tambah Guru
        </button>
    </div>

    {{-- STATS --}}
    <div class="mg-stats">
        <div class="mg-stat">
            <div class="mg-stat-icon mg-stat-icon--all">
                <i class="ti ti-users"></i>
            </div>
            <div>
                <div class="mg-stat-val">{{ $teachers->count() }}</div>
                <div class="mg-stat-key">Total Guru</div>
            </div>
        </div>
        <div class="mg-stat">
            <div class="mg-stat-icon mg-stat-icon--active">
                <i class="ti ti-user-check"></i>
            </div>
            <div>
                <div class="mg-stat-val mg-stat-val--active">{{ $teachers->where('is_active', true)->count() }}</div>
                <div class="mg-stat-key">Aktif</div>
            </div>
        </div>
        <div class="mg-stat">
            <div class="mg-stat-icon mg-stat-icon--inactive">
                <i class="ti ti-user-off"></i>
            </div>
            <div>
                <div class="mg-stat-val mg-stat-val--inactive">{{ $teachers->where('is_active', false)->count() }}</div>
                <div class="mg-stat-key">Nonaktif</div>
            </div>
        </div>
    </div>

    {{-- FORM TAMBAH --}}
    <div class="mg-add-card" id="add-card">
        <button class="mg-add-header" onclick="toggleAdd()" type="button" aria-expanded="false" id="add-toggle-btn">
            <div class="mg-add-header-left">
                <span class="mg-add-icon"><i class="ti ti-user-plus"></i></span>
                <div>
                    <div class="mg-add-title">Tambah Guru Baru</div>
                    <div class="mg-add-sub">Token akan digenerate otomatis</div>
                </div>
            </div>
            <i class="ti ti-chevron-down mg-add-chevron" id="add-chev"></i>
        </button>
        <div class="mg-add-body" id="add-body">
            <form method="POST" action="{{ route('teacher.store') }}">
                @csrf
                <div class="mg-field-row">
                    <div class="mg-field">
                        <label class="mg-label">Nama Lengkap <span class="mg-required">*</span></label>
                        <input name="name" type="text" class="mg-input" placeholder="Contoh: Bu Husnul" required value="{{ old('name') }}">
                    </div>
                    <div class="mg-field">
                        <label class="mg-label">Nomor HP</label>
                        <input name="phone" type="text" class="mg-input" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="mg-add-footer">
                    <button type="submit" class="mg-btn-submit">
                        <i class="ti ti-check"></i>
                        Simpan Guru
                    </button>
                    <button type="button" class="mg-btn-cancel" onclick="toggleAdd()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="mg-table-card">
        <div class="mg-table-header">
            <div>
                <div class="mg-table-title">
                    <i class="ti ti-chalkboard-teacher"></i>
                    Daftar Guru
                </div>
                <div class="mg-table-count">{{ $teachers->count() }} guru terdaftar</div>
            </div>
            <div class="mg-table-search">
                <i class="ti ti-search"></i>
                <input type="text" id="search-input" placeholder="Cari nama atau token..." oninput="filterTable(this.value)">
            </div>
        </div>

        <div class="mg-table-wrap">
            <table class="mg-tbl" id="guru-table">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Guru</th>
                        <th>No. HP</th>
                        <th class="text-center">Jadwal</th>
                        <th class="text-center">Booking</th>
                        <th class="text-center">Tugas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $t)
                    @php
                        $initials = collect(explode(' ', $t->name))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
                        $colors = ['blue','green','amber','teal','coral','pink'];
                        $color = $colors[crc32($t->token) % count($colors)];
                    @endphp
                    <tr data-search="{{ strtolower($t->name . ' ' . $t->token) }}">
                        <td>
                            <span class="mg-token">
                                <i class="ti ti-key"></i>
                                {{ $t->token }}
                            </span>
                        </td>
                        <td>
                            <div class="mg-guru-cell">
                                <div class="mg-avatar mg-avatar--{{ $color }}">{{ $initials }}</div>
                                <div>
                                    <div class="mg-guru-name" id="name-display-{{ $t->id }}">{{ $t->name }}</div>
                                    {{-- Edit inline form --}}
                                    <div class="mg-edit-form" id="edit-form-{{ $t->id }}">
                                        <form method="POST" action="{{ route('teacher.update', $t) }}" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;margin-top:4px">
                                            @csrf @method('PATCH')
                                            <input type="text" name="name" class="mg-input-sm" value="{{ $t->name }}" placeholder="Nama" required style="width:130px">
                                            <input type="text" name="phone" class="mg-input-sm" value="{{ $t->phone }}" placeholder="No HP" style="width:110px">
                                            <input type="hidden" name="is_active" value="{{ $t->is_active ? 1 : 0 }}">
                                            <button type="submit" class="mg-btn-xs mg-btn-xs--save">Simpan</button>
                                            <button type="button" class="mg-btn-xs mg-btn-xs--cancel" onclick="cancelEdit({{ $t->id }})">Batal</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="mg-phone">{{ $t->phone ?? '—' }}</td>
                        <td class="text-center mg-count">{{ $t->schedules_count }}</td>
                        <td class="text-center mg-count">{{ $t->bookings_count }}</td>
                        <td class="text-center mg-count">{{ $t->assignments_count }}</td>
                        <td>
                            @if($t->is_active)
                                <span class="mg-badge mg-badge--active">
                                    <span class="mg-badge-dot"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="mg-badge mg-badge--inactive">
                                    <span class="mg-badge-dot"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="mg-actions">
                                <button class="mg-btn-action" onclick="toggleEdit({{ $t->id }})">
                                    <i class="ti ti-edit"></i>
                                    Edit
                                </button>

                                @if($t->phone)
                                @php
                                    $waPhone = preg_replace('/[^0-9]/', '', $t->phone);
                                    if(str_starts_with($waPhone, '0')) $waPhone = '62' . substr($waPhone, 1);
                                    $waMsg = "Halo " . $t->name . ", berikut adalah Token Guru Anda untuk mengelola tugas di Lab Management Nuris Jember: *" . $t->token . "*\n\nSilakan masuk melalui link berikut:\n" . route('assignment.admin', ['token' => $t->token]);
                                @endphp
                                <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($waMsg) }}"
                                   target="_blank"
                                   class="mg-btn-wa">
                                    <i class="ti ti-brand-whatsapp"></i>
                                    WA
                                </a>
                                @endif

                                @if($t->is_active)
                                <form method="POST" action="{{ route('teacher.destroy', $t) }}"
                                      onsubmit="return confirm('Nonaktifkan guru {{ addslashes($t->name) }}?')"
                                      style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="mg-btn-deact">Nonaktifkan</button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('teacher.update', $t) }}" style="display:inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="name" value="{{ $t->name }}">
                                    <input type="hidden" name="phone" value="{{ $t->phone }}">
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit" class="mg-btn-activate">Aktifkan</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="mg-empty">
                            <i class="ti ti-mood-empty"></i>
                            <div>Belum ada data guru</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer link --}}
    <div class="mg-footer">
        <a href="{{ route('assignment.admin') }}" class="mg-btn-panel">
            <i class="ti ti-clipboard-list"></i>
            Buka Panel Tugas
            <i class="ti ti-arrow-right"></i>
        </a>
    </div>

</div>

@vite(['resources/js/teacher.js'])

</x-app-layout>