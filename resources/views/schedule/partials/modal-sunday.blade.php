{{-- resources/views/schedule/partials/modal-sunday.blade.php --}}
<div class="modal-overlay" id="sunday-modal-overlay"
     onclick="if(event.target===this)closeSundayModal()"
     role="dialog" aria-modal="true" aria-labelledby="sunday-modal-title">
    <div class="modal-box" id="sunday-modal-box">
        <div class="modal-head">
            <div class="modal-head-top">
                <div>
                    <p class="modal-eyebrow">📅 Booking Hari Minggu</p>
                    <h2 class="modal-title" id="sunday-modal-title">Booking Seharian</h2>
                </div>
                <button class="modal-close" onclick="closeSundayModal()" aria-label="Tutup form booking Minggu">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-badges">
                <span class="badge" id="sb-lab">🖥 -</span>
                <span class="badge" id="sb-date">📅 -</span>
                <span class="badge" style="background:rgba(248,113,113,.15);color:#fca5a5;border-color:rgba(248,113,113,.3)">
                    🕐 Seharian · 07:00–12:45
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('sunday.booking.store') }}" class="modal-body">
            @csrf
            <input type="hidden" name="resource_id"  id="sb_rid">
            <input type="hidden" name="booking_date" id="sb_date_val">
            <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">

            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 13px;font-size:12px;color:#991b1b;font-weight:600;">
                🔴 Booking ini akan menggunakan lab <strong>seharian penuh</strong> di hari Minggu.
            </div>

            <div class="field-row">
                <div style="position:relative">
                    <label class="field-label">Nama Pengajar *</label>
                    <input name="teacher_name" id="sb_teacher_name" type="text"
                        placeholder="Nama lengkap" class="inp" required autocomplete="off"
                        oninput="filterTeacherSunday(this.value)">
                    <div id="sb_teacher_sug"
                         style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid #ACC8A2;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.1);z-index:999;max-height:180px;overflow-y:auto">
                    </div>
                </div>
                <div>
                    <label class="field-label">Nomor HP *</label>
                    <input name="teacher_phone" id="sb_teacher_phone" type="text"
                        placeholder="08xxxxxxxxxx" class="inp" required>
                </div>
            </div>

            <div>
                <label class="field-label">Unit Sekolah *</label>
                <select name="organization_id" id="sb_org" class="inp" required
                    onchange="loadKelasSunday(this.value)" style="appearance:auto">
                    <option value="">— Pilih unit sekolah —</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label">Kelas *</label>
                <select name="class_id" id="sb_class" class="inp" required disabled style="appearance:auto">
                    <option value="">— Pilih unit sekolah dulu —</option>
                </select>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label">Mata Pelajaran *</label>
                    <input name="subject_name" type="text" placeholder="Contoh: TIK" class="inp" required>
                </div>
                <div>
                    <label class="field-label">Jumlah Peserta *</label>
                    <input name="participant_count" type="number" min="1" placeholder="0" class="inp" required>
                </div>
            </div>

            <div>
                <label class="field-label">Judul Kegiatan *</label>
                <input name="title" type="text" placeholder="Contoh: Latihan KIR" class="inp" required>
            </div>

            <div>
                <label class="field-label">Keterangan</label>
                <input name="description" type="text" placeholder="Opsional" class="inp">
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeSundayModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">✓ Ajukan Booking Minggu</button>
            </div>
        </form>
    </div>
</div>