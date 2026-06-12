<x-app-layout>
<x-slot name="title">Edit Jadwal Penting</x-slot>

@vite(['resources/css/important.css'])

<div class="jp-wrap">

    {{-- HEADER --}}
    <div class="jp-form-header">
        <a href="{{ route('important-schedule.index') }}" class="jp-back-btn">
            <i class="ti ti-arrow-left"></i>
        </a>
        <div>
            <h1 class="jp-page-title">Edit Jadwal Penting</h1>
            <p class="jp-page-sub">{{ $importantSchedule->title }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('important-schedule.update', $importantSchedule) }}" id="jp-form">
    @csrf @method('PATCH')

    @php
        $isFullDay     = old('is_full_day', $importantSchedule->is_full_day);
        $selectedColor = old('color', $importantSchedule->color ?? '#EF4444');
        $colors = [
            '#EF4444' => 'Merah',
            '#F97316' => 'Oranye',
            '#EAB308' => 'Kuning',
            '#22C55E' => 'Hijau',
            '#3B82F6' => 'Biru',
            '#8B5CF6' => 'Ungu',
            '#EC4899' => 'Pink',
            '#1A2517' => 'Hitam',
        ];
    @endphp

    <div class="jp-form-layout">

        {{-- ── LEFT: MAIN FORM ── --}}
        <div class="jp-form-main">

            {{-- Section: Informasi Event --}}
            <div class="jp-section">
                <div class="jp-section-header">
                    <span class="jp-section-num">1</span>
                    <span class="jp-section-title">Informasi Event</span>
                </div>
                <div class="jp-section-body">

                    <div class="jp-field">
                        <label class="jp-label">Nama Event <span class="jp-req">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $importantSchedule->title) }}"
                               class="jp-input {{ $errors->has('title') ? 'jp-input--error' : '' }}"
                               placeholder="Nama event...">
                        @error('title')<div class="jp-error-msg"><i class="ti ti-alert-circle"></i> {{ $message }}</div>@enderror
                    </div>

                    <div class="jp-field-row">
                        <div class="jp-field">
                            <label class="jp-label">Jenis Event <span class="jp-req">*</span></label>
                            <div class="jp-select-wrap">
                                <select name="type" class="jp-select">
                                    @foreach($typeLabels as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $importantSchedule->type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <i class="ti ti-chevron-down jp-select-icon"></i>
                            </div>
                        </div>
                        <div class="jp-field">
                            <label class="jp-label">Lab <span class="jp-req">*</span></label>
                            <div class="jp-select-wrap">
                                <select name="resource_id" class="jp-select {{ $errors->has('resource_id') ? 'jp-input--error' : '' }}">
                                    @foreach($resources as $r)
                                    <option value="{{ $r->id }}" {{ old('resource_id', $importantSchedule->resource_id) == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                <i class="ti ti-chevron-down jp-select-icon"></i>
                            </div>
                        </div>
                    </div>

                    <div class="jp-field" style="max-width:260px">
                        <label class="jp-label">Tanggal <span class="jp-req">*</span></label>
                        <input type="date" name="date"
                               value="{{ old('date', $importantSchedule->date->toDateString()) }}"
                               class="jp-input {{ $errors->has('date') ? 'jp-input--error' : '' }}">
                        @error('date')<div class="jp-error-msg"><i class="ti ti-alert-circle"></i> {{ $message }}</div>@enderror
                    </div>

                </div>
            </div>

            {{-- Section: Waktu --}}
            <div class="jp-section">
                <div class="jp-section-header">
                    <span class="jp-section-num">2</span>
                    <span class="jp-section-title">Waktu Pemblokiran</span>
                </div>
                <div class="jp-section-body">

                    <div class="jp-toggle-row">
                        <div class="jp-toggle-info">
                            <div class="jp-toggle-title">Blokir Seharian</div>
                            <div class="jp-toggle-sub">Semua slot di tanggal ini akan diblokir</div>
                        </div>
                        <label class="jp-toggle-switch">
                            <input type="checkbox" name="is_full_day" value="1" id="fullDayToggle"
                                   {{ $isFullDay ? 'checked' : '' }}
                                   onchange="toggleSlotSection(this.checked)">
                            <span class="jp-toggle-track {{ $isFullDay ? 'on' : '' }}" id="toggleTrack">
                                <span class="jp-toggle-thumb" id="toggleThumb" style="{{ $isFullDay ? 'left:22px' : '' }}"></span>
                            </span>
                        </label>
                    </div>

                    <div id="slotSection" class="jp-slot-section {{ $isFullDay ? 'hidden' : '' }}">
                        <div class="jp-field-row">
                            <div class="jp-field">
                                <label class="jp-label">Slot Mulai</label>
                                <div class="jp-select-wrap">
                                    <select name="start_slot_id" class="jp-select">
                                        <option value="">— Pilih Slot —</option>
                                        @foreach($timeSlots as $slot)
                                        <option value="{{ $slot->id }}"
                                            {{ old('start_slot_id', $importantSchedule->start_slot_id) == $slot->id ? 'selected' : '' }}>
                                            {{ $slot->name }} · {{ substr($slot->start_time,0,5) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <i class="ti ti-chevron-down jp-select-icon"></i>
                                </div>
                            </div>
                            <div class="jp-field">
                                <label class="jp-label">Slot Selesai</label>
                                <div class="jp-select-wrap">
                                    <select name="end_slot_id" class="jp-select">
                                        <option value="">— Pilih Slot —</option>
                                        @foreach($timeSlots as $slot)
                                        <option value="{{ $slot->id }}"
                                            {{ old('end_slot_id', $importantSchedule->end_slot_id) == $slot->id ? 'selected' : '' }}>
                                            {{ $slot->name }} · {{ substr($slot->end_time,0,5) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <i class="ti ti-chevron-down jp-select-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Section: Keterangan --}}
            <div class="jp-section">
                <div class="jp-section-header">
                    <span class="jp-section-num">3</span>
                    <span class="jp-section-title">Keterangan <span class="jp-optional">opsional</span></span>
                </div>
                <div class="jp-section-body">
                    <div class="jp-field">
                        <textarea name="description" rows="3" class="jp-input jp-textarea"
                                  placeholder="Catatan tambahan...">{{ old('description', $importantSchedule->description) }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: SIDEBAR ── --}}
        <div class="jp-form-sidebar">

            {{-- Warna Badge --}}
            <div class="jp-section">
                <div class="jp-section-header">
                    <i class="ti ti-palette jp-section-icon"></i>
                    <span class="jp-section-title">Warna Badge</span>
                </div>
                <div class="jp-section-body">
                    <p class="jp-sidebar-hint">Warna tampil di kalender jadwal</p>
                    <div class="jp-color-grid" id="colorPicker">
                        @foreach($colors as $hex => $name)
                        <button type="button"
                                class="jp-color-btn {{ $selectedColor === $hex ? 'active' : '' }}"
                                style="background: {{ $hex }}"
                                title="{{ $name }}"
                                onclick="selectColor('{{ $hex }}', this)">
                            <i class="ti ti-check jp-color-check"></i>
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="color" id="colorInput" value="{{ $selectedColor }}">
                    <div class="jp-color-preview" id="colorPreview">
                        <div class="jp-preview-dot" id="previewDot" style="background: {{ $selectedColor }}"></div>
                        <span class="jp-preview-label" id="previewText">{{ $importantSchedule->title }}</span>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="jp-danger-box">
                <div class="jp-danger-title">
                    <i class="ti ti-alert-triangle"></i>
                    Hapus Event
                </div>
                <p class="jp-danger-sub">Menghapus event ini akan membuka kembali slot yang sebelumnya terblokir.</p>
                <form method="POST" action="{{ route('important-schedule.destroy', $importantSchedule) }}"
                      onsubmit="return confirm('Yakin ingin menghapus jadwal penting ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="jp-btn-danger-full">
                        <i class="ti ti-trash"></i>
                        Hapus Event Ini
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- FORM FOOTER --}}
    <div class="jp-form-footer">
        <a href="{{ route('important-schedule.index') }}" class="jp-btn-cancel">Batal</a>
        <button type="submit" class="jp-btn-primary">
            <i class="ti ti-device-floppy"></i>
            Simpan Perubahan
        </button>
    </div>

    </form>
</div>

@vite(['resources/js/important.js'])

</x-app-layout>