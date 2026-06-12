/**
 * booking.js — Lab Management · Booking Admin
 * Weekly table + CRUD modals
 */

/* ─── HELPERS ───────────────────────────────── */
const $id = id => document.getElementById(id);
function bodyLock()   { document.body.style.overflow = 'hidden'; }
function bodyUnlock() { document.body.style.overflow = ''; }

/* ─── WEEK NAVIGATION ───────────────────────── */
function getWeekInput() {
    return $id('week-picker');
}

function currentWeekValue() {
    const inp = getWeekInput();
    return inp ? inp.value : null;
}

function navigateWeek(offset) {
    const inp = getWeekInput();
    if (!inp || !inp.value) return;

    // Parse format "2026-W24" → Date object (Monday of that week)
    const date = parseWeekValue(inp.value);
    if (!date) return;

    // Geser 7 hari
    date.setDate(date.getDate() + offset * 7);

    // Set back ke input format YYYY-Www
    inp.value = formatWeekValue(date);
    submitWeekForm();
}

function goToday() {
    const inp = getWeekInput();
    if (!inp) return;
    inp.value = formatWeekValue(new Date());
    submitWeekForm();
}

/**
 * Parse "2026-W24" → Date (Monday of that ISO week)
 */
function parseWeekValue(weekStr) {
    // weekStr = "2026-W24"
    const match = weekStr.match(/^(\d{4})-W(\d{2})$/);
    if (!match) return null;

    const year = parseInt(match[1]);
    const week = parseInt(match[2]);

    // Jan 4 selalu di week 1 ISO
    const jan4  = new Date(year, 0, 4);
    const day   = jan4.getDay() || 7;          // 1=Mon … 7=Sun
    const monday = new Date(jan4);
    monday.setDate(jan4.getDate() - (day - 1) + (week - 1) * 7);
    return monday;
}

/**
 * Format Date → "2026-W24" (ISO week)
 */
function formatWeekValue(date) {
    const d    = new Date(date);
    d.setHours(0, 0, 0, 0);
    // Set to Thursday of this week (ISO week belongs to year of its Thursday)
    d.setDate(d.getDate() + 3 - ((d.getDay() + 6) % 7));
    const year = d.getFullYear();
    const jan1 = new Date(year, 0, 1);
    const week = Math.ceil(((d - jan1) / 86400000 + jan1.getDay() + 1) / 7);
    return `${year}-W${String(week).padStart(2, '0')}`;
}

function submitWeekForm() {
    const form = $id('week-form');
    if (form) form.submit();
}



/* ─── TAB SWITCHER ──────────────────────────── */
function bkSwitchTab(id) {
    document.querySelectorAll('.bk-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.bk-tab-btn').forEach(b => b.classList.remove('active'));

    const skeleton = $id('bk-skeleton');
    if (skeleton) skeleton.style.display = 'block';
    $id('bk-tab-' + id).classList.add('active');

    setTimeout(() => {
        if (skeleton) skeleton.style.display = 'none';
        const panel = $id('bk-panel-' + id);
        if (!panel) return;
        panel.style.display = '';
        panel.style.animation = 'none';
        void panel.offsetWidth;
        panel.style.animation = '';
    }, 220);
}

/* ─── MODAL: ADD BOOKING ─────────────────────── */
function bkOpenAdd(resourceId, resourceName, slotId, slotName, slotTime, dateStr, dateLabel) {
    $id('bk-add-rid').value  = resourceId;
    $id('bk-add-sid').value  = slotId;
    $id('bk-add-date').value = dateStr;

    $id('bk-add-b-lab').textContent  = resourceName;
    $id('bk-add-b-date').textContent = dateLabel;
    $id('bk-add-b-slot').textContent = slotName + ' · ' + slotTime;

    // Reset form
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

/* ─── MODAL: VIEW/EDIT BOOKING ───────────────── */
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

    // Actions - show approve/reject only for pending
    const approveForm = $id('bk-view-approve-form');
    const rejectBtn   = $id('bk-view-reject-btn');
    const detailLink  = $id('bk-view-detail-link');

    const base = window.BOOKING_ROUTE_BASE || '/booking';

    if (approveForm) {
        approveForm.action = base + '/' + id + '/approve';
        approveForm.style.display = status === 'pending' ? 'block' : 'none';
    }
    if (rejectBtn) {
        rejectBtn.style.display = status === 'pending' ? 'inline-flex' : 'none';
        rejectBtn.onclick = function() {
            bkCloseView();
            openReject(id, title, teacher);
        };
    }
    if (detailLink) {
        detailLink.href = base + '/' + id;
    }

    // Delete form
    const delForm = $id('bk-view-delete-form');
    if (delForm) delForm.action = base + '/' + id;

    $id('bk-view-modal').classList.add('open');
    bodyLock();
}

function bkCloseView() {
    $id('bk-view-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── MODAL: REJECT ─────────────────────────── */
function openReject(id, title, teacher, type = 'regular') {
    $id('reject-subtitle').textContent = teacher + ' — ' + title;

    const base = window.BOOKING_ROUTE_BASE || '/booking';
    $id('reject-form').action = base + '/' + id + '/reject';
    $id('reject-type').value  = type;

    $id('reject-modal').classList.add('open');
    bodyLock();
}

function closeReject() {
    $id('reject-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── KEYBOARD: ESC ─────────────────────────── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        bkCloseAdd();
        bkCloseView();
        closeReject();
    }
});

/* ─── AUTO-DISMISS FLASH ────────────────────── */
document.querySelectorAll('.flash').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s, transform .4s';
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(-6px)';
        setTimeout(() => el.remove(), 420);
    }, 4000);
});

/* ─── EXPOSE TO GLOBAL ──────────────────────── */
window.navigateWeek  = navigateWeek;
window.goToday       = goToday;
window.submitWeekForm = submitWeekForm;
window.bkSwitchTab   = bkSwitchTab;
window.bkOpenAdd     = bkOpenAdd;
window.bkCloseAdd    = bkCloseAdd;
window.bkOpenView    = bkOpenView;
window.bkCloseView   = bkCloseView;
window.openReject    = openReject;
window.closeReject   = closeReject;