/**
 * inventory.js — Logic halaman Inventaris Publik
 * Pastikan file ini di-import di vite.config.js
 */

let currentView = 'table';
let currentLab  = window.firstLabId ?? 0;

// ══════════════════════════════════════════════════════════════════
// SheetJS Lazy Loader — ~1MB hanya dimuat saat klik Excel
// ══════════════════════════════════════════════════════════════════
let xlsxLoaded    = false;
let xlsxLoading   = false;
let xlsxCallbacks = [];

function loadXLSX(callback) {
    if (xlsxLoaded) { callback(); return; }

    xlsxCallbacks.push(callback);
    if (xlsxLoading) return;
    xlsxLoading = true;

    const btn = document.getElementById('btn-excel-main');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = `<span class="xlsx-loading">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
        </svg>
        Memuat...
    </span>`;
    btn.disabled = true;

    const script  = document.createElement('script');
    script.src    = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
    script.onload = function () {
        xlsxLoaded  = true;
        xlsxLoading = false;
        btn.innerHTML = originalHTML;
        btn.disabled  = false;
        xlsxCallbacks.forEach(cb => cb());
        xlsxCallbacks = [];
    };
    script.onerror = function () {
        xlsxLoading   = false;
        xlsxCallbacks = [];
        btn.innerHTML = originalHTML;
        btn.disabled  = false;
        showToast('❌ Gagal memuat library Excel. Cek koneksi internet.');
    };
    document.head.appendChild(script);
}

// ══════════════════════════════════════════════════════════════════
// TAB SWITCH
// ══════════════════════════════════════════════════════════════════
function switchLab(id, btn) {
    if (currentLab === id) return;
    currentLab = id;
    window.currentLab = id; // Update global scope

    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.lab-panel').forEach(p => p.classList.remove('active'));

    const skeleton = document.getElementById('skeleton');
    skeleton.style.display = 'block';

    setTimeout(() => {
        skeleton.style.display = 'none';
        const panel = document.getElementById('lab-' + id);
        panel.classList.add('active');
        panel.style.animation = 'none';
        void panel.offsetWidth;
        panel.style.animation = '';
        filterTable();
    }, 180);
}

// ══════════════════════════════════════════════════════════════════
// VIEW TOGGLE (tabel / kartu)
// ══════════════════════════════════════════════════════════════════
function setView(v) {
    currentView = v;
    const btnTable = document.getElementById('btn-table');
    const btnCard  = document.getElementById('btn-card');
    if (btnTable) btnTable.classList.toggle('active', v === 'table');
    if (btnCard)  btnCard.classList.toggle('active',  v === 'card');
    document.querySelectorAll('[id^="table-"]').forEach(el => el.style.display = v === 'table' ? '' : 'none');
    document.querySelectorAll('[id^="cards-"]').forEach(el => el.classList.toggle('show', v === 'card'));
    filterTable();
}

// ══════════════════════════════════════════════════════════════════
// DROPDOWN EXCEL
// ══════════════════════════════════════════════════════════════════
function toggleExcelDropdown(event) {
    event.stopPropagation();
    document.getElementById('excelDropdown').classList.toggle('show');
}

window.addEventListener('click', function (e) {
    const dropdown = document.getElementById('excelDropdown');
    if (dropdown && dropdown.classList.contains('show') && !dropdown.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

// ══════════════════════════════════════════════════════════════════
// FILTER (search + kategori + kondisi)
// ══════════════════════════════════════════════════════════════════
function filterTable() {
    const search   = document.getElementById('search-inp').value.toLowerCase();
    const cat      = document.getElementById('cat-filter').value;
    const cond     = document.getElementById('cond-filter').value;
    const panel    = document.querySelector('.lab-panel.active');
    const clearBtn = document.getElementById('search-clear');

    if (clearBtn) clearBtn.style.display = search ? 'flex' : 'none';
    if (!panel) return;

    // Reset highlight
    panel.querySelectorAll('.search-target').forEach(el => {
        el.innerHTML = el.innerText;
    });

    let n = 1;
    panel.querySelectorAll('tbody tr:not(.empty-row):not(.no-result-row)').forEach(row => {
        const match = (!search || row.dataset.name.includes(search) || row.dataset.brand.includes(search) || row.dataset.specs.includes(search))
                   && (!cat   || row.dataset.category  === cat)
                   && (!cond  || row.dataset.condition === cond);
        row.style.display = match ? '' : 'none';

        if (match) {
            row.querySelector('.no-col').textContent = n++;
            if (search && search.length > 1) {
                row.querySelectorAll('.search-target').forEach(el => {
                    const regex = new RegExp(`(${search})`, 'gi');
                    el.innerHTML = el.innerText.replace(regex, '<mark class="search-match">$1</mark>');
                });
            }
        }
    });

    // Filter kartu
    panel.querySelectorAll('.inv-card').forEach(card => {
        const match = (!search || card.dataset.name.includes(search) || card.dataset.brand.includes(search) || card.dataset.specs.includes(search))
                   && (!cat   || card.dataset.category  === cat)
                   && (!cond  || card.dataset.condition === cond);
        card.style.display = match ? '' : 'none';
    });

    // Tampilkan no-result row kalau semua tersembunyi
    const tbody        = panel.querySelector('tbody');
    const visibleRows  = [...tbody.querySelectorAll('tr:not(.empty-row):not(.no-result-row)')].filter(r => r.style.display !== 'none');
    let   noResultRow  = tbody.querySelector('.no-result-row');

    if (visibleRows.length === 0 && !tbody.querySelector('.empty-row')) {
        if (!noResultRow) {
            noResultRow = document.createElement('tr');
            noResultRow.className = 'no-result-row';
            const td = document.createElement('td');
            td.colSpan = 11;
            td.innerHTML = `<div class="empty-state">
                <div class="empty-icon no-result">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#92400e" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M8 11h6"/>
                    </svg>
                </div>
                <p class="empty-title">Tidak ada barang yang cocok</p>
                <p class="empty-desc">Coba ubah kata kunci atau reset filter.</p>
                <div class="btn-group">
                    <button class="empty-btn primary" onclick="resetFilter()">Reset Filter</button>
                </div>
            </div>`;
            noResultRow.appendChild(td);
            tbody.appendChild(noResultRow);
        }
        noResultRow.style.display = '';
    } else if (noResultRow) {
        noResultRow.style.display = 'none';
    }
}

function resetSearch() {
    document.getElementById('search-inp').value = '';
    filterTable();
    document.getElementById('search-inp').focus();
}

function resetFilter() {
    document.getElementById('search-inp').value  = '';
    document.getElementById('cat-filter').value  = '';
    document.getElementById('cond-filter').value = '';
    filterTable();
}

// ══════════════════════════════════════════════════════════════════
// TOAST
// ══════════════════════════════════════════════════════════════════
function showToast(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// ══════════════════════════════════════════════════════════════════
// EXPORT EXCEL — Lab aktif saja
// ══════════════════════════════════════════════════════════════════
function exportExcel() {
    document.getElementById('excelDropdown').classList.remove('show');
    loadXLSX(function () {
        const labName = document.querySelector('.tab-btn.active').textContent.trim().split('\n')[0].trim();
        const rows    = [['No','Nama Barang','Kategori','Merk','Model','Spesifikasi','Total','Baik','Rusak','Cadangan','Catatan']];

        document.querySelector('.lab-panel.active tbody')
            .querySelectorAll('tr:not(.empty-row):not(.no-result-row)')
            .forEach(row => {
                if (row.style.display === 'none') return;
                const c         = row.querySelectorAll('td');
                const brandDivs = c[3].querySelectorAll('.search-target');
                rows.push([
                    parseInt(c[0].textContent) || '',
                    (c[1].querySelector('.search-target')?.innerText || '').trim(),
                    (c[2].querySelector('.badge')?.innerText        || '').trim(),
                    (brandDivs[0]?.innerText || '').trim(),
                    (brandDivs[1]?.innerText || '').trim(),
                    (c[4].querySelector('.search-target')?.innerText || '').trim(),
                    parseInt(c[5].textContent) || 0,
                    parseInt(c[6].textContent) || 0,
                    parseInt(c[7].textContent) || 0,
                    parseInt(c[8].textContent) || 0,
                    c[10].textContent.trim(),
                ]);
            });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(rows);
        ws['!cols'] = [5,25,14,16,14,20,7,7,7,9,20].map(w => ({wch: w}));
        XLSX.utils.book_append_sheet(wb, ws, labName.substring(0, 31));
        XLSX.writeFile(wb, `Inventaris_${labName.replace(/\s+/g,'_')}_${new Date().toISOString().slice(0,10)}.xlsx`);
        showToast('📗 Excel berhasil diunduh');
    });
}

// ══════════════════════════════════════════════════════════════════
// EXPORT EXCEL — Semua lab (multi-sheet)
// ══════════════════════════════════════════════════════════════════
function exportAllExcel() {
    document.getElementById('excelDropdown').classList.remove('show');
    loadXLSX(function () {
        const wb = XLSX.utils.book_new();

        document.querySelectorAll('.lab-panel').forEach(panel => {
            const labName = panel.querySelector('.panel-title').textContent.trim();
            const rows    = [['No','Nama Barang','Kategori','Merk','Model','Spesifikasi','Total','Baik','Rusak','Cadangan','Catatan']];

            panel.querySelectorAll('tbody tr:not(.empty-row):not(.no-result-row)').forEach((row, idx) => {
                const c         = row.querySelectorAll('td');
                const brandDivs = c[3].querySelectorAll('.search-target');
                rows.push([
                    idx + 1,
                    (c[1].querySelector('.search-target')?.innerText || '').trim(),
                    (c[2].querySelector('.badge')?.innerText        || '').trim(),
                    (brandDivs[0]?.innerText || '').trim(),
                    (brandDivs[1]?.innerText || '').trim(),
                    (c[4].querySelector('.search-target')?.innerText || '').trim(),
                    parseInt(c[5].textContent) || 0,
                    parseInt(c[6].textContent) || 0,
                    parseInt(c[7].textContent) || 0,
                    parseInt(c[8].textContent) || 0,
                    c[10].textContent.trim(),
                ]);
            });

            if (rows.length > 1) {
                const ws = XLSX.utils.aoa_to_sheet(rows);
                ws['!cols'] = [5,25,14,16,14,20,7,7,7,9,20].map(w => ({wch: w}));
                XLSX.utils.book_append_sheet(wb, ws, labName.substring(0, 31));
            }
        });

        XLSX.writeFile(wb, `Inventaris_Semua_Lab_${new Date().toISOString().slice(0,10)}.xlsx`);
        showToast('📊 Semua lab berhasil diunduh');
    });
}

// ══════════════════════════════════════════════════════════════════
// EXPORT PDF (buka tab baru lalu print)
// ══════════════════════════════════════════════════════════════════
function exportPDF() {
    const labName = document.querySelector('.tab-btn.active').textContent.trim().split('\n')[0].trim();
    const rows    = [];

    document.querySelector('.lab-panel.active tbody')
        .querySelectorAll('tr:not(.empty-row):not(.no-result-row)')
        .forEach(row => {
            if (row.style.display === 'none') return;
            const c = row.querySelectorAll('td');
            rows.push([c[0],c[1],c[2],c[3],c[4],c[5],c[6],c[7],c[8],c[9]].map(x => x.textContent.trim()));
        });

    const html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Inventaris ${labName}</title>
    <style>
        body{font-family:Arial,sans-serif;font-size:11px;color:#1A2517;padding:20px}
        h2{font-size:16px;margin-bottom:4px}p{font-size:11px;color:#666;margin-bottom:14px}
        table{width:100%;border-collapse:collapse}
        th{background:#1A2517;color:#ACC8A2;padding:7px 8px;text-align:left;font-size:10px}
        td{padding:6px 8px;border-bottom:1px solid #e5e7eb;font-size:11px}
        tr:nth-child(even) td{background:#f8faf7}
        .footer{margin-top:16px;font-size:10px;color:#999;text-align:center}
    </style></head><body>
    <h2>Inventaris ${labName}</h2>
    <p>Lab Management – Nuris Jember &nbsp;|&nbsp; Dicetak: ${new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'})}</p>
    <table><thead><tr>
        <th>No</th><th>Nama Barang</th><th>Kategori</th><th>Merk/Model</th>
        <th>Spesifikasi</th><th>Total</th><th>Baik</th><th>Rusak</th><th>Cadangan</th><th>Kondisi</th>
    </tr></thead>
    <tbody>${rows.map(r => '<tr>' + r.map(c => `<td>${c}</td>`).join('') + '</tr>').join('')}</tbody>
    </table>
    <div class="footer">Total ${rows.length} jenis barang</div>
    </body></html>`;

    const win = window.open('', '_blank');
    win.document.write(html);
    win.document.close();
    win.focus();
    setTimeout(() => { win.print(); showToast('PDF siap dicetak'); }, 500);
}

// ══════════════════════════════════════════════════════════════════
// EXPOSE KE GLOBAL — wajib karena Vite bundle sebagai ES module
// ══════════════════════════════════════════════════════════════════
window.currentLab          = currentLab;
window.switchLab           = switchLab;
window.setView             = setView;
window.toggleExcelDropdown = toggleExcelDropdown;
window.filterTable         = filterTable;
window.resetSearch         = resetSearch;
window.resetFilter         = resetFilter;
window.exportExcel         = exportExcel;
window.exportAllExcel      = exportAllExcel;
window.exportPDF           = exportPDF;
window.showToast           = showToast;

// ══════════════════════════════════════════════════════════════════
// INIT — jalankan setelah DOM siap
// ══════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth <= 768) {
        setView('card');
    }
});