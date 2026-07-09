{{-- resources/views/schedule/partials/modal-booking.blade.php --}}
<div class="modal-overlay" id="modal-overlay"
     onclick="if(event.target===this)closeModal()"
     role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-box" id="modal-box">
        <div class="modal-head">
            <div class="modal-head-top">
                <div>
                    <p class="modal-eyebrow">Form Booking Lab</p>
                    <h2 class="modal-title" id="modal-title">Ajukan Penggunaan Lab</h2>
                </div>
                <button class="modal-close" onclick="closeModal()" aria-label="Tutup form booking">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-badges">
                <span class="badge" id="b-lab">🖥 -</span>
                <span class="badge" id="b-date">📅 -</span>
                <span class="badge" id="b-slot">🕐 -</span>
            </div>
        </div>

        <form method="POST" action="/booking" class="modal-body">
            @csrf
            <input type="hidden" name="resource_id"  id="f_rid">
            <input type="hidden" name="time_slot_id" id="f_sid">
            <input type="hidden" name="booking_date" id="f_date">
            <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">

            <div class="slot-duration-wrap">
                <label class="field-label">🕐 Durasi Booking</label>
                <p style="font-size:11px;color:var(--muted);margin-bottom:8px;line-height:1.5">
                    Pilih sampai slot mana kamu butuhkan. Slot pertama sudah otomatis terpilih.
                </p>
                <div class="slot-opts" id="slot-options"></div>
                <input type="hidden" name="extra_slot_ids" id="f_extra_slots" value="">
            </div>

            <div class="field-row">
                <div style="position:relative">
                    <label class="field-label">Nama Pengajar *</label>
                    <input name="teacher_name" id="inp_teacher_name" type="text"
                        placeholder="Nama lengkap" class="inp" required
                        value="{{ old('teacher_name') }}" autocomplete="off"
                        oninput="filterTeacher(this.value)">
                    <div id="teacher_suggestions"
                         style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid #ACC8A2;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.1);z-index:999;max-height:180px;overflow-y:auto">
                    </div>
                </div>
                <div>
                    <label class="field-label">Nomor HP *</label>
                    <input name="teacher_phone" id="inp_teacher_phone" type="text"
                        placeholder="08xxxxxxxxxx" class="inp" required
                        value="{{ old('teacher_phone') }}">
                </div>
            </div>

            {{-- Quota Info --}}
            <div id="quota_info" style="display:none;background:#f0f7ee;border:1.5px solid #ACC8A2;border-radius:10px;padding:12px 14px;margin-bottom:16px">
                <div style="font-size:12px;font-weight:700;color:#1A2517;margin-bottom:6px">Kuota Mingguan</div>
                <div style="display:flex;gap:10px;align-items:center">
                    <div id="quota_progress" style="flex:1;height:8px;background:#e5e7eb;border-radius:999px;overflow:hidden">
                        <div id="quota_bar" style="height:100%;width:0;background:linear-gradient(90deg,#5DCA85,#4CAF50);border-radius:999px;transition:width .3s ease"></div>
                    </div>
                    <span id="quota_text" style="font-size:13px;font-weight:600;color:#1A2517;white-space:nowrap">0/5</span>
                </div>
            </div>

            <div>
                <label class="field-label">Unit Sekolah *</label>
                <select name="organization_id" id="f_org" class="inp" required
                    onchange="loadKelas(this.value)" style="appearance:auto">
                    <option value="">— Pilih unit sekolah —</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                        {{ $org->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label">Kelas *</label>
                <select name="class_id" id="f_class" class="inp" required disabled style="appearance:auto">
                    <option value="">— Pilih unit sekolah dulu —</option>
                </select>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label">Mata Pelajaran *</label>
                    <input name="subject_name" type="text" placeholder="Contoh: TIK"
                        class="inp" required value="{{ old('subject_name') }}">
                </div>
                <div>
                    <label class="field-label">Jumlah Peserta *</label>
                    <input name="participant_count" type="number" min="1" placeholder="0"
                        class="inp" required value="{{ old('participant_count') }}">
                </div>
            </div>

            <div>
                <label class="field-label">Judul Kegiatan *</label>
                <input name="title" type="text" placeholder="Contoh: Praktikum Microsoft Excel"
                    class="inp" required value="{{ old('title') }}">
            </div>

            <div>
                <label class="field-label">Keterangan</label>
                <input name="description" type="text" placeholder="Opsional"
                    class="inp" value="{{ old('description') }}">
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">✓ Ajukan Booking</button>
            </div>
        </form>
    </div>
</div>