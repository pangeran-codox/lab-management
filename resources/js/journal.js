/**
 * journal.js — Lab Management · Jurnal Lab
 * Depends on: window.JOURNAL_DATA (object)
 */

/* ─── HELPERS ───────────────────────────────────────────────── */
const $ = id => document.getElementById(id);

function bodyLock()   { document.body.style.overflow = 'hidden'; }
function bodyUnlock() { document.body.style.overflow = ''; }

let selectedFiles = [];

/* ─── TAB SWITCHER ──────────────────────────────────────────── */
function switchTab(id) {
    document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    $('tab-' + id).classList.add('active');
    const panel = $('panel-' + id);
    panel.style.display = '';
    panel.style.animation = 'none';
    void panel.offsetWidth;
    panel.style.animation = '';
}

function syncHiddenArrayInputs(containerId, fieldName, values) {
    const container = $(containerId);

    container.innerHTML = values.map(value =>
        `<input type="hidden" name="${fieldName}[]" value="${escHtml(value)}">`
    ).join('');
}

/* ─── MODAL: TAMBAH JURNAL ─────────────────────────────────── */
function openAddModal(groupId) {
    const data = window.JOURNAL_DATA || {};
    const group = data.journalGroups?.[groupId];

    if (!group) return;

    // Set hidden inputs
    $('add-source-type').value = group.source_type;
    $('add-resource-id').value = group.resource_id;
    $('add-journal-date').value = new URLSearchParams(window.location.search).get('date')
        || new Date().toISOString().slice(0, 10);
    syncHiddenArrayInputs('add-source-ids', 'source_ids', group.source_ids || []);
    syncHiddenArrayInputs('add-time-slot-ids', 'time_slot_ids', group.time_slot_ids || []);

    // Build preview
    const teacherName = group.teacher_name || '';
    const className = group.class_name || '';
    const subjectName = group.subject_name || '';
    const activity = group.activity || '';
    const slotInfo = [group.slot_name, group.slot_time].filter(Boolean).join(' · ');
    const slotCount = group.slot_count > 1 ? `${group.slot_count} jam pelajaran` : '1 jam pelajaran';

    const previewHtml = `
        <div style="font-size: 12px; line-height: 1.6;">
            ${teacherName ? `<div style="font-weight:700; color:var(--g9);">${escHtml(teacherName)}</div>` : ''}
            ${className ? `<div style="color:var(--g7);">${escHtml(className)}</div>` : ''}
            ${subjectName ? `<div style="color:var(--sub);">${escHtml(subjectName)}</div>` : ''}
            ${slotInfo ? `<div style="margin-top:4px; color:var(--muted);">${escHtml(slotInfo)} (${escHtml(slotCount)})</div>` : ''}
            ${activity ? `<div style="margin-top:6px; padding-top:6px; border-top:1px dashed var(--border);">${escHtml(activity)}</div>` : ''}
        </div>
    `;
    $('preview-info').innerHTML = previewHtml;

    // Reset form
    $('add-notes').value = '';
    $('add-photos').value = '';
    selectedFiles = [];
    renderPhotoPreviews();

    $('add-modal').classList.add('open');
    bodyLock();
}

function closeModal() {
    $('add-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── FILE UPLOAD HANDLING ─────────────────────────────────── */
function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    addFiles(files);
}

function addFiles(files) {
    selectedFiles = [
        ...selectedFiles,
        ...files.filter(f => f.type.startsWith('image/'))
    ].slice(0, 5); // max 5 files
    renderPhotoPreviews();
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    renderPhotoPreviews();
}

function renderPhotoPreviews() {
    const previewsContainer = $('photo-previews');
    const dropzoneContent = $('dropzone-content');
    const submitBtn = $('submit-btn');

    if (selectedFiles.length === 0) {
        previewsContainer.innerHTML = '';
        dropzoneContent.style.display = '';
        submitBtn.disabled = true;
        return;
    }

    dropzoneContent.style.display = 'none';
    submitBtn.disabled = false;

    previewsContainer.innerHTML = selectedFiles.map((file, index) => {
        const url = URL.createObjectURL(file);
        return `
            <div class="photo-preview">
                <img src="${url}" alt="Preview">
                <button type="button" class="photo-preview-remove" onclick="removeFile(${index})">&times;</button>
            </div>
        `;
    }).join('');

    // Update the file input with selected files
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(f => dataTransfer.items.add(f));
    $('add-photos').files = dataTransfer.files;
}

// Dropzone drag & drop
document.addEventListener('DOMContentLoaded', () => {
    const dropzone = $('photo-dropzone');
    if (!dropzone) return;

    dropzone.addEventListener('click', () => $('add-photos').click());

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'));
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'));
    });

    dropzone.addEventListener('drop', e => {
        const files = Array.from(e.dataTransfer.files);
        addFiles(files);
    });
});

/* ─── KEYBOARD: ESC closes modal ───────────────────────────── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal();
    }
});

/* ─── UTILS ─────────────────────────────────────────────────── */
function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/* ─── AUTO-DISMISS FLASH ────────────────────────────────────── */
document.querySelectorAll('.flash').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s, transform .4s';
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(-6px)';
        setTimeout(() => el.remove(), 420);
    }, 4000);
});

/* ─── SHEETJS LAZY LOADER (Excel Export) ─────────────────────── */
let xlsxLoaded    = false;
let xlsxLoading   = false;
let xlsxCallbacks = [];

function loadXLSX(callback) {
    if (xlsxLoaded) { callback(); return; }

    const btn = document.getElementById('btn-export-excel');
    const label = btn?.querySelector('.btn-export-label');

    xlsxCallbacks.push(callback);
    if (xlsxLoading) return;

    xlsxLoading = true;
    const prevHtml = label?.innerHTML || null;
    if (label) {
        label.innerHTML = `<span class="xlsx-loading" style="display:inline-flex;align-items:center;gap:6px;">
            <svg style="width:12px;height:12px;animation:spin 0.8s linear infinite;" fill="none" viewBox="0 0 24 24">
                <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            Memuat XLSX...
        </span>`;
    }

    const script = document.createElement('script');
    script.type = 'text/javascript';
    script.src    = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
    script.onload = function () {
        xlsxLoaded  = true;
        xlsxLoading = false;
        if (label && prevHtml) label.innerHTML = prevHtml;
        xlsxCallbacks.forEach(cb => cb());
        xlsxCallbacks = [];
    };
    script.onerror = function () {
        xlsxLoading   = false;
        xlsxCallbacks = [];
        if (label && prevHtml) label.innerHTML = prevHtml;
        alert('Gagal memuat library Excel (XLSX). Periksa koneksi internet Anda.');
    };
    document.body.appendChild(script);
}

/* ─── EXPORT EXCEL ───────────────────────────────────────────── */
function buildExcelRowsForResource(resourceId, resourceName) {
    const data = window.JOURNAL_DATA || {};
    const rows = (data.resourceRows || {})[resourceId] || [];

    const excelRows = [];
    excelRows.push([resourceName]);
    excelRows.push([
        'Jam',
        'Guru',
        'Kelas',
        'Materi / Mapel',
        'Kegiatan',
        'Catatan',
        'Jumlah Foto',
        'Status',
    ]);

    for (const row of rows) {
        if (row.type === 'empty') {
            excelRows.push([
                `${row.slot_name} (${row.slot_time})`,
                '-', '-', '-', 'Tidak ada kegiatan', '-', 0, '-',
            ]);
            continue;
        }

        const group = row.group || {};
        const journal = group.journal || null;
        const teacher = (journal?.teacher_name || group.teacher_name || '');
        const className = (journal?.class_name || group.class_name || '');
        const subject = (journal?.subject_name || group.subject_name || '');
        const activity = (journal?.activity || group.activity || '');
        const notes = (journal?.notes || '');
        const photoCount = (journal?.photos || []).length;
        const status = journal ? 'Sudah Diisi' : 'Belum Diisi';
        const timeLabel = `${group.slot_name || ''} (${group.slot_time || ''})${group.slot_count > 1 ? ' · ' + group.slot_count + ' jam' : ''}`;

        excelRows.push([
            timeLabel,
            teacher,
            className,
            subject,
            activity,
            notes,
            photoCount,
            status,
        ]);
    }

    excelRows.push([]); // spacer
    return excelRows;
}

function exportJournalExcel(scope = 'current') {
    loadXLSX(function () {
        const data = window.JOURNAL_DATA || {};
        const resources = data.resources || [];
        const dateParam = (data.date || new Date().toISOString().slice(0, 10));
        const dateStr = String(dateParam).replace(/-/g, '');

        if (scope === 'current') {
            let currentResource = resources[0] || null;
            const firstActiveTab = document.querySelector('.tab-btn.active');
            if (firstActiveTab && firstActiveTab.id) {
                const tabId = String(firstActiveTab.id).replace('tab-', '');
                currentResource = resources.find(r => String(r.id) === tabId) || currentResource;
            }

            if (!currentResource) {
                alert('Lab tidak ditemukan.');
                return;
            }

            const rows = buildExcelRowsForResource(currentResource.id, currentResource.name);
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(rows);

            // Column widths
            ws['!cols'] = [
                { wch: 24 }, // Jam
                { wch: 24 }, // Guru
                { wch: 18 }, // Kelas
                { wch: 22 }, // Materi
                { wch: 40 }, // Kegiatan
                { wch: 36 }, // Catatan
                { wch: 10 }, // Foto
                { wch: 12 }, // Status
            ];

            XLSX.utils.book_append_sheet(wb, ws, currentResource.name.substring(0, 31));
            XLSX.writeFile(wb, `Jurnal_${currentResource.name.replace(/\s+/g, '_')}_${dateStr}.xlsx`);
            return;
        }

        // ── Scope: "all" (Semua Lab, 1 workbook dengan multi sheet) ──
        const wb = XLSX.utils.book_new();

        for (const resource of resources) {
            const rows = buildExcelRowsForResource(resource.id, resource.name);
            const ws = XLSX.utils.aoa_to_sheet(rows);

            ws['!cols'] = [
                { wch: 24 }, // Jam
                { wch: 24 }, // Guru
                { wch: 18 }, // Kelas
                { wch: 22 }, // Materi
                { wch: 40 }, // Kegiatan
                { wch: 36 }, // Catatan
                { wch: 10 }, // Foto
                { wch: 12 }, // Status
            ];

            const sheetName = resource.name.substring(0, 31);
            XLSX.utils.book_append_sheet(wb, ws, sheetName);
        }

        XLSX.writeFile(wb, `Jurnal_Semua_Lab_${dateStr}.xlsx`);
    });
}

// ─── EXPOSE TO GLOBAL (required for inline onclick in Blade) ───
window.switchTab = switchTab;
window.openAddModal = openAddModal;
window.closeModal = closeModal;
window.handleFileSelect = handleFileSelect;
window.removeFile = removeFile;
window.exportJournalExcel = exportJournalExcel;
window.loadXLSX = loadXLSX;
