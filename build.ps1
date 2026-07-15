<#
================================================================
 build.ps1 — Script build image lab-management (otomatis)
================================================================

CARA PAKAI:
  1. Simpan file ini di root folder project (C:\laragon\www\lab-management)
  2. Pastikan file .env.production ada di folder yang sama (untuk ambil
     REVERB_APP_KEY & REVERB_HOST otomatis, supaya TIDAK PERNAH mismatch
     lagi dengan production)
  3. Jalankan di PowerShell:

       .\build.ps1 -Version v2.7

     atau sekalian push ke Docker Hub:

       .\build.ps1 -Version v2.7 -Push

  Script ini otomatis:
    - Baca REVERB_APP_KEY, REVERB_HOST, REVERB_PORT, REVERB_SCHEME
      dari .env.production (SUMBER KEBENARAN, bukan diketik manual)
    - Build pakai --secret mount (key TIDAK ke-bake permanen di layer
      image, jadi tidak ada lagi warning SecretsUsedInArgOrEnv)
    - Hapus file secret sementara otomatis setelah build selesai
    - (Opsional) push ke Docker Hub kalau pakai flag -Push
================================================================
#>

param(
    [Parameter(Mandatory=$true)]
    [string]$Version,

    [switch]$Push,

    [string]$EnvFile = ".env.production",

    [string]$ImageName = "iswant/lab-management"
)

$ErrorActionPreference = "Stop"

Write-Host "=== Build lab-management image: $ImageName`:$Version ===" -ForegroundColor Cyan

# ── 1. Pastikan Docker Desktop jalan ─────────────────────────────
try {
    docker info | Out-Null
} catch {
    Write-Host "ERROR: Docker Desktop belum jalan. Buka Docker Desktop dulu, tunggu sampai ready, lalu jalankan ulang script ini." -ForegroundColor Red
    exit 1
}

# ── 2. Pastikan file .env sumber ada ─────────────────────────────
if (-not (Test-Path $EnvFile)) {
    Write-Host "ERROR: File '$EnvFile' tidak ditemukan di folder ini." -ForegroundColor Red
    Write-Host "Copy dulu .env production dari server ke sini dengan nama '$EnvFile', atau sesuaikan parameter -EnvFile." -ForegroundColor Yellow
    exit 1
}

# ── 3. Fungsi kecil untuk ambil value dari file .env ─────────────
function Get-EnvValue {
    param([string]$Key, [string]$File)
    $line = Select-String -Path $File -Pattern "^$Key=" | Select-Object -First 1
    if (-not $line) { return $null }
    $value = $line.Line -replace "^$Key=", ""
    return $value.Trim('"').Trim()
}

$reverbKey    = Get-EnvValue -Key "REVERB_APP_KEY" -File $EnvFile
$reverbHost   = Get-EnvValue -Key "APP_URL" -File $EnvFile   # fallback, disesuaikan di bawah
$reverbPort   = "443"
$reverbScheme = "https"

# Kalau kamu punya VITE_REVERB_HOST terpisah di .env, baca itu; kalau tidak, turunkan dari APP_URL
$viteHost = Get-EnvValue -Key "VITE_REVERB_HOST" -File $EnvFile
if ($viteHost) {
    $reverbHost = $viteHost
} elseif ($reverbHost) {
    $reverbHost = ($reverbHost -replace "^https?://", "") -replace "/$", ""
}

if (-not $reverbKey) {
    Write-Host "ERROR: REVERB_APP_KEY tidak ditemukan di $EnvFile" -ForegroundColor Red
    exit 1
}

Write-Host "REVERB_APP_KEY  : $($reverbKey.Substring(0,8))... (disembunyikan sebagian)" -ForegroundColor Gray
Write-Host "REVERB_HOST     : $reverbHost" -ForegroundColor Gray
Write-Host "REVERB_PORT     : $reverbPort" -ForegroundColor Gray
Write-Host "REVERB_SCHEME   : $reverbScheme" -ForegroundColor Gray

# ── 4. Buat file secret sementara ────────────────────────────────
$secretFile = Join-Path $env:TEMP "reverb_key_$(Get-Random).txt"
Set-Content -Path $secretFile -Value $reverbKey -NoNewline -Encoding ascii

try {
    Write-Host "`n=== Menjalankan docker build ===" -ForegroundColor Cyan

    docker build `
        --secret id=vite_reverb_app_key,src=$secretFile `
        --build-arg VITE_REVERB_HOST=$reverbHost `
        --build-arg VITE_REVERB_PORT=$reverbPort `
        --build-arg VITE_REVERB_SCHEME=$reverbScheme `
        -t "$ImageName`:$Version" `
        .

    if ($LASTEXITCODE -ne 0) {
        throw "docker build gagal (exit code $LASTEXITCODE)"
    }

    Write-Host "`n=== Build sukses: $ImageName`:$Version ===" -ForegroundColor Green

    # ── 5. Verifikasi key ke-bake dengan benar ──────────────────
    Write-Host "`n=== Verifikasi REVERB key di hasil build ===" -ForegroundColor Cyan
    $verifyDir = "$env:TEMP\verify_assets_$Version"
    if (Test-Path $verifyDir) { Remove-Item $verifyDir -Recurse -Force -ErrorAction SilentlyContinue }

    $containerId = docker create "$ImageName`:$Version"

    # Pakai cmd /c supaya output "Successfully copied..." dari docker cp
    # (yang kadang nulis ke stderr) TIDAK dianggap exception oleh PowerShell.
    cmd /c "docker cp ${containerId}:/var/www/html/public/build/assets `"$verifyDir`"" 2>&1 | Out-Null
    docker rm $containerId | Out-Null

    if (Test-Path $verifyDir) {
        $found = Get-ChildItem -Path $verifyDir -Filter "app-*.js" -ErrorAction SilentlyContinue |
                 Select-String -Pattern $reverbKey -ErrorAction SilentlyContinue

        if ($found) {
            Write-Host "COCOK: REVERB_APP_KEY di frontend JS sesuai dengan .env" -ForegroundColor Green
        } else {
            Write-Host "PERINGATAN: Key TIDAK ditemukan di file JS. Cek manual sebelum deploy!" -ForegroundColor Yellow
        }
        Remove-Item $verifyDir -Recurse -Force -ErrorAction SilentlyContinue
    } else {
        Write-Host "PERINGATAN: Gagal mengambil file assets untuk verifikasi (folder tidak ditemukan). Cek manual sebelum deploy!" -ForegroundColor Yellow
    }

    # ── 6. Push opsional ─────────────────────────────────────────
    if ($Push) {
        Write-Host "`n=== Push ke Docker Hub ===" -ForegroundColor Cyan
        docker push "$ImageName`:$Version"
        if ($LASTEXITCODE -ne 0) {
            throw "docker push gagal (exit code $LASTEXITCODE)"
        }
        Write-Host "`n=== Push sukses: $ImageName`:$Version ===" -ForegroundColor Green
    } else {
        Write-Host "`nBelum di-push. Jalankan manual kalau sudah yakin:" -ForegroundColor Yellow
        Write-Host "  docker push $ImageName`:$Version" -ForegroundColor Yellow
    }

} finally {
    # ── 7. Selalu hapus file secret, apapun hasilnya ────────────
    if (Test-Path $secretFile) {
        Remove-Item $secretFile -Force
        Write-Host "`nFile secret sementara sudah dihapus." -ForegroundColor Gray
    }
}

Write-Host "`n=== SELESAI ===" -ForegroundColor Cyan
Write-Host "Langkah selanjutnya di server (tameng):" -ForegroundColor White
Write-Host "  1. Edit docker-compose.swarm.yml -> ganti tag image ke $Version" -ForegroundColor White
Write-Host "  2. set -a; source .env; set +a" -ForegroundColor White
Write-Host "  3. docker stack deploy -c docker-compose.swarm.yml lab-management" -ForegroundColor White
Write-Host "  4. php artisan migrate --force (kalau ada migration baru)" -ForegroundColor White