<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Link Tidak Valid – Lab Control</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f8fafc;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
.card{background:#fff;border-radius:32px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.1);width:100%;max-width:440px;overflow:hidden;animation:fadeUp .5s cubic-bezier(0.16, 1, 0.3, 1) both;text-align:center;border:1px solid #e2e8f0}
.card-head{padding:48px 32px 40px;background:#003d24;position:relative;border-bottom:6px solid #B9D9EB}
.icon-wrap{width:80px;height:80px;border-radius:24px;background:rgba(185,217,235,0.1);border:2px solid rgba(185,217,235,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
.card-title{font-family:'Outfit',sans-serif;font-weight:800;font-size:26px;color:#fff;margin-bottom:8px;letter-spacing:-0.02em}
.card-sub{font-size:14px;color:rgba(185,217,235,0.6);font-weight:500}
.card-body{padding:40px 32px}
.msg-box{background:#fef2f2;border:1px solid #fecaca;border-radius:20px;padding:20px;font-size:15px;color:#b91c1c;font-weight:700;margin-bottom:24px;line-height:1.5}
.info-text{font-size:14px;color:#64748b;line-height:1.7;margin-bottom:32px}
.btn-home{display:inline-flex;align-items:center;justify-content:center;padding:14px 28px;background:#003d24;color:#fff;text-decoration:none;border-radius:14px;font-weight:700;font-size:15px;transition:all 0.2s}
.btn-home:hover{background:#005c36;transform:translateY(-2px);box-shadow:0 10px 15px -3px rgba(0,0,0,0.1)}
footer{margin-top:32px;font-size:13px;color:#94a3b8;text-align:center;font-weight:500}
</style>
</head>
<body>
<div>
    <div class="card">
        <div class="card-head">
            <div class="icon-wrap">
                <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#B9D9EB" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="card-title">Akses Terbatas</div>
            <div class="card-sub">Lab Control · Nuris Jember</div>
        </div>
        <div class="card-body">
            <div class="msg-box">
                {{ $message ?? 'Link tidak valid atau sudah expired.' }}
            </div>
            <p class="info-text">
                Link akses kontrol internet lab hanya berlaku selama durasi sesi praktik berlangsung. Silakan hubungi admin atau instruktur Anda untuk mendapatkan link akses yang baru.
            </p>
            <a href="/" class="btn-home">Kembali ke Beranda</a>
        </div>
    </div>
    <footer>© {{ date('Y') }} Lab Management · Nuris Jember</footer>
</div>
</body>
</html>