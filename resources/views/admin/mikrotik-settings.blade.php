<x-app-layout>
<x-slot name="title">Pengaturan MikroTik</x-slot>

<style>
.mt-card { background:#fff; border:1px solid #cce4f0; border-radius:14px; margin-bottom:20px; overflow:hidden; box-shadow:0 1px 4px rgba(0,61,36,.05); }
.mt-card-hd { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 20px; border-bottom:1px solid #e8f4fb; }
.mt-card-title { font-family:Outfit,sans-serif; font-weight:700; font-size:15px; color:#0d2416; display:flex; align-items:center; gap:8px; }
.mt-card-body { padding:0; }
.mt-device-info { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:10px; padding:16px 20px; background:#f8fcff; border-bottom:1px solid #e8f4fb; }
.mt-info-item { display:flex; flex-direction:column; gap:2px; }
.mt-info-lbl { font-size:11px; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
.mt-info-val { font-size:13px; color:#0d2416; font-weight:500; font-family:'JetBrains Mono',monospace; }
.mt-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.mt-badge.on  { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
.mt-badge.off { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.mt-labs-grid { padding:16px 20px; display:grid; grid-template-columns:1fr 1fr; gap:12px; }
@media(max-width:640px){ .mt-labs-grid{ grid-template-columns:1fr; } .mt-device-info{ grid-template-columns:1fr 1fr; } }
.mt-lab-card { border:1px solid #cce4f0; border-radius:10px; padding:14px; background:#fafcff; position:relative; }
.mt-lab-card.inactive { opacity:.55; border-style:dashed; }
.mt-lab-key { font-family:'JetBrains Mono',monospace; font-weight:700; font-size:16px; color:#003d24; }
.mt-lab-res  { font-size:12px; color:#6b8fa3; margin-top:2px; }
.mt-lab-details { margin-top:10px; display:flex; flex-direction:column; gap:3px; }
.mt-lab-row { display:flex; justify-content:space-between; font-size:11px; }
.mt-lab-row span:first-child { color:#9ca3af; }
.mt-lab-row span:last-child { color:#374151; font-weight:500; font-family:'JetBrains Mono',monospace; }
.mt-lab-actions { display:flex; gap:6px; margin-top:10px; }
.mt-add-lab-slot { border:2px dashed #cce4f0; border-radius:10px; padding:20px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; background:#f5fafd; cursor:pointer; transition:border-color .15s, background .15s; }
.mt-add-lab-slot:hover { border-color:#B9D9EB; background:#eef6fb; }
.mt-add-lab-slot span { font-size:12px; color:#6b8fa3; font-weight:600; }
/* buttons */
.btn-primary { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; background:#003d24; color:#B9D9EB; border:none; cursor:pointer; transition:background .15s; }
.btn-primary:hover { background:#00693E; }
.btn-sm { padding:5px 12px; font-size:12px; border-radius:7px; font-weight:600; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:4px; transition:background .15s; }
.btn-edit   { background:#eff6ff; color:#1d4ed8; }
.btn-edit:hover { background:#dbeafe; }
.btn-del    { background:#fef2f2; color:#dc2626; }
.btn-del:hover  { background:#fee2e2; }
.btn-test   { background:#f0fdf4; color:#16a34a; }
.btn-test:hover { background:#dcfce7; }
/* modal */
.mt-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:900; align-items:center; justify-content:center; padding:16px; }
.mt-modal-overlay.open { display:flex; }
.mt-modal-box { background:#fff; border-radius:16px; width:100%; max-width:560px; box-shadow:0 20px 60px rgba(0,0,0,.25); overflow:hidden; max-height:90vh; display:flex; flex-direction:column; }
.mt-modal-hd { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid #e8f4fb; flex-shrink:0; }
.mt-modal-title { font-family:Outfit,sans-serif; font-weight:700; font-size:15px; color:#0d2416; }
.mt-modal-close { background:none; border:none; font-size:20px; color:#9ca3af; cursor:pointer; line-height:1; }
.mt-modal-body  { padding:20px 22px; overflow-y:auto; }
.mt-modal-footer{ padding:14px 22px; border-top:1px solid #e8f4fb; display:flex; justify-content:flex-end; gap:8px; flex-shrink:0; }
.mt-form-row  { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
.mt-form-grp  { margin-bottom:12px; }
.mt-form-row.single { grid-template-columns:1fr; }
@media(max-width:480px){ .mt-form-row{ grid-template-columns:1fr; } }
.mt-lbl { font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:4px; }
.mt-inp { border:1px solid #d1e9f6; border-radius:8px; padding:8px 12px; font-size:13px; color:#1a2e22; width:100%; outline:none; background:#fafcff; transition:border-color .2s; }
.mt-inp:focus { border-color:#B9D9EB; box-shadow:0 0 0 3px rgba(185,217,235,.2); }
.mt-inp-prefix { display:flex; border:1px solid #d1e9f6; border-radius:8px; overflow:hidden; background:#fafcff; }
.mt-inp-prefix span { padding:8px 10px; background:#f0f7fc; font-size:12px; color:#6b8fa3; border-right:1px solid #d1e9f6; white-space:nowrap; }
.mt-inp-prefix input { border:none; outline:none; padding:8px 12px; font-size:13px; flex:1; background:transparent; }
.mt-hint { font-size:11px; color:#9ca3af; margin-top:3px; }
/* empty state */
.mt-empty { text-align:center; padding:40px 20px; color:#9ca3af; }
.mt-empty-ic { font-size:32px; display:block; margin-bottom:8px; }
</style>

{{-- Page header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <div>
        <h1 style="font-family:Outfit,sans-serif;font-weight:800;font-size:22px;color:#0d2416">Pengaturan MikroTik</h1>
        <p style="font-size:13px;color:#6b8fa3;margin-top:2px">Kelola perangkat router dan mapping lab yang di-handle tiap perangkat (maks. 2 lab/perangkat)</p>
    </div>
    <button class="btn-primary" onclick="openModal('modal-add-device')">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Perangkat
    </button>
</div>

{{-- ── Daftar Devices ── --}}
@forelse($devices as $device)
<div class="mt-card">
    {{-- Header Device --}}
    <div class="mt-card-hd">
        <div class="mt-card-title">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#003d24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
            {{ $device->name }}
            <span class="mt-badge {{ $device->is_active ? 'on' : 'off' }}">
                {{ $device->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
            <span style="font-size:11px;color:#9ca3af;font-weight:400">{{ $device->labs->count() }}/2 lab</span>
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
            {{-- Test Connection --}}
            <form method="POST" action="{{ route('mikrotik.test', $device) }}">
                @csrf
                <button type="submit" class="btn-sm btn-test">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Test Koneksi
                </button>
            </form>
            {{-- Edit Device --}}
            <button class="btn-sm btn-edit" onclick="openEditDevice({{ $device->id }}, {{ $device->toJson() }})">
                ✏ Edit
            </button>
            {{-- Delete Device --}}
            <form method="POST" action="{{ route('mikrotik.device.destroy', $device) }}"
                  onsubmit="return confirm('Hapus perangkat {{ addslashes($device->name) }} beserta semua lab-nya?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-sm btn-del">🗑 Hapus</button>
            </form>
        </div>
    </div>

    {{-- Info Device --}}
    <div class="mt-device-info">
        <div class="mt-info-item">
            <span class="mt-info-lbl">Host / IP</span>
            <span class="mt-info-val">{{ $device->host }}</span>
        </div>
        <div class="mt-info-item">
            <span class="mt-info-lbl">Port</span>
            <span class="mt-info-val">{{ $device->port }}</span>
        </div>
        <div class="mt-info-item">
            <span class="mt-info-lbl">Username</span>
            <span class="mt-info-val">{{ $device->username }}</span>
        </div>
        <div class="mt-info-item">
            <span class="mt-info-lbl">Bot URL</span>
            <span class="mt-info-val" style="font-size:12px">{{ $device->bot_url }}</span>
        </div>
        <div class="mt-info-item">
            <span class="mt-info-lbl">Bot Token</span>
            <span class="mt-info-val">{{ $device->bot_token ? '••••••••' : '—' }}</span>
        </div>
        @if($device->notes)
        <div class="mt-info-item" style="grid-column:1/-1">
            <span class="mt-info-lbl">Catatan</span>
            <span class="mt-info-val" style="font-family:inherit;font-size:12px">{{ $device->notes }}</span>
        </div>
        @endif
    </div>

    {{-- Labs Grid --}}
    <div class="mt-labs-grid">
        @foreach($device->labs as $lab)
        @php
            $labTeknisi = $lab->resource
                ? $lab->resource->users->where('role', 'teknisi')
                : collect();
            // Teknisi yang belum di-assign ke lab ini (untuk dropdown)
            $assignedIds = $labTeknisi->pluck('id')->toArray();
            $availableTeknisi = $teknisiList->whereNotIn('id', $assignedIds);
        @endphp
        <div class="mt-lab-card {{ $lab->is_active ? '' : 'inactive' }}">
            <div style="display:flex;align-items:flex-start;justify-content:space-between">
                <div>
                    <div class="mt-lab-key">{{ $lab->lab_key }}</div>
                    <div class="mt-lab-res">{{ $lab->resource->name ?? '—' }}</div>
                </div>
                <span class="mt-badge {{ $lab->is_active ? 'on' : 'off' }}">{{ $lab->is_active ? 'Aktif' : 'Off' }}</span>
            </div>
            <div class="mt-lab-details">
                <div class="mt-lab-row"><span>Bot Lab ID</span><span>{{ $lab->bot_lab_id }}</span></div>
                @if($lab->network) <div class="mt-lab-row"><span>Network</span><span>{{ $lab->network }}</span></div> @endif
                @if($lab->vlan_id) <div class="mt-lab-row"><span>VLAN</span><span>{{ $lab->vlan_id }}</span></div> @endif
                @if($lab->interface) <div class="mt-lab-row"><span>Interface</span><span>{{ $lab->interface }}</span></div> @endif
                @if($lab->dhcp_server) <div class="mt-lab-row"><span>DHCP Server</span><span>{{ $lab->dhcp_server }}</span></div> @endif
                @if($lab->nat_comment) <div class="mt-lab-row"><span>NAT Comment</span><span>{{ $lab->nat_comment }}</span></div> @endif
            </div>

            {{-- ── Seksi Teknisi ── --}}
            <div style="margin-top:10px;padding-top:10px;border-top:1px dashed #cce4f0">
                <div style="font-size:11px;font-weight:700;color:#6b8fa3;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px">
                    👨‍🔧 Teknisi
                </div>

                {{-- Daftar teknisi yang sudah di-assign --}}
                @forelse($labTeknisi as $tek)
                <div style="display:flex;align-items:center;justify-content:space-between;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:7px;padding:5px 8px;margin-bottom:4px">
                    <div>
                        <span style="font-size:12px;font-weight:600;color:#0d2416">{{ $tek->full_name }}</span>
                        @if($tek->phone)
                        <span style="font-size:10px;color:#6b8fa3;margin-left:4px">{{ $tek->phone }}</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('mikrotik.lab.teknisi.unassign', $lab) }}">
                        @csrf @method('DELETE')
                        <input type="hidden" name="user_id" value="{{ $tek->id }}">
                        <button type="submit" title="Lepas teknisi"
                            style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:14px;padding:0 2px;line-height:1"
                            onclick="return confirm('Lepas {{ addslashes($tek->full_name) }} dari lab ini?')">×</button>
                    </form>
                </div>
                @empty
                <div style="font-size:11px;color:#9ca3af;font-style:italic;padding:2px 0">Belum ada teknisi</div>
                @endforelse

                {{-- Tambah teknisi (hanya jika belum 2 teknisi & masih ada pilihan) --}}
                @if($labTeknisi->count() < 2 && $availableTeknisi->count() > 0)
                <form method="POST" action="{{ route('mikrotik.lab.teknisi.assign', $lab) }}"
                      style="display:flex;gap:6px;margin-top:6px">
                    @csrf
                    <select name="user_id" class="mt-inp" style="flex:1;padding:5px 8px;font-size:12px" required>
                        <option value="">+ Assign teknisi...</option>
                        @foreach($availableTeknisi as $tek)
                        <option value="{{ $tek->id }}">
                            {{ $tek->full_name }}
                            @php
                                $labCount = $tek->resources()->count();
                            @endphp
                            ({{ $labCount }}/2 lab)
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-sm btn-test" style="white-space:nowrap">Assign</button>
                </form>
                @elseif($labTeknisi->count() >= 2)
                <div style="font-size:10px;color:#9ca3af;margin-top:4px">Maks. 2 teknisi per lab</div>
                @elseif($availableTeknisi->count() === 0 && $labTeknisi->count() < 2)
                <div style="font-size:10px;color:#9ca3af;margin-top:4px">
                    Semua teknisi sudah di-assign ke 2 lab
                </div>
                @endif
            </div>

            <div class="mt-lab-actions">
                <button class="btn-sm btn-edit" style="flex:1" onclick="openEditLab({{ $lab->id }}, {{ $lab->toJson() }})">✏ Edit</button>
                <form method="POST" action="{{ route('mikrotik.lab.destroy', $lab) }}" style="flex:1"
                      onsubmit="return confirm('Hapus lab {{ $lab->lab_key }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm btn-del" style="width:100%">🗑 Hapus</button>
                </form>
            </div>
        </div>
        @endforeach

        {{-- Slot tambah lab jika masih < 2 --}}
        @if($device->labs->count() < 2)
        <div class="mt-add-lab-slot" onclick="openAddLab({{ $device->id }}, '{{ addslashes($device->name) }}')">
            <span style="font-size:24px">➕</span>
            <span>Tambah Lab</span>
            <span style="font-size:10px;color:#b0c8d9">Slot {{ $device->labs->count() + 1 }}/2</span>
        </div>
        @endif
    </div>
</div>
@empty
<div class="mt-card">
    <div class="mt-empty">
        <span class="mt-empty-ic">🖥️</span>
        <p style="font-size:14px;font-weight:600;color:#374151">Belum ada perangkat MikroTik</p>
        <p style="font-size:12px;margin-top:4px">Klik "Tambah Perangkat" untuk mendaftarkan router pertama</p>
    </div>
</div>
@endforelse

{{-- ════════ MODAL: Tambah Device ════════ --}}
<div id="modal-add-device" class="mt-modal-overlay">
    <div class="mt-modal-box">
        <div class="mt-modal-hd">
            <div class="mt-modal-title">🖥️ Tambah Perangkat MikroTik</div>
            <button class="mt-modal-close" onclick="closeModal('modal-add-device')">×</button>
        </div>
        <form method="POST" action="{{ route('mikrotik.device.store') }}">
            @csrf
            <div class="mt-modal-body">
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Nama Perangkat <span style="color:#ef4444">*</span></label>
                        <input type="text" name="name" class="mt-inp" placeholder="MikroTik Gedung A" required>
                    </div>
                    <div>
                        <label class="mt-lbl">Host / IP Address <span style="color:#ef4444">*</span></label>
                        <input type="text" name="host" class="mt-inp" placeholder="192.168.1.1" required>
                    </div>
                </div>
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Port API <span style="color:#ef4444">*</span></label>
                        <input type="number" name="port" class="mt-inp" value="8728" min="1" max="65535" required>
                        <p class="mt-hint">Default Winbox API: 8728</p>
                    </div>
                    <div>
                        <label class="mt-lbl">Username <span style="color:#ef4444">*</span></label>
                        <input type="text" name="username" class="mt-inp" value="admin" required>
                    </div>
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">Password <span style="color:#ef4444">*</span></label>
                    <input type="password" name="password" class="mt-inp" placeholder="••••••••" required>
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">URL Bot Python <span style="color:#ef4444">*</span></label>
                    <input type="url" name="bot_url" class="mt-inp" placeholder="http://170.1.0.46:5000" required>
                    <p class="mt-hint">URL server bot Python yang mengelola MikroTik ini</p>
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">Bot Token (opsional)</label>
                    <input type="text" name="bot_token" class="mt-inp" placeholder="Bearer token untuk auth ke bot">
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">Catatan</label>
                    <input type="text" name="notes" class="mt-inp" placeholder="Opsional">
                </div>
            </div>
            <div class="mt-modal-footer">
                <button type="button" class="btn-sm btn-edit" onclick="closeModal('modal-add-device')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perangkat</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════ MODAL: Edit Device ════════ --}}
<div id="modal-edit-device" class="mt-modal-overlay">
    <div class="mt-modal-box">
        <div class="mt-modal-hd">
            <div class="mt-modal-title">✏ Edit Perangkat MikroTik</div>
            <button class="mt-modal-close" onclick="closeModal('modal-edit-device')">×</button>
        </div>
        <form id="form-edit-device" method="POST" action="">
            @csrf @method('PATCH')
            <div class="mt-modal-body">
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Nama Perangkat <span style="color:#ef4444">*</span></label>
                        <input type="text" name="name" id="ed-name" class="mt-inp" required>
                    </div>
                    <div>
                        <label class="mt-lbl">Host / IP Address <span style="color:#ef4444">*</span></label>
                        <input type="text" name="host" id="ed-host" class="mt-inp" required>
                    </div>
                </div>
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Port API <span style="color:#ef4444">*</span></label>
                        <input type="number" name="port" id="ed-port" class="mt-inp" min="1" max="65535" required>
                    </div>
                    <div>
                        <label class="mt-lbl">Username <span style="color:#ef4444">*</span></label>
                        <input type="text" name="username" id="ed-username" class="mt-inp" required>
                    </div>
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">Password</label>
                    <input type="password" name="password" class="mt-inp" placeholder="Kosongkan jika tidak ingin mengubah">
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">URL Bot Python <span style="color:#ef4444">*</span></label>
                    <input type="url" name="bot_url" id="ed-bot-url" class="mt-inp" required>
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">Bot Token (opsional)</label>
                    <input type="text" name="bot_token" id="ed-bot-token" class="mt-inp" placeholder="Kosongkan jika tidak menggunakan token">
                </div>
                <div class="mt-form-row">
                    <div class="mt-form-grp" style="margin:0">
                        <label class="mt-lbl">Status</label>
                        <label style="display:flex;align-items:center;gap:8px;margin-top:4px;cursor:pointer">
                            <input type="checkbox" name="is_active" id="ed-is-active" style="width:16px;height:16px;accent-color:#003d24">
                            <span style="font-size:13px;color:#374151">Perangkat Aktif</span>
                        </label>
                    </div>
                    <div class="mt-form-grp" style="margin:0">
                        <label class="mt-lbl">Catatan</label>
                        <input type="text" name="notes" id="ed-notes" class="mt-inp">
                    </div>
                </div>
            </div>
            <div class="mt-modal-footer">
                <button type="button" class="btn-sm btn-edit" onclick="closeModal('modal-edit-device')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════ MODAL: Tambah Lab ════════ --}}
<div id="modal-add-lab" class="mt-modal-overlay">
    <div class="mt-modal-box">
        <div class="mt-modal-hd">
            <div class="mt-modal-title">➕ Tambah Lab ke <span id="add-lab-device-name" style="color:#003d24"></span></div>
            <button class="mt-modal-close" onclick="closeModal('modal-add-lab')">×</button>
        </div>
        <form id="form-add-lab" method="POST" action="">
            @csrf
            <div class="mt-modal-body">
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Lab Key <span style="color:#ef4444">*</span></label>
                        <input type="text" name="lab_key" class="mt-inp" placeholder="lab7" required>
                        <p class="mt-hint">Huruf kecil, tanpa spasi, contoh: lab7, labsmp</p>
                    </div>
                    <div>
                        <label class="mt-lbl">Bot Lab ID <span style="color:#ef4444">*</span></label>
                        <input type="number" name="bot_lab_id" class="mt-inp" min="1" placeholder="1" required>
                        <p class="mt-hint">ID lab di bot Python</p>
                    </div>
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">Laboratorium (Resource) <span style="color:#ef4444">*</span></label>
                    <select name="resource_id" class="mt-inp" required>
                        <option value="">— Pilih Lab —</option>
                        @foreach($resources as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Interface MikroTik</label>
                        <input type="text" name="interface" class="mt-inp" placeholder="lab 7">
                    </div>
                    <div>
                        <label class="mt-lbl">NAT Comment</label>
                        <input type="text" name="nat_comment" class="mt-inp" placeholder="lab 7">
                    </div>
                </div>
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">DHCP Server</label>
                        <input type="text" name="dhcp_server" class="mt-inp" placeholder="dhcp2">
                    </div>
                    <div>
                        <label class="mt-lbl">VLAN ID</label>
                        <input type="number" name="vlan_id" class="mt-inp" min="1" max="4094" placeholder="77">
                    </div>
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">Network (CIDR)</label>
                    <input type="text" name="network" class="mt-inp" placeholder="192.168.70.0/24">
                </div>
            </div>
            <div class="mt-modal-footer">
                <button type="button" class="btn-sm btn-edit" onclick="closeModal('modal-add-lab')">Batal</button>
                <button type="submit" class="btn-primary">Tambah Lab</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════ MODAL: Edit Lab ════════ --}}
<div id="modal-edit-lab" class="mt-modal-overlay">
    <div class="mt-modal-box">
        <div class="mt-modal-hd">
            <div class="mt-modal-title">✏ Edit Lab</div>
            <button class="mt-modal-close" onclick="closeModal('modal-edit-lab')">×</button>
        </div>
        <form id="form-edit-lab" method="POST" action="">
            @csrf @method('PATCH')
            <div class="mt-modal-body">
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Lab Key <span style="color:#ef4444">*</span></label>
                        <input type="text" name="lab_key" id="el-lab-key" class="mt-inp" required>
                    </div>
                    <div>
                        <label class="mt-lbl">Bot Lab ID <span style="color:#ef4444">*</span></label>
                        <input type="number" name="bot_lab_id" id="el-bot-lab-id" class="mt-inp" min="1" required>
                    </div>
                </div>
                <div class="mt-form-grp">
                    <label class="mt-lbl">Laboratorium (Resource) <span style="color:#ef4444">*</span></label>
                    <select name="resource_id" id="el-resource-id" class="mt-inp" required>
                        <option value="">— Pilih Lab —</option>
                        @foreach($resources as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Interface MikroTik</label>
                        <input type="text" name="interface" id="el-interface" class="mt-inp">
                    </div>
                    <div>
                        <label class="mt-lbl">NAT Comment</label>
                        <input type="text" name="nat_comment" id="el-nat-comment" class="mt-inp">
                    </div>
                </div>
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">DHCP Server</label>
                        <input type="text" name="dhcp_server" id="el-dhcp-server" class="mt-inp">
                    </div>
                    <div>
                        <label class="mt-lbl">VLAN ID</label>
                        <input type="number" name="vlan_id" id="el-vlan-id" class="mt-inp" min="1" max="4094">
                    </div>
                </div>
                <div class="mt-form-row">
                    <div>
                        <label class="mt-lbl">Network (CIDR)</label>
                        <input type="text" name="network" id="el-network" class="mt-inp">
                    </div>
                    <div style="display:flex;align-items:flex-end;padding-bottom:4px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="is_active" id="el-is-active" style="width:16px;height:16px;accent-color:#003d24">
                            <span style="font-size:13px;color:#374151">Lab Aktif</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-modal-footer">
                <button type="button" class="btn-sm btn-edit" onclick="closeModal('modal-edit-lab')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

// Close on overlay click
document.querySelectorAll('.mt-modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});

// ── Edit Device ──
function openEditDevice(id, data) {
    const base = '{{ url("settings/mikrotik/devices") }}';
    document.getElementById('form-edit-device').action = `${base}/${id}`;
    document.getElementById('ed-name').value      = data.name      ?? '';
    document.getElementById('ed-host').value      = data.host      ?? '';
    document.getElementById('ed-port').value      = data.port      ?? 8728;
    document.getElementById('ed-username').value  = data.username  ?? '';
    document.getElementById('ed-bot-url').value   = data.bot_url   ?? '';
    document.getElementById('ed-bot-token').value = '';  // token disembunyikan
    document.getElementById('ed-notes').value     = data.notes     ?? '';
    document.getElementById('ed-is-active').checked = data.is_active == true || data.is_active == 1;
    openModal('modal-edit-device');
}

// ── Add Lab ──
function openAddLab(deviceId, deviceName) {
    const base = '{{ url("settings/mikrotik/devices") }}';
    document.getElementById('form-add-lab').action = `${base}/${deviceId}/labs`;
    document.getElementById('add-lab-device-name').textContent = deviceName;
    openModal('modal-add-lab');
}

// ── Edit Lab ──
function openEditLab(id, data) {
    const base = '{{ url("settings/mikrotik/labs") }}';
    document.getElementById('form-edit-lab').action    = `${base}/${id}`;
    document.getElementById('el-lab-key').value        = data.lab_key      ?? '';
    document.getElementById('el-bot-lab-id').value     = data.bot_lab_id   ?? '';
    document.getElementById('el-resource-id').value    = data.resource_id  ?? '';
    document.getElementById('el-interface').value      = data.interface    ?? '';
    document.getElementById('el-nat-comment').value    = data.nat_comment  ?? '';
    document.getElementById('el-dhcp-server').value    = data.dhcp_server  ?? '';
    document.getElementById('el-vlan-id').value        = data.vlan_id      ?? '';
    document.getElementById('el-network').value        = data.network      ?? '';
    document.getElementById('el-is-active').checked    = data.is_active == true || data.is_active == 1;
    openModal('modal-edit-lab');
}
</script>

</x-app-layout>
