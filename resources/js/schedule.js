/**
 * schedule.js — JavaScript untuk halaman jadwal publik
 * resources/js/schedule.js  (di-compile Vite → public/build/)
 *
 * Variabel ALL_SLOTS dan TEACHERS di-inject dari blade via:
 *   <script>
 *     window.ALL_SLOTS = @json(...);
 *     window.TEACHERS  = @json(...);
 *   </script>
 * (harus sebelum @vite di @section('vite'))
 */

/* ─── TEACHER AUTOCOMPLETE ───────────────────────────────────────────────── */

function buildSuggestionItem(name, phone, onClickFn) {
    const item = document.createElement('div');
    item.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center';
    item.addEventListener('mouseover', function() { item.style.background = '#f0f7ee'; });
    item.addEventListener('mouseout',  function() { item.style.background = ''; });
    item.addEventListener('click', onClickFn);

    const nameSpan = document.createElement('span');
    nameSpan.style.cssText = 'font-weight:600;color:#1A2517';
    nameSpan.textContent = name;

    const phoneSpan = document.createElement('span');
    phoneSpan.style.cssText = 'font-size:11px;color:#9ca3af';
    phoneSpan.textContent = maskPhone(phone) || ''; // Gunakan maskPhone untuk tampilan

    item.appendChild(nameSpan);
    item.appendChild(phoneSpan);
    return item;
}

function filterTeacher(val) {
    var box = document.getElementById('teacher_suggestions');
    if (!val || val.length < 2) { box.style.display = 'none'; return; }
    var filtered = window.TEACHERS.filter(function(t) {
        return t.name.toLowerCase().includes(val.toLowerCase());
    });
    if (!filtered.length) { box.style.display = 'none'; return; }
    box.innerHTML = '';
    filtered.forEach(function(t) {
        box.appendChild(buildSuggestionItem(t.name, t.phone, function() {
            selectTeacher(t.name, t.phone || '');
        }));
    });
    box.style.display = 'block';
}

function selectTeacher(name, phone) {
    document.getElementById('inp_teacher_name').value  = name;
    document.getElementById('inp_teacher_phone').value = phone;
    document.getElementById('teacher_suggestions').style.display = 'none';
}

function filterTeacherSunday(val) {
    var box = document.getElementById('sb_teacher_sug');
    if (!val || val.length < 2) { box.style.display = 'none'; return; }
    var filtered = window.TEACHERS.filter(function(t) {
        return t.name.toLowerCase().includes(val.toLowerCase());
    });
    if (!filtered.length) { box.style.display = 'none'; return; }
    box.innerHTML = '';
    filtered.forEach(function(t) {
        box.appendChild(buildSuggestionItem(t.name, t.phone, function() {
            selectTeacherSunday(t.name, t.phone || '');
        }));
    });
    box.style.display = 'block';
}

function selectTeacherSunday(name, phone) {
    document.getElementById('sb_teacher_name').value  = name;
    document.getElementById('sb_teacher_phone').value = phone;
    document.getElementById('sb_teacher_sug').style.display = 'none';
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function(e) {
    if (!e.target.closest('#inp_teacher_name') && !e.target.closest('#teacher_suggestions'))
        document.getElementById('teacher_suggestions').style.display = 'none';
    if (!e.target.closest('#sb_teacher_name') && !e.target.closest('#sb_teacher_sug'))
        document.getElementById('sb_teacher_sug').style.display = 'none';
});

/* ─── TABS ───────────────────────────────────────────────────────────────── */

function switchTab(id) {
    document.querySelectorAll('.lab-panel').forEach(function(p) { p.style.display = 'none'; });
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('tab-active'); });
    document.getElementById('skeleton').style.display = 'block';
    document.getElementById('tab-' + id).classList.add('tab-active');
    setTimeout(function() {
        document.getElementById('skeleton').style.display = 'none';
        var panel = document.getElementById('panel-' + id);
        panel.style.display = '';
        panel.style.animation = 'none';
        void panel.offsetWidth;
        panel.style.animation = '';
    }, 180);
}

/* ─── BOOKING MODAL ──────────────────────────────────────────────────────── */

function openBooking(rid, rname, sid, sname, stime, dayEn, dayId, date, bookedSlots) {
    bookedSlots = bookedSlots || [];
    document.getElementById('f_rid').value  = rid;
    document.getElementById('f_sid').value  = sid;
    document.getElementById('f_extra_slots').value = '';
    document.getElementById('f_date').value = date;

    var d  = new Date(date + 'T00:00:00');
    var mn = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('b-lab').textContent  = '🖥 ' + rname;
    document.getElementById('b-date').textContent = '📅 ' + dayId + ', ' + d.getDate() + ' ' + mn[d.getMonth()] + ' ' + d.getFullYear();
    document.getElementById('b-slot').textContent = '🕐 ' + sname + ' · ' + stime;

    var slotIdx   = window.ALL_SLOTS.findIndex(function(s) { return s.id == sid; });
    var wrap      = document.getElementById('slot-options');
    var startTime = stime.split('\u2013')[0].split('-')[0].trim();
    wrap.innerHTML = '';

    var availableSlots = [];
    for (var i = slotIdx; i < window.ALL_SLOTS.length; i++) {
        if (i > slotIdx && bookedSlots.includes(window.ALL_SLOTS[i].id)) break;
        availableSlots.push(window.ALL_SLOTS[i]);
    }

    var selectedCount = 1;

    function updateExtraSlots() {
        var extras = availableSlots.slice(1, selectedCount).map(function(s) { return s.id; });
        document.getElementById('f_extra_slots').value = extras.join(',');
        wrap.querySelectorAll('.slot-opt:not(.slot-opt-full)').forEach(function(btn, i) {
            btn.classList.toggle('selected', i < selectedCount);
        });
        var lastSlot = availableSlots[selectedCount - 1];
        var endTime  = lastSlot.end_time ? lastSlot.end_time.slice(0,5) : '';
        document.getElementById('b-slot').textContent = '\uD83D\uDD50 ' + sname
            + (selectedCount > 1 ? ' \u2013 ' + lastSlot.name : '')
            + ' \u00B7 ' + startTime + (endTime ? '\u2013' + endTime : '');
    }

    availableSlots.forEach(function(slot, i) {
        var btn  = document.createElement('button');
        btn.type = 'button';
        btn.className = 'slot-opt' + (i === 0 ? ' selected' : '');
        var endT = slot.end_time ? slot.end_time.slice(0,5) : '';
        if (i === 0) {
            btn.innerHTML = '<strong>' + slot.name + '</strong><br><span style="font-size:10px">' + startTime + (endT ? '\u2013' + endT : '') + '</span>';
        } else {
            btn.innerHTML = '<strong>+ ' + slot.name + '</strong><br><span style="font-size:10px">s/d ' + endT + '</span>';
        }
        btn.onclick = function() { selectedCount = i + 1; updateExtraSlots(); };
        wrap.appendChild(btn);
    });

    if (availableSlots.length > 2) {
        var btnAll = document.createElement('button');
        btnAll.type = 'button';
        btnAll.className = 'slot-opt slot-opt-full';
        btnAll.style.cssText = 'background:linear-gradient(135deg,#1A2517,#2a3826);color:#ACC8A2;border-color:#3d5438;min-width:90px';
        var lastT   = availableSlots[availableSlots.length - 1];
        var lastEnd = lastT.end_time ? lastT.end_time.slice(0,5) : '';
        btnAll.innerHTML = '<strong>Full (' + availableSlots.length + ')</strong><br><span style="font-size:10px">' + startTime + (lastEnd ? '\u2013' + lastEnd : '') + '</span>';
        btnAll.onclick = function() {
            selectedCount = availableSlots.length;
            updateExtraSlots();
            wrap.querySelectorAll('.slot-opt').forEach(function(b) { b.classList.remove('selected'); });
            btnAll.classList.add('selected');
        };
        wrap.appendChild(btnAll);
    }

    updateExtraSlots();

    document.getElementById('f_class').innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
    document.getElementById('f_class').disabled  = true;
    document.getElementById('f_org').value = '';

    document.getElementById('modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    var overlay = document.getElementById('modal-overlay');
    var box     = document.getElementById('modal-box');
    box.style.transition = 'opacity .16s, transform .16s';
    box.style.opacity    = '0';
    box.style.transform  = 'translateY(10px) scale(.97)';
    setTimeout(function() {
        overlay.classList.remove('show');
        box.style.transition = '';
        box.style.opacity    = '';
        box.style.transform  = '';
        document.body.style.overflow = '';
    }, 160);
}

/* ─── DETAIL MODAL ───────────────────────────────────────────────────────── */

var TYPE_CONFIG = {
    tetap:    { label: '📅 Jadwal Tetap',         headBg: 'linear-gradient(135deg,#2a4a28,#3a6b38)', typeColor: 'rgba(172,200,162,.7)', teacherColor: '#d6ead2' },
    approved: { label: '✓ Booking Disetujui',     headBg: 'linear-gradient(135deg,#14532d,#166534)', typeColor: '#86efac',             teacherColor: '#d1fae5' },
    pending:  { label: '⏳ Menunggu Persetujuan', headBg: 'linear-gradient(135deg,#78350f,#92400e)', typeColor: '#fcd34d',             teacherColor: '#fef3c7' },
};

function maskPhone(phone) {
    const clean = String(phone || '').replace(/\D/g, '');
    if (clean.length <= 5) return phone;
    const first3 = clean.slice(0,3);
    const last2 = clean.slice(-2);
    const middle = clean.length - 5;
    return first3 + '*'.repeat(middle) + last2;
}

function showDetail(d) {
    var cfg = TYPE_CONFIG[d.type] || TYPE_CONFIG.tetap;

    document.getElementById('detail-head').style.background = cfg.headBg;
    document.getElementById('d-type').style.color    = cfg.typeColor;
    document.getElementById('d-type').textContent    = cfg.label;
    document.getElementById('d-teacher').style.color = cfg.teacherColor;
    document.getElementById('d-teacher').textContent = d.teacher;

    var rows = [
        { icon: '🖥', key: 'Lab',   val: d.lab },
        { icon: '📚', key: 'Kelas', val: d.class_name || '-' },
        { icon: '📖', key: 'Mapel', val: d.subject || '-' },
        { icon: '📅', key: 'Hari',  val: d.day + ', ' + d.date },
        { icon: '🕐', key: 'Slot',  val: d.slot + ' · ' + d.time },
    ];
    if (d.title)        rows.push({ icon: '📝', key: 'Kegiatan',   val: d.title });
    if (d.participants) rows.push({ icon: '👥', key: 'Peserta',    val: d.participants + ' orang' });
    if (d.desc)         rows.push({ icon: '💬', key: 'Keterangan', val: d.desc });

    var body = document.getElementById('detail-body');
    body.innerHTML = '';

    rows.forEach(function(r) {
        var wrap = document.createElement('div');
        wrap.className = 'detail-row';

        var iconEl = document.createElement('span');
        iconEl.className = 'detail-icon';
        iconEl.textContent = r.icon;

        var info = document.createElement('div');

        var keyEl = document.createElement('div');
        keyEl.className = 'detail-key';
        keyEl.textContent = r.key;

        var valEl = document.createElement('div');
        valEl.className = 'detail-val';
        valEl.textContent = r.val;

        info.appendChild(keyEl);
        info.appendChild(valEl);
        wrap.appendChild(iconEl);
        wrap.appendChild(info);
        body.appendChild(wrap);
    });

    // Add masked phone and WhatsApp button if phone exists
    if (d.phone) {
        // Phone row
        var phoneRow = document.createElement('div');
        phoneRow.className = 'detail-row';
        
        var phoneIcon = document.createElement('span');
        phoneIcon.className = 'detail-icon';
        phoneIcon.textContent = '📞';
        
        var phoneInfo = document.createElement('div');
        var phoneKey = document.createElement('div');
        phoneKey.className = 'detail-key';
        phoneKey.textContent = 'No. HP';
        var phoneVal = document.createElement('div');
        phoneVal.className = 'detail-val';
        phoneVal.textContent = maskPhone(d.phone);
        
        phoneInfo.appendChild(phoneKey);
        phoneInfo.appendChild(phoneVal);
        phoneRow.appendChild(phoneIcon);
        phoneRow.appendChild(phoneInfo);
        body.appendChild(phoneRow);
        
        // WhatsApp button
        var waDiv = document.createElement('div');
        waDiv.style.marginTop = '12px';
        
        var waLink = document.createElement('a');
        waLink.href = 'https://wa.me/' + d.phone.replace(/\D/g, '');
        waLink.target = '_blank';
        waLink.style.cssText = 'display:flex;align-items:center;justify-content:center;gap:8px;padding:10px 14px;background:#25d366;color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px';
        
        var waSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        waSvg.setAttribute('width', '18');
        waSvg.setAttribute('height', '18');
        waSvg.setAttribute('fill', 'currentColor');
        waSvg.setAttribute('viewBox', '0 0 24 24');
        
        var waPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        waPath.setAttribute('d', 'M20.52 3.48a11.85 11.85 0 00-17 0 11.89 11.89 0 00-2.74 8.46 11.93 11.93 0 003.56 8.29l.17.18-.4 1.46-1.47.4.18.18a11.92 11.92 0 008.68 3.04h.01a11.94 11.94 0 008.46-3.05 11.89 11.89 0 003.05-8.46 11.85 11.85 0 00-3.48-8.46zM17.86 15.9c-.33.93-1.91 1.78-2.65 1.8-.68.02-1.55.04-2.51-.16-.58-.12-1.32-.42-2.27-.88-3.96-1.93-6.53-6.3-6.7-6.6-.17-.3-1.4-2.33 1.41-4.48 1.25-.97 2.5-1.13 3.04-1.13.47 0 1.09-.18 1.68.9.59 1.08.79 1.87 1.01 2.35.22.48.11.9-.06 1.28-.18.38-.5.61-.93.97-.43.36-.75.54-1.07.72-.32.18-.07.86.43 1.89.5 1.03 1.03 1.69 1.86 2.24 1.22.8 2.22.7 2.89.62.67-.08 2.08-.85 2.38-1.68.3-.83.3-1.54.21-1.54-.08-.13-.28-.2-.6-.32-.32-.16-1.9-.93-2.19-1.04-.29-.11-.5-.16-.72.11-.22.27-.84.98-1.03 1.18-.19.2-.38.22-.7.08-.32-.14-1.34-.49-2.55-1.57-.94-.84-1.57-1.88-1.76-2.2-.19-.32-.02-.49.14-.64.14-.14.33-.36.5-.54.17-.18.27-.3.4-.5.13-.2.06-.37-.03-.52-.09-.15-.79-1.9-1.08-2.58-.29-.68-.58-.58-.72-.59-.12 0-.26 0-.4.06-.14.06-.36.14-.55.42-.19.28-.73.71-.73 1.73 0 1.01.75 1.99.86 2.13.11.14 1.58 2.42 3.82 3.41.54.24 1.04.37 1.49.48.7.17 1.34.14 1.84.09.59-.06 1.9-.78 2.17-1.54.27-.76.27-1.41.19-1.54-.08-.13-.28-.2-.6-.32z');
        
        waSvg.appendChild(waPath);
        var waText = document.createTextNode('Chat WhatsApp');
        waLink.appendChild(waSvg);
        waLink.appendChild(waText);
        waDiv.appendChild(waLink);
        body.appendChild(waDiv);
    }

    document.getElementById('detail-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDetail() {
    var box = document.getElementById('detail-box');
    box.style.transition = 'opacity .16s, transform .16s';
    box.style.opacity    = '0';
    box.style.transform  = 'translateY(10px) scale(.97)';
    setTimeout(function() {
        document.getElementById('detail-overlay').classList.remove('show');
        box.style.transition = box.style.opacity = box.style.transform = '';
        document.body.style.overflow = '';
    }, 160);
}

/* ─── KEYBOARD CLOSE ─────────────────────────────────────────────────────── */

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModal(); closeDetail(); closeSundayModal(); }
});

/* ─── AJAX WEEK NAVIGATION ───────────────────────────────────────────────── */

var fetchController    = null;
var currentActiveTabId = null;

function getNextMonday() {
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var day = today.getDay();
    var daysToMonday = day === 0 ? 1 : 8 - day;
    var next = new Date(today);
    next.setDate(today.getDate() + daysToMonday);
    return next;
}

function changeWeek(week) {
    var targetDate = new Date(week + 'T00:00:00');
    if (targetDate > getNextMonday()) {
        showToast('⛔ Jadwal hanya bisa dilihat sampai 1 minggu ke depan.');
        return;
    }

    if (fetchController) fetchController.abort();
    fetchController = new AbortController();

    var activeTab = document.querySelector('.tab-btn.tab-active');
    if (activeTab) currentActiveTabId = activeTab.id.replace('tab-', '');

    document.querySelectorAll('.lab-panel').forEach(function(p) { p.style.display = 'none'; });
    document.getElementById('skeleton').style.display = 'block';
    document.querySelectorAll('.week-btn').forEach(function(b) { b.disabled = true; b.style.opacity = '.6'; });

    var url = new URL(window.location.href);
    url.searchParams.set('week', week);
    window.history.pushState({ week: week }, '', url.toString());

    fetch(url.toString(), {
        signal: fetchController.signal,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.text(); })
    .then(function(html) { fetchController = null; applyWeekResponse(html, week); })
    .catch(function(err) {
        if (err.name === 'AbortError') return;
        fetchController = null;
        window.location.href = url.toString();
    });
}

function applyWeekResponse(html, week) {
    var parser = new DOMParser();
    var newDoc = parser.parseFromString(html, 'text/html');

    var newLabel = newDoc.getElementById('week-label');
    if (newLabel) document.getElementById('week-label').innerHTML = newLabel.innerHTML;

    var newWrap = newDoc.getElementById('panels-wrap');
    var oldWrap = document.getElementById('panels-wrap');
    if (newWrap && oldWrap) oldWrap.innerHTML = newWrap.innerHTML;

    var newBtns = newDoc.querySelectorAll('.week-btn');
    var oldBtns = document.querySelectorAll('.week-btn');
    newBtns.forEach(function(nb, i) {
        if (!oldBtns[i]) return;
        var match = nb.getAttribute('onclick') && nb.getAttribute('onclick').match(/'([^']+)'/);
        if (match) oldBtns[i].setAttribute('onclick', "changeWeek('" + match[1] + "')");
    });

    // Update ALL_SLOTS dari response baru
    newDoc.querySelectorAll('script').forEach(function(s) {
        var m = s.textContent.match(/window\.ALL_SLOTS\s*=\s*(\[[\s\S]*?\]);/);
        if (m) { try { window.ALL_SLOTS = JSON.parse(m[1]); } catch(e) {} }
    });

    document.getElementById('skeleton').style.display = 'none';
    document.querySelectorAll('.lab-panel').forEach(function(p) { p.style.display = 'none'; });

    var targetId = currentActiveTabId || (document.querySelector('.lab-panel') && document.querySelector('.lab-panel').id.replace('panel-', ''));
    var target   = targetId ? document.getElementById('panel-' + targetId) : document.querySelector('.lab-panel');
    if (target) {
        target.style.display = '';
        target.style.animation = 'none';
        void target.offsetWidth;
        target.style.animation = '';
    }

    document.querySelectorAll('.week-btn').forEach(function(b) { b.disabled = false; b.style.opacity = ''; });

    var nextBtn = document.querySelector('.week-btn-next');
    if (nextBtn) {
        if (new Date(week + 'T00:00:00') >= getNextMonday()) {
            nextBtn.disabled = true;
            nextBtn.style.opacity = '.4';
            nextBtn.style.cursor = 'not-allowed';
            nextBtn.style.pointerEvents = 'none';
        } else {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '';
            nextBtn.style.cursor = '';
            nextBtn.style.pointerEvents = '';
        }
    }
}

window.addEventListener('popstate', function(e) {
    var week = e.state && e.state.week;
    if (!week) return;
    if (fetchController) { fetchController.abort(); fetchController = null; }
    var activeTab = document.querySelector('.tab-btn.tab-active');
    if (activeTab) currentActiveTabId = activeTab.id.replace('tab-', '');
    document.querySelectorAll('.lab-panel').forEach(function(p) { p.style.display = 'none'; });
    document.getElementById('skeleton').style.display = 'block';
    document.querySelectorAll('.week-btn').forEach(function(b) { b.disabled = true; b.style.opacity = '.6'; });
    var url = new URL(window.location.href);
    fetchController = new AbortController();
    fetch(url.toString(), {
        signal: fetchController.signal,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.text(); })
    .then(function(html) { fetchController = null; applyWeekResponse(html, week); })
    .catch(function(err) { if (err.name === 'AbortError') return; window.location.href = url.toString(); });
});

/* ─── TOAST ──────────────────────────────────────────────────────────────── */

function showToast(msg) {
    var toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(function() { toast.classList.remove('show'); }, 3000);
}

/* ─── AJAX KELAS ─────────────────────────────────────────────────────────── */

function loadKelas(orgId) {
    var sel = document.getElementById('f_class');
    if (!orgId) {
        sel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
        sel.disabled = true;
        return;
    }
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled  = true;
    fetch('/kelas?organization_id=' + encodeURIComponent(orgId))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            sel.innerHTML = '<option value="">— Pilih kelas —</option>';
            data.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value       = c.id;
                opt.textContent = c.name;
                sel.appendChild(opt);
            });
            sel.disabled = false;
        })
        .catch(function() { sel.innerHTML = '<option value="">Gagal memuat</option>'; });
}

function loadKelasSunday(orgId) {
    var sel = document.getElementById('sb_class');
    if (!orgId) {
        sel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
        sel.disabled = true;
        return;
    }
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled  = true;
    fetch('/kelas?organization_id=' + encodeURIComponent(orgId))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            sel.innerHTML = '<option value="">— Pilih kelas —</option>';
            data.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value       = c.id;
                opt.textContent = c.name;
                sel.appendChild(opt);
            });
            sel.disabled = false;
        })
        .catch(function() { sel.innerHTML = '<option value="">Gagal memuat</option>'; });
}

/* ─── SUNDAY BOOKING ─────────────────────────────────────────────────────── */

function openSundayBooking(rid, rname, date) {
    document.getElementById('sb_rid').value      = rid;
    document.getElementById('sb_date_val').value = date;
    var d  = new Date(date + 'T00:00:00');
    var mn = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('sb-lab').textContent  = '🖥 ' + rname;
    document.getElementById('sb-date').textContent = '📅 Minggu, ' + d.getDate() + ' ' + mn[d.getMonth()] + ' ' + d.getFullYear();
    document.getElementById('sb_teacher_name').value  = '';
    document.getElementById('sb_teacher_phone').value = '';
    document.getElementById('sb_org').value = '';
    document.getElementById('sb_class').innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
    document.getElementById('sb_class').disabled  = true;
    document.getElementById('sunday-modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeSundayModal() {
    var overlay = document.getElementById('sunday-modal-overlay');
    var box     = document.getElementById('sunday-modal-box');
    box.style.transition = 'opacity .16s, transform .16s';
    box.style.opacity    = '0';
    box.style.transform  = 'translateY(10px) scale(.97)';
    setTimeout(function() {
        overlay.classList.remove('show');
        box.style.transition = box.style.opacity = box.style.transform = '';
        document.body.style.overflow = '';
    }, 160);
}

/* ─── DOUBLE SUBMIT PREVENTION ───────────────────────────────────────────── */

document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        var btn = this.querySelector('.btn-submit');
        if (!btn) return;
        if (btn.dataset.loading === 'true') { e.preventDefault(); return; }
        btn.dataset.loading = 'true';
        btn.disabled = true;
        btn.style.opacity = '.7';
        btn.style.cursor = 'not-allowed';
        btn.textContent = '⏳ Memproses...';
        setTimeout(function() {
            btn.dataset.loading = 'false';
            btn.disabled = false;
            btn.style.opacity = '';
            btn.style.cursor = '';
            btn.textContent = '✓ Ajukan Booking';
        }, 10000);
    });
});

/* ─── REALTIME REVERB ────────────────────────────────────────────────────── */

function initRealtime() {
    if (typeof window.Echo === 'undefined') {
        console.warn('Echo not found. Realtime updates disabled.');
        return;
    }

    window.Echo.channel('schedules')
        .listen('.schedule.updated', (e) => {
            console.log('Schedule update received:', e);
            
            // Langsung update tabel tanpa reload halaman
            refreshScheduleUI();
        });
}

function refreshScheduleUI() {
    if (fetchController) return;
    
    const url = new URL(window.location.href);
    fetchController = new AbortController();

    fetch(url.toString(), {
        signal: fetchController.signal,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => {
        fetchController = null;
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');
        const newWrap = newDoc.getElementById('panels-wrap');
        const oldWrap = document.getElementById('panels-wrap');
        
        if (!newWrap || !oldWrap) return;

        const activeTab = document.querySelector('.tab-btn.tab-active');
        const activeId = activeTab ? activeTab.id.replace('tab-', '') : null;

        oldWrap.innerHTML = newWrap.innerHTML;

        // Update ALL_SLOTS
        newDoc.querySelectorAll('script').forEach(s => {
            const m = s.textContent.match(/window\.ALL_SLOTS\s*=\s*(\[[\s\S]*?\]);/);
            if (m) { try { window.ALL_SLOTS = JSON.parse(m[1]); } catch(e) {} }
        });

        // Restore active tab
        document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');
        const target = activeId 
            ? document.getElementById('panel-' + activeId) 
            : document.querySelector('.lab-panel');
        
        if (target) {
            target.style.display = '';
            target.style.animation = 'none';
            void target.offsetWidth;
            target.style.animation = 'panelIn .3s cubic-bezier(.16,1,.3,1)';
        }

        showToast('🔄 Jadwal diperbarui secara otomatis');
    })
    .catch(err => {
        if (err.name === 'AbortError') return;
        fetchController = null;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initRealtime();
});

/* ─── EXPOSE KE WINDOW (diperlukan karena Vite load JS sebagai module) ────── */
/* Fungsi-fungsi ini dipanggil dari onclick di HTML, harus ada di global scope  */

window.filterTeacher       = filterTeacher;
window.filterTeacherSunday = filterTeacherSunday;
window.switchTab           = switchTab;
window.openBooking         = openBooking;
window.closeModal          = closeModal;
window.showDetail          = showDetail;
window.closeDetail         = closeDetail;
window.changeWeek          = changeWeek;
window.loadKelas           = loadKelas;
window.loadKelasSunday     = loadKelasSunday;
window.openSundayBooking   = openSundayBooking;
window.closeSundayModal    = closeSundayModal;

/* ─── WRAP FUNGSI MODAL ─────────────────────────────────────────────────── */
/* Harus setelah EXPOSE agar window.xxx sudah terisi fungsi yang benar        */

(function() {
    var _origOpenBooking  = window.openBooking;
    var _origOpenSunday   = window.openSundayBooking;
    var _origShowDetail   = window.showDetail;

    window.openBooking = function() {
        _origOpenBooking.apply(this, arguments);
    };
    window.openSundayBooking = function() {
        _origOpenSunday.apply(this, arguments);
    };
    window.showDetail = function() {
        _origShowDetail.apply(this, arguments);
    };
})();