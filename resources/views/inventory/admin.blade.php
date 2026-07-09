<x-app-layout>
<x-slot name="title">Kelola Inventaris</x-slot>

@push('styles')
    @vite('resources/css/inventoryadmin.css')
@endpush
@push('scripts')
    <script>
        window.IA_CONFIG = { initialLabId: {{ $resources->isNotEmpty() ? $resources->first()->id : 'null' }} };
    </script>
    @vite('resources/js/inventoryadmin.js')
@endpush

{{-- ── Toast container ── --}}
<div id="ia-toast-wrap"></div>

{{-- ── Flash session → dibaca JS sebagai toast ── --}}
@if(session('success'))
<div class="ia-flash-data" data-msg="{{ session('success') }}" data-type="ok" style="display:none"></div>
@endif
@if($errors->has('error'))
<div class="ia-flash-data" data-msg="{{ $errors->first('error') }}" data-type="err" style="display:none"></div>
@endif

{{-- ══ STATS ══ --}}
<div class="ia-stats">
    <div class="ia-stat s-total">
        <div class="ia-stat-icon">📦</div>
        <div class="ia-stat-lbl">Total Jenis</div>
        <div class="ia-stat-val">{{ $stats['total_items'] }}</div>
    </div>
    <div class="ia-stat s-units">
        <div class="ia-stat-icon">🗂</div>
        <div class="ia-stat-lbl">Total Unit</div>
        <div class="ia-stat-val">{{ $stats['total_units'] }}</div>
    </div>
    <div class="ia-stat s-good">
        <div class="ia-stat-icon">✅</div>
        <div class="ia-stat-lbl">Unit Baik</div>
        <div class="ia-stat-val">{{ $stats['total_good'] }}</div>
    </div>
    <div class="ia-stat s-broken">
        <div class="ia-stat-icon">⚠️</div>
        <div class="ia-stat-lbl">Unit Rusak</div>
        <div class="ia-stat-val">{{ $stats['total_broken'] }}</div>
    </div>
</div>

{{-- Tabs Lab --}}
<div class="tab-bar">
    @foreach($resources as $i => $lab)
        @php $cnt = $items->where('resource_id', $lab->id)->count(); @endphp
        <button class="tab-btn {{ $loop->first ? 'active' : '' }}"
                onclick="switchLab({{ $lab->id }}, this)">
            {{ $lab->name }}
            @if($cnt > 0)
                <span class="tab-count">{{ $cnt }}</span>
            @endif
        </button>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('inventory.admin') }}" class="ia-toolbar">
    <div class="ia-search-box">
        <span class="ia-search-icon">🔍</span>
        <input type="text" name="search" id="inv-search"
            value="{{ request('search') }}"
            placeholder="Cari nama, merk, spesifikasi..."
            class="ia-search-inp">
        <button type="button" class="ia-search-clear" id="inv-search-clear" aria-label="Hapus">×</button>
    </div>
    <select name="category" class="ia-select">
        <option value="">Semua Kategori</option>
        @foreach($categories as $k=>$v)
        <option value="{{ $k }}" {{ request('category')===$k?'selected':'' }}>{{ $v }}</option>
        @endforeach
    </select>
    <select name="condition" class="ia-select">
        <option value="">Semua Kondisi</option>
        @foreach($conditions as $k=>$v)
        <option value="{{ $k }}" {{ request('condition')===$k?'selected':'' }}>{{ $v }}</option>
        @endforeach
    </select>
    <button type="submit" class="ia-btn ia-btn-filter">Filter</button>
    @if(request()->hasAny(['search','category','condition']))
    <a href="{{ route('inventory.admin') }}" class="ia-btn-reset">× Reset</a>
    @endif
</form>

<div class="ia-actions-bar">
        <div class="ia-export-group">
            <a href="{{ route('inventory.report.pdf') }}?{{ http_build_query(request()->all()) }}" class="ia-btn-export pdf" title="Editor & Cetak PDF Laporan">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editor & Cetak PDF
            </a>
            <button id="ia-btn-export-excel" class="ia-btn-export excel" title="Export ke Excel">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </button>
            <a href="{{ route('inventory.maintenance.index') }}" class="ia-btn-maintenance" title="Lihat Log Perbaikan">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Log Perbaikan
            </a>
        </div>
        <button id="ia-btn-add" class="ia-btn ia-btn-add"><span>+</span> Tambah Barang</button>
    </div>

{{-- ══ SPLIT LAYOUT ══ --}}
<div class="ia-split">

    {{-- Tabel kiri --}}
    <div class="ia-table-box">
        @foreach($resources as $lab)
        @php
            $labItems = $items->where('resource_id', $lab->id)->values();
            $isFirst = $loop->first;
        @endphp
        <div class="lab-panel" data-lab-id="{{ $lab->id }}" style="display:{{ $isFirst ? 'block' : 'none' }}">
            <div class="ia-table-head">
                <span class="ia-table-title">Daftar Inventaris - {{ $lab->name }}</span>
                <span class="ia-table-count">{{ $labItems->count() }} item</span>
            </div>

            @if($labItems->isEmpty())
            <div class="ia-detail-empty" style="padding:48px 24px">
                <span class="ia-detail-empty-ic">📦</span>
                <p>Belum ada data inventaris untuk lab ini</p>
            </div>
            @else
            <div class="ia-table-wrap">
                <table class="ia-table">
                    <thead>
                        <tr>
                            <th class="sortable" data-col="name">
                                <span class="ia-th-inner">Nama Barang <span class="ia-sort-icon">⇅</span></span>
                            </th>
                            <th>Kategori</th>
                            <th class="sortable" data-col="brand">
                                <span class="ia-th-inner">Merk <span class="ia-sort-icon">⇅</span></span>
                            </th>
                            <th class="tc sortable" data-col="qty">
                                <span class="ia-th-inner">Total <span class="ia-sort-icon">⇅</span></span>
                            </th>
                            <th class="tc sortable" data-col="good">
                                <span class="ia-th-inner">Baik <span class="ia-sort-icon">⇅</span></span>
                            </th>
                            <th class="tc sortable" data-col="broken">
                                <span class="ia-th-inner">Rusak <span class="ia-sort-icon">⇅</span></span>
                            </th>
                            <th class="tc">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labItems as $item)
                        <tr class="ia-row"
                            onclick="if(window.iaSelectRow){window.iaSelectRow(this)}else{console.error('iaSelectRow not loaded yet')}"
                            data-id="{{ $item->id }}"
                            data-name="{{ e($item->item_name) }}"
                            data-lab="{{ e($item->resource->name ?? '') }}"
                            data-category="{{ $item->category }}"
                            data-condition="{{ $item->condition }}"
                            data-brand="{{ e($item->brand ?? '') }}"
                            data-model="{{ e($item->model ?? '') }}"
                            data-serial="{{ e($item->serial_number ?? '') }}"
                            data-specs="{{ e($item->specifications ?? '') }}"
                            data-qty="{{ $item->quantity }}"
                            data-good="{{ $item->quantity_good }}"
                            data-broken="{{ $item->quantity_broken }}"
                            data-backup="{{ $item->quantity_backup }}"
                            data-notes="{{ e($item->notes ?? '') }}"
                        >
                            <td>
                                <div class="ia-name">{{ $item->item_name }}</div>
                                @if($item->specifications)
                                <div class="ia-spec" title="{{ $item->specifications }}">{{ $item->specifications }}</div>
                                @endif
                            </td>
                            <td><span class="ia-badge cat-{{ $item->category }}">{{ $categories[$item->category] ?? $item->category }}</span></td>
                            <td>
                                <div class="ia-brand">{{ $item->brand ?? '–' }}</div>
                                @if($item->model)<div class="ia-model">{{ $item->model }}</div>@endif
                            </td>
                            <td class="tc"><span class="ia-num ia-nt">{{ $item->quantity }}</span></td>
                            <td class="tc"><span class="ia-num ia-ng">{{ $item->quantity_good }}</span></td>
                            <td class="tc"><span class="ia-num ia-nb {{ $item->quantity_broken>0?'bad':'' }}">{{ $item->quantity_broken }}</span></td>
                            <td class="tc"><span class="ia-badge cond-{{ $item->condition }}">{{ $conditions[$item->condition] ?? $item->condition }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endforeach

    </div>

    {{-- ══ DETAIL CARD kanan ══ --}}
    <div class="ia-detail-card" id="ia-detail-card" data-quick-base="/inventaris-admin">
        <div class="ia-detail-empty">
            <span class="ia-detail-empty-ic">👆</span>
            <p>Klik salah satu baris<br>untuk melihat detail barang</p>
        </div>

        <div class="ia-detail-hd">
            <div class="ia-detail-badge-row">
                <span id="ia-d-cat"  class="ia-badge"></span>
                <span id="ia-d-cond" class="ia-badge"></span>
            </div>
            <div class="ia-detail-item-name" id="ia-d-name"></div>
            <div class="ia-detail-item-lab"  id="ia-d-lab"></div>
        </div>

        <div class="ia-detail-body">
            {{-- Qty boxes --}}
            <div class="ia-qty-row">
                <div class="ia-qty-box qb-total">
                    <div class="ia-qty-box-val" id="ia-d-qty">0</div>
                    <div class="ia-qty-box-lbl">Total</div>
                </div>
                <div class="ia-qty-box qb-good">
                    <div class="ia-qty-box-val" id="ia-d-good">0</div>
                    <div class="ia-qty-box-lbl">Baik</div>
                </div>
                <div class="ia-qty-box qb-broken" id="ia-d-broken-box">
                    <div class="ia-qty-box-val" id="ia-d-broken">0</div>
                    <div class="ia-qty-box-lbl">Rusak</div>
                </div>
                <div class="ia-qty-box qb-backup">
                    <div class="ia-qty-box-val" id="ia-d-backup">0</div>
                    <div class="ia-qty-box-lbl">Cadangan</div>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="ia-progress-wrap">
                <div class="ia-progress-lbl">
                    <span class="ia-progress-lbl-text">Kondisi Unit</span>
                    <span class="ia-progress-lbl-pct" id="ia-d-pct">–</span>
                </div>
                <div class="ia-progress-track">
                    <div class="ia-progress-bar" id="ia-d-bar" style="width:0%"></div>
                </div>
            </div>

            <div class="ia-detail-divider"></div>

            <div class="ia-field">
                <div class="ia-field-lbl">Merk / Brand</div>
                <div class="ia-field-val" id="ia-d-brand">–</div>
            </div>
            <div class="ia-field">
                <div class="ia-field-lbl">Model / Tipe</div>
                <div class="ia-field-val" id="ia-d-model">–</div>
            </div>
            <div class="ia-field">
                <div class="ia-field-lbl">No. Seri</div>
                <div class="ia-field-val" id="ia-d-serial">–</div>
            </div>
            <div class="ia-field">
                <div class="ia-field-lbl">Spesifikasi</div>
                <div class="ia-field-val" id="ia-d-specs">–</div>
            </div>
            <div class="ia-field">
                <div class="ia-field-lbl">Catatan</div>
                <div class="ia-field-val" id="ia-d-notes">–</div>
            </div>

            {{-- ── Quick Actions ── --}}
            <div class="ia-quick-panel">
                <div class="ia-quick-title">Aksi Cepat</div>

                {{-- Tandai Rusak --}}
                <div class="ia-quick-action" id="qa-broken-wrap">
                    <div class="ia-quick-action-lbl">
                        <span class="ia-quick-icon">⚠️</span>
                        <div>
                            <div class="ia-quick-name">Tandai Rusak</div>
                            <div class="ia-quick-desc">Pindah dari Baik → Rusak</div>
                        </div>
                    </div>
                    <div class="ia-quick-ctrl">
                        <button class="ia-qc-btn" id="qa-broken-dec">−</button>
                        <span class="ia-qc-val" id="qa-broken-val">0</span>
                        <button class="ia-qc-btn" id="qa-broken-inc">+</button>
                    </div>
                </div>

                {{-- Gunakan Cadangan --}}
                <div class="ia-quick-action" id="qa-backup-wrap">
                    <div class="ia-quick-action-lbl">
                        <span class="ia-quick-icon">🔄</span>
                        <div>
                            <div class="ia-quick-name">Gunakan Cadangan</div>
                            <div class="ia-quick-desc">Cadangan → Baik</div>
                        </div>
                    </div>
                    <div class="ia-quick-ctrl">
                        <button class="ia-qc-btn" id="qa-backup-dec">−</button>
                        <span class="ia-qc-val" id="qa-backup-val">0</span>
                        <button class="ia-qc-btn" id="qa-backup-inc">+</button>
                    </div>
                </div>

                {{-- Barang Diperbaiki --}}
                <div class="ia-quick-action" id="qa-fixed-wrap">
                    <div class="ia-quick-action-lbl">
                        <span class="ia-quick-icon">✅</span>
                        <div>
                            <div class="ia-quick-name">Barang Diperbaiki</div>
                            <div class="ia-quick-desc">Rusak → Baik</div>
                        </div>
                    </div>
                    <div class="ia-quick-ctrl">
                        <button class="ia-qc-btn" id="qa-fixed-dec">−</button>
                        <span class="ia-qc-val" id="qa-fixed-val">0</span>
                        <button class="ia-qc-btn" id="qa-fixed-inc">+</button>
                    </div>
                </div>

                {{-- Preview perubahan --}}
                <div class="ia-quick-preview" id="qa-preview" style="display:none">
                    <div class="ia-quick-preview-row">
                        <span>Baik</span>
                        <span><span id="qa-prev-good-old" class="ia-prev-old"></span> → <strong id="qa-prev-good-new"></strong></span>
                    </div>
                    <div class="ia-quick-preview-row">
                        <span>Rusak</span>
                        <span><span id="qa-prev-broken-old" class="ia-prev-old"></span> → <strong id="qa-prev-broken-new"></strong></span>
                    </div>
                    <div class="ia-quick-preview-row">
                        <span>Cadangan</span>
                        <span><span id="qa-prev-backup-old" class="ia-prev-old"></span> → <strong id="qa-prev-backup-new"></strong></span>
                    </div>
                </div>

                <button class="ia-quick-save" id="qa-save-btn" disabled>
                    Simpan Perubahan
                </button>
            </div>

            <div class="ia-detail-divider"></div>

            <div class="ia-detail-actions">
                <button id="ia-d-btn-maintenance" class="ia-detail-btn-edit" style="background:#0ea5e9;border-color:#0ea5e9;color:#fff;margin-bottom:8px;width:100%;display:flex;align-items:center;justify-content:center;gap:8px">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Catat Perbaikan
                </button>
                <button id="ia-d-btn-edit" class="ia-detail-btn-edit">✏ Edit Barang</button>
                <button id="ia-d-btn-del"  class="ia-detail-btn-del">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </div>
        </div>
    </div>

</div>{{-- end .ia-split --}}


{{-- ══════════════ MODAL KONFIRMASI HAPUS ══════════════ --}}
<div id="confirm-del-modal" class="ia-modal-overlay">
    <div class="ia-confirm-box">
        <span class="ia-confirm-icon">🗑️</span>
        <div class="ia-confirm-title">Hapus Barang?</div>
        <div class="ia-confirm-msg">
            Barang <span class="ia-confirm-name" id="ia-confirm-item-name"></span>
            akan dihapus permanen dan tidak bisa dikembalikan.
        </div>
        <form id="ia-del-form" method="POST" action="">
            @csrf @method('DELETE')
            <div class="ia-confirm-btns">
                <button type="button" id="ia-cancel-confirm" class="ia-confirm-cancel">Batal</button>
                <button type="submit" class="ia-confirm-del">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════ MODAL TAMBAH ══════════════ --}}
<div id="add-modal" class="ia-modal-overlay">
    <div class="ia-modal-box">
        <div class="ia-modal-hd">
            <div>
                <div class="ia-modal-title">Tambah Barang</div>
                <div class="ia-modal-sub">Data inventaris laboratorium</div>
            </div>
            <button id="ia-close-add" class="ia-modal-close" aria-label="Tutup">×</button>
        </div>
        <form method="POST" action="{{ route('inventory.admin.store') }}" class="ia-modal-body">
            @csrf
            <div class="ia-form-row">
                <div>
                    <label class="ia-lbl">Laboratorium <span>*</span></label>
                    <select name="resource_id" class="ia-inp" required>
                        <option value="">— Pilih Lab —</option>
                        @foreach($resources as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ia-lbl">Kategori <span>*</span></label>
                    <select name="category" class="ia-inp" required>
                        <option value="">— Pilih —</option>
                        @foreach($categories as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="ia-form-grp">
                <label class="ia-lbl">Nama Barang <span>*</span></label>
                <input type="text" name="item_name" id="add-item-name" class="ia-inp" required placeholder="Contoh: Komputer PC">
            </div>
            <div class="ia-form-row">
                <div>
                    <label class="ia-lbl">Merk / Brand</label>
                    <input type="text" name="brand" class="ia-inp" placeholder="Contoh: DELL">
                </div>
                <div>
                    <label class="ia-lbl">Model / Tipe</label>
                    <input type="text" name="model" class="ia-inp" placeholder="Contoh: Optiplex 3060">
                </div>
            </div>
            <div class="ia-form-grp">
                <label class="ia-lbl">Spesifikasi</label>
                <input type="text" name="specifications" class="ia-inp" placeholder="Contoh: Intel i5, RAM 8GB, SSD 256GB">
            </div>
            <div class="ia-form-row">
                <div>
                    <label class="ia-lbl">No. Seri</label>
                    <input type="text" name="serial_number" class="ia-inp" placeholder="Opsional">
                </div>
                <div>
                    <label class="ia-lbl">Kondisi <span>*</span></label>
                    <select name="condition" class="ia-inp" required>
                        @foreach($conditions as $k=>$v)
                        <option value="{{ $k }}" {{ $k==='good'?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="ia-divider"></div>
            <div class="ia-form-row-4">
                <div><label class="ia-lbl">Total</label><input type="number" name="quantity" id="add-qty" class="ia-inp" value="1" min="0" required></div>
                <div><label class="ia-lbl">Baik</label><input type="number" name="quantity_good" id="add-good" class="ia-inp" value="1" min="0" required></div>
                <div><label class="ia-lbl">Rusak</label><input type="number" name="quantity_broken" id="add-broken" class="ia-inp" value="0" min="0" required></div>
                <div><label class="ia-lbl">Cadangan</label><input type="number" name="quantity_backup" class="ia-inp" value="0" min="0" required></div>
            </div>
            <div class="ia-qty-hint">* Rusak dihitung otomatis dari Total − Baik.</div>
            <div class="ia-form-grp">
                <label class="ia-lbl">Catatan</label>
                <input type="text" name="notes" class="ia-inp" placeholder="Opsional">
            </div>
            <div class="ia-modal-footer">
                <button type="button" id="ia-cancel-add" class="ia-btn-cancel">Batal</button>
                <button type="submit" class="ia-btn-save">+ Simpan Barang</button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════ MODAL EDIT ══════════════ --}}
<div id="edit-modal" class="ia-modal-overlay">
    <div class="ia-modal-box">
        <div class="ia-modal-hd">
            <div>
                <div class="ia-modal-title">Edit Barang</div>
                <div class="ia-modal-sub">Perbarui data inventaris</div>
            </div>
            <button id="ia-close-edit" class="ia-modal-close" aria-label="Tutup">×</button>
        </div>
        <form id="edit-form" method="POST" class="ia-modal-body">
            @csrf @method('PATCH')
            <div class="ia-form-row">
                <div>
                    <label class="ia-lbl">Kategori <span>*</span></label>
                    <select name="category" id="e-category" class="ia-inp" required>
                        @foreach($categories as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ia-lbl">Kondisi <span>*</span></label>
                    <select name="condition" id="e-condition" class="ia-inp" required>
                        @foreach($conditions as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="ia-form-grp">
                <label class="ia-lbl">Nama Barang <span>*</span></label>
                <input type="text" name="item_name" id="e-name" class="ia-inp" required>
            </div>
            <div class="ia-form-row">
                <div><label class="ia-lbl">Merk / Brand</label><input type="text" name="brand" id="e-brand" class="ia-inp"></div>
                <div><label class="ia-lbl">Model / Tipe</label><input type="text" name="model" id="e-model" class="ia-inp"></div>
            </div>
            <div class="ia-form-grp">
                <label class="ia-lbl">Spesifikasi</label>
                <input type="text" name="specifications" id="e-specs" class="ia-inp">
            </div>
            <div class="ia-form-grp">
                <label class="ia-lbl">No. Seri</label>
                <input type="text" name="serial_number" id="e-serial" class="ia-inp">
            </div>
            <div class="ia-divider"></div>
            <div class="ia-form-row-4">
                <div><label class="ia-lbl">Total</label><input type="number" name="quantity" id="e-qty" class="ia-inp" min="0" required></div>
                <div><label class="ia-lbl">Baik</label><input type="number" name="quantity_good" id="e-good" class="ia-inp" min="0" required></div>
                <div><label class="ia-lbl">Rusak</label><input type="number" name="quantity_broken" id="e-broken" class="ia-inp" min="0" required></div>
                <div><label class="ia-lbl">Cadangan</label><input type="number" name="quantity_backup" id="e-backup" class="ia-inp" min="0" required></div>
            </div>
            <div class="ia-qty-hint">* Rusak dihitung otomatis dari Total − Baik.</div>
            <div class="ia-form-grp">
                <label class="ia-lbl">Catatan</label>
                <input type="text" name="notes" id="e-notes" class="ia-inp">
            </div>
            <div class="ia-modal-footer">
                <button type="button" id="ia-cancel-edit" class="ia-btn-cancel">Batal</button>
                <button type="submit" class="ia-btn-save">✓ Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════ MODAL LOG PERBAIKAN ══════════════ --}}
<div id="maintenance-modal" class="ia-modal-overlay">
    <div class="ia-modal-box">
        <div class="ia-modal-hd">
            <div>
                <div class="ia-modal-title">Catat Log Perbaikan</div>
                <div class="ia-modal-sub" id="m-item-name-sub">Barang</div>
            </div>
            <button id="ia-close-maintenance" class="ia-modal-close" aria-label="Tutup">×</button>
        </div>
        <form method="POST" action="{{ route('inventory.maintenance.store') }}" class="ia-modal-body">
            @csrf
            <input type="hidden" name="lab_inventory_id" id="m-inventory-id">
            
            <div class="ia-form-row">
                <div>
                    <label class="ia-lbl">Tanggal Perbaikan <span>*</span></label>
                    <input type="date" name="maintenance_date" class="ia-inp" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="ia-lbl">Jenis Perbaikan <span>*</span></label>
                    <select name="maintenance_type" class="ia-inp" required>
                        <option value="Perbaikan">Perbaikan</option>
                        <option value="Penggantian">Penggantian</option>
                        <option value="Perawatan Rutin">Perawatan Rutin</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="ia-form-grp">
                <label class="ia-lbl">Deskripsi Kerusakan / Tindakan <span>*</span></label>
                <textarea name="description" class="ia-inp" rows="3" required placeholder="Jelaskan apa yang rusak dan apa yang diperbaiki..."></textarea>
            </div>

            <div class="ia-form-row">
                <div>
                    <label class="ia-lbl">Biaya (Rp)</label>
                    <input type="number" name="cost" class="ia-inp" value="0" min="0">
                </div>
                <div>
                    <label class="ia-lbl">Status <span>*</span></label>
                    <select name="status" class="ia-inp" required>
                        <option value="Selesai">Selesai</option>
                        <option value="Menunggu Suku Cadang">Menunggu Suku Cadang</option>
                        <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                    </select>
                </div>
            </div>

            <div class="ia-divider"></div>
            
            <div class="ia-form-grp">
                <label class="ia-lbl">Update Inventaris Otomatis?</label>
                <div style="display:flex;align-items:center;gap:12px;margin-top:8px">
                    <input type="number" name="fix_quantity" class="ia-inp" value="0" min="0" style="width:80px">
                    <span class="text-xs text-gray-500">Unit berhasil diperbaiki (Pindah dari <b>Rusak</b> ke <b>Baik</b>)</span>
                </div>
            </div>

            <div class="ia-modal-footer">
                <button type="button" id="ia-cancel-maintenance" class="ia-btn-cancel">Batal</button>
                <button type="submit" class="ia-btn-save">✓ Simpan Log</button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>