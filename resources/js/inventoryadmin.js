/**
 * inventoryadmin.js  —  v2
 * Fitur: detail card, sorting kolom, highlight rusak,
 *        progress bar, toast notification, modal konfirmasi hapus,
 *        export CSV
 */

/* ══════════════════════════════════════
   LABEL MAPS
══════════════════════════════════════ */
const IA_CAT = {
    computer:'Komputer', peripheral:'Peripheral', furniture:'Furnitur',
    network:'Network', software:'Software', other:'Lainnya',
};
const IA_COND = {
    excellent:'Sangat Baik', good:'Baik', fair:'Sedang', poor:'Buruk', broken:'Rusak',
};

/* ══════════════════════════════════════
   TOAST
══════════════════════════════════════ */
function iaToast(msg, type = 'ok') {
    const wrap = document.getElementById('ia-toast-wrap');
    if (!wrap) return;
    const t = document.createElement('div');
    t.className = `ia-toast t-${type}`;
    t.innerHTML = `<span class="ia-toast-ic">${type === 'ok' ? '✓' : '⚠'}</span>
                   <span>${msg}</span>
                   <button class="ia-toast-close" aria-label="Tutup">×</button>`;
    t.querySelector('.ia-toast-close').addEventListener('click', () => dismissToast(t));
    wrap.appendChild(t);
    setTimeout(() => dismissToast(t), 4000);
}
function dismissToast(el) {
    el.classList.add('out');
    setTimeout(() => el.remove(), 320);
}

/* ══════════════════════════════════════
   DETAIL CARD
══════════════════════════════════════ */
function iaSelectRow(el) {
    document.querySelectorAll('.ia-row').forEach(r => r.classList.remove('active'));
    el.classList.add('active');

    const d = el.dataset;

    // Header
    document.getElementById('ia-d-name').textContent = d.name;
    document.getElementById('ia-d-lab').textContent  = d.lab || '–';

    const catEl = document.getElementById('ia-d-cat');
    catEl.textContent = IA_CAT[d.category] || d.category;
    catEl.className   = 'ia-badge cat-' + d.category;

    const condEl = document.getElementById('ia-d-cond');
    condEl.textContent = IA_COND[d.condition] || d.condition;
    condEl.className   = 'ia-badge cond-' + d.condition;

    // Qty boxes
    const qty    = parseInt(d.qty)    || 0;
    const good   = parseInt(d.good)   || 0;
    const broken = parseInt(d.broken) || 0;
    const backup = parseInt(d.backup) || 0;

    document.getElementById('ia-d-qty').textContent    = qty;
    document.getElementById('ia-d-good').textContent   = good;
    document.getElementById('ia-d-broken').textContent = broken;
    document.getElementById('ia-d-backup').textContent = backup;
    document.getElementById('ia-d-broken-box').classList.toggle('has', broken > 0);

    // Progress bar  (baik / total)
    const pct = qty > 0 ? Math.round((good / qty) * 100) : 0;
    const bar = document.getElementById('ia-d-bar');
    const pctEl = document.getElementById('ia-d-pct');
    bar.style.width = pct + '%';
    bar.className = 'ia-progress-bar' + (pct < 50 ? ' danger' : pct < 80 ? ' warn' : '');
    pctEl.textContent = pct + '% baik';

    // Fields
    document.getElementById('ia-d-brand').textContent  = d.brand  || '–';
    document.getElementById('ia-d-model').textContent  = d.model  || '–';
    document.getElementById('ia-d-serial').textContent = d.serial || '–';

    const specEl  = document.getElementById('ia-d-specs');
    specEl.textContent = d.specs || '–';
    specEl.className   = d.specs ? 'ia-field-val' : 'ia-field-val empty';

    const notesEl = document.getElementById('ia-d-notes');
    notesEl.textContent = d.notes || '–';
    notesEl.className   = d.notes ? 'ia-field-val' : 'ia-field-val empty';

    // Sync state Quick Action ke baris yang dipilih
    QA.id     = d.id;
    QA.qty    = parseInt(d.qty)    || 0;
    QA.good   = parseInt(d.good)   || 0;
    QA.broken = parseInt(d.broken) || 0;
    QA.backup = parseInt(d.backup) || 0;
    qaReset();

    // Tombol Edit
    document.getElementById('ia-d-btn-edit').dataset.row = JSON.stringify(d);

    // Tombol Hapus — simpan id & nama ke confirm modal
    document.getElementById('ia-d-btn-del').dataset.id   = d.id;
    document.getElementById('ia-d-btn-del').dataset.name = d.name;

    document.getElementById('ia-detail-card').classList.add('has-item');
}

/* ══════════════════════════════════════
   MODAL — TAMBAH
══════════════════════════════════════ */
function openAdd() {
    document.getElementById('add-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('add-item-name')?.focus(), 100);
}
function closeAdd() {
    document.getElementById('add-modal').classList.remove('open');
    document.body.style.overflow = '';
}

/* ══════════════════════════════════════
   MODAL — EDIT
══════════════════════════════════════ */
function openEdit(d) {
    document.getElementById('edit-form').action  = '/inventaris-admin/' + d.id;
    document.getElementById('e-name').value      = d.name      || '';
    document.getElementById('e-category').value  = d.category  || '';
    document.getElementById('e-brand').value     = d.brand     || '';
    document.getElementById('e-model').value     = d.model     || '';
    document.getElementById('e-serial').value    = d.serial    || '';
    document.getElementById('e-specs').value     = d.specs     || '';
    document.getElementById('e-condition').value = d.condition || '';
    document.getElementById('e-qty').value       = d.qty       || 0;
    document.getElementById('e-good').value      = d.good      || 0;
    document.getElementById('e-broken').value    = d.broken    || 0;
    document.getElementById('e-backup').value    = d.backup    || 0;
    document.getElementById('e-notes').value     = d.notes     || '';

    document.getElementById('edit-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('e-name')?.focus(), 100);
}
function closeEdit() {
    document.getElementById('edit-modal').classList.remove('open');
    document.body.style.overflow = '';
}

/* ══════════════════════════════════════
   MODAL — KONFIRMASI HAPUS
══════════════════════════════════════ */
function openConfirmDel(id, name) {
    document.getElementById('ia-confirm-item-name').textContent = name;
    document.getElementById('ia-del-form').action = '/inventaris-admin/' + id;
    document.getElementById('confirm-del-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeConfirmDel() {
    document.getElementById('confirm-del-modal').classList.remove('open');
    document.body.style.overflow = '';
}

/* ══════════════════════════════════════
   HITUNG RUSAK OTOMATIS
══════════════════════════════════════ */
function calcBroken(prefix) {
    const qty  = parseInt(document.getElementById(prefix + '-qty').value)  || 0;
    const good = parseInt(document.getElementById(prefix + '-good').value) || 0;
    document.getElementById(prefix + '-broken').value = Math.max(0, qty - good);
}

/* ══════════════════════════════════════
   SORTING KOLOM
══════════════════════════════════════ */
let sortCol = null, sortAsc = true;

function initSorting() {
    document.querySelectorAll('.ia-table thead th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.col;
            if (sortCol === col) { sortAsc = !sortAsc; }
            else { sortCol = col; sortAsc = true; }

            // Update ikon
            document.querySelectorAll('.ia-table thead th.sortable').forEach(t => {
                t.classList.remove('sort-asc', 'sort-desc');
                t.querySelector('.ia-sort-icon').textContent = '⇅';
            });
            th.classList.add(sortAsc ? 'sort-asc' : 'sort-desc');
            th.querySelector('.ia-sort-icon').textContent = sortAsc ? '↑' : '↓';

            sortTable(col, sortAsc);
        });
    });
}

function sortTable(col, asc) {
    const tbody = document.querySelector('.ia-table tbody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr.ia-row'));

    rows.sort((a, b) => {
        let va = a.dataset[col] || '';
        let vb = b.dataset[col] || '';
        // Coba numeric
        const na = parseFloat(va), nb = parseFloat(vb);
        if (!isNaN(na) && !isNaN(nb)) return asc ? na - nb : nb - na;
        return asc ? va.localeCompare(vb, 'id') : vb.localeCompare(va, 'id');
    });

    rows.forEach(r => tbody.appendChild(r));
}

/* ══════════════════════════════════════
   EXPORT CSV
══════════════════════════════════════ */
function exportCSV() {
    const rows = document.querySelectorAll('.ia-table tbody tr.ia-row');
    if (!rows.length) { iaToast('Tidak ada data untuk diexport', 'err'); return; }

    const headers = ['Nama Barang','Lab','Kategori','Merk','Model','No. Seri',
                     'Spesifikasi','Kondisi','Total','Baik','Rusak','Cadangan','Catatan'];

    const csvRows = [headers.join(',')];
    rows.forEach(r => {
        const d = r.dataset;
        const cols = [
            d.name, d.lab, IA_CAT[d.category]||d.category, d.brand, d.model,
            d.serial, d.specs, IA_COND[d.condition]||d.condition,
            d.qty, d.good, d.broken, d.backup, d.notes
        ].map(v => `"${(v||'').replace(/"/g,'""')}"`);
        csvRows.push(cols.join(','));
    });

    const blob = new Blob(['\uFEFF' + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `inventaris_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    iaToast(`${rows.length} data berhasil diexport`);
}

/* ══════════════════════════════════════
   HIGHLIGHT BARIS RUSAK
══════════════════════════════════════ */
function initBrokenHighlight() {
    document.querySelectorAll('.ia-row').forEach(tr => {
        if (parseInt(tr.dataset.broken) > 0) tr.classList.add('has-broken');
    });
}

/* ══════════════════════════════════════
   MODAL — MAINTENANCE LOG
══════════════════════════════════════ */
function openMaintenance() {
    const d = JSON.parse(document.getElementById('ia-d-btn-edit').dataset.row || '{}');
    if (!d.id) return;

    document.getElementById('m-inventory-id').value = d.id;
    document.getElementById('m-item-name-sub').textContent = d.name;
    
    document.getElementById('maintenance-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeMaintenance() {
    document.getElementById('maintenance-modal').classList.remove('open');
    document.body.style.overflow = '';
}

/* ══════════════════════════════════════
   EXPORT EXCEL (Real .xlsx via SheetJS)
══════════════════════════════════════ */
function exportToExcel() {
    const btn = document.getElementById('ia-btn-export-excel');
    const originalHTML = btn.innerHTML;
    
    // Lazy load SheetJS
    if (typeof XLSX === 'undefined') {
        btn.innerHTML = '<span>⏳</span> Memuat...';
        btn.disabled = true;
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
        script.onload = () => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            doExport();
        };
        document.head.appendChild(script);
    } else {
        doExport();
    }

    function doExport() {
        const rows = [['No', 'Nama Barang', 'Lab', 'Kategori', 'Merk', 'Model', 'Total', 'Baik', 'Rusak', 'Cadangan', 'Kondisi']];
        
        document.querySelectorAll('.ia-table tbody tr.ia-row').forEach((tr, i) => {
            if (tr.style.display === 'none') return;
            const d = tr.dataset;
            rows.push([
                i + 1,
                d.name,
                d.lab,
                d.category,
                d.brand,
                d.model,
                parseInt(d.qty),
                parseInt(d.good),
                parseInt(d.broken),
                parseInt(d.backup),
                d.condition
            ]);
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(rows);
        
        // Auto width
        const wscols = [5, 25, 15, 12, 15, 15, 8, 8, 8, 10, 10].map(w => ({ wch: w }));
        ws['!cols'] = wscols;

        XLSX.utils.book_append_sheet(wb, ws, "Inventaris");
        XLSX.writeFile(wb, `Laporan_Inventaris_${new Date().toISOString().slice(0,10)}.xlsx`);
    }
}

/* ══════════════════════════════════════
   DOM READY
══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

    /* Klik baris tabel */
    document.querySelector('.ia-table tbody')?.addEventListener('click', e => {
        const row = e.target.closest('tr.ia-row');
        if (row) iaSelectRow(row);
    });

    /* Tombol Tambah */
    document.getElementById('ia-btn-add')?.addEventListener('click', openAdd);

    /* Tombol Export Excel */
    document.getElementById('ia-btn-export-excel')?.addEventListener('click', exportToExcel);

    /* Tombol Log Perbaikan */
    document.getElementById('ia-d-btn-maintenance')?.addEventListener('click', openMaintenance);

    /* Tombol Export CSV lama (dihapus/diganti) */
    // document.getElementById('ia-btn-export')?.addEventListener('click', exportCSV);

    /* Tombol Edit di detail card */
    document.getElementById('ia-d-btn-edit')?.addEventListener('click', function () {
        const d = JSON.parse(this.dataset.row || '{}');
        openEdit(d);
    });

    /* Tombol Hapus di detail card → buka confirm modal */
    document.getElementById('ia-d-btn-del')?.addEventListener('click', function () {
        openConfirmDel(this.dataset.id, this.dataset.name);
    });

    /* Modal Add */
    document.getElementById('ia-close-add')?.addEventListener('click',  closeAdd);
    document.getElementById('ia-cancel-add')?.addEventListener('click', closeAdd);
    document.getElementById('add-modal')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeAdd();
    });

    /* Modal Edit */
    document.getElementById('ia-close-edit')?.addEventListener('click',  closeEdit);
    document.getElementById('ia-cancel-edit')?.addEventListener('click', closeEdit);
    document.getElementById('edit-modal')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeEdit();
    });

    /* Modal Confirm Delete */
    document.getElementById('ia-close-confirm')?.addEventListener('click',  closeConfirmDel);
    document.getElementById('ia-cancel-confirm')?.addEventListener('click', closeConfirmDel);
    document.getElementById('confirm-del-modal')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeConfirmDel();
    });

    /* Modal Maintenance */
    document.getElementById('ia-close-maintenance')?.addEventListener('click',  closeMaintenance);
    document.getElementById('ia-cancel-maintenance')?.addEventListener('click', closeMaintenance);
    document.getElementById('maintenance-modal')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeMaintenance();
    });

    /* Escape */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeAdd(); closeEdit(); closeConfirmDel(); closeMaintenance(); }
    });

    /* calcBroken */
    ['add-qty','add-good'].forEach(id =>
        document.getElementById(id)?.addEventListener('input', () => calcBroken('add'))
    );
    ['e-qty','e-good'].forEach(id =>
        document.getElementById(id)?.addEventListener('input', () => calcBroken('e'))
    );

    /* Search clear */
    const sinp  = document.getElementById('inv-search');
    const sclear = document.getElementById('inv-search-clear');
    if (sinp && sclear) {
        const sync = () => sclear.classList.toggle('visible', sinp.value.length > 0);
        sinp.addEventListener('input', sync);
        sync();
        sclear.addEventListener('click', () => { sinp.value=''; sync(); sinp.closest('form').submit(); });
    }

    /* Init fitur baru */
    initSorting();
    initBrokenHighlight();

    /* Flash session → Toast */
    document.querySelectorAll('.ia-flash-data').forEach(el => {
        iaToast(el.dataset.msg, el.dataset.type || 'ok');
        el.remove();
    });
});

/* ══════════════════════════════════════
   QUICK ACTION PANEL
   State: berapa unit yang akan dipindah
══════════════════════════════════════ */

// State quick action — direset tiap klik baris baru
let QA = {
    id: null,
    qty: 0, good: 0, broken: 0, backup: 0,  // nilai saat ini di DB
    deltaBroken: 0,   // +n = nambah rusak (kurangi baik)
    deltaBackup: 0,   // +n = pakai cadangan (cadangan−, baik+)
    deltaFixed:  0,   // +n = diperbaiki (rusak−, baik+)
};

function qaReset() {
    QA.deltaBroken = 0;
    QA.deltaBackup = 0;
    QA.deltaFixed  = 0;
    document.getElementById('qa-broken-val').textContent = 0;
    document.getElementById('qa-backup-val').textContent = 0;
    document.getElementById('qa-fixed-val').textContent  = 0;
    document.getElementById('qa-preview').style.display  = 'none';
    document.getElementById('qa-save-btn').disabled      = true;
    qaUpdateButtonStates();
}

/** Hitung nilai akhir good/broken/backup setelah delta diterapkan */
function qaCompute() {
    let good   = QA.good   - QA.deltaBroken + QA.deltaBackup + QA.deltaFixed;
    let broken = QA.broken + QA.deltaBroken - QA.deltaFixed;
    let backup = QA.backup - QA.deltaBackup;
    return { good, broken, backup };
}

/** Update preview dan enable/disable tombol save */
function qaRefreshUI() {
    const { good, broken, backup } = qaCompute();
    const hasChange = QA.deltaBroken !== 0 || QA.deltaBackup !== 0 || QA.deltaFixed !== 0;

    // Preview
    const preview = document.getElementById('qa-preview');
    if (hasChange) {
        preview.style.display = 'block';
        document.getElementById('qa-prev-good-old').textContent   = QA.good;
        document.getElementById('qa-prev-good-new').textContent   = good;
        document.getElementById('qa-prev-broken-old').textContent = QA.broken;
        document.getElementById('qa-prev-broken-new').textContent = broken;
        document.getElementById('qa-prev-backup-old').textContent = QA.backup;
        document.getElementById('qa-prev-backup-new').textContent = backup;

        // Warnai nilai baru
        document.getElementById('qa-prev-good-new').style.color   = good < QA.good   ? '#dc2626' : '#15803d';
        document.getElementById('qa-prev-broken-new').style.color = broken > QA.broken ? '#dc2626' : '#15803d';
        document.getElementById('qa-prev-backup-new').style.color = backup < QA.backup ? '#92400e' : '#15803d';
    } else {
        preview.style.display = 'none';
    }

    // Enable save hanya kalau ada perubahan dan semua nilai valid
    const valid = good >= 0 && broken >= 0 && backup >= 0;
    document.getElementById('qa-save-btn').disabled = !hasChange || !valid;

    qaUpdateButtonStates();
}

/** Disable tombol +/- yang tidak bisa dipakai */
function qaUpdateButtonStates() {
    const { good, broken, backup } = qaCompute();

    // Tandai Rusak: bisa tambah rusak selama masih ada baik
    document.getElementById('qa-broken-inc').disabled = good   <= 0;
    document.getElementById('qa-broken-dec').disabled = QA.deltaBroken <= 0;

    // Gunakan Cadangan: bisa selama ada cadangan
    document.getElementById('qa-backup-inc').disabled = backup <= 0;
    document.getElementById('qa-backup-dec').disabled = QA.deltaBackup <= 0;

    // Diperbaiki: bisa selama ada yang rusak
    document.getElementById('qa-fixed-inc').disabled = broken <= 0;
    document.getElementById('qa-fixed-dec').disabled = QA.deltaFixed <= 0;
}

/** Init semua event listener quick action */
function initQuickActions() {
    // Tandai Rusak
    document.getElementById('qa-broken-inc')?.addEventListener('click', () => {
        QA.deltaBroken++;
        document.getElementById('qa-broken-val').textContent = QA.deltaBroken;
        qaRefreshUI();
    });
    document.getElementById('qa-broken-dec')?.addEventListener('click', () => {
        if (QA.deltaBroken > 0) QA.deltaBroken--;
        document.getElementById('qa-broken-val').textContent = QA.deltaBroken;
        qaRefreshUI();
    });

    // Gunakan Cadangan
    document.getElementById('qa-backup-inc')?.addEventListener('click', () => {
        QA.deltaBackup++;
        document.getElementById('qa-backup-val').textContent = QA.deltaBackup;
        qaRefreshUI();
    });
    document.getElementById('qa-backup-dec')?.addEventListener('click', () => {
        if (QA.deltaBackup > 0) QA.deltaBackup--;
        document.getElementById('qa-backup-val').textContent = QA.deltaBackup;
        qaRefreshUI();
    });

    // Diperbaiki
    document.getElementById('qa-fixed-inc')?.addEventListener('click', () => {
        QA.deltaFixed++;
        document.getElementById('qa-fixed-val').textContent = QA.deltaFixed;
        qaRefreshUI();
    });
    document.getElementById('qa-fixed-dec')?.addEventListener('click', () => {
        if (QA.deltaFixed > 0) QA.deltaFixed--;
        document.getElementById('qa-fixed-val').textContent = QA.deltaFixed;
        qaRefreshUI();
    });

    // Tombol Simpan
    document.getElementById('qa-save-btn')?.addEventListener('click', qaSave);
}

/** Kirim PATCH ke controller quickUpdate via fetch */
async function qaSave() {
    const saveBtn = document.getElementById('qa-save-btn');
    if (!QA.id) return;

    const { good, broken, backup } = qaCompute();

    saveBtn.disabled  = true;
    saveBtn.textContent = 'Menyimpan...';
    saveBtn.classList.add('loading');

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const url       = `/inventaris-admin/${QA.id}/quick-update`;

        const res = await fetch(url, {
            method:  'PATCH',
            headers: {
                'Content-Type':     'application/json',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                quantity_good:   good,
                quantity_broken: broken,
                quantity_backup: backup,
            }),
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            iaToast(data.message || 'Gagal menyimpan.', 'err');
            return;
        }

        // Update state QA dengan nilai baru dari server
        QA.good   = data.item.quantity_good;
        QA.broken = data.item.quantity_broken;
        QA.backup = data.item.quantity_backup;

        // Update detail card qty boxes & progress bar
        document.getElementById('ia-d-qty').textContent    = data.item.quantity;
        document.getElementById('ia-d-good').textContent   = data.item.quantity_good;
        document.getElementById('ia-d-broken').textContent = data.item.quantity_broken;
        document.getElementById('ia-d-backup').textContent = data.item.quantity_backup;
        document.getElementById('ia-d-broken-box').classList.toggle('has', data.item.quantity_broken > 0);

        // Update progress bar
        const pct    = data.item.quantity > 0
            ? Math.round((data.item.quantity_good / data.item.quantity) * 100) : 0;
        const bar    = document.getElementById('ia-d-bar');
        const pctEl  = document.getElementById('ia-d-pct');
        bar.style.width = pct + '%';
        bar.className   = 'ia-progress-bar' + (pct < 50 ? ' danger' : pct < 80 ? ' warn' : '');
        pctEl.textContent = pct + '% baik';

        // Update badge kondisi
        const condEl = document.getElementById('ia-d-cond');
        condEl.textContent = IA_COND[data.item.condition] || data.item.condition;
        condEl.className   = 'ia-badge cond-' + data.item.condition;

        // Update baris tabel
        const row = document.querySelector(`.ia-row[data-id="${QA.id}"]`);
        if (row) {
            row.dataset.good    = data.item.quantity_good;
            row.dataset.broken  = data.item.quantity_broken;
            row.dataset.backup  = data.item.quantity_backup;
            row.dataset.condition = data.item.condition;

            // Update angka di tabel
            row.querySelector('.ia-ng')?.replaceWith(
                Object.assign(document.createElement('span'), {
                    className: 'ia-num ia-ng', textContent: data.item.quantity_good
                })
            );
            const brokenSpan = row.querySelector('.ia-nb');
            if (brokenSpan) {
                brokenSpan.textContent = data.item.quantity_broken;
                brokenSpan.classList.toggle('bad', data.item.quantity_broken > 0);
            }
            // Badge kondisi di tabel
            const condBadge = row.querySelector('.ia-badge[class*="cond-"]');
            if (condBadge) {
                condBadge.className   = 'ia-badge cond-' + data.item.condition;
                condBadge.textContent = IA_COND[data.item.condition] || data.item.condition;
            }
            // Highlight rusak
            row.classList.toggle('has-broken', data.item.quantity_broken > 0);
        }

        // Update stats cards
        if (data.stats) {
            qaUpdateStats(data.stats);
        }

        // Reset quick action panel
        qaReset();
        iaToast('Data berhasil diperbarui.');

    } catch (err) {
        console.error(err);
        iaToast('Terjadi kesalahan jaringan.', 'err');
    } finally {
        saveBtn.textContent = 'Simpan Perubahan';
        saveBtn.classList.remove('loading');
    }
}

/** Update stats cards dari response server */
function qaUpdateStats(stats) {
    // Sesuaikan key dengan yang dikembalikan calculateStats()
    const map = {
        'total_items': document.querySelector('.s-total .ia-stat-val'),
        'total_units': document.querySelector('.s-units .ia-stat-val'),
        'total_good':  document.querySelector('.s-good  .ia-stat-val'),
        'total_broken':document.querySelector('.s-broken .ia-stat-val'),
    };
    Object.entries(map).forEach(([key, el]) => {
        if (el && stats[key] !== undefined) {
            // Animasi angka berubah
            el.style.transition = 'opacity .15s';
            el.style.opacity    = '0';
            setTimeout(() => {
                el.textContent  = stats[key];
                el.style.opacity = '1';
            }, 150);
        }
    });
}

// Panggil initQuickActions di DOMContentLoaded
// (tambahkan ke dalam block DOMContentLoaded yang sudah ada)
document.addEventListener('DOMContentLoaded', () => {
    initQuickActions();
});