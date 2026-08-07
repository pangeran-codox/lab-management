# Deployment Guide — Lab Management System

## Daftar Isi

- [Overview](#overview)
- [Prasyarat Server](#prasyarat-server)
- [Environment Variables Production](#environment-variables-production)
- [Build Docker Image](#build-docker-image)
- [Setup Docker Swarm](#setup-docker-swarm)
- [Deploy Stack](#deploy-stack)
- [Update / Rolling Deploy](#update--rolling-deploy)
- [Nginx Proxy Manager](#nginx-proxy-manager)
- [Database Backup](#database-backup)
- [Monitoring](#monitoring)
- [Troubleshooting](#troubleshooting)

---

## Overview

Production environment menggunakan **Docker Swarm** dengan topologi:

```
Server (single node atau multi-node)
├── lab_app      × 2 replicas   (PHP-FPM, worker nodes)
├── lab_nginx    × 1 replica    (Nginx reverse proxy)
├── lab_queue    × 1 replica    (Laravel Horizon)
├── lab_scheduler× 1 replica    (Laravel Cron)
└── lab_reverb   × 1 replica    (WebSocket Reverb)

Shared infrastructure (bisa di server terpisah):
├── postgres:16-alpine          (database)
├── redis:7-alpine              (cache/session/queue)
└── npm (Nginx Proxy Manager)   (SSL termination)
```

Image production: `iswant/lab-management:v{VERSION}` (Docker Hub)

---

## Prasyarat Server

### Hardware Minimum
- CPU: 2 vCPU
- RAM: 4 GB
- Disk: 40 GB (SSD direkomendasikan)

### Software
```bash
# Docker Engine 24+
curl -fsSL https://get.docker.com | sh

# Verifikasi
docker --version
docker compose version
```

### Ports yang Dibutuhkan
| Port | Service | Deskripsi |
|------|---------|-----------|
| 80, 443 | Nginx Proxy Manager | HTTP/HTTPS public |
| 8080 | lab_nginx | App (di-proxy oleh NPM) |
| 8084 | lab_reverb | WebSocket (di-proxy oleh NPM) |
| 5432 | PostgreSQL | Database (internal only) |
| 6379 | Redis | Cache (internal only) |

---

## Environment Variables Production

Buat file `.env.production` atau inject via Docker Swarm secrets/configs:

```env
# ──────────────────────────────────────────
# App
# ──────────────────────────────────────────
APP_NAME="Lab Management"
APP_ENV=production
APP_KEY=base64:GENERATE_WITH_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://lab.nuris.sch.id
FORCE_HTTPS=true

TELESCOPE_ENABLED=false

# ──────────────────────────────────────────
# Database Utama
# ──────────────────────────────────────────
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=lab_management
DB_USERNAME=laravel
DB_PASSWORD=STRONG_PASSWORD_HERE

# ──────────────────────────────────────────
# Database Finance
# ──────────────────────────────────────────
DB_FINANCE_HOST=postgres
DB_FINANCE_PORT=5432
DB_FINANCE_DATABASE=finance
DB_FINANCE_USERNAME=laravel
DB_FINANCE_PASSWORD=STRONG_PASSWORD_HERE

# ──────────────────────────────────────────
# Redis
# ──────────────────────────────────────────
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=REDIS_PASSWORD_HERE
REDIS_DB=2
REDIS_CACHE_DB=3

# ──────────────────────────────────────────
# Queue, Cache, Session
# ──────────────────────────────────────────
BROADCAST_DRIVER=reverb
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# ──────────────────────────────────────────
# Reverb WebSocket
# ──────────────────────────────────────────
REVERB_APP_ID=lab-management
REVERB_APP_KEY=YOUR_REVERB_KEY
REVERB_APP_SECRET=YOUR_REVERB_SECRET
REVERB_HOST=lab_reverb
REVERB_PORT=8084
REVERB_SCHEME=http

# Untuk Vite (dibake saat build)
VITE_REVERB_APP_KEY=YOUR_REVERB_KEY
VITE_REVERB_HOST=lab.nuris.sch.id
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https

# ──────────────────────────────────────────
# MikroTik Bot
# ──────────────────────────────────────────
BOT_URL=http://YOUR_BOT_SERVER:5000
BOT_TOKEN=YOUR_BOT_TOKEN
BOT_WEBHOOK_URL=https://lab.nuris.sch.id/api/webhook/lab-session

# ──────────────────────────────────────────
# WhatsApp Baileys
# ──────────────────────────────────────────
BAILEYS_URL=http://YOUR_BAILEYS_SERVER:3002
BAILEYS_API_KEY=YOUR_API_KEY

# ──────────────────────────────────────────
# Mail (opsional)
# ──────────────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@nuris.sch.id
MAIL_FROM_NAME="Lab Management Nuris"
```

---

## Build Docker Image

### 1. Persiapkan secret Reverb

```bash
mkdir -p secrets
echo "YOUR_REVERB_APP_KEY" > secrets/vite_reverb_app_key.txt
```

### 2. Build dengan BuildKit

```bash
# Set versi
export VERSION=v2.1

# Build menggunakan BuildKit secret (key tidak masuk ke image layer)
DOCKER_BUILDKIT=1 docker build \
  --target production \
  --secret id=vite_reverb_app_key,src=secrets/vite_reverb_app_key.txt \
  --build-arg VITE_REVERB_HOST=lab.nuris.sch.id \
  --build-arg VITE_REVERB_PORT=443 \
  --build-arg VITE_REVERB_SCHEME=https \
  -t iswant/lab-management:${VERSION} \
  -t iswant/lab-management:latest \
  .
```

### 3. Push ke Docker Hub

```bash
docker login
docker push iswant/lab-management:${VERSION}
docker push iswant/lab-management:latest
```

> **Catatan:** VITE_* variables di-bake ke dalam JavaScript bundle saat build. Pastikan nilai `VITE_REVERB_HOST` sesuai domain production sebelum build.

---

## Setup Docker Swarm

### Inisialisasi Swarm (pertama kali)

```bash
# Di node manager
docker swarm init --advertise-addr YOUR_SERVER_IP

# Jika multi-node, join worker nodes:
docker swarm join --token SWARM_TOKEN YOUR_MANAGER_IP:2377
```

### Buat Network & Volume

```bash
# Network shared (pakai jika ada service lain di server yang sama)
docker network create --driver overlay --attachable network

# Volume untuk static files publik
docker volume create public_files
```

### Setup Database (pertama kali)

```bash
# Jalankan PostgreSQL sementara untuk setup
docker run -d \
  --name postgres_setup \
  --network network \
  -e POSTGRES_PASSWORD=STRONG_PASSWORD \
  postgres:16-alpine

# Masuk ke container
docker exec -it postgres_setup psql -U postgres

# Buat databases dan user
CREATE DATABASE lab_management;
CREATE DATABASE finance;
CREATE USER laravel WITH PASSWORD 'STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON DATABASE lab_management TO laravel;
GRANT ALL PRIVILEGES ON DATABASE finance TO laravel;
\q

# Cleanup
docker rm -f postgres_setup
```

---

## Deploy Stack

### 1. Buat file `.env.swarm` di server

```bash
# Di server production
cat > /opt/lab-management/.env.swarm << 'EOF'
DB_DATABASE=lab_management
DB_USERNAME=laravel
DB_PASSWORD=STRONG_PASSWORD
REDIS_PASSWORD=REDIS_PASSWORD
REVERB_HOST=lab_reverb
REVERB_PORT=8084
REVERB_APP_ID=lab-management
REVERB_APP_KEY=YOUR_KEY
REVERB_APP_SECRET=YOUR_SECRET
EOF
```

### 2. Deploy stack

```bash
# Clone atau copy docker-compose.swarm.yml ke server
# Kemudian:

cd /opt/lab-management

docker stack deploy \
  --compose-file docker-compose.swarm.yml \
  --with-registry-auth \
  --env-file .env.swarm \
  lab
```

### 3. Jalankan migrasi (pertama kali)

```bash
# Ambil ID salah satu container app
docker ps | grep lab_app

# Jalankan migrasi
docker exec -it CONTAINER_ID php artisan migrate --force

# Seed data awal (jika dibutuhkan)
docker exec -it CONTAINER_ID php artisan db:seed --force

# Buat storage symlink
docker exec -it CONTAINER_ID php artisan storage:link
```

### 4. Verifikasi deploy

```bash
# Cek status stack
docker stack ps lab

# Semua service harus berstatus Running
docker service ls

# Cek log app
docker service logs lab_app --tail 50
docker service logs lab_nginx --tail 20
```

---

## Update / Rolling Deploy

Untuk update ke versi baru tanpa downtime:

```bash
# 1. Build & push image baru
export VERSION=v2.2
DOCKER_BUILDKIT=1 docker build --target production \
  --secret id=vite_reverb_app_key,src=secrets/vite_reverb_app_key.txt \
  -t iswant/lab-management:${VERSION} .
docker push iswant/lab-management:${VERSION}

# 2. Update image di Swarm (rolling update)
docker service update \
  --image iswant/lab-management:${VERSION} \
  --update-order start-first \
  --update-parallelism 1 \
  lab_app

# Update queue worker juga
docker service update \
  --image iswant/lab-management:${VERSION} \
  lab_queue

docker service update \
  --image iswant/lab-management:${VERSION} \
  lab_scheduler
```

Entrypoint script akan otomatis menjalankan `php artisan migrate --force` saat container baru start.

---

## Nginx Proxy Manager

Konfigurasi SSL dan domain di Nginx Proxy Manager (NPM):

### Proxy Host untuk App

```
Domain: lab.nuris.sch.id
Forward Hostname: YOUR_SERVER_IP
Forward Port: 8080
SSL: Let's Encrypt (auto)
Force SSL: Yes
```

**Custom Nginx Config** (tambahkan di tab Advanced):
```nginx
# Upload limit untuk jurnal dan tugas
client_max_body_size 25M;

# WebSocket support untuk Reverb
location /app {
    proxy_pass http://YOUR_SERVER_IP:8084;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_read_timeout 86400;
}
```

> **Catatan:** Jika NPM dan app di server yang sama, gunakan internal Docker network IP bukan `localhost`.

---

## Database Backup

### Backup Otomatis via Cron

Tambahkan ke crontab di server:

```bash
# Edit crontab
crontab -e

# Backup harian jam 02:00
0 2 * * * docker exec postgres pg_dumpall -U postgres | \
  gzip > /opt/backups/lab-$(date +\%Y\%m\%d).sql.gz

# Hapus backup lebih dari 30 hari
0 3 * * * find /opt/backups -name "lab-*.sql.gz" -mtime +30 -delete
```

### Manual Backup

```bash
# Backup semua database
docker exec postgres pg_dumpall -U postgres > backup_$(date +%Y%m%d).sql

# Backup database tertentu
docker exec postgres pg_dump -U postgres lab_management > lab_$(date +%Y%m%d).sql
docker exec postgres pg_dump -U postgres finance > finance_$(date +%Y%m%d).sql

# Restore
docker exec -i postgres psql -U postgres < backup_20260724.sql
```

### Backup Storage Files

```bash
# Backup semua file upload (foto jurnal + tugas)
# Asumsi storage di-mount atau di-copy dari container

# Salin dari volume atau mount point
tar -czf storage_backup_$(date +%Y%m%d).tar.gz /path/to/storage/
```

---

## Monitoring

### Laravel Horizon

Akses dashboard Horizon di: `https://lab.nuris.sch.id/horizon`

```bash
# Status queue
docker exec CONTAINER_ID php artisan horizon:status

# Pause queue (maintenance)
docker exec CONTAINER_ID php artisan horizon:pause

# Resume
docker exec CONTAINER_ID php artisan horizon:continue
```

### Health Check Commands

```bash
# Status semua service Swarm
docker service ls

# Log real-time
docker service logs lab_app -f
docker service logs lab_queue -f
docker service logs lab_reverb -f

# Cek koneksi database
docker exec CONTAINER_ID php artisan db:show

# Cek koneksi Redis
docker exec CONTAINER_ID php artisan tinker --execute="Redis::ping()"

# Cek queue
docker exec CONTAINER_ID php artisan queue:monitor redis
```

### Prometheus (opsional)

Sistem menggunakan `spatie/laravel-prometheus`. Metrics tersedia di `/metrics` (butuh konfigurasi auth di production).

---

## Troubleshooting

### Container app terus restart

```bash
# Lihat exit code
docker service ps lab_app --no-trunc

# Lihat log error
docker service logs lab_app --tail 100

# Masalah umum:
# 1. APP_KEY tidak di-set → php artisan key:generate
# 2. Database tidak bisa diakses → cek DB_HOST, password
# 3. Storage permission → chmod -R 775 storage bootstrap/cache
```

### WebSocket tidak terhubung

```bash
# 1. Cek reverb container running
docker service ls | grep reverb

# 2. Cek log reverb
docker service logs lab_reverb --tail 50

# 3. Cek VITE_REVERB_* sesuai domain production
# Variabel ini di-bake saat BUILD — jika salah, perlu rebuild image

# 4. Cek NPM config — pastikan WebSocket proxy dikonfigurasi
```

### Upload file gagal (413 Error)

```bash
# Pastikan client_max_body_size di Nginx sudah 25M
# Cek di:
# 1. docker/nginx/default.swarm.conf → client_max_body_size 25M
# 2. NPM Custom Nginx config → client_max_body_size 25M
# 3. php-prod.ini → upload_max_filesize = 20M, post_max_size = 20M

# Apply perubahan Nginx
docker service update --force lab_nginx
```

### Migrasi gagal

```bash
# Lihat status migrasi
docker exec CONTAINER_ID php artisan migrate:status

# Rollback migration terakhir (hati-hati di production!)
docker exec CONTAINER_ID php artisan migrate:rollback

# Force run migration
docker exec CONTAINER_ID php artisan migrate --force --no-interaction
```

### Cache stale setelah deploy

```bash
# Clear semua cache
docker exec CONTAINER_ID php artisan optimize:clear

# Rebuild cache
docker exec CONTAINER_ID php artisan optimize
```

### MikroTik tidak merespons

```bash
# Cek koneksi ke bot dari container app
docker exec CONTAINER_ID curl -v http://BOT_URL:5000/health

# Cek log MikroTik service
docker exec CONTAINER_ID php artisan tinker
>>> app(\App\Services\MikroTikService::class)->getLabStatus('lab7')
```
