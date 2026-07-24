<x-app-layout>
<x-slot name="title">Kelola File & Backup</x-slot>

<style>
.fm-card {
    background:#fff;
    border:1px solid #cce4f0;
    border-radius:14px;
    margin-bottom:20px;
    overflow:hidden;
    box-shadow:0 1px 4px rgba(0,61,36,.05);
}
.fm-card-hd {
    display:flex;align-items:center;gap:12px;
    padding:18px 24px 16px;border-bottom:1px solid #e8f4fb;
}
.fm-card-icon {
    width:38px;height:38px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;font-size:18px;
}
.fm-card-title { font-family:Outfit,sans-serif;font-weight:700;font-size:15px;color:#0d2416; }
.fm-card-desc  { font-size:12px;color:#6b8fa3;margin-top:1px; }
.fm-body { padding:20px 24px 24px; }

/* Summary stat cards */
.fm-stats {
    display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;
}
@media(max-width:700px){ .fm-stats { grid-template-columns:1fr; } }
.fm-stat {
    background:#fff;border:1px solid #cce4f0;border-radius:14px;
    padding:18px 20px;box-shadow:0 1px 4px rgba(0,61,36,.05);
}
.fm-stat-label {
    font-size:11px;font-weight:700;color:#6b8fa3;text-transform:uppercase;
    letter-spacing:.5px;display:flex;align-items:center;gap:6px;margin-bottom:8px;
}
.fm-stat-value { font-family:Outfit,sans-serif;font-weight:800;font-size:24px;color:#0d2416; }
.fm-stat-sub { font-size:12px;color:#9ca3af;margin-top:3px; }
.fm-stat-sub.warn { color:#dc2626;font-weight:600; }

/* Tabs (reuse pill pattern dari settings) */
.fm-tabs { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px; }
.fm-tab {
    padding:7px 16px;border-radius:20px;font-size:12px;font-weight:600;
    border:1px solid #cce4f0;color:#3d7a9e;background:#fff;cursor:pointer;
    transition:all .15s;text-decoration:none;
}
.fm-tab:hover, .fm-tab.active {
    background:#003d24;color:#B9D9EB;border-color:#003d24;
}

.fm-search {
    border:1px solid #d1e9f6;border-radius:9px;
    padding:9px 13px;font-size:13px;color:#1a2e22;
    outline:none;width:100%;max-width:320px;background:#fafcff;
    margin-bottom:16px;
}
.fm-search:focus { border-color:#B9D9EB;box-shadow:0 0 0 3px rgba(185,217,235,.25); }

/* Table */
.fm-table-wrap { overflow-x:auto; }
.fm-table { width:100%;border-collapse:collapse;font-size:13px; }
.fm-table th {
    text-align:left;padding:10px 14px;background:#f5fafd;
    font-size:11px;font-weight:700;color:#6b8fa3;text-transform:uppercase;
    letter-spacing:.4px;border-bottom:1px solid #e8f4fb;white-space:nowrap;
}
.fm-table td {
    padding:10px 14px;border-bottom:1px solid #f0f7fc;vertical-align:middle;
}
.fm-table tr:hover td { background:#fafcff; }
.fm-thumb {
    width:40px;height:40px;border-radius:8px;object-fit:cover;
    border:1px solid #e8f4fb;flex-shrink:0;
}
.fm-file-icon {
    width:40px;height:40px;border-radius:8px;background:#f0f7fc;
    display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;
}
.fm-file-name { font-weight:600;color:#0d2416;font-size:12.5px; }
.fm-file-sub  { font-size:11px;color:#9ca3af;margin-top:1px; }
.fm-badge {
    display:inline-block;padding:2px 9px;border-radius:12px;
    font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;
}
.fm-badge.jurnal { background:#f0fdf4;color:#166534; }
.fm-badge.tugas  { background:#eff6ff;color:#1e40af; }
.fm-badge.missing { background:#fef2f2;color:#dc2626; }
.fm-actions { display:flex;gap:6px; }
.fm-btn-icon {
    width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;
    border:1px solid #e8f4fb;background:#fff;cursor:pointer;transition:background .15s;
    text-decoration:none;
}
.fm-btn-icon:hover { background:#f5fafd; }
.fm-btn-icon.danger { border-color:#fecaca;color:#dc2626; }
.fm-btn-icon.danger:hover { background:#fef2f2; }
.fm-empty { text-align:center;padding:40px 20px;color:#9ca3af;font-size:13px; }
</style>

<div style="margin-bottom:20px">
    <h1 style="font-family:Outfit,sans-serif;font-weight:800;font-size:22px;color:#0d2416">Kelola File & Backup</h1>
    <p style="font-size:13px;color:#6b8fa3;margin-top:2px">Semua file jurnal dan tugas dalam satu tempat — pantau kapasitas dan bersihkan file lama</p>
</div>

{{-- ═══ SUMMARY CARDS ═══ --}}
<div class="fm-stats">
    <div class="fm-stat">
        <div class="fm-stat-label">📦 Total Keseluruhan</div>
        <div class="fm-stat-value">{{ $summary['total']['total_label'] }}</div>
        <div class="fm-stat-sub">{{ $summary['total']['count'] }} file</div>
        @if($summary['total']['missing'] > 0)
            <div class="fm-stat-sub warn">⚠ {{ $summary['total']['missing'] }} file hilang dari disk</div>
        @endif
    </div>
    <div class="fm-stat">
        <div class="fm-stat-label">📸 Foto Jurnal</div>
        <div class="fm-stat-value">{{ $summary['jurnal']['total_label'] }}</div>
        <div class="fm-stat-sub">{{ $summary['jurnal']['count'] }} foto</div>
        @if($summary['jurnal']['missing'] > 0)
            <div class="fm-stat-sub warn">⚠ {{ $summary['jurnal']['missing'] }} hilang</div>
        @endif
    </div>
    <div class="fm-stat">
        <div class="fm-stat-label">📄 File Tugas</div>
        <div class="fm-stat-value">{{ $summary['tugas']['total_label'] }}</div>
        <div class="fm-stat-sub">{{ $summary['tugas']['count'] }} file</div>
        @if($summary['tugas']['missing'] > 0)
            <div class="fm-stat-sub warn">⚠ {{ $summary['tugas']['missing'] }} hilang</div>
        @endif
    </div>
</div>

{{-- ═══ TABS ═══ --}}
<div class="fm-tabs">
    <a href="{{ route('file-manager.index', ['tab' => 'all', 'search' => $search]) }}"
       class="fm-tab {{ $tab === 'all' ? 'active' : '' }}">Semua ({{ $summary['total']['count'] }})</a>
    <a href="{{ route('file-manager.index', ['tab' => 'jurnal', 'search' => $search]) }}"
       class="fm-tab {{ $tab === 'jurnal' ? 'active' : '' }}">📸 Jurnal ({{ $summary['jurnal']['count'] }})</a>
    <a href="{{ route('file-manager.index', ['tab' => 'tugas', 'search' => $search]) }}"
       class="fm-tab {{ $tab === 'tugas' ? 'active' : '' }}">📄 Tugas ({{ $summary['tugas']['count'] }})</a>
</div>

{{-- ═══ SEARCH ═══ --}}
<form method="GET" action="{{ route('file-manager.index') }}">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru, siswa, kelas, atau file..."
        class="fm-search" onchange="this.form.submit()">
</form>

{{-- ═══ TABLE ═══ --}}
<div class="fm-card">
    <div class="fm-body" style="padding:0">
        <div class="fm-table-wrap">
            <table class="fm-table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Tipe</th>
                        <th>Detail</th>
                        <th>Ukuran</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($files as $f)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                @if($f['type'] === 'jurnal')
                                    <img src="{{ $f['url'] }}" class="fm-thumb" alt="Foto jurnal">
                                @else
                                    <div class="fm-file-icon">
                                        {{ match($f['ext']) {
                                            'pdf' => '📕', 'doc','docx' => '📘', 'xls','xlsx' => '📗',
                                            'zip','rar' => '🗜', 'jpg','jpeg','png' => '🖼', default => '📄'
                                        } }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fm-file-name">{{ $f['file_name'] ?? basename($f['path']) }}</div>
                                    <div class="fm-file-sub">{{ $f['ext'] ? strtoupper($f['ext']) : '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fm-badge {{ $f['type'] }}">{{ $f['type'] === 'jurnal' ? 'Jurnal' : ucfirst($f['subtype'] ?? 'Tugas') }}</span>
                            @if(!$f['exists'])
                                <br><span class="fm-badge missing" style="margin-top:4px">Hilang</span>
                            @endif
                        </td>
                        <td>
                            @if($f['type'] === 'jurnal')
                                <div style="font-size:12.5px;color:#374151">{{ $f['teacher_name'] }} · {{ $f['class_name'] }}</div>
                                <div class="fm-file-sub">{{ $f['resource_name'] }}</div>
                            @else
                                <div style="font-size:12.5px;color:#374151">
                                    {{ $f['student_name'] ?? '(Lampiran guru)' }} · {{ $f['class_name'] }}
                                </div>
                                <div class="fm-file-sub">{{ $f['assignment'] }}</div>
                            @endif
                        </td>
                        <td style="white-space:nowrap;font-variant-numeric:tabular-nums">{{ $f['size_label'] }}</td>
                        <td style="white-space:nowrap;color:#6b8fa3;font-size:12px">{{ $f['date'] }}</td>
                        <td>
                            <div class="fm-actions">
                                @if($f['type'] === 'jurnal')
                                    <a href="{{ route('file-manager.journal.download', $f['id']) }}" class="fm-btn-icon" title="Download">⬇</a>
                                    <form method="POST" action="{{ route('file-manager.journal.destroy', $f['id']) }}"
                                          onsubmit="return confirm('Hapus foto jurnal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="fm-btn-icon danger" title="Hapus">🗑</button>
                                    </form>
                                @else
                                    @if(!empty($f['download_route']))
                                        <a href="{{ route($f['download_route'], $f['download_id']) }}" class="fm-btn-icon" title="Download">⬇</a>
                                    @endif
                                    <form method="POST" action="{{ route($f['delete_route'], $f['delete_id']) }}"
                                          onsubmit="return confirm('Hapus file ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="fm-btn-icon danger" title="Hapus">🗑</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="fm-empty">Tidak ada file ditemukan.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</x-app-layout>