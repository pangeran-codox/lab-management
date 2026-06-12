/* ─────────────────────────────────────────────────────────────
   guru.js — Manajemen Guru
   ───────────────────────────────────────────────────────────── */

/**
 * Toggle form tambah guru
 */
window.toggleAdd = function () {
    const body  = document.getElementById('add-body');
    const chev  = document.getElementById('add-chev');
    const btn   = document.getElementById('add-toggle-btn');
    const isOpen = body.classList.toggle('open');
    chev.classList.toggle('open', isOpen);
    btn.setAttribute('aria-expanded', isOpen);
};

/**
 * Toggle inline edit form per guru
 */
window.toggleEdit = function (id) {
    const form = document.getElementById('edit-form-' + id);
    if (!form) return;
    form.classList.toggle('show');
};

/**
 * Tutup inline edit form
 */
window.cancelEdit = function (id) {
    const form = document.getElementById('edit-form-' + id);
    if (form) form.classList.remove('show');
};

/**
 * Filter tabel berdasarkan input pencarian
 */
window.filterTable = function (query) {
    const rows = document.querySelectorAll('#guru-table tbody tr[data-search]');
    const q    = query.toLowerCase().trim();
    rows.forEach(row => {
        const haystack = row.getAttribute('data-search') || '';
        row.style.display = (!q || haystack.includes(q)) ? '' : 'none';
    });
};

/**
 * Auto-buka form tambah jika ada validation error
 */
document.addEventListener('DOMContentLoaded', () => {
    const hasError = document.querySelector('.mg-flash--err');
    if (hasError) {
        const body = document.getElementById('add-body');
        const chev = document.getElementById('add-chev');
        const btn  = document.getElementById('add-toggle-btn');
        if (body) {
            body.classList.add('open');
            chev && chev.classList.add('open');
            btn  && btn.setAttribute('aria-expanded', 'true');
        }
    }
});