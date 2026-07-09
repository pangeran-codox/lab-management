@extends('finance.layouts.app')
@section('title', 'Tambah Transaksi — Keuangan Tim')
@section('page-title', 'Tambah Transaksi')

@push('styles')
<style>
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes slideRight {
    from { opacity: 0; transform: translateX(-16px); }
    to   { opacity: 1; transform: translateX(0); }
}
.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

.form-wrap { max-width: 620px; }

/* ── Type Toggle ── */
.type-toggle {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 10px; margin-bottom: 22px;
}
.type-opt { position: relative; }
.type-opt input { position: absolute; opacity: 0; width: 0; height: 0; }
.type-lbl {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    padding: 14px 16px;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 14px; font-weight: 600; color: var(--muted);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
    user-select: none;
    position: relative; overflow: hidden;
}
.type-lbl::before {
    content: ''; position: absolute;
    inset: 0; opacity: 0;
    transition: opacity 0.2s;
}
.type-lbl .lbl-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.25s;
    flex-shrink: 0;
}
.type-lbl .lbl-icon svg { width: 18px; height: 18px; }

/* Income option */
.type-opt input[value="income"]:checked ~ .type-lbl {
    border-color: #16a34a;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    color: #15803d;
    transform: scale(1.02);
    box-shadow: 0 6px 20px rgba(22,163,74,0.2);
}
.type-opt input[value="income"]:checked ~ .type-lbl .lbl-icon {
    background: #16a34a; color: white;
    box-shadow: 0 4px 12px rgba(22,163,74,0.4);
}

/* Expense option */
.type-opt input[value="expense"]:checked ~ .type-lbl {
    border-color: var(--red);
    background: linear-gradient(135deg, #fff1f2, #ffe4e6);
    color: #be123c;
    transform: scale(1.02);
    box-shadow: 0 6px 20px rgba(255,77,109,0.2);
}
.type-opt input[value="expense"]:checked ~ .type-lbl .lbl-icon {
    background: var(--red); color: white;
    box-shadow: 0 4px 12px rgba(255,77,109,0.4);
}

/* Unselected icon */
.type-lbl .lbl-icon {
    background: var(--bg);
    color: var(--muted);
}

/* ── Amount Field ── */
.amount-wrap { position: relative; }
.amount-prefix {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%);
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px; font-weight: 600; color: var(--muted);
    pointer-events: none;
}
.amount-input { padding-left: 42px !important; font-family: 'JetBrains Mono', monospace !important; font-size: 16px !important; font-weight: 600 !important; }

/* ── Description chips suggestion ── */
.desc-hint { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 7px; }
.desc-chip {
    padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 600;
    background: var(--bg); border: 1px solid var(--border);
    color: var(--muted); cursor: pointer;
    transition: all 0.15s;
}
.desc-chip:hover { background: var(--g1); color: white; border-color: var(--g1); }

/* ── Divider ── */
.form-divider {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: var(--muted);
    display: flex; align-items: center; gap: 10px;
    margin: 20px 0 16px;
}
.form-divider::before, .form-divider::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
}

/* ── Form Actions ── */
.form-actions { display: flex; gap: 10px; margin-top: 24px; }

/* ── Info box ── */
.info-box {
    background: rgba(0,232,122,0.06);
    border: 1px solid rgba(0,232,122,0.2);
    border-radius: 10px; padding: 12px 14px;
    font-size: 12px; color: #15803d;
    display: flex; align-items: flex-start; gap: 8px;
    margin-bottom: 20px;
}
.info-box svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }

/* ── Upload Area ── */
.upload-area {
    border: 2px dashed var(--border);
    border-radius: 12px; padding: 28px 20px;
    text-align: center; cursor: pointer;
    transition: all 0.2s; background: var(--bg);
}
.upload-area:hover, .upload-area.drag-over {
    border-color: var(--accent);
    background: rgba(0,232,122,0.04);
}
.upload-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: var(--border); color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px;
}
.upload-icon svg { width: 22px; height: 22px; }
.upload-text { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.upload-hint { font-size: 11px; color: var(--muted); }

@media (max-width: 600px) {
    .form-wrap { max-width: 100%; }
    .form-row { grid-template-columns: 1fr !important; }
}
</style>
@endpush

@section('content')

<div class="form-wrap float-in" style="animation-delay:.05s">

    <div class="card">
        <div class="card-head">
            <span class="card-title">Transaksi Baru</span>
            <a href="{{ route('finance.transactions.index') }}" class="btn btn-secondary" style="font-size:11.5px;padding:6px 12px;">
                ← Kembali
            </a>
        </div>
        <div class="card-body">

            <div class="info-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Notifikasi WhatsApp akan dikirim otomatis setelah transaksi disimpan.
            </div>

            <form method="POST" action="{{ route('finance.transactions.store') }}" id="trxForm" enctype="multipart/form-data">
                @csrf

                {{-- Type Toggle --}}
                <div class="form-group">
                    <label class="form-label">Tipe Transaksi</label>
                    <div class="type-toggle">
                        <div class="type-opt">
                            <input type="radio" name="type" id="t_income" value="income"
                                   {{ old('type', 'income') === 'income' ? 'checked' : '' }}>
                            <label for="t_income" class="type-lbl">
                                <div class="lbl-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                </div>
                                Pemasukan
                            </label>
                        </div>
                        <div class="type-opt">
                            <input type="radio" name="type" id="t_expense" value="expense"
                                   {{ old('type') === 'expense' ? 'checked' : '' }}>
                            <label for="t_expense" class="type-lbl">
                                <div class="lbl-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                </div>
                                Pengeluaran
                            </label>
                        </div>
                    </div>
                    @error('type')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- Amount --}}
                <div class="form-group">
                    <label class="form-label" for="amount">Jumlah</label>
                    <div class="amount-wrap">
                        <span class="amount-prefix">Rp</span>
                        <input type="number" name="amount" id="amount"
                               class="form-control amount-input {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                               value="{{ old('amount') }}" min="1" required placeholder="0"
                               oninput="formatPreview(this)">
                    </div>
                    <div id="amountPreview" style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--muted);margin-top:5px;"></div>
                    @error('amount')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                <div class="form-row" style="grid-template-columns:1fr 1fr;">
                    {{-- Kategori --}}
                    <div class="form-group">
                        <label class="form-label" for="category_id">Kategori</label>
                        <select name="category_id" id="category_id"
                                class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Pilih --</option>
                            <optgroup label="Pemasukan">
                                @foreach($categories->where('type', 'income') as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Pengeluaran">
                                @foreach($categories->where('type', 'expense') as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('category_id')<p class="invalid-feedback">{{ $message }}</p>@enderror
                    </div>

                    {{-- Tanggal --}}
                    <div class="form-group">
                        <label class="form-label" for="transaction_date">Tanggal</label>
                        <input type="date" name="transaction_date" id="transaction_date"
                               class="form-control {{ $errors->has('transaction_date') ? 'is-invalid' : '' }}"
                               value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                        @error('transaction_date')<p class="invalid-feedback">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="form-group">
                    <label class="form-label" for="description">Keterangan</label>
                    <input type="text" name="description" id="description"
                           class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                           value="{{ old('description') }}" required placeholder="Deskripsi singkat transaksi">
                    <div class="desc-hint" id="descHints">
                        <span class="desc-chip" onclick="setDesc('Dana operasional')">Dana operasional</span>
                        <span class="desc-chip" onclick="setDesc('Beli ATK')">Beli ATK</span>
                        <span class="desc-chip" onclick="setDesc('Konsumsi rapat')">Konsumsi rapat</span>
                        <span class="desc-chip" onclick="setDesc('Transport')">Transport</span>
                        <span class="desc-chip" onclick="setDesc('Dana institusi')">Dana institusi</span>
                    </div>
                    @error('description')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- Sumber Dana --}}
                <div class="form-group">
                    <label class="form-label" for="account_id">Sumber Dana</label>
                    <select name="account_id" id="account_id"
                            class="form-control {{ $errors->has('account_id') ? 'is-invalid' : '' }}" required>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}"
                                {{ old('account_id', $accounts->first()?->id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }} — Rp {{ number_format($acc->balance, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_id')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                <div class="form-divider">Opsional</div>

                @if($periods->isNotEmpty())
                <div class="form-group">
                    <label class="form-label" for="budget_period_id">Periode Anggaran</label>
                    <select name="budget_period_id" id="budget_period_id" class="form-control">
                        <option value="">-- Tidak dikaitkan ke anggaran --</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}"
                                {{ old('budget_period_id') == $period->id || ($period->month == now()->month && $period->year == now()->year) ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="form-hint">Kaitkan ke anggaran agar penggunaan budget terpantau otomatis</p>
                </div>
                @endif

                <div class="form-row" style="grid-template-columns:1fr 1fr;">
                    <div class="form-group">
                        <label class="form-label" for="reference">No. Referensi / Bukti</label>
                        <input type="text" name="reference" id="reference" class="form-control"
                               value="{{ old('reference') }}" placeholder="No. kwitansi, nota, dll">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="notes">Catatan</label>
                        <input type="text" name="notes" id="notes" class="form-control"
                               value="{{ old('notes') }}" placeholder="Catatan tambahan">
                    </div>
                </div>

                {{-- Foto / Lampiran --}}
                <div class="form-group">
                    <label class="form-label">
                        Foto Nota / Barang
                        <span style="font-weight:400;color:var(--muted)">(opsional)</span>
                    </label>
                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('attachment').click()">
                        <div class="upload-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="upload-text">Klik atau seret foto ke sini</div>
                        <div class="upload-hint">JPG, PNG, WEBP — maks. 5 MB</div>
                    </div>
                    <input type="file" name="attachment" id="attachment"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           style="display:none" onchange="previewAttachment(this)">
                    <div id="attachPreview" style="display:none;margin-top:10px">
                        <div style="position:relative;display:inline-block">
                            <img id="previewImg" src="" alt="Preview"
                                 style="max-width:200px;max-height:160px;border-radius:10px;
                                        border:2px solid var(--border);object-fit:cover">
                            <button type="button" onclick="clearAttachment()"
                                    style="position:absolute;top:-8px;right:-8px;
                                           width:22px;height:22px;border-radius:50%;
                                           background:var(--red);color:white;border:none;
                                           cursor:pointer;font-size:12px;display:flex;
                                           align-items:center;justify-content:center">×</button>
                        </div>
                        <div id="previewName" style="font-size:11px;color:var(--muted);margin-top:5px"></div>
                    </div>
                    @error('attachment')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan & Kirim Notif WA
                    </button>
                    <a href="{{ route('finance.transactions.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function formatPreview(input) {
    const val = parseFloat(input.value);
    const el  = document.getElementById('amountPreview');
    if (!val || val <= 0) { el.textContent = ''; return; }
    el.textContent = '= Rp ' + val.toLocaleString('id-ID');
}

function setDesc(text) {
    document.getElementById('description').value = text;
    document.getElementById('description').focus();
}

// ── Attachment preview ──
function previewAttachment(input) {
    const file = input.files[0];
    const wrap = document.getElementById('attachPreview');
    const img  = document.getElementById('previewImg');
    const name = document.getElementById('previewName');

    if (!file) {
        wrap.style.display = 'none';
        return;
    }

    // Validasi ukuran maksimal 5MB
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file maksimal 5 MB');
        input.value = '';
        wrap.style.display = 'none';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result;
        name.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        wrap.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function clearAttachment() {
    const input = document.getElementById('attachment');
    input.value = '';
    document.getElementById('attachPreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
}

// ── Drag & drop support ──
const uploadArea  = document.getElementById('uploadArea');
const attachInput = document.getElementById('attachment');

['dragover', 'dragenter'].forEach(evt => {
    uploadArea.addEventListener(evt, function(e) {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });
});

['dragleave', 'dragend'].forEach(evt => {
    uploadArea.addEventListener(evt, function(e) {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
    });
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    const files = e.dataTransfer.files;
    if (files.length) {
        attachInput.files = files;
        previewAttachment(attachInput);
    }
});

// Submit loading state
document.getElementById('trxForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg style="animation:spin .8s linear infinite;width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Menyimpan...`;
});

const style = document.createElement('style');
style.textContent = `@keyframes spin { to { transform: rotate(360deg); } }`;
document.head.appendChild(style);
</script>
@endpush