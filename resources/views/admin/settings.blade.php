<x-app-layout>
<x-slot name="title">Pengaturan Sistem</x-slot>

<style>
.set-card {
    background:#fff;
    border:1px solid #cce4f0;
    border-radius:14px;
    margin-bottom:20px;
    overflow:hidden;
    box-shadow:0 1px 4px rgba(0,61,36,.05);
}
.set-card-hd {
    display:flex;
    align-items:center;
    gap:12px;
    padding:18px 24px 16px;
    border-bottom:1px solid #e8f4fb;
}
.set-card-icon {
    width:38px;height:38px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;font-size:18px;
}
.set-card-title { font-family:Outfit,sans-serif;font-weight:700;font-size:15px;color:#0d2416; }
.set-card-desc  { font-size:12px;color:#6b8fa3;margin-top:1px; }
.set-body { padding:20px 24px 24px; }
.set-row  { display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px; }
.set-row.single { grid-template-columns:1fr; }
@media(max-width:600px){ .set-row { grid-template-columns:1fr; } }
.set-group { display:flex;flex-direction:column;gap:5px; }
.set-label { font-size:13px;font-weight:600;color:#374151; }
.set-sublabel { font-size:11px;color:#9ca3af;margin-top:1px; }
.set-input {
    border:1px solid #d1e9f6;border-radius:9px;
    padding:9px 13px;font-size:13px;color:#1a2e22;
    outline:none;transition:border-color .2s,box-shadow .2s;width:100%;
    background:#fafcff;
}
.set-input:focus { border-color:#B9D9EB;box-shadow:0 0 0 3px rgba(185,217,235,.25); }
textarea.set-input { resize:vertical;min-height:72px; }
.set-toggle-row {
    display:flex;align-items:center;justify-content:space-between;
    padding:12px 16px;border:1px solid #e8f4fb;border-radius:10px;
    margin-bottom:10px;cursor:pointer;
    transition:background .15s;
}
.set-toggle-row:hover { background:#f5fafd; }
.set-toggle-lbl { font-size:13px;font-weight:500;color:#1a2e22; }
.set-toggle-sub { font-size:11px;color:#9ca3af;margin-top:2px; }
/* toggle switch */
.toggle-switch { position:relative;width:44px;height:24px;flex-shrink:0; }
.toggle-switch input { opacity:0;width:0;height:0;position:absolute; }
.toggle-track {
    position:absolute;inset:0;border-radius:24px;
    background:#d1d5db;transition:background .2s;cursor:pointer;
}
.toggle-switch input:checked + .toggle-track { background:#00693E; }
.toggle-track::after {
    content:'';position:absolute;top:3px;left:3px;
    width:18px;height:18px;border-radius:50%;background:#fff;
    transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2);
}
.toggle-switch input:checked + .toggle-track::after { transform:translateX(20px); }
.set-save-btn {
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 20px;border-radius:9px;border:none;cursor:pointer;
    font-size:13px;font-weight:600;transition:background .15s,box-shadow .15s;
    background:#003d24;color:#B9D9EB;
}
.set-save-btn:hover { background:#00693E;box-shadow:0 4px 12px rgba(0,61,36,.2); }
.set-btn-danger {
    background:#fef2f2;color:#dc2626;border:1px solid #fecaca;
    padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;
    cursor:pointer;transition:background .15s;
}
.set-btn-danger:hover { background:#fee2e2; }
.set-logo-preview {
    display:flex;align-items:center;gap:16px;
    padding:14px;border:1px dashed #cce4f0;border-radius:10px;
    background:#f5fafd;margin-bottom:12px;
}
.set-logo-preview img { width:72px;height:72px;object-fit:contain;border-radius:8px;border:1px solid #e8f4fb; }
.set-logo-empty {
    width:72px;height:72px;border-radius:8px;border:1px dashed #cce4f0;
    background:#f0f7fc;display:flex;align-items:center;justify-content:center;
    font-size:24px;
}
.set-section-nav {
    display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;
}
.set-nav-pill {
    padding:7px 16px;border-radius:20px;font-size:12px;font-weight:600;
    border:1px solid #cce4f0;color:#3d7a9e;background:#fff;cursor:pointer;
    transition:all .15s;text-decoration:none;
}
.set-nav-pill:hover, .set-nav-pill.active {
    background:#003d24;color:#B9D9EB;border-color:#003d24;
}
.set-divider { height:1px;background:#e8f4fb;margin:16px 0; }
.set-input-prefix {
    display:flex;align-items:center;
    border:1px solid #d1e9f6;border-radius:9px;overflow:hidden;background:#fafcff;
}
.set-input-prefix span {
    padding:9px 12px;background:#f0f7fc;font-size:12px;color:#6b8fa3;
    border-right:1px solid #d1e9f6;white-space:nowrap;
}
.set-input-prefix input {
    border:none;outline:none;padding:9px 13px;font-size:13px;
    color:#1a2e22;flex:1;background:transparent;
}
</style>

{{-- Page header --}}
<div style="margin-bottom:20px">
    <h1 style="font-family:Outfit,sans-serif;font-weight:800;font-size:22px;color:#0d2416">Pengaturan Sistem</h1>
    <p style="font-size:13px;color:#6b8fa3;margin-top:2px">Konfigurasi identitas, notifikasi, booking, lab control, dan laporan</p>
</div>

{{-- Quick-nav pills --}}
<div class="set-section-nav">
    <a href="#sec-identitas"  class="set-nav-pill">🏫 Identitas</a>
    <a href="#sec-logo"       class="set-nav-pill">🖼 Logo</a>
    <a href="#sec-wa"         class="set-nav-pill">📲 WhatsApp</a>
    <a href="#sec-booking"    class="set-nav-pill">📅 Booking</a>
    <a href="#sec-labcontrol" class="set-nav-pill">🌐 Lab Control</a>
    <a href="#sec-laporan"    class="set-nav-pill">📄 Laporan</a>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 1 — Identitas Sekolah / Lab
════════════════════════════════════════════════════════════ --}}
<div class="set-card" id="sec-identitas">
    <div class="set-card-hd">
        <div class="set-card-icon" style="background:#f0fdf4">🏫</div>
        <div>
            <div class="set-card-title">Identitas Sekolah / Lab</div>
            <div class="set-card-desc">Nama institusi, penanggung jawab, dan kontak yang tampil di laporan</div>
        </div>
    </div>
    <div class="set-body">
        <form method="POST" action="{{ route('settings.identitas') }}">
            @csrf
            <div class="set-row">
                <div class="set-group">
                    <label class="set-label">Nama Institusi <span style="color:#ef4444">*</span></label>
                    <input type="text" name="site_name" class="set-input"
                        value="{{ old('site_name', $s['site_name']) }}"
                        placeholder="Contoh: Nuris Jember" required>
                    @error('site_name')<p style="font-size:11px;color:#dc2626;margin-top:3px">{{ $message }}</p>@enderror
                </div>
                <div class="set-group">
                    <label class="set-label">Kepala Lab / Penanggung Jawab</label>
                    <input type="text" name="site_head_name" class="set-input"
                        value="{{ old('site_head_name', $s['site_head_name']) }}"
                        placeholder="Contoh: Ahmad Fauzi, S.Kom">
                </div>
            </div>
            <div class="set-row">
                <div class="set-group">
                    <label class="set-label">Alamat</label>
                    <input type="text" name="site_address" class="set-input"
                        value="{{ old('site_address', $s['site_address']) }}"
                        placeholder="Contoh: Jl. PB Sudirman No.12, Jember">
                </div>
                <div class="set-group">
                    <label class="set-label">Nomor Telepon / Kontak</label>
                    <input type="text" name="site_phone" class="set-input"
                        value="{{ old('site_phone', $s['site_phone']) }}"
                        placeholder="Contoh: (0331) 485222">
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;margin-top:4px">
                <button type="submit" class="set-save-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Identitas
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 2 — Logo
════════════════════════════════════════════════════════════ --}}
<div class="set-card" id="sec-logo">
    <div class="set-card-hd">
        <div class="set-card-icon" style="background:#fefce8">🖼</div>
        <div>
            <div class="set-card-title">Logo Laporan</div>
            <div class="set-card-desc">Logo yang tampil di kop laporan inventaris dan penggunaan lab (JPG/PNG/SVG, maks 2MB)</div>
        </div>
    </div>
    <div class="set-body">
        {{-- Preview logo saat ini --}}
        <div class="set-logo-preview">
            @if($s['site_logo'])
                <img src="{{ asset('storage/' . $s['site_logo']) }}" alt="Logo">
                <div>
                    <p style="font-size:13px;font-weight:600;color:#0d2416">Logo aktif</p>
                    <p style="font-size:11px;color:#6b8fa3;margin-top:2px">{{ basename($s['site_logo']) }}</p>
                    <form method="POST" action="{{ route('settings.delete-logo') }}" style="display:inline;margin-top:8px">
                        @csrf @method('DELETE')
                        <button type="submit" class="set-btn-danger"
                            onclick="return confirm('Hapus logo ini?')">🗑 Hapus Logo</button>
                    </form>
                </div>
            @else
                <div class="set-logo-empty">🖼</div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:#6b8fa3">Belum ada logo</p>
                    <p style="font-size:11px;color:#9ca3af;margin-top:2px">Upload logo untuk ditampilkan di laporan</p>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('settings.upload-logo') }}" enctype="multipart/form-data">
            @csrf
            <div class="set-row">
                <div class="set-group">
                    <label class="set-label">Pilih File Logo</label>
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                        class="set-input" style="padding:7px 13px" required>
                    @error('logo')<p style="font-size:11px;color:#dc2626;margin-top:3px">{{ $message }}</p>@enderror
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;margin-top:4px">
                <button type="submit" class="set-save-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Logo
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 3 — WhatsApp / Notifikasi
════════════════════════════════════════════════════════════ --}}
<div class="set-card" id="sec-wa">
    <div class="set-card-hd">
        <div class="set-card-icon" style="background:#f0fdf4">📲</div>
        <div>
            <div class="set-card-title">Notifikasi WhatsApp</div>
            <div class="set-card-desc">Aktifkan atau nonaktifkan pengiriman notifikasi WA untuk tiap modul</div>
        </div>
    </div>
    <div class="set-body">
        <form method="POST" action="{{ route('settings.wa') }}">
            @csrf

            {{-- Nomor Admin --}}
            <div class="set-group" style="margin-bottom:16px">
                <label class="set-label">Nomor WA Admin (penerima notifikasi)</label>
                <div class="set-sublabel">Format internasional tanpa +, contoh: 628123456789</div>
                <div class="set-input-prefix" style="margin-top:6px">
                    <span>+</span>
                    <input type="text" name="wa_admin_number"
                        value="{{ old('wa_admin_number', $s['wa_admin_number']) }}"
                        placeholder="628123456789" maxlength="20">
                </div>
                @error('wa_admin_number')<p style="font-size:11px;color:#dc2626;margin-top:3px">{{ $message }}</p>@enderror
            </div>

            <div class="set-divider"></div>
            <p style="font-size:12px;font-weight:600;color:#6b8fa3;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px">Toggle Notifikasi</p>

            {{-- Toggle: Booking --}}
            <label class="set-toggle-row" for="tog_booking">
                <div>
                    <div class="set-toggle-lbl">📅 Notifikasi Booking</div>
                    <div class="set-toggle-sub">Kirim WA ke guru saat booking disetujui atau ditolak</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="tog_booking" name="wa_notify_booking"
                        {{ ($s['wa_notify_booking'] ?? '1') === '1' ? 'checked' : '' }}>
                    <span class="toggle-track"></span>
                </label>
            </label>

            {{-- Toggle: Lab Control --}}
            <label class="set-toggle-row" for="tog_lab">
                <div>
                    <div class="set-toggle-lbl">🌐 Notifikasi Lab Control</div>
                    <div class="set-toggle-sub">Kirim link kontrol internet ke HP guru saat sesi lab dimulai</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="tog_lab" name="wa_notify_lab"
                        {{ ($s['wa_notify_lab'] ?? '1') === '1' ? 'checked' : '' }}>
                    <span class="toggle-track"></span>
                </label>
            </label>

            <div style="display:flex;justify-content:flex-end;margin-top:16px">
                <button type="submit" class="set-save-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Pengaturan WA
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 4 — Booking
════════════════════════════════════════════════════════════ --}}
<div class="set-card" id="sec-booking">
    <div class="set-card-hd">
        <div class="set-card-icon" style="background:#eff6ff">📅</div>
        <div>
            <div class="set-card-title">Pengaturan Booking</div>
            <div class="set-card-desc">Buka/tutup booking publik dan batasi jangkauan tanggal booking</div>
        </div>
    </div>
    <div class="set-body">
        <form method="POST" action="{{ route('settings.booking') }}">
            @csrf

            {{-- Toggle: Booking Open --}}
            <label class="set-toggle-row" for="tog_booking_open" style="margin-bottom:16px">
                <div>
                    <div class="set-toggle-lbl">🔓 Buka Booking Publik</div>
                    <div class="set-toggle-sub">Jika dinonaktifkan, halaman booking akan menampilkan pesan "sedang ditutup"</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="tog_booking_open" name="booking_open"
                        {{ ($s['booking_open'] ?? '1') === '1' ? 'checked' : '' }}>
                    <span class="toggle-track"></span>
                </label>
            </label>

            <div class="set-row">
                <div class="set-group">
                    <label class="set-label">Batas Maksimal Hari ke Depan</label>
                    <div class="set-sublabel">Guru tidak bisa booking lebih dari N hari ke depan dari hari ini</div>
                    <div class="set-input-prefix" style="margin-top:6px">
                        <input type="number" name="booking_max_days"
                            value="{{ old('booking_max_days', $s['booking_max_days'] ?? 30) }}"
                            min="1" max="365" style="width:80px;text-align:center">
                        <span style="border-left:1px solid #d1e9f6;border-right:none">hari</span>
                    </div>
                    @error('booking_max_days')<p style="font-size:11px;color:#dc2626;margin-top:3px">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:4px">
                <button type="submit" class="set-save-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Pengaturan Booking
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 5 — Lab Control / Sesi
════════════════════════════════════════════════════════════ --}}
<div class="set-card" id="sec-labcontrol">
    <div class="set-card-hd">
        <div class="set-card-icon" style="background:#f0fdf4">🌐</div>
        <div>
            <div class="set-card-title">Lab Control & Sesi</div>
            <div class="set-card-desc">Auto-generate token dan toleransi waktu setelah sesi berakhir</div>
        </div>
    </div>
    <div class="set-body">
        <form method="POST" action="{{ route('settings.lab-control') }}">
            @csrf

            {{-- Toggle: Auto-generate --}}
            <label class="set-toggle-row" for="tog_auto_session" style="margin-bottom:16px">
                <div>
                    <div class="set-toggle-lbl">⚡ Auto-generate Token dari Jadwal Rutin</div>
                    <div class="set-toggle-sub">Token lab dibuat otomatis H-5 menit sebelum jadwal rutin dimulai</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="tog_auto_session" name="lab_session_auto"
                        {{ ($s['lab_session_auto'] ?? '1') === '1' ? 'checked' : '' }}>
                    <span class="toggle-track"></span>
                </label>
            </label>

            <div class="set-row">
                <div class="set-group">
                    <label class="set-label">Toleransi Token Setelah Sesi Berakhir</label>
                    <div class="set-sublabel">Token masih bisa digunakan selama N menit setelah waktu selesai</div>
                    <div class="set-input-prefix" style="margin-top:6px">
                        <input type="number" name="lab_token_grace"
                            value="{{ old('lab_token_grace', $s['lab_token_grace'] ?? 10) }}"
                            min="0" max="60" style="width:80px;text-align:center">
                        <span style="border-left:1px solid #d1e9f6;border-right:none">menit</span>
                    </div>
                    @error('lab_token_grace')<p style="font-size:11px;color:#dc2626;margin-top:3px">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:4px">
                <button type="submit" class="set-save-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Pengaturan Lab Control
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 6 — Laporan
════════════════════════════════════════════════════════════ --}}
<div class="set-card" id="sec-laporan">
    <div class="set-card-hd">
        <div class="set-card-icon" style="background:#fdf4ff">📄</div>
        <div>
            <div class="set-card-title">Catatan Footer Laporan</div>
            <div class="set-card-desc">Teks tambahan di bagian bawah laporan PDF — kop surat otomatis dari identitas sekolah</div>
        </div>
    </div>
    <div class="set-body">

        {{-- Info: kop dari identitas --}}
        <div style="display:flex;align-items:flex-start;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 14px;margin-bottom:16px">
            <span style="font-size:16px;flex-shrink:0">ℹ️</span>
            <div style="font-size:12px;color:#166534;line-height:1.6">
                <strong>Kop laporan otomatis</strong> dari data Identitas Sekolah/Lab di atas —
                nama institusi, alamat, nomor telepon, dan logo. Tidak perlu diisi ulang di sini.
            </div>
        </div>

        {{-- Preview kop --}}
        <div style="background:#f5fafd;border:1px solid #cce4f0;border-radius:10px;padding:14px 16px;margin-bottom:16px">
            <p style="font-size:11px;font-weight:600;color:#6b8fa3;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">Preview Kop Laporan</p>
            <div style="display:flex;align-items:center;gap:14px;border-bottom:2px solid #003d24;padding-bottom:10px">
                @if($s['site_logo'])
                    <img src="{{ asset('storage/' . $s['site_logo']) }}" style="height:48px;object-fit:contain;border-radius:4px">
                @else
                    <div style="width:48px;height:48px;border-radius:6px;background:#e8f4fb;display:flex;align-items:center;justify-content:center;font-size:20px">🖼</div>
                @endif
                <div style="flex:1">
                    <div style="font-size:15px;font-weight:700;color:#0d2416">{{ $s['site_name'] ?: '(Nama Institusi)' }}</div>
                    @if($s['site_address'])
                    <div style="font-size:11px;color:#6b8fa3;margin-top:2px">{{ $s['site_address'] }}</div>
                    @endif
                    @if($s['site_phone'])
                    <div style="font-size:11px;color:#6b8fa3">Telp. {{ $s['site_phone'] }}</div>
                    @endif
                </div>
            </div>
            @if($s['report_footer'])
            <div style="margin-top:8px;border-top:1px solid #cce4f0;padding-top:8px">
                <pre style="font-family:inherit;font-size:11px;color:#6b8fa3;margin:0;white-space:pre-wrap">{{ $s['report_footer'] }}</pre>
            </div>
            @endif
        </div>

        <form method="POST" action="{{ route('settings.laporan') }}">
            @csrf
            <div class="set-row single">
                <div class="set-group">
                    <label class="set-label">Catatan Footer Laporan</label>
                    <div class="set-sublabel">Muncul di bawah tabel laporan. Bisa berisi catatan, keperluan TTD, atau informasi tambahan.</div>
                    <textarea name="report_footer" class="set-input" style="margin-top:6px"
                        placeholder="Contoh: Diperiksa oleh Kepala Laboratorium">{{ old('report_footer', $s['report_footer']) }}</textarea>
                    @error('report_footer')<p style="font-size:11px;color:#dc2626;margin-top:3px">{{ $message }}</p>@enderror
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end">
                <button type="submit" class="set-save-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Footer
                </button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>
