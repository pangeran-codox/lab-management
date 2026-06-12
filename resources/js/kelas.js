/**
 * MANAJEMEN KELAS — kelas.js
 * Entry point Vite. CSS di-import di sini supaya Vite memprosesnya
 * bersama-sama dan menghasilkan satu file CSS teroptimasi.
 *
 * vite.config.js:
 *   input: ['resources/js/app.js', 'resources/js/kelas.js']
 *
 * blade:
 *   @vite(['resources/js/kelas.js'])
 */

import '../css/kelas.css';

/* ─── Toast ────────────────────────────────────────────────── */
let _toastTimer = null;

function showToast(message) {
    const el = document.getElementById('kls-toast');
    if (!el) return;
    el.textContent = message;
    el.classList.add('kls-toast--show');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => el.classList.remove('kls-toast--show'), 2400);
}

/* ─── Toggle form tambah ────────────────────────────────────── */
function toggleAddForm() {
    const body    = document.getElementById('kls-add-body');
    const chevron = document.getElementById('kls-add-chevron');
    const head    = document.getElementById('kls-add-head');
    if (!body) return;

    const isOpen = body.classList.toggle('kls-add-body--open');
    chevron?.classList.toggle('kls-add-chevron--open', isOpen);
    head?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

/* ─── Toggle / cancel edit inline ──────────────────────────── */
function toggleEdit(id) {
    document.getElementById(`kls-edit-form-${id}`)?.classList.toggle('kls-edit-form--show');
}

function cancelEdit(id) {
    document.getElementById(`kls-edit-form-${id}`)?.classList.remove('kls-edit-form--show');
}

/* ─── Copy PIN ──────────────────────────────────────────────── */
function copyPin(pin) {
    const done = () => showToast(`✓ PIN ${pin} disalin`);

    if (navigator.clipboard) {
        navigator.clipboard.writeText(pin).then(done).catch(() => showToast('Gagal menyalin PIN'));
        return;
    }

    // Fallback HTTP / browser lama
    const el = Object.assign(document.createElement('textarea'), {
        value: pin, style: 'position:fixed;opacity:0'
    });
    document.body.appendChild(el);
    el.select();
    try { document.execCommand('copy'); done(); } catch { showToast('Gagal menyalin PIN'); }
    document.body.removeChild(el);
}

/* ─── Search & Filter ───────────────────────────────────────── */
function applyFilter() {
    const q      = (document.getElementById('kls-search')?.value ?? '').toLowerCase().trim();
    const school = document.getElementById('kls-school-filter')?.value ?? '';
    let visible  = 0;

    document.querySelectorAll('.kls-row').forEach(row => {
        const match =
            (q === '' || (row.dataset.search ?? '').toLowerCase().includes(q)) &&
            (school === '' || row.dataset.schoolId === school);

        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    const emptySearch = document.getElementById('kls-empty-search');
    if (emptySearch) emptySearch.style.display = visible === 0 ? '' : 'none';
}

/* ─── Keyboard: buka form tambah via Enter / Space ─────────── */
function handleAddHeadKey(e) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        toggleAddForm();
    }
}

/* ─── Event Delegation (satu listener, semua tombol) ────────── */
function onDocClick(e) {
    // Toggle form tambah
    if (e.target.closest('#kls-add-head')) { toggleAddForm(); return; }

    // Copy PIN
    const copyBtn = e.target.closest('[data-copy-pin]');
    if (copyBtn) { copyPin(copyBtn.dataset.copyPin); return; }

    // Edit
    const editBtn = e.target.closest('[data-edit-id]');
    if (editBtn) { toggleEdit(editBtn.dataset.editId); return; }

    // Cancel edit
    const cancelBtn = e.target.closest('[data-cancel-id]');
    if (cancelBtn) { cancelEdit(cancelBtn.dataset.cancelId); return; }
}

function onDocSubmit(e) {
    // Konfirmasi hapus
    const confirmForm = e.target.closest('[data-confirm]');
    if (confirmForm && !confirm(confirmForm.dataset.confirm)) {
        e.preventDefault(); return;
    }

    // Konfirmasi reset PIN
    const resetForm = e.target.closest('[data-confirm-reset]');
    if (resetForm && !confirm(resetForm.dataset.confirmReset)) {
        e.preventDefault();
    }
}

/* ─── Init ──────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click',  onDocClick);
    document.addEventListener('submit', onDocSubmit);

    document.getElementById('kls-search')
        ?.addEventListener('input', applyFilter);

    document.getElementById('kls-school-filter')
        ?.addEventListener('change', applyFilter);

    document.getElementById('kls-add-head')
        ?.addEventListener('keydown', handleAddHeadKey);

    // Buka form otomatis jika ada validation error dari Laravel
    if (document.querySelector('.kls-input--error, .kls-select--error')) {
        toggleAddForm();
    }
});