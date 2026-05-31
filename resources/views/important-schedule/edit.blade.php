<x-app-layout>
<x-slot name="title">Edit Jadwal Penting</x-slot>

<style>
.form-section { background:#fff;border-radius:14px;border:1px solid #e8f0e6;padding:20px;box-shadow:0 1px 4px rgba(26,37,23,.05);margin-bottom:16px; }
.form-label { font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px; }
.color-opt { width:28px;height:28px;border-radius:50%;cursor:pointer;border:3px solid transparent;transition:border-color .15s; }
.color-opt.selected, .color-opt:hover { border-color: #1A2517; }
</style>

{{-- HEADER --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
    <a href="{{ route('important-schedule.index') }}"
       style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e8f0e6;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#6b7280;background:#fff">
        ←
    </a>
    <div>
        <h1 style="font-family:Outfit,sans-serif;font-weight:800;font-size:20px;color:#1A2517;margin:0">Edit Jadwal Penting</h1>
        <p style="font-size:12px;color:#9ca3af;margin:3px 0 0">{{ $importantSchedule->title }}</p>
    </div>
</div>

<form method="POST" action="{{ route('important-schedule.update', $importantSchedule) }}">
@csrf @method('PATCH')

@php
$isFullDay     = old('is_full_day', $importantSchedule->is_full_day);
$selectedColor = old('color', $importantSchedule->color ?? '#EF4444');
@endphp

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start">

    {{-- MAIN FORM --}}
    <div>
        <div class="form-section">
            <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0 0 16px">Informasi Event</h2>

            {{-- Title --}}
            <div style="margin-bottom:16px">
                <label class="form-label">Nama Event <span style="color:#ef4444">*</span></label>
                <input type="text" name="title" value="{{ old('title', $importantSchedule->title) }}"
                       class="input-modern {{ $errors->has('title') ? 'error' : '' }}"
                       placeholder="Nama event...">
                @error('title')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
            </div>

            {{-- Type + Lab --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
                <div>
                    <label class="form-label">Jenis Event <span style="color:#ef4444">*</span></label>
                    <select name="type" class="input-modern">
                        @foreach($typeLabels as $val => $label)
                        <option value="{{ $val }}" {{ old('type', $importantSchedule->type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Lab <span style="color:#ef4444">*</span></label>
                    <select name="resource_id" class="input-modern">
                        @foreach($resources as $r)
                        <option value="{{ $r->id }}" {{ old('resource_id', $importantSchedule->resource_id) == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Date --}}
            <div style="margin-bottom:16px">
                <label class="form-label">Tanggal <span style="color:#ef4444">*</span></label>
                <input type="date" name="date"
                       value="{{ old('date', $importantSchedule->date->toDateString()) }}"
                       class="input-modern {{ $errors->has('date') ? 'error' : '' }}">
                @error('date')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
            </div>

            {{-- Full Day Toggle --}}
            <div style="margin-bottom:16px;background:#f8faf7;border-radius:10px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="font-size:13px;font-weight:700;color:#1A2517;margin:0">Blokir Seharian</p>
                    <p style="font-size:11px;color:#9ca3af;margin:2px 0 0">Semua slot di tanggal ini akan diblokir</p>
                </div>
                <label style="position:relative;display:inline-block;width:44px;height:24px;cursor:pointer">
                    <input type="checkbox" name="is_full_day" value="1" id="fullDayToggle"
                           {{ $isFullDay ? 'checked' : '' }}
                           onchange="toggleSlotSection(this.checked)"
                           style="opacity:0;width:0;height:0">
                    <span style="position:absolute;inset:0;background:{{ $isFullDay ? '#ACC8A2' : '#d1d5db' }};border-radius:999px;transition:.3s" id="toggleBg"></span>
                    <span style="position:absolute;top:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:.3s;box-shadow:0 1px 3px rgba(0,0,0,.2);left:{{ $isFullDay ? '22px' : '2px' }}" id="toggleDot"></span>
                </label>
            </div>

            {{-- Slot Range --}}
            <div id="slotSection" style="{{ $isFullDay ? 'display:none' : '' }}">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div>
                        <label class="form-label">Slot Mulai</label>
                        <select name="start_slot_id" class="input-modern">
                            <option value="">-- Pilih Slot --</option>
                            @foreach($timeSlots as $slot)
                            <option value="{{ $slot->id }}"
                                {{ old('start_slot_id', $importantSchedule->start_slot_id) == $slot->id ? 'selected' : '' }}>
                                {{ $slot->name }} ({{ substr($slot->start_time,0,5) }})
                            </option>
                            @endforeach
                        </select>
                        @error('start_slot_id')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Slot Selesai</label>
                        <select name="end_slot_id" class="input-modern">
                            <option value="">-- Pilih Slot --</option>
                            @foreach($timeSlots as $slot)
                            <option value="{{ $slot->id }}"
                                {{ old('end_slot_id', $importantSchedule->end_slot_id) == $slot->id ? 'selected' : '' }}>
                                {{ $slot->name }} ({{ substr($slot->end_time,0,5) }})
                            </option>
                            @endforeach
                        </select>
                        @error('end_slot_id')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div style="margin-top:16px">
                <label class="form-label">Keterangan <span style="color:#9ca3af;font-weight:400">(opsional)</span></label>
                <textarea name="description" rows="3" class="input-modern"
                          placeholder="Catatan tambahan...">{{ old('description', $importantSchedule->description) }}</textarea>
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div>
        <div class="form-section">
            <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0 0 12px">🎨 Warna Badge</h2>
            <div style="display:flex;gap:10px;flex-wrap:wrap" id="colorPicker">
                @php $colors = ['#EF4444','#F97316','#EAB308','#22C55E','#3B82F6','#8B5CF6','#EC4899','#1A2517']; @endphp
                @foreach($colors as $c)
                <div class="color-opt {{ $selectedColor === $c ? 'selected' : '' }}"
                     style="background:{{ $c }}"
                     onclick="selectColor('{{ $c }}', this)"></div>
                @endforeach
            </div>
            <input type="hidden" name="color" id="colorInput" value="{{ $selectedColor }}">
            <div style="margin-top:14px;padding:10px 14px;border-radius:10px;border:1.5px solid #e8f0e6;display:flex;align-items:center;gap:10px">
                <div id="previewDot" style="width:10px;height:10px;border-radius:50%;background:{{ $selectedColor }}"></div>
                <span style="font-size:12px;font-weight:700;color:#1A2517">{{ $importantSchedule->title }}</span>
            </div>
        </div>

        {{-- Danger zone --}}
        <div style="background:#fff5f5;border-radius:14px;border:1px solid #fecaca;padding:16px">
            <p style="font-size:12px;font-weight:700;color:#991b1b;margin:0 0 10px">⚠️ Hapus Event</p>
            <p style="font-size:11px;color:#b91c1c;margin:0 0 12px">Menghapus event ini akan membuka kembali slot yang sebelumnya terblokir.</p>
            <form method="POST" action="{{ route('important-schedule.destroy', $importantSchedule) }}"
                  onsubmit="return confirm('Yakin ingin menghapus jadwal penting ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                        style="width:100%;background:#fee2e2;color:#991b1b;border:none;border-radius:8px;padding:8px;font-size:12px;font-weight:700;cursor:pointer">
                    🗑 Hapus Event Ini
                </button>
            </form>
        </div>
    </div>

</div>

{{-- FOOTER --}}
<div style="display:flex;align-items:center;justify-content:flex-end;gap:12px;margin-top:4px">
    <a href="{{ route('important-schedule.index') }}"
       style="padding:10px 20px;border-radius:10px;border:1.5px solid #e8f0e6;font-size:13px;font-weight:600;color:#6b7280;text-decoration:none;background:#fff">
        Batal
    </a>
    <button type="submit"
            style="background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;border:none;border-radius:10px;padding:10px 24px;font-size:13px;font-weight:700;cursor:pointer">
        Simpan Perubahan
    </button>
</div>

</form>

<script>
function toggleSlotSection(isFullDay) {
    document.getElementById('slotSection').style.display = isFullDay ? 'none' : '';
    const bg  = document.getElementById('toggleBg');
    const dot = document.getElementById('toggleDot');
    bg.style.background = isFullDay ? '#ACC8A2' : '#d1d5db';
    dot.style.left      = isFullDay ? '22px' : '2px';
}

function selectColor(color, el) {
    document.querySelectorAll('.color-opt').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('colorInput').value = color;
    document.getElementById('previewDot').style.background = color;
}
</script>

</x-app-layout>