<x-app-layout>
<x-slot name="title">Manajemen Kelas</x-slot>

<style>
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.wrap{animation:fadeUp .4s ease both}
.add-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden;margin-bottom:20px}
.add-head{padding:14px 20px;background:linear-gradient(135deg,#1A2517,#2a3826);display:flex;align-items:center;justify-content:space-between;cursor:pointer}
.add-head-title{font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:15px;color:#fff}
.add-toggle{color:#ACC8A2;transition:transform .2s}
.add-toggle.open{transform:rotate(180deg)}
.add-body{padding:20px;display:none}
.add-body.open{display:block}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:500px){.field-row{grid-template-columns:1fr}}
.field{margin-bottom:14px}
.field-label{display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.inp{width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;font-size:13px;font-family:inherit;background:#fafcf9;outline:none;transition:border-color .15s}
.inp:focus{border-color:#ACC8A2;box-shadow:0 0 0 3px rgba(172,200,162,.1)}
.btn-add{padding:10px 22px;border-radius:10px;border:none;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;transition:transform .15s,box-shadow .15s}
.btn-add:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(26,37,23,.25)}
.tbl-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden}
.tbl-head{padding:14px 20px;border-bottom:1px solid #e8f0e6;display:flex;align-items:center;justify-content:space-between}
.tbl-head-title{font-size:13px;font-weight:700;color:#1A2517}
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:12px;min-width:700px}
thead tr{background:#f8faf7;border-bottom:2px solid #e8f0e6}
thead th{padding:9px 14px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;white-space:nowrap}
tbody tr{border-top:1px solid #f5f5f5;transition:background .1s}
tbody tr:hover{background:#fafcf9}
tbody td{padding:11px 14px;vertical-align:middle}
.edit-form{display:none;gap:6px;align-items:center;flex-wrap:wrap;margin-top:6px}
.edit-form.show{display:flex}
.inp-sm{border:1.5px solid #e5e7eb;border-radius:7px;padding:5px 9px;font-size:12px;font-family:inherit;outline:none;background:#fafcf9;transition:border-color .15s}
.btn-sm{padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:background .15s}
.btn-save{background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2}
.btn-cancel{background:#f3f4f6;color:#6b7280}
.btn-edit{background:#f0f4ef;color:#1A2517;border:1px solid #e8f0e6;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer}
.btn-delete{background:#fef2f2;color:#ef4444;border:1px solid #fecaca;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer}

/* ─── PIN STYLES ─────────────────────── */
.pin-badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-family: 'Plus Jakarta Sans', monospace; font-weight: 700;
    font-size: 14px; letter-spacing: .12em;
    color: #1A2517; background: #f0f7f0;
    border: 1.5px solid #c8e0c8; border-radius: 8px;
    padding: 4px 10px;
}
.pin-badge.no-pin {
    color: #9ca3af; background: #f9fafb;
    border-color: #e5e7eb; font-size: 11px;
    letter-spacing: 0; font-weight: 500;
}
.btn-copy {
    background: none; border: none; cursor: pointer;
    color: #9ca3af; padding: 2px; border-radius: 4px;
    transition: color .15s;
}
.btn-copy:hover { color: #3d6b3d; }
.btn-reset {
    background: #fff8e6; color: #d97706;
    border: 1px solid #fde68a;
    padding: 4px 9px; border-radius: 7px;
    font-size: 10px; font-weight: 700; cursor: pointer;
    font-family: inherit; transition: background .15s;
    white-space: nowrap;
}
.btn-reset:hover { background: #fef3c7; }

/* Toast copy */
.copy-toast {
    position: fixed; bottom: 24px; left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: #1A2517; color: #ACC8A2;
    padding: 9px 18px; border-radius: 10px;
    font-size: 13px; font-weight: 600;
    box-shadow: 0 4px 20px rgba(0,0,0,.2);
    opacity: 0; transition: opacity .25s, transform .25s;
    pointer-events: none; z-index: 9999;
}
.copy-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>

<div class="wrap">
    {{-- SEARCH & FILTER --}}
    <div style="margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap">
        <div style="flex:1;min-width:260px;position:relative">
            <input type="text" id="cls-search" onkeyup="filterClasses()" placeholder="Cari nama kelas atau jurusan..."
                   style="width:100%;padding:10px 16px 10px 40px;border-radius:12px;border:1.5px solid #e8f0e6;background:#fff;font-size:13px;outline:none;transition:border-color .15s">
            <svg style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <select id="school-filter" onchange="filterClasses()"
                style="padding:10px 16px;border-radius:12px;border:1.5px solid #e8f0e6;background:#fff;font-size:13px;outline:none;color:#1A2517">
            <option value="">Semua Sekolah</option>
            @foreach($organizations as $org)
            <option value="{{ $org->id }}">{{ $org->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- FORM TAMBAH --}}
    <div class="add-card">
        <div class="add-head" onclick="toggleAdd()">
            <div class="add-head-title">➕ Tambah Kelas Baru</div>
            <svg class="add-toggle" id="add-toggle" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div class="add-body" id="add-body">
            <form method="POST" action="{{ route('class.store') }}">
                @csrf
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Sekolah / Lembaga *</label>
                        <select name="organization_id" class="inp" required>
                            <option value="">Pilih Sekolah</option>
                            @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label class="field-label">Tingkat *</label>
                        <input name="grade_level" type="text" class="inp" placeholder="Contoh: X, XI, XII atau 7, 8, 9" required>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Nama Kelas *</label>
                        <input name="name" type="text" class="inp" placeholder="Contoh: TKJ 1, RPL 2" required>
                    </div>
                    <div class="field">
                        <label class="field-label">Jurusan</label>
                        <input name="major" type="text" class="inp" placeholder="Contoh: Teknik Komputer dan Jaringan">
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Jumlah Siswa</label>
                        <input name="student_count" type="number" class="inp" placeholder="36">
                    </div>
                    <div class="field">
                        <label class="field-label">Tahun Akademik *</label>
                        <input name="academic_year" type="text" class="inp" placeholder="2023/2024" required value="{{ date('Y').'/'.(date('Y')+1) }}">
                    </div>
                </div>
                <div style="background:#f0f9f4;border:1px solid #c8e0c8;border-radius:10px;padding:10px 14px;font-size:12px;color:#3d6b3d;margin-bottom:14px">
                    🔑 PIN kelas akan di-generate otomatis setelah kelas ditambahkan.
                </div>
                <button type="submit" class="btn-add">✓ Tambah Kelas</button>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="tbl-card">
        <div class="tbl-head">
            <span class="tbl-head-title">🏫 Daftar Kelas</span>
            <span style="font-size:11px;color:#9ca3af">PIN digunakan siswa untuk mengakses halaman tugas</span>
        </div>
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Sekolah</th>
                        <th>Kelas</th>
                        <th>Tingkat/Jurusan</th>
                        <th>Siswa</th>
                        <th>PIN Tugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="cls-tbody">
                    @forelse($classes as $idx => $cls)
                    <tr class="cls-row"
                        data-school-id="{{ $cls->organization_id }}"
                        data-search="{{ strtolower($cls->name . ' ' . $cls->major . ' ' . ($cls->organization->name ?? '')) }}">
                        <td>{{ $idx + 1 }}</td>
                        <td><span style="font-weight:600;color:#1A2517">{{ $cls->organization->name ?? '-' }}</span></td>
                        <td>
                            <div style="font-weight:700">{{ $cls->name }}</div>
                            <div id="edit-form-{{ $cls->id }}" class="edit-form">
                                <form method="POST" action="{{ route('class.update', $cls) }}" style="display:contents">
                                    @csrf @method('PATCH')
                                    <select name="organization_id" class="inp-sm" required style="width:140px">
                                        @foreach($organizations as $org)
                                        <option value="{{ $org->id }}" {{ $cls->organization_id == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="name" class="inp-sm" value="{{ $cls->name }}" style="width:100px" required>
                                    <input type="text" name="grade_level" class="inp-sm" value="{{ $cls->grade_level }}" style="width:60px" required>
                                    <input type="text" name="major" class="inp-sm" value="{{ $cls->major }}" placeholder="Jurusan" style="width:100px">
                                    <input type="number" name="student_count" class="inp-sm" value="{{ $cls->student_count }}" style="width:60px">
                                    <input type="text" name="academic_year" class="inp-sm" value="{{ $cls->academic_year }}" style="width:90px" required>
                                    <button type="submit" class="btn-sm btn-save">Simpan</button>
                                    <button type="button" class="btn-sm btn-cancel" onclick="cancelEdit({{ $cls->id }})">Batal</button>
                                </form>
                            </div>
                        </td>
                        <td style="color:#6b7280">{{ $cls->grade_level }} {{ $cls->major }}</td>
                        <td style="font-weight:700">{{ $cls->student_count ?? 0 }} <small style="font-weight:400;color:#9ca3af">siswa</small></td>

                        {{-- ─── KOLOM PIN ─── --}}
                        <td>
                            @if($cls->pin)
                            <div style="display:flex;align-items:center;gap:6px">
                                <span class="pin-badge" id="pin-{{ $cls->id }}">{{ $cls->pin }}</span>
                                {{-- Tombol copy --}}
                                <button class="btn-copy" onclick="copyPin('{{ $cls->pin }}')" title="Salin PIN">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                {{-- Tombol reset PIN --}}
                                <form method="POST" action="{{ route('class.reset-pin', $cls) }}"
                                      onsubmit="return confirm('Reset PIN kelas {{ $cls->name }}? PIN lama tidak bisa dipakai lagi.')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-reset" title="Generate PIN baru">🔄 Reset</button>
                                </form>
                            </div>
                            @else
                            <div style="display:flex;align-items:center;gap:6px">
                                <span class="pin-badge no-pin">Belum ada PIN</span>
                                <form method="POST" action="{{ route('class.reset-pin', $cls) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-reset">🔑 Generate</button>
                                </form>
                            </div>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex;gap:5px">
                                <button class="btn-edit" onclick="toggleEdit({{ $cls->id }})">Edit</button>
                                <form method="POST" action="{{ route('class.destroy', $cls) }}" onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:#9ca3af">Belum ada data kelas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Toast copy --}}
<div class="copy-toast" id="copy-toast">✓ PIN disalin</div>

<script>
function filterClasses() {
    var query    = document.getElementById('cls-search').value.toLowerCase();
    var schoolId = document.getElementById('school-filter').value;
    document.querySelectorAll('.cls-row').forEach(function(row) {
        var search   = row.getAttribute('data-search') || '';
        var rowSchool = row.getAttribute('data-school-id') || '';
        var match = search.includes(query) && (schoolId === '' || rowSchool === schoolId);
        row.style.display = match ? '' : 'none';
    });
}

function toggleAdd() {
    document.getElementById('add-body').classList.toggle('open');
    document.getElementById('add-toggle').classList.toggle('open');
}
function toggleEdit(id) {
    document.getElementById('edit-form-' + id).classList.toggle('show');
}
function cancelEdit(id) {
    document.getElementById('edit-form-' + id).classList.remove('show');
}

function copyPin(pin) {
    navigator.clipboard.writeText(pin).then(function() {
        var toast = document.getElementById('copy-toast');
        toast.classList.add('show');
        setTimeout(function() { toast.classList.remove('show'); }, 2000);
    });
}
</script>

</x-app-layout>