<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $labName }} – Lab Control</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f0f4f8;min-height:100vh;color:#1e293b}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.6}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes wave{0%{transform:scale(1);opacity:0.8}100%{transform:scale(1.5);opacity:0}}

/* HEADER */
.hdr{background:#003d24;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:4px solid #B9D9EB;position:sticky;top:0;z-index:100}
.hdr-brand{display:flex;align-items:center;gap:12px}
.hdr-dot{width:10px;height:10px;border-radius:50%;background:#B9D9EB;position:relative}
.hdr-dot::after{content:'';position:absolute;inset:-4px;border-radius:50%;border:2px solid #B9D9EB;animation:wave 2s infinite}
.hdr-name{font-family:'Outfit',sans-serif;font-weight:700;font-size:16px;color:#fff;letter-spacing:-0.01em}
.btn-end{display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;background:rgba(248,113,113,0.15);color:#fca5a5;font-size:11px;font-weight:700;cursor:pointer;border:1px solid rgba(248,113,113,0.2);transition:all .2s}
.btn-end:hover{background:#ef4444;color:#fff}

/* MAIN LAYOUT */
.main{max-width:1000px;margin:0 auto;padding:20px;display:grid;grid-template-columns:320px 1fr;gap:20px;animation:fadeUp .4s ease both}

/* LEFT COLUMN: CONTROLS */
.sidebar{display:flex;flex-direction:column;gap:20px}

/* MASTER CARD */
.card-master{background:#fff;border-radius:24px;padding:24px;box-shadow:0 10px 25px -5px rgba(0,0,0,0.05);border:1px solid #e2e8f0}
.card-title{font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:20px;display:flex;align-items:center;gap:8px}
.card-title::before{content:'';width:4px;height:12px;background:#003d24;border-radius:2px}

.status-display{text-align:center;margin-bottom:24px}
.status-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:999px;font-size:13px;font-weight:800;margin-bottom:12px}
.status-badge.online{background:#dcfce7;color:#15803d}
.status-badge.offline{background:#fee2e2;color:#b91c1c}
.status-badge.checking{background:#fef9c3;color:#a16207}

.status-main{font-family:'Outfit',sans-serif;font-weight:800;font-size:32px;color:#0f172a;line-height:1}
.status-sub{font-size:13px;color:#64748b;margin-top:8px}

/* TOGGLE BUTTONS */
.toggle-group{display:grid;gap:12px}
.btn-toggle{padding:16px;border-radius:16px;border:none;cursor:pointer;font-family:inherit;font-weight:700;font-size:14px;display:flex;align-items:center;justify-content:center;gap:10px;transition:all .2s;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1)}
.btn-toggle:disabled{opacity:0.4;cursor:not-allowed}
.btn-toggle.on{background:#16a34a;color:#fff}
.btn-toggle.on:hover:not(:disabled){background:#15803d;transform:translateY(-2px)}
.btn-toggle.off{background:#dc2626;color:#fff}
.btn-toggle.off:hover:not(:disabled){background:#b91c1c;transform:translateY(-2px)}

/* STATS ROW */
.stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.stat-box{background:#f8fafc;padding:16px;border-radius:16px;border:1px solid #e2e8f0;text-align:center}
.stat-val{font-family:'Outfit',sans-serif;font-size:20px;font-weight:800;color:#0f172a}
.stat-lab{font-size:10px;color:#64748b;font-weight:600;text-transform:uppercase}

/* RIGHT COLUMN: LAB MAP */
.content-area{background:#fff;border-radius:28px;padding:24px;box-shadow:0 10px 25px -5px rgba(0,0,0,0.05);border:1px solid #e2e8f0;display:flex;flex-direction:column}
.area-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #f1f5f9}
.area-title{font-family:'Outfit',sans-serif;font-size:18px;font-weight:800;color:#0f172a}
.area-legend{display:flex;gap:16px;font-size:11px;font-weight:700;color:#64748b}
.legend-item{display:flex;align-items:center;gap:6px}
.legend-dot{width:8px;height:8px;border-radius:2px}

/* PC GRID MAP */
.lab-map-container{flex:1;background:#f1f5f9;border-radius:20px;padding:32px;position:relative;min-height:400px;overflow:auto}
.lab-map-grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(70px, 1fr));gap:24px;justify-items:center}

.pc-unit{width:64px;display:flex;flex-direction:column;align-items:center;gap:6px;transition:all 0.3s ease}
.pc-screen{width:54px;height:40px;background:#1e293b;border-radius:6px;border:3px solid #334155;position:relative;display:flex;align-items:center;justify-content:center;transition:all 0.3s}
.pc-screen::after{content:'';position:absolute;bottom:-8px;left:50%;transform:translateX(-50%);width:20px;height:6px;background:#334155;border-radius:2px}
.pc-screen::before{content:'';position:absolute;bottom:-12px;left:50%;transform:translateX(-50%);width:32px;height:4px;background:#334155;border-radius:2px}

.pc-unit.online .pc-screen{background:#22c55e;border-color:#16a34a;box-shadow:0 0 15px rgba(34,197,94,0.4)}
.pc-unit.online .pc-screen::after, .pc-unit.online .pc-screen::before{background:#16a34a}
.pc-unit.online .pc-status-icon{color:#fff;font-size:14px;animation:pulse 2s infinite}

.pc-label{font-size:10px;font-weight:800;color:#64748b;text-align:center;font-family:'JetBrains Mono',monospace}
.pc-unit.online .pc-label{color:#15803d}

.pc-tooltip{position:absolute;background:#0f172a;color:#fff;padding:8px 12px;border-radius:8px;font-size:10px;pointer-events:none;opacity:0;transition:opacity 0.2s;z-index:10;box-shadow:0 10px 15px -3px rgba(0,0,0,0.3);width:max-content}
.pc-unit:hover .pc-tooltip{opacity:1}

/* MOBILE RESPONSIVE */
@media (max-width: 850px) {
    .main{grid-template-columns:1fr}
    .sidebar{order:1}
    .content-area{order:2}
}

/* SESSION BAR FLOATING */
.session-float{background:#fff;border-radius:12px;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;border:1px solid #e2e8f0}
.session-timer{display:flex;align-items:center;gap:12px}
.timer-val{font-family:'JetBrains Mono',monospace;font-weight:800;color:#003d24;font-size:15px}

/* LOADING */
.loading-overlay{position:fixed;inset:0;background:rgba(0,30,18,0.7);display:none;align-items:center;justify-content:center;z-index:999;backdrop-filter:blur(8px)}
.loading-overlay.active{display:flex}
.loading-box{background:#fff;border-radius:24px;padding:40px;text-align:center;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25)}
.spinner{width:48px;height:48px;border:4px solid #f1f5f9;border-top-color:#16a34a;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 20px}
</style>
</head>
<body>

<div class="hdr">
    <div class="hdr-brand">
        <div class="hdr-dot"></div>
        <div class="hdr-name">{{ $labName }} Control Center</div>
    </div>
    <form method="POST" action="{{ route('lab.logout', $token) }}">
        @csrf
        <button type="submit" class="btn-end" onclick="return confirm('Akhiri sesi kontrol?')">Keluar</button>
    </form>
</div>

<div class="main">
    
    <div class="sidebar">
        
        {{-- SESSION & TIMER --}}
        <div class="card-master">
            <div class="card-title">Sesi Aktif</div>
            <div class="session-timer">
                <div style="flex:1">
                    <div style="font-size:11px;color:#64748b">Waktu Tersisa</div>
                    <div class="timer-val" id="countdown">--:--</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:11px;color:#64748b">Pengajar</div>
                    <div style="font-size:13px;font-weight:700;color:#0f172a">{{ $session->teacher_name }}</div>
                </div>
            </div>
        </div>

        {{-- INTERNET CONTROL --}}
        <div class="card-master">
            <div class="card-title">Kontrol Internet</div>
            <div class="status-display">
                <div class="status-badge checking" id="status-badge">Memeriksa...</div>
                <div class="status-main" id="status-label">--</div>
                <div class="status-sub" id="status-desc">Menghubungi MikroTik...</div>
            </div>
            
            <div class="toggle-group">
                <button class="btn-toggle on" id="btn-on" onclick="toggleInternet('on')" disabled>
                    <span>⚡</span> Hidupkan Internet
                </button>
                <button class="btn-toggle off" id="btn-off" onclick="toggleInternet('off')" disabled>
                    <span>🔒</span> Putuskan Internet
                </button>
            </div>
        </div>

        {{-- REALTIME STATS --}}
        <div class="card-master">
            <div class="card-title">Statistik Lab</div>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-val" id="info-devices">0</div>
                    <div class="stat-lab">Total PC</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val" id="info-online" style="color:#16a34a">0</div>
                    <div class="stat-lab">Online</div>
                </div>
            </div>
            <div style="font-size:10px;color:#94a3b8;margin-top:12px;text-align:center">
                Terakhir update: <span id="info-update">--:--</span>
            </div>
        </div>

    </div>

    <div class="content-area">
        <div class="area-hdr">
            <div class="area-title">Live Lab Map</div>
            <div class="area-legend">
                <div class="legend-item"><div class="legend-dot" style="background:#22c55e"></div> Online</div>
                <div class="legend-item"><div class="legend-dot" style="background:#1e293b"></div> Offline</div>
            </div>
        </div>

        <div class="lab-map-container">
            <div class="lab-map-grid" id="devices-list">
                {{-- Grid PC akan di-render di sini --}}
            </div>
        </div>
        
        <div style="margin-top:16px;display:flex;justify-content:space-between;align-items:center">
            <div style="font-size:11px;color:#94a3b8">Klik PC untuk detail IP/MAC</div>
            <button class="btn-refresh" onclick="loadStatus()" style="background:#003d24;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:11px">↺ Refresh Map</button>
        </div>
    </div>

</div>

{{-- LOADING & TOAST (Tetap sama namun desain dipercantik di CSS) --}}
<div class="loading-overlay" id="loading">
    <div class="loading-box">
        <div class="spinner"></div>
        <div class="loading-text" id="loading-text">Memproses...</div>
    </div>
</div>

<div class="toast" id="toast">
    <span id="toast-icon" style="font-size:18px">✅</span>
    <div class="toast-msg" id="toast-msg"></div>
</div>

<script>
const TOKEN      = '{{ $token }}';
const SESSION_END = new Date('{{ $session->session_end->toIso8601String() }}');
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const STATUS_URL = '/lab-control/' + TOKEN + '/status';
const TOGGLE_URL = '/lab-control/' + TOKEN + '/toggle';

function updateCountdown() {
    const diff = Math.floor((SESSION_END - new Date()) / 1000);
    const el = document.getElementById('countdown');
    if (diff <= 0) {
        el.textContent = 'SELESAI';
        setButtonsEnabled(false);
        return;
    }
    const m = Math.floor(diff / 60);
    const s = diff % 60;
    el.textContent = `${m}:${String(s).padStart(2,'0')}`;
}
setInterval(updateCountdown, 1000);
updateCountdown();

async function loadStatus() {
    try {
        const res  = await fetch(STATUS_URL);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Gagal');

        const online = data.nat_enabled;
        const badge  = document.getElementById('status-badge');
        const label  = document.getElementById('status-label');
        const desc   = document.getElementById('status-desc');

        badge.className = 'status-badge ' + (online ? 'online' : 'offline');
        badge.textContent = online ? '● Active' : '○ Blocked';
        label.textContent = online ? 'Internet ON' : 'Internet OFF';
        desc.textContent  = online ? 'Akses publik diizinkan' : 'Akses publik diputus';

        document.getElementById('info-devices').textContent = (data.devices || []).length;
        document.getElementById('info-online').textContent  = data.active_users ?? 0;
        document.getElementById('info-update').textContent  = new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});

        setButtonsEnabled(true);
        renderLabMap(data.devices || []);
    } catch(e) {
        document.getElementById('status-label').textContent = 'Error';
        showToast('Koneksi terputus: ' + e.message, 'err');
    }
}

function renderLabMap(devices) {
    const el = document.getElementById('devices-list');
    if (!devices.length) {
        el.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#94a3b8">Belum ada PC terdeteksi di jaringan</div>';
        return;
    }
    
    let html = '';
    devices.forEach((d, i) => {
        const pcNum = i + 1;
        html += `
        <div class="pc-unit ${d.active ? 'online' : ''}">
            <div class="pc-screen">
                ${d.active ? '<span class="pc-status-icon">✔</span>' : ''}
            </div>
            <div class="pc-label">PC-${String(pcNum).padStart(2, '0')}</div>
            <div class="pc-tooltip">
                <strong>${d.hostname || 'PC-'+pcNum}</strong><br>
                IP: ${d.ip}<br>
                MAC: ${d.mac}
            </div>
        </div>`;
    });
    el.innerHTML = html;
}

async function toggleInternet(action) {
    const txt = action === 'on' ? 'Membuka' : 'Memutus';
    showLoading(`${txt} akses internet...`);
    setButtonsEnabled(false);

    try {
        const res  = await fetch(TOGGLE_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({action})
        });
        const data = await res.json();
        hideLoading();

        if (data.success) {
            showToast(`Berhasil: Internet telah ${action === 'on' ? 'diaktifkan' : 'dimatikan'}`, 'ok');
            await loadStatus();
        } else throw new Error(data.error);
    } catch(e) {
        hideLoading();
        showToast('Gagal: ' + e.message, 'err');
        setButtonsEnabled(true);
    }
}

function setButtonsEnabled(v) {
    document.getElementById('btn-on').disabled  = !v;
    document.getElementById('btn-off').disabled = !v;
}
function showLoading(msg) {
    document.getElementById('loading-text').textContent = msg;
    document.getElementById('loading').classList.add('active');
}
function hideLoading() {
    document.getElementById('loading').classList.remove('active');
}
function showToast(msg, type) {
    const t = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    document.getElementById('toast-icon').textContent = type === 'ok' ? '✅' : '❌';
    t.className = `toast active ${type}`;
    setTimeout(() => t.classList.remove('active'), 4000);
}

loadStatus();
setInterval(loadStatus, 15000);
</script>
</body>
</html>