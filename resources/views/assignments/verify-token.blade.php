<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Guru – Lab Management</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{
    --primary: #00693E;
    --primary-dark: #003d24;
    --accent: #B9D9EB;
    --bg-body: #f8fafc;
    --text-main: #0d2416;
    --text-muted: #6b8fa3;
    --border-color: #e2e8f0;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:var(--bg-body);color:var(--text-main);min-height:100vh;display:flex;flex-direction:column}
a{text-decoration:none}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}

/* NAVBAR */
.navbar{background:linear-gradient(135deg,var(--primary-dark),var(--primary));padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 16px rgba(0,61,36,.28)}
.brand{display:flex;align-items:center;gap:10px}
.brand-icon{width:34px;height:34px;border-radius:10px;background:rgba(185,217,235,.12);border:1.5px solid rgba(185,217,235,.25);display:flex;align-items:center;justify-content:center}
.brand-name{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff}
.brand-sub{font-size:10px;color:rgba(185,217,235,.4)}
.nav-btn{padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;color:var(--accent);border:1.5px solid rgba(185,217,235,.3);transition:background .15s}
.nav-btn:hover{background:rgba(185,217,235,.08)}

/* CENTER */
.center{flex:1;display:flex;align-items:center;justify-content:center;padding:24px}

/* CARD */
.card{background:#fff;border-radius:20px;border:1px solid var(--border-color);box-shadow:0 4px 24px rgba(0,61,36,.1);width:100%;max-width:420px;overflow:hidden;animation:fadeUp .4s ease both}
.card-head{padding:28px 28px 24px;background:linear-gradient(135deg,var(--primary-dark),var(--primary));text-align:center}
.card-icon{width:56px;height:56px;border-radius:16px;background:rgba(185,217,235,.12);border:1.5px solid rgba(185,217,235,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 14px}
.card-title{font-family:'Outfit',sans-serif;font-weight:800;font-size:20px;color:#fff;margin-bottom:5px}
.card-sub{font-size:12px;color:rgba(185,217,235,.5)}
.card-body{padding:24px 28px 28px}

/* FORM */
.field{margin-bottom:16px}
.field-label{display:block;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.inp-token{width:100%;border:2px solid var(--border-color);border-radius:12px;padding:13px 16px;font-size:18px;font-family:'Outfit',sans-serif;font-weight:700;letter-spacing:.12em;text-align:center;text-transform:uppercase;background:#fafcf9;outline:none;transition:border-color .15s,box-shadow .15s;color:var(--text-main)}
.inp-token:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(185,217,235,.12)}
.inp-token::placeholder{font-size:14px;letter-spacing:0;color:#d1d5db;font-weight:400}
.btn-submit{width:100%;padding:13px;border-radius:12px;border:none;background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:var(--accent);font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;transition:transform .15s,box-shadow .15s,filter .15s;margin-top:4px}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,61,36,.3);filter:brightness(1.08)}

/* INFO */
.info-box{background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:13px 15px;margin-top:16px}
.info-title{font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px}
.info-item{display:flex;align-items:flex-start;gap:8px;font-size:12px;color:var(--text-muted);margin-bottom:5px}
.info-item:last-child{margin-bottom:0}

/* ERROR */
.error-box{background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:12px 15px;margin-bottom:16px;font-size:13px;font-weight:600;color:#991b1b}

/* FOOTER */
footer{text-align:center;padding:16px;font-size:12px;color:var(--text-muted)}
</style>
</head>
<body>

<div class="navbar">
    <div class="brand">
        <div class="brand-icon">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#B9D9EB" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <div class="brand-name">Lab Management</div>
            <div class="brand-sub">Nuris Jember</div>
        </div>
    </div>
    <a href="{{ route('assignment.public') }}" class="nav-btn">← Halaman Tugas</a>
</div>

<div class="center">
    <div class="card">
        <div class="card-head">
            <div class="card-icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#B9D9EB" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div class="card-title">Masuk Panel Guru</div>
            <div class="card-sub">Masukkan token unikmu untuk mengelola tugas</div>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="error-box">⚠ {{ $errors->first() }}</div>
            @endif

            <form method="GET" action="{{ route('assignment.admin') }}">
                <div class="field">
                    <label class="field-label">Token Guru</label>
                    <input
                        type="text"
                        name="token"
                        class="inp-token"
                        placeholder="GRU-001"
                        maxlength="10"
                        required
                        autofocus
                        oninput="this.value = this.value.toUpperCase()"
                        value="{{ old('token') }}"
                    >
                </div>
                <button type="submit" class="btn-submit">→ Masuk Panel</button>
            </form>

            <div class="info-box">
                <div class="info-title">Informasi</div>
                <div class="info-item">
                    <span>🔑</span>
                    <span>Token diberikan oleh admin sekolah. Format: <strong>GRU-XXX</strong></span>
                </div>
                <div class="info-item">
                    <span>📋</span>
                    <span>Kamu hanya bisa melihat tugas yang kamu buat sendiri</span>
                </div>
                <div class="info-item">
                    <span>💬</span>
                    <span>Belum punya token? Hubungi admin lab</span>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>© {{ date('Y') }} Lab Management System · Nuris Jember</footer>

</body>
</html>