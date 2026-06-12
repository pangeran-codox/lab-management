/**
 * jadwal.js — Lab Management · Jadwal Admin
 * Depends on: window.TEACHERS (array), window.SCHEDULE_ROUTE_BASE (string)
 */

/* ─── HELPERS ───────────────────────────────────────────────── */
const $ = id => document.getElementById(id);

function bodyLock()   { document.body.style.overflow = 'hidden'; }
function bodyUnlock() { document.body.style.overflow = ''; }

/* ─── TAB SWITCHER ──────────────────────────────────────────── */
function switchTab(id) {
    document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    const skeleton = $('skeleton');
    skeleton.style.display = 'block';
    $('tab-' + id).classList.add('active');

    setTimeout(() => {
        skeleton.style.display = 'none';
        const panel = $('panel-' + id);
        panel.style.display  = '';
        panel.style.animation = 'none';
        void panel.offsetWidth;           // reflow to restart animation
        panel.style.animation = '';
    }, 260);
}

/* ─── MODAL: TAMBAH ─────────────────────────────────────────── */
function openAdd(rid, rname, sid, sname, stime, dayEn, dayId) {
    $('add-f-rid').value = rid;
    $('add-f-sid').value = sid;
    $('add-f-day').value = dayEn;

    $('add-b-lab').textContent   = rname;
    $('add-b-day').textContent   = dayId;
    $('add-b-slot').textContent  = sname + ' · ' + stime;

    // reset form state
    const orgSel = document.querySelector('#add-modal select[name="organization_id"]');
    orgSel.value = '';

    const clsSel = $('add-class');
    clsSel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
    clsSel.disabled  = true;

    $('add-teacher-inp').value = '';
    $('add-teacher-sug').style.display = 'none';

    $('add-modal').classList.add('open');
    bodyLock();
}

function closeAdd() {
    $('add-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── MODAL: EDIT ───────────────────────────────────────────── */
function openEdit(id, teacher, subject, notes, status, className, dayId, slotName, slotTime, labName) {
    const base = window.SCHEDULE_ROUTE_BASE || '/jadwal-admin';
    $('edit-form').action = base + '/' + id;

    $('edit-modal-title').textContent = teacher;
    $('edit-teacher').value = teacher;
    $('edit-subject').value = subject;
    $('edit-notes').value   = notes;
    $('edit-status').value  = status;

    $('edit-b-lab').textContent  = labName;
    $('edit-b-day').textContent  = dayId + ' · ' + className;
    $('edit-b-slot').textContent = slotName + ' · ' + slotTime;
    $('edit-teacher-sug').style.display = 'none';

    $('edit-modal').classList.add('open');
    bodyLock();
}

function closeEdit() {
    $('edit-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── MODAL: DELETE ─────────────────────────────────────────── */
function confirmDelete(id, teacher) {
    const base = window.SCHEDULE_ROUTE_BASE || '/jadwal-admin';
    $('delete-form').action = base + '/' + id;
    $('delete-desc').textContent = teacher;
    $('delete-modal').classList.add('open');
    bodyLock();
}

function closeDelete() {
    $('delete-modal').classList.remove('open');
    bodyUnlock();
}

/* ─── KEYBOARD: ESC closes all ──────────────────────────────── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeAdd();
        closeEdit();
        closeDelete();
    }
});

/* ─── AUTOCOMPLETE: GURU ────────────────────────────────────── */
function filterTeacher(inputId, sugId, val) {
    const box = $(sugId);
    if (!val || val.length < 2) { box.style.display = 'none'; return; }

    const teachers = window.TEACHERS || [];
    const lower    = val.toLowerCase();
    const matches  = teachers.filter(t => t.name.toLowerCase().includes(lower)).slice(0, 8);

    if (!matches.length) { box.style.display = 'none'; return; }

    box.innerHTML = matches.map(t => `
        <div class="suggest-item"
             onmousedown="selectTeacher('${inputId}','${sugId}','${t.name.replace(/'/g, "\\'")}')"
        >
            <span class="suggest-item-name">${escHtml(t.name)}</span>
            ${t.phone ? `<span class="suggest-item-phone">${escHtml(t.phone)}</span>` : ''}
        </div>
    `).join('');
    box.style.display = 'block';
}

function selectTeacher(inputId, sugId, name) {
    $(inputId).value = name;
    $(sugId).style.display = 'none';
}

// Close suggestions when clicking outside
document.addEventListener('click', function (e) {
    if (!e.target.closest('#add-teacher-inp') && !e.target.closest('#add-teacher-sug'))
        $('add-teacher-sug').style.display = 'none';
    if (!e.target.closest('#edit-teacher') && !e.target.closest('#edit-teacher-sug'))
        $('edit-teacher-sug').style.display = 'none';
});

/* ─── AJAX: LOAD KELAS BY ORGANISASI ────────────────────────── */
function loadKelasAdd(orgId) {
    const sel = $('add-class');
    if (!orgId) {
        sel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
        sel.disabled  = true;
        return;
    }

    sel.innerHTML = '<option value="">Memuat kelas...</option>';
    sel.disabled  = true;

    const endpoint = window.KELAS_API_URL
        ? window.KELAS_API_URL + '?organization_id=' + orgId
        : '/kelas?organization_id=' + orgId;

    fetch(endpoint)
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            sel.innerHTML = '<option value="">— Pilih kelas —</option>';
            data.forEach(c => {
                const opt   = document.createElement('option');
                opt.value   = c.id;
                opt.textContent = c.name;
                sel.appendChild(opt);
            });
            sel.disabled = false;
        })
        .catch(() => {
            sel.innerHTML = '<option value="">Gagal memuat kelas</option>';
        });
}

/* ─── UTILS ─────────────────────────────────────────────────── */
function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* ─── AUTO-DISMISS FLASH ────────────────────────────────────── */
document.querySelectorAll('.flash').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s, transform .4s';
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(-6px)';
        setTimeout(() => el.remove(), 420);
    }, 4000);
});

// ─── EXPOSE TO GLOBAL (required for inline onclick in Blade) ───
window.switchTab     = switchTab;
window.openAdd       = openAdd;
window.closeAdd      = closeAdd;
window.openEdit      = openEdit;
window.closeEdit     = closeEdit;
window.confirmDelete = confirmDelete;
window.closeDelete   = closeDelete;
window.filterTeacher = filterTeacher;
window.selectTeacher = selectTeacher;
window.loadKelasAdd  = loadKelasAdd;