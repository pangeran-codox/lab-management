# ================================
# Lab Management - Docker Commands
# ================================

# Default: dev
COMPOSE_FILE ?= docker-compose.dev.yml

# ─────────────────────────────
# DEV
# ─────────────────────────────
dev-up:
	docker compose -f docker-compose.dev.yml up -d

dev-down:
	docker compose -f docker-compose.dev.yml down

dev-build:
	docker compose -f docker-compose.dev.yml build --no-cache

dev-logs:
	docker compose -f docker-compose.dev.yml logs -f

dev-restart:
	docker compose -f docker-compose.dev.yml restart

# ─────────────────────────────
# ARTISAN shortcuts (dev)
# ─────────────────────────────
migrate:
	docker compose -f docker-compose.dev.yml exec app php artisan migrate

migrate-fresh:
	docker compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed

seed:
	docker compose -f docker-compose.dev.yml exec app php artisan db:seed

tinker:
	docker compose -f docker-compose.dev.yml exec app php artisan tinker

cache-clear:
	docker compose -f docker-compose.dev.yml exec app php artisan cache:clear
	docker compose -f docker-compose.dev.yml exec app php artisan config:clear
	docker compose -f docker-compose.dev.yml exec app php artisan route:clear
	docker compose -f docker-compose.dev.yml exec app php artisan view:clear

shell:
	docker compose -f docker-compose.dev.yml exec app bash

# ─────────────────────────────
# PROD
# ─────────────────────────────
prod-build:
	docker compose -f docker-compose.prod.yml build --no-cache

prod-up:
	docker compose -f docker-compose.prod.yml up -d

prod-down:
	docker compose -f docker-compose.prod.yml down

prod-logs:
	docker compose -f docker-compose.prod.yml logs -f

prod-migrate:
	docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

prod-shell:
	docker compose -f docker-compose.prod.yml exec app bash

# ─────────────────────────────
# UTILITY
# ─────────────────────────────
ps:
	docker compose -f docker-compose.dev.yml ps

prune:
	docker system prune -f

.PHONY: dev-up dev-down dev-build dev-logs dev-restart migrate migrate-fresh seed tinker cache-clear shell prod-build prod-up prod-down prod-logs prod-migrate prod-shell ps prune