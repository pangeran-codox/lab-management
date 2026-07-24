/**
 * booking.js — Lab Management · Booking Admin
 * Weekly table AJAX navigation + CRUD modals + WebSocket realtime update
 *
 * Dependencies:
 *   window.Echo             — setup di bootstrap.js (Laravel Echo + Reverb)
 *   window.BOOKING_ROUTE_BASE  — base URL /booking
 *   window.BOOKING_WEEKLY_URL  — route /booking/weekly-grid
 */

/* ─── HELPERS ───────────────────────────────────────────────── */
const $id = id => document.getElementById(id);
function bodyLock()   { document.body.style.overflow = 'hidden'; }
function bodyUnlock() { document.body.style.overflow = ''; }

/* ─── WEEKLY GRID: STATE ────────────────────────────────────── */
// Minggu aktif saat ini (format: "YYYY-MM-DD" = hari pertama minggu / Sunday)
// Dibaca dari data attribute di #bk-weekly-wrap yang di-render server
function getCurrentWeekStart() {
    const wrap = $id('bk-weekly-wrap');
    return wrap ? wrap.dataset.weekStart : null;
}

function getPrevWeek() {
    const wrap = $id('bk-weekly-wrap');
    return wrap ? wrap.dataset.prevWeek : null;
}

function getNextWeek() {
    const wrap = $id('bk-weekly-wrap');
    return wrap ? wrap.dataset.nextWeek : null;
}

/* ─── WEEKLY GRID: FETCH & SWAP ─────────────────────────────── */
let _weekFetchController = null; // AbortController untuk cancel request sebelumnya

/**
 * Fetch partial HTML weekly-table untuk tanggal tertentu,
 * lalu swap isi #bk-weekly-container tanpa reload halaman.
 *
 * @param {string} weekDate  Format "YYYY-MM-DD" (tanggal apapun dalam minggu target)
 * @param {boolean} pushState  Apakah URL browser di-update (default: true)
 */
async function bkLoadWeek(weekDate, pushState = true) {
    const container = $id('bk-weekly-container');
    if (!container) return;

    // Batalkan request sebelumnya jika masih in-flight
    if (_weekFetchController) {
        _weekFetchController.abort();
    }
    _weekFetchController = new AbortController();

    // Tampilkan skeleton
    const skeleton = $id('bk-skeleton');
    if (skeleton) skeleton.style.display = 'block';

    // Sembunyikan semua panel saat loading
    container.querySelectorAll('.bk-panel').forEach(p => p.style.opacity = '0.4');

    try {
        const url = new URL(window.BOOKING_WEEKLY_URL || '/booking/weekly-grid', window.location.origin);
        url.searchParams.set('week', weekDate);

        const resp = await fetch(url.toString(), {
            signal: _weekFetchController.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);

        const html = await resp.text();

        // Swap konten
        container.innerHTML = html;

        // Update URL browser agar bisa di-bookmark / refresh
        if (pushState) {
            const pageUrl = new URL(window.location.href);
            pageUrl.searchParams.set('week', weekDate);
            history.pushState({ week: weekDate }, '', pageUrl.toString());
        }

        // Restore tab yang aktif (ambil dari tab yang masih ter-klik sebelumnya)
        const activeTab = document.querySelector('.bk-tab-btn.active');
        if (activeTab) {
            const tabId = activeTab.id.replace('bk-tab-', '');
            bkRestoreTab(tabId);
        }

    } catch (err) {
        if (err.name === 'AbortError') return; // Request di-cancel, normal
        console.error('[booking] loadWeek error:', err);
        // Kembalikan opacity panel kalau error
        container.querySelectorAll('.bk-panel').forEach(p => p.style.opacity = '');
    } finally {
        if (skeleton) skeleton.style.display = 'none';
    }
}

/** Restore panel yang ditampilkan setelah swap HTML */
function bkRestoreTab(id) {
    document.querySelectorAll('.bk-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.bk-tab-btn').forEach(b => b.classList.remove('active'));

    const tab = $id('bk-tab-' + id);
    if (tab) tab.classList.add('active');

    const panel = $id('bk-panel-' + id);
    if (panel) {
        panel.style.display   = '';
        panel.style.opacity   = '';
        panel.style.animation = 'none';
        void panel.offsetWidth;
        panel.style.animation = '';
    }
}

/* ─── WEEK NAVIGATION ───────────────────────────────────────── */

/**
 * Navigasi ke tanggal tertentu (string "YYYY-MM-DD")
 * Dipanggil dari tombol prev/next di weekly-header partial
 */
function bkNavigateWeek(weekDate) {
    if (!weekDate) return;
    bkLoadWeek(weekDate);
}

/** Navigasi ke minggu ini */
function bkGoToday() {
    // Hitung hari Minggu (start of week) dari hari ini
    const now  = new Date();
    const day  = now.getDay(); // 0=Sun … 6=Sat
    const diff = now.getDate() - day;
    const sun  = new Date(now.setDate(diff));
    const iso  = sun.toISOString().slice(0, 10);
    bkLoadWeek(iso);
}

// Tangani tombol back/forward browser
window.addEventListener('popstate', (e) => {
    const weekDate = e.state?.week
        || new URLSearchParams(window.location.search).get('week');
    if (weekDate) {
        bkLoadWeek(weekDate, false); // jangan push state lagi
    }
});

/* ─── TAB SWITCHER ──────────────────────────────────────────── */
function bkSwitchTab(id) {
    document.querySelectorAll('.bk-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.bk-tab-btn').forEach(b => b.classList.remove('active'));

    const skeleton = $id('bk-skeleton');
    if (skeleton) skeleton.style.display = 'block';

    const tabBtn = $id('bk-tab-' + id);
    if (tabBtn) tabBtn.classList.add('active');

    setTimeout(() => {
        if (skeleton) skeleton.style.display = 'none';
        const panel = $id('bk-panel-' + id);
        if (!panel) return;
        panel.style.display   = '';
        panel.style.opacity   = '';
        panel.style.animation = 'none';
        void panel.offsetWidth;
        panel.style.animation = '';
    }, 200);
}

/* ─── WEBSOCKET: REALTIME UPDATE ────────────────────────────── */
// Debounce reload agar tidak fire berkali-kali jika ada burst event
let _wsReloadTimer = null;

function bkScheduleReload(delayMs = 800) {
    clearTimeout(_wsReloadTimer);
    _wsReloadTimer = setTimeout(() => {
        const currentWeek = getCurrentWeekStart();
        if (currentWeek) {
            bkLoadWeek(currentWeek, false); // reload minggu yang sedang ditampilkan
        }
    }, delayMs);
}

// Subscribe channel 'schedules' — event ini di-broadcast setelah approve/reject/delete booking
// (lihat BookingController: broadcast(new ScheduleUpdated(...)))
if (window.Echo) {
    window.Echo.channel('schedules')
        .listen('.schedule.updated', (e) => {
            // Cek apakah event ini relevan dengan minggu yang sedang ditampilkan
            const currentWeek = getCurrentWeekStart();
            if (!currentWeek) return;

            // Hitung range minggu saat ini (Sun–Sat)
            const weekStart = new Date(currentWeek);
            weekStart.setHours(0, 0, 0, 0);
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            weekEnd.setHours(23, 59, 59, 999);

            const eventDate = e?.data?.booking_date ? new Date(e.data.booking_date) : null;

            // Hanya reload kalau event-nya di minggu yang sedang dibuka
            // atau kalau tidak ada info tanggal (reload safe)
            if (!eventDate || (eventDate >= weekStart && eventDate <= weekEnd)) {
                bkScheduleReload(600);
            }
        });
}

/* ─── MODAL: ADD BOOKING ─────────────────────────────────────── */
function bkOpenAdd(resourceId, resourceName, slotId, slotName, slotTime, dateStr, dateLabel) {
    $id('bk-add-rid').value  = resourceId;
    $id('bk-add-sid').value  = slotId;
    $id('bk-add-date').value = dateStr;

    $id('bk-add-b-lab').textContent  = resourceName;
    $id('bk-add-b-date').textContent = dateLabel;
    $id('bk-add-b-slot').textContent = slotName + ' · ' + slotTime;

    // Reset form lalu isi ulang hidden fields (reset() akan clear semua)
    $id('bk-add-form').reset();
    $id('bk-add-rid').value  = resourceId;
    $id('bk-add-sid').value  = slotId;
    $id('bk-add-date').value = dateStr;

    $id('bk-add-modal').classList.add('open');
    bodyLock();
}

function bkCloseAdd() {
    $id('bk-add-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── MODAL: VIEW / APPROVE / REJECT BOOKING ────────────────── */
function bkOpenView(id, teacher, className, subject, status, slotName, slotTime, dateLabel, labName, title) {
    $id('bk-view-title').textContent   = title || teacher;
    $id('bk-view-teacher').textContent = teacher;
    $id('bk-view-class').textContent   = className;
    $id('bk-view-subject').textContent = subject || '-';
    $id('bk-view-b-lab').textContent   = labName;
    $id('bk-view-b-date').textContent  = dateLabel;
    $id('bk-view-b-slot').textContent  = slotName + ' · ' + slotTime;

    // Status badge
    const badge = $id('bk-view-status');
    badge.className = 'badge badge-' + status;
    const labels = { approved: '✓ Disetujui', pending: '⏳ Pending', rejected: '✗ Ditolak' };
    badge.textContent = labels[status] || status;

    const base = window.BOOKING_ROUTE_BASE || '/booking';

    // Approve form — hanya tampil untuk pending
    const approveForm = $id('bk-view-approve-form');
    if (approveForm) {
        approveForm.action       = `${base}/${id}/approve`;
        approveForm.style.display = status === 'pending' ? 'block' : 'none';
    }

    // Reject button — hanya tampil untuk pending
    const rejectBtn = $id('bk-view-reject-btn');
    if (rejectBtn) {
        rejectBtn.style.display = status === 'pending' ? 'inline-flex' : 'none';
        rejectBtn.onclick = () => { bkCloseView(); openReject(id, title, teacher); };
    }

    // Detail link
    const detailLink = $id('bk-view-detail-link');
    if (detailLink) detailLink.href = `${base}/${id}`;

    // Delete form
    const delForm = $id('bk-view-delete-form');
    if (delForm) delForm.action = `${base}/${id}`;

    $id('bk-view-modal').classList.add('open');
    bodyLock();
}

function bkCloseView() {
    $id('bk-view-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── MODAL: REJECT (single) ─────────────────────────────────── */
function openReject(id, title, teacher, type = 'regular') {
    $id('reject-subtitle').textContent = `${teacher} — ${title}`;

    const base = window.BOOKING_ROUTE_BASE || '/booking';
    $id('reject-form').action = `${base}/${id}/reject`;
    $id('reject-type').value  = type;

    // Hapus hidden inputs group kalau ada dari sesi sebelumnya
    $id('reject-form')
        .querySelectorAll('[name="teacher_name"],[name="resource_id"],[name="booking_date"]')
        .forEach(el => el.remove());

    // Pastikan _method = PATCH
    let methodInput = $id('reject-form').querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = Object.assign(document.createElement('input'), { type: 'hidden', name: '_method' });
        $id('reject-form').prepend(methodInput);
    }
    methodInput.value = 'PATCH';

    $id('reject-submit-btn').textContent = '✗ Tolak Booking';
    $id('reject-modal').classList.add('open');
    bodyLock();
}

/* ─── MODAL: REJECT (group) ──────────────────────────────────── */
function openRejectGroup(teacherName, resourceId, bookingDate, count) {
    $id('reject-subtitle').textContent = `Tolak ${count} slot booking ${teacherName} sekaligus`;

    const base = window.BOOKING_ROUTE_BASE || '/booking';
    $id('reject-form').action = `${base}/reject-group`;
    $id('reject-type').value  = 'regular';

    // Hapus semua hidden inputs lama
    $id('reject-form')
        .querySelectorAll('[name="teacher_name"],[name="resource_id"],[name="booking_date"],[name="_method"]')
        .forEach(el => el.remove());

    // Tambah hidden inputs untuk group reject
    [
        { name: 'teacher_name', value: teacherName },
        { name: 'resource_id',  value: resourceId },
        { name: 'booking_date', value: bookingDate },
    ].forEach(({ name, value }) => {
        const inp = Object.assign(document.createElement('input'), { type: 'hidden', name, value });
        $id('reject-form').prepend(inp);
    });

    $id('reject-submit-btn').textContent = `✗ Tolak ${count} Slot`;
    $id('reject-modal').classList.add('open');
    bodyLock();
}

function closeReject() {
    $id('reject-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── KEYBOARD: ESC ──────────────────────────────────────────── */
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    bkCloseAdd();
    bkCloseView();
    closeReject();
});

/* ─── AUTO-DISMISS FLASH ─────────────────────────────────────── */
document.querySelectorAll('.flash').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s, transform .4s';
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(-6px)';
        setTimeout(() => el.remove(), 420);
    }, 4000);
});

/* ─── EXPOSE TO GLOBAL (required for inline onclick in Blade) ── */
window.bkNavigateWeek   = bkNavigateWeek;
window.bkGoToday        = bkGoToday;
window.bkSwitchTab      = bkSwitchTab;
window.bkOpenAdd        = bkOpenAdd;
window.bkCloseAdd       = bkCloseAdd;
window.bkOpenView       = bkOpenView;
window.bkCloseView      = bkCloseView;
window.openReject       = openReject;
window.openRejectGroup  = openRejectGroup;
window.closeReject      = closeReject;
