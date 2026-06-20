{{-- resources/views/schedule/partials/modal-detail.blade.php --}}
<div class="detail-overlay" id="detail-overlay"
     onclick="if(event.target===this)closeDetail()"
     role="dialog" aria-modal="true" aria-labelledby="d-teacher">
    <div class="detail-box" id="detail-box">
        <div class="detail-head" id="detail-head">
            <div class="detail-head-top">
                <div>
                    <div class="detail-type" id="d-type"></div>
                    <div class="detail-teacher" id="d-teacher"></div>
                </div>
                <button class="detail-close" onclick="closeDetail()" aria-label="Tutup detail">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="detail-body" id="detail-body"></div>
    </div>
</div>

<script>
function showDetail(d) {
    const overlay = document.getElementById('detail-overlay');
    const head    = document.getElementById('detail-head');
    const dType   = document.getElementById('d-type');
    const dTeacher= document.getElementById('d-teacher');
    const body    = document.getElementById('detail-body');

    // Reset accent class di header
    head.className = 'detail-head';

    // ── Konfigurasi per type ──
    if (d.type === 'important') {
        head.classList.add('detail-head--important');
        dType.textContent    = '🔒 Jadwal Penting';
        dTeacher.textContent = d.title ?? '';

        body.innerHTML = `
            ${d.desc ? `
            <div class="detail-row">
                <span class="detail-icon">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </span>
                <span>${escHtml(d.desc)}</span>
            </div>` : ''}
            <div class="detail-row">
                <span class="detail-icon">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <span>${escHtml(d.slot ?? '')}${d.time ? ' · ' + escHtml(d.time) : ''}</span>
            </div>
            <div class="detail-row">
                <span class="detail-icon">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <span>${escHtml(d.lab ?? '')}</span>
            </div>
            <div class="detail-badge detail-badge--important">Tidak tersedia untuk booking</div>
        `;

    } else if (d.type === 'tetap') {
        head.classList.add('detail-head--tetap');
        dType.textContent    = 'Jadwal Tetap';
        dTeacher.textContent = d.teacher ?? '';

        body.innerHTML = `
            ${d.class_name ? rowHtml(iconClass(), d.class_name) : ''}
            ${d.subject    ? rowHtml(iconBook(),  d.subject)    : ''}
            ${rowHtml(iconClock(), (d.slot ?? '') + (d.time ? ' · ' + d.time : ''))}
            ${rowHtml(iconLab(),   d.lab ?? '')}
        `;

    } else {
        // approved / pending
        const isPending = d.type === 'pending';
        head.classList.add(isPending ? 'detail-head--pending' : 'detail-head--approved');
        dType.textContent    = isPending ? '⏳ Menunggu Persetujuan' : '✓ Disetujui';
        dTeacher.textContent = d.teacher ?? '';

        body.innerHTML = `
            ${d.class_name   ? rowHtml(iconClass(),   d.class_name)                  : ''}
            ${d.subject      ? rowHtml(iconBook(),    d.subject)                     : ''}
            ${d.title        ? rowHtml(iconDoc(),     d.title)                       : ''}
            ${rowHtml(iconClock(), (d.slot ?? '') + (d.time ? ' · ' + d.time : ''))}
            ${rowHtml(iconLab(),   d.lab ?? '')}
            ${d.participants ? rowHtml(iconPeople(),  d.participants + ' peserta')     : ''}
            ${d.desc         ? rowHtml(iconText(),    d.desc)                        : ''}
            ${d.phone        ? `<div class="detail-row">
                <span class="detail-icon">${iconPhone()}</span>
                <span style="font-weight:600;color:var(--g7)">${maskPhone(d.phone)}</span>
            </div>
            <div style="margin-top:12px">
                <a href="https://wa.me/${d.phone.replace(/\D/g, '')}" target="_blank"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px 14px;background:#25d366;color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48a11.85 11.85 0 00-17 0 11.89 11.89 0 00-2.74 8.46 11.93 11.93 0 003.56 8.29l.17.18-.4 1.46-1.47.4.18.18a11.92 11.92 0 008.68 3.04h.01a11.94 11.94 0 008.46-3.05 11.89 11.89 0 003.05-8.46 11.85 11.85 0 00-3.48-8.46zM17.86 15.9c-.33.93-1.91 1.78-2.65 1.8-.68.02-1.55.04-2.51-.16-.58-.12-1.32-.42-2.27-.88-3.96-1.93-6.53-6.3-6.7-6.6-.17-.3-1.4-2.33 1.41-4.48 1.25-.97 2.5-1.13 3.04-1.13.47 0 1.09-.18 1.68.9.59 1.08.79 1.87 1.01 2.35.22.48.11.9-.06 1.28-.18.38-.5.61-.93.97-.43.36-.75.54-1.07.72-.32.18-.07.86.43 1.89.5 1.03 1.03 1.69 1.86 2.24 1.22.8 2.22.7 2.89.62.67-.08 2.08-.85 2.38-1.68.3-.83.3-1.54.21-1.68-.09-.14-.33-.22-.65-.38-.32-.16-1.9-.93-2.19-1.04-.29-.11-.5-.16-.72.11-.22.27-.84.98-1.03 1.18-.19.2-.38.22-.7.08-.32-.14-1.34-.49-2.55-1.57-.94-.84-1.57-1.88-1.76-2.2-.19-.32-.02-.49.14-.64.14-.14.33-.36.5-.54.17-.18.27-.3.4-.5.13-.2.06-.37-.03-.52-.09-.15-.79-1.9-1.08-2.58-.29-.68-.58-.58-.72-.59-.12 0-.26 0-.4.06-.14.06-.36.14-.55.42-.19.28-.73.71-.73 1.73 0 1.01.75 1.99.86 2.13.11.14 1.58 2.42 3.82 3.41.54.24 1.04.37 1.49.48.7.17 1.34.14 1.84.09.59-.06 1.9-.78 2.17-1.54.27-.76.27-1.41.19-1.54-.08-.13-.28-.2-.6-.32z"/>
                    </svg>
                    Chat WhatsApp
                </a>
            </div>` : ''}
            <div class="detail-badge ${isPending ? 'detail-badge--pending' : 'detail-badge--approved'}">
                ${isPending ? '⏳ Menunggu konfirmasi admin' : '✓ Booking telah disetujui'}
            </div>
        `;
    }

    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeDetail() {
    document.getElementById('detail-overlay').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });

// ─── Helpers ───
function escHtml(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function rowHtml(icon, text) {
    if (!text) return '';
    return `<div class="detail-row"><span class="detail-icon">${icon}</span><span>${escHtml(text)}</span></div>`;
}
function maskPhone(phone) {
    const clean = String(phone ?? '').replace(/\D/g,'');
    if (clean.length <= 5) return escHtml(phone);
    const first3 = clean.slice(0,3);
    const last2 = clean.slice(-2);
    const middle = clean.length - 5;
    const masked = first3 + '*'.repeat(middle) + last2;
    // Format with spaces if needed
    return masked;
}

function iconClock()  { return `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`; }
function iconLab()    { return `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`; }
function iconClass()  { return `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`; }
function iconBook()   { return `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>`; }
function iconDoc()    { return `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`; }
function iconPeople() { return `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>`; }
function iconText()   { return `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>`; }
function iconPhone()  { return `<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>`; }
</script>