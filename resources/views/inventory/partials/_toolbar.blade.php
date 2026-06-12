<div class="toolbar">
    {{-- Search --}}
    <div class="search-container">
        <input type="text" id="search-inp" class="filter-inp"
               value="{{ request('search') }}"
               placeholder="🔍 Cari nama barang, merk, spesifikasi..."
               oninput="filterTable()">
        <span class="search-clear" id="search-clear" onclick="resetSearch()" style="display:none">×</span>
    </div>

    {{-- Filter Kategori --}}
    <select id="cat-filter" class="filter-inp" onchange="filterTable()">
        <option value="">Semua Kategori</option>
        <option value="computer">💻 Komputer</option>
        <option value="peripheral">🖱 Peripheral</option>
        <option value="network">🌐 Jaringan</option>
        <option value="furniture">🪑 Furniture</option>
        <option value="software">💿 Software</option>
        <option value="other">📦 Lainnya</option>
    </select>

    {{-- Filter Kondisi --}}
    <select id="cond-filter" class="filter-inp" onchange="filterTable()">
        <option value="">Semua Kondisi</option>
        <option value="excellent">✨ Sangat Baik</option>
        <option value="good">✓ Baik</option>
        <option value="fair">⚠ Cukup</option>
        <option value="poor">⚠ Buruk</option>
        <option value="broken">✗ Rusak</option>
    </select>

    {{-- View Toggle --}}
    <div class="view-toggle">
        <button class="view-btn active" id="btn-table" onclick="setView('table')">☰ Tabel</button>
        <button class="view-btn"        id="btn-card"  onclick="setView('card')">⊞ Kartu</button>
    </div>

    {{-- Export --}}
    <div class="export-group">
        <div class="dropdown">
            <button class="btn-export btn-excel" id="btn-excel-main" onclick="toggleExcelDropdown(event)">
                <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 17l1.5-2.5L8.5 12H10l.75 1.5L11.5 12H13l-1.5 2.5L13 17h-1.5l-.75-1.5-.75 1.5H8.5z"/>
                </svg>
                Excel
            </button>
            <div id="excelDropdown" class="dropdown-content">
                <div class="dropdown-header">Pilihan Export Excel</div>
                <a href="javascript:void(0)" onclick="exportExcel()">📗 Lab Aktif Saja</a>
                <a href="javascript:void(0)" onclick="exportAllExcel()" style="color:#16a34a;font-weight:700">📊 Semua Lab (Multi-Sheet)</a>
            </div>
        </div>

        <button class="btn-export btn-pdf" onclick="window.location.href='{{ route('inventory.public.pdf') }}?resource_id=' + currentLab" title="Editor & Cetak PDF Inventaris">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editor & Cetak PDF
        </button>
    </div>
</div>