#!/bin/sh
# docker/php/entrypoint.sh
# Dijalankan saat container start — SETELAH .env di-inject
# Sehingga artisan cache membaca nilai production yang benar

set -e

echo "[entrypoint] Clearing stale cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "[entrypoint] Building production cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "[entrypoint] Running migrations (if any)..."
php artisan migrate --force --no-interaction

echo "[entrypoint] Starting PHP-FPM..."
exec "$@"
