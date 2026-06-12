<x-app-layout>
<x-slot name="title">Tambah Jadwal Penting</x-slot>

@vite(['resources/css/important.css'])

<div class="jp-wrap">

    {{-- HEADER --}}
    <div class="jp-form-header">
        <a href="{{ route('important-schedule.index') }}" class="jp-back-btn">
            <i class="ti ti-arrow-left"></i>
        </a>
        <div>
            <h1 class="jp-page-title">Tambah Jadwal Penting</h1>
            <p class="jp-page-sub">Event ini akan memblokir booking di slot yang dipilih</p>
        </div>
    </div>

    <form method="POST" action="{{ route('important-schedule.store') }}" id="jp-form">
    @csrf

    @php
        $selectedColor = old('color', '#EF4444');
        $isFullDay     = old('is_full_day') ? true : false;
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

                    {{-- Nama Event --}}
                    <div class="jp-field">
                        <label class="jp-label">Nama Event <span class="jp-req">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="jp-input {{ $errors->has('title') ? 'jp-input--error' : '' }}"
                               placeholder="cth: Ujian Semester Ganjil, Olimpiade Matematika...">
                        @error('title')<div class="jp-error-msg"><i class="ti ti-alert-circle"></i> {{ $message }}</div>@enderror
                    </div>

                    {{-- Jenis + Lab --}}
                    <div class="jp-field-row">
                        <div class="jp-field">
                            <label class="jp-label">Jenis Event <span class="jp-req">*</span></label>
                            <div class="jp-select-wrap">
                                <select name="type" class="jp-select {{ $errors->has('type') ? 'jp-input--error' : '' }}">
                                    @foreach($typeLabels as $val => $label)
                                    <option value="{{ $val }}" {{ old('type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <i class="ti ti-chevron-down jp-select-icon"></i>
                            </div>
                            @error('type')<div class="jp-error-msg"><i class="ti ti-alert-circle"></i> {{ $message }}</div>@enderror
                        </div>
                        <div class="jp-field">
                            <label class="jp-label">Lab <span class="jp-req">*</span></label>
                            <div class="jp-select-wrap">
                                <select name="resource_id" class="jp-select {{ $errors->has('resource_id') ? 'jp-input--error' : '' }}">
                                    <option value="">— Pilih Lab —</option>
                                    @foreach($resources as $r)
                                    <option value="{{ $r->id }}" {{ old('resource_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                <i class="ti ti-chevron-down jp-select-icon"></i>
                            </div>
                            @error('resource_id')<div class="jp-error-msg"><i class="ti ti-alert-circle"></i> {{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="jp-field" style="max-width:260px">
                        <label class="jp-label">Tanggal <span class="jp-req">*</span></label>
                        <input type="date" name="date" value="{{ old('date') }}"
                               class="jp-input {{ $errors->has('date') ? 'jp-input--error' : '' }}"
                               min="{{ today()->toDateString() }}">
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

                    {{-- Full Day Toggle --}}
                    <div class="jp-toggle-row">
                        <div class="jp-toggle-info">
                            <div class="jp-toggle-title">Blokir Seharian</div>
                            <div class="jp-toggle-sub">Semua slot di tanggal ini akan diblokir</div>
                        </div>
                        <label class="jp-toggle-switch">
                            <input type="checkbox" name="is_full_day" value="1" id="fullDayToggle"
                                   {{ $isFullDay ? 'checked' : '' }}
                                   onchange="toggleSlotSection(this.checked)">
                            <span class="jp-toggle-track" id="toggleTrack">
                                <span class="jp-toggle-thumb" id="toggleThumb"></span>
                            </span>
                        </label>
                    </div>

                    {{-- Slot Range --}}
                    <div id="slotSection" class="jp-slot-section {{ $isFullDay ? 'hidden' : '' }}">
                        <div class="jp-field-row">
                            <div class="jp-field">
                                <label class="jp-label">Slot Mulai <span class="jp-req">*</span></label>
                                <div class="jp-select-wrap">
                                    <select name="start_slot_id" class="jp-select {{ $errors->has('start_slot_id') ? 'jp-input--error' : '' }}">
                                        <option value="">— Pilih Slot —</option>
                                        @foreach($timeSlots as $slot)
                                        <option value="{{ $slot->id }}" {{ old('start_slot_id') == $slot->id ? 'selected' : '' }}>
                                            {{ $slot->name }} · {{ substr($slot->start_time,0,5) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <i class="ti ti-chevron-down jp-select-icon"></i>
                                </div>
                                @error('start_slot_id')<div class="jp-error-msg"><i class="ti ti-alert-circle"></i> {{ $message }}</div>@enderror
                            </div>
                            <div class="jp-field">
                                <label class="jp-label">Slot Selesai <span class="jp-req">*</span></label>
                                <div class="jp-select-wrap">
                                    <select name="end_slot_id" class="jp-select {{ $errors->has('end_slot_id') ? 'jp-input--error' : '' }}">
                                        <option value="">— Pilih Slot —</option>
                                        @foreach($timeSlots as $slot)
                                        <option value="{{ $slot->id }}" {{ old('end_slot_id') == $slot->id ? 'selected' : '' }}>
                                            {{ $slot->name }} · {{ substr($slot->end_time,0,5) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <i class="ti ti-chevron-down jp-select-icon"></i>
                                </div>
                                @error('end_slot_id')<div class="jp-error-msg"><i class="ti ti-alert-circle"></i> {{ $message }}</div>@enderror
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
                                  placeholder="Catatan tambahan mengenai event ini...">{{ old('description') }}</textarea>
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

                    {{-- Preview --}}
                    <div class="jp-color-preview" id="colorPreview">
                        <div class="jp-preview-dot" id="previewDot" style="background: {{ $selectedColor }}"></div>
                        <span class="jp-preview-label" id="previewText">Preview Event</span>
                    </div>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="jp-info-box">
                <div class="jp-info-title">
                    <i class="ti ti-info-circle"></i>
                    Info Pemblokiran
                </div>
                <ul class="jp-info-list">
                    <li>Slot yang diblokir tidak bisa di-booking</li>
                    <li>Jadwal tetap yang ada tetap tampil</li>
                    <li>Event tampil dengan warna di kalender</li>
                    <li>Tombol booking disembunyikan di slot terblokir</li>
                </ul>
            </div>

        </div>
    </div>

    {{-- FORM FOOTER --}}
    <div class="jp-form-footer">
        <a href="{{ route('important-schedule.index') }}" class="jp-btn-cancel">Batal</a>
        <button type="submit" class="jp-btn-primary">
            <i class="ti ti-calendar-plus"></i>
            Simpan Jadwal Penting
        </button>
    </div>

    </form>
</div>

@vite(['resources/js/important.js'])

</x-app-layout>