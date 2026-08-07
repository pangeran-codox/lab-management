{{-- resources/views/journal/index.blade.php --}}
@extends('layouts.public-schedule')

@section('title', 'Jurnal Lab')

@section('vite')
@vite(['resources/css/journal.css', 'resources/js/journal.js'])
@endsection

@section('content')

{{-- Pass data ke JS --}}
@php
    $journalData = [
        'journalGroups' => $journalGroups,
        'resourceRows' => $resourceRows,
        'resources' => $resources->map(fn($r) => ['id' => $r->id, 'name' => $r->name])->values(),
        'storeUrl' => route('journal.store'),
        'date' => $date,
    ];
@endphp
<script>
    window.JOURNAL_DATA = @json($journalData);
</script>

<div class="max-w-screen-xl mx-auto px-4 lg:px-8 py-6">

    {{-- ════════════════════════════════════════
         NAVIGASI TANGGAL
    ════════════════════════════════════════ --}}
    <div class="date-nav">
        <a href="{{ route('journal.index', ['date' => $prevDate]) }}" class="date-nav-btn">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Sebelumnya
        </a>
        <div class="date-nav-current">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</span>
        </div>
        <a href="{{ route('journal.index', ['date' => $nextDate]) }}" class="date-nav-btn">
            Berikutnya
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    {{-- ════════════════════════════════════════
         EXPORT ACTIONS
    ════════════════════════════════════════ --}}
    <div class="journal-export-wrap">
        <a href="{{ route('journal.export.pdf', ['date' => $date]) }}" target="_blank" class="btn btn-outline btn-small">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export PDF
        </a>
        <button type="button" class="btn btn-outline btn-small" id="btn-export-excel" onclick="exportJournalExcel('current')">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="btn-export-label">Excel (Lab Aktif)</span>
        </button>
        <button type="button" class="btn btn-outline btn-small" onclick="exportJournalExcel('all')">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Excel (Semua Lab)
        </button>
    </div>

    {{-- ════════════════════════════════════════
         FLASH MESSAGES
    ════════════════════════════════════════ --}}
    @if(session('success'))
    <div class="flash success">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ════════════════════════════════════════
         TABS LAB
    ════════════════════════════════════════ --}}
    <div class="lab-tabs">
        @foreach($resources as $i => $resource)
        <button
            onclick="switchTab({{ $resource->id }})"
            id="tab-{{ $resource->id }}"
            class="tab-btn {{ $i === 0 ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ $resource->name }}
        </button>
        @endforeach
    </div>

    {{-- ════════════════════════════════════════
         LAB PANELS
    ════════════════════════════════════════ --}}
    <div id="panels-wrap">
    @foreach($resources as $i => $resource)
    <div id="panel-{{ $resource->id }}" class="lab-panel" style="{{ $i !== 0 ? 'display:none' : '' }}">
        <div class="panel-card">
            <div class="panel-hdr">
                <div class="panel-hdr-left">
                    <div class="panel-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="panel-name">{{ $resource->name }}</div>
                        @if($resource->capacity)
                        <div class="panel-cap">Kapasitas {{ $resource->capacity }} komputer</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="tbl-scroll">
                <table>
                    <thead>
                        <tr>
                            <th class="th-time">Jam</th>
                            <th>Kegiatan</th>
                            <th class="th-action">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($resourceRows[$resource->id] ?? [] as $row)
                        @if($row['type'] === 'empty')
                        <tr>
                            <td class="td-time">
                                <div class="slot-name">{{ $row['slot_name'] }}</div>
                                <div class="slot-time">{{ $row['slot_time'] }}</div>
                            </td>
                            <td>
                                <div class="journal-empty">
                                    <span class="text-muted">Tidak ada kegiatan</span>
                                </div>
                            </td>
                            <td class="td-action"></td>
                        </tr>
                        @else
                        @php $group = $row['group']; @endphp
                        <tr>
                            <td class="td-time">
                                <div class="slot-name">{{ $group['slot_name'] }}</div>
                                <div class="slot-time">{{ $group['slot_time'] }}</div>
                                @if($group['slot_count'] > 1)
                                <span class="badge badge-muted">{{ $group['slot_count'] }} jam</span>
                                @endif
                            </td>
                            <td>
                                @if($group['journal'])
                                <div class="journal-filled">
                                    <div class="journal-info">
                                        <div class="journal-teacher">{{ $group['journal']['teacher_name'] }}</div>
                                        @if($group['journal']['class_name'])
                                        <div class="journal-class">{{ $group['journal']['class_name'] }}</div>
                                        @endif
                                        @if($group['journal']['subject_name'])
                                        <div class="journal-subject">{{ $group['journal']['subject_name'] }}</div>
                                        @endif
                                        @if($group['journal']['activity'])
                                        <div class="journal-activity">{{ $group['journal']['activity'] }}</div>
                                        @endif
                                        @if($group['journal']['notes'])
                                        <div class="journal-notes">{{ $group['journal']['notes'] }}</div>
                                        @endif
                                    </div>
                                    @if(!empty($group['journal']['photos']))
                                    <div class="journal-photos">
                                        @foreach($group['journal']['photos'] as $photo)
                                        <a href="{{ $photo['url'] }}" target="_blank" class="journal-photo">
                                            <img src="{{ $photo['url'] }}" alt="Foto jurnal">
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @else
                                <div class="journal-empty">
                                    <div class="journal-info">
                                        <div class="journal-teacher">{{ $group['teacher_name'] }}</div>
                                        @if($group['class_name'])
                                        <div class="journal-class">{{ $group['class_name'] }}</div>
                                        @endif
                                        @if($group['subject_name'])
                                        <div class="journal-subject">{{ $group['subject_name'] }}</div>
                                        @endif
                                        @if($group['activity'])
                                        <div class="journal-activity">{{ $group['activity'] }}</div>
                                        @endif
                                    </div>
                                    @if($group['is_eligible'])
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-small"
                                        onclick="openAddModal('{{ $group['id'] }}')">
                                        Isi Jurnal
                                    </button>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="td-action">
                                @if($group['journal'])
                                    <span class="badge badge-success">Sudah Diisi</span>
                                @elseif($group['is_eligible'])
                                    <button
                                        type="button"
                                        class="btn btn-outline btn-small"
                                        onclick="openAddModal('{{ $group['id'] }}')">
                                        Isi Jurnal
                                    </button>
                                @else
                                    <span class="badge badge-muted">Belum Waktunya</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
    </div>

    {{-- ════════════════════════════════════════
         MODAL: TAMBAH JURNAL
    ════════════════════════════════════════ --}}
    <div id="add-modal" class="modal-overlay" onclick="if(event.target===this)closeModal()">
        <div class="modal-box">
            <div class="modal-hdr">
                <div class="modal-hdr-row">
                    <div>
                        <p class="modal-eyebrow">Jurnal Lab</p>
                        <h2 class="modal-title">Isi Jurnal</h2>
                    </div>
                    <button class="modal-close" type="button" onclick="closeModal()" aria-label="Tutup">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('journal.store') }}" class="modal-body" id="add-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="source_type" id="add-source-type">
                <input type="hidden" name="resource_id" id="add-resource-id">
                <input type="hidden" name="journal_date" id="add-journal-date">
                <div id="add-source-ids"></div>
                <div id="add-time-slot-ids"></div>

                <div class="modal-preview">
                    <div id="preview-info"></div>
                </div>

                <div>
                    <label class="field-label" for="add-notes">Catatan (Opsional)</label>
                    <textarea id="add-notes" name="notes" class="inp" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>

                <div>
                    <label class="field-label" for="add-photos">Foto (Minimal 1)</label>
                    <div id="photo-dropzone" class="dropzone">
                        <input type="file" id="add-photos" name="photos[]" accept="image/*" multiple hidden onchange="handleFileSelect(event)">
                        <div id="dropzone-content">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p>Drag & drop foto di sini atau klik untuk memilih</p>
                            <p class="text-sm text-muted">Maksimal 5 foto, masing-masing 5MB</p>
                        </div>
                        <div id="photo-previews" class="photo-previews"></div>
                    </div>
                </div>
            </form>

            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn btn-ghost">Batal</button>
                <button type="submit" form="add-form" class="btn btn-primary" id="submit-btn" disabled>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Jurnal
                </button>
            </div>
        </div>
    </div>

</div>

@endsection
