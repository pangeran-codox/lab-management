/**
 * SEKOLAH & KELAS — sekolah.js
 * Entry point Vite. CSS di-import di sini.
 *
 * vite.config.js:
 *   input: ['resources/js/app.js', 'resources/js/sekolah.js']
 *
 * blade:
 *   @vite(['resources/js/sekolah.js'])
 */

import '../css/sekolah.css';

/* ── Toast ─────────────────────────────────────────────────── */
let _toastTimer = null;

function showToast(message) {
    const el = document.getElementById('sk-toast');
    if (!el) return;
    el.textContent = message;
    el.classList.add('sk-toast--show');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => el.classList.remove('sk-toast--show'), 2400);
}

/* ── Global State for Filtering ────────────────────────────── */
let currentType = 'all';
let searchQuery = '';

/* ── Toggle form tambah sekolah ─────────────────────────────── */
function toggleAddSchool(show = true) {
    const form = document.getElementById('add-school-form');
    if (!form) return;
    form.style.display = show ? 'block' : 'none';
    if (show) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ── Toggle edit panel sekolah ──────────────────────────────── */
function toggleEditOrg(id, show = true) {
    const panel = document.getElementById(`sk-edit-org-${id}`);
    if (!panel) return;
    panel.style.display = show ? 'block' : 'none';
}

/* ── Combined Filter Logic ──────────────────────────────────── */
function applyFilters() {
    let visibleCount = 0;
    const cards = document.querySelectorAll('.sk-org-card');
    
    cards.forEach(card => {
        const cardType = (card.dataset.type || '').toUpperCase();
        const cardSearch = (card.dataset.search || '').toLowerCase();
        
        const typeMatch = currentType === 'all' || cardType === currentType;
        const searchMatch = searchQuery === '' || cardSearch.includes(searchQuery);
        
        const isVisible = typeMatch && searchMatch;
        card.style.display = isVisible ? 'flex' : 'none';
        if (isVisible) visibleCount++;
    });

    const emptyState = document.getElementById('sk-empty');
    if (emptyState) {
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

/* ── Event delegation ───────────────────────────────────────── */
function onDocClick(e) {
    // Toggle Add School
    if (e.target.closest('#btn-toggle-add')) {
        toggleAddSchool(true);
        return;
    }
    if (e.target.closest('#btn-close-add') || e.target.closest('#btn-cancel-add')) {
        toggleAddSchool(false);
        return;
    }

    // Toggle Edit School
    const btnEdit = e.target.closest('[data-edit-org]');
    if (btnEdit) {
        toggleEditOrg(btnEdit.dataset.editOrg, true);
        return;
    }
    const btnCancelEdit = e.target.closest('[data-cancel-edit]');
    if (btnCancelEdit) {
        toggleEditOrg(btnCancelEdit.dataset.cancelEdit, false);
        return;
    }

    // Filter by Type
    const filterBtn = e.target.closest('.sk-filter-pill');
    if (filterBtn) {
        e.preventDefault();
        const pills = document.querySelectorAll('.sk-filter-pill');
        pills.forEach(b => b.classList.remove('active'));
        filterBtn.classList.add('active');
        
        const rawType = filterBtn.getAttribute('data-filter') || 'all';
        currentType = rawType === 'all' ? 'all' : rawType.toUpperCase();
        
        applyFilters();
        return;
    }
}

/* ── Init ───────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', onDocClick);

    // Search input
    const searchInput = document.getElementById('sk-search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase().trim();
            applyFilters();
        });
    }

    // Auto-dismiss flash message
    const flash = document.getElementById('sk-flash');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-10px)';
            setTimeout(() => flash.remove(), 500);
        }, 5000);
    }
});
