#!/usr/bin/env bash
# ==============================================================================
# GEMDTEK — production deploy script (Natro shared + SSH)
# ==============================================================================
# Usage on the server:
#   cd ~/gemdtek.com && bash deploy/deploy.sh
#
# What it does (idempotent — safe to re-run):
#   1. Pulls the latest main from git
#   2. Installs production composer dependencies (no dev, optimised autoload)
#   3. Runs pending migrations
#   4. Clears stale caches, rebuilds config/route/view caches
#   5. Symlinks storage/app/public → public/storage (for uploaded media)
#   6. Restarts the queue worker (if running)
# ==============================================================================

set -euo pipefail

# Resolve project root (this script lives in deploy/)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$(dirname "$SCRIPT_DIR")"
cd "$APP_DIR"

echo "==> Deploying GEMDTEK from: $APP_DIR"
echo "==> Git branch: $(git rev-parse --abbrev-ref HEAD)"

# ----- 1) Source -------------------------------------------------------------
echo "==> Pulling latest from origin/main..."
git fetch --all --prune
git reset --hard origin/main
git submodule update --init --recursive || true

# ----- 2) Composer -----------------------------------------------------------
echo "==> Installing PHP dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ----- 3) Maintenance window -------------------------------------------------
# Keep this tight — Filament panel users will see the maintenance page.
echo "==> Entering maintenance mode..."
php artisan down --render="errors::503" --retry=15 || true

cleanup() {
    echo "==> Bringing app back up..."
    php artisan up || true
}
trap cleanup EXIT

# ----- 4) Migrations ---------------------------------------------------------
echo "==> Running migrations..."
php artisan migrate --force --no-interaction

# ----- 5) Storage symlink ----------------------------------------------------
if [ ! -L public/storage ]; then
    echo "==> Linking storage..."
    php artisan storage:link
fi

# ----- 6) Optimise -----------------------------------------------------------
echo "==> Clearing & rebuilding caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Filament resource discovery is cached too
php artisan filament:cache-components || true
php artisan icons:cache || true

# ----- 7) Permissions --------------------------------------------------------
# Natro shared genelde user-owned, ama yine de defansif:
echo "==> Ensuring writable storage + bootstrap/cache..."
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

# ----- 8) Queue worker bounce ------------------------------------------------
# Eğer queue:work supervisor altında çalışıyorsa restart sinyali bırakır.
echo "==> Signalling queue workers to restart..."
php artisan queue:restart || true

echo ""
echo "✓ Deploy finished at $(date -u +'%Y-%m-%dT%H:%M:%SZ')"
echo "  Tail logs:    tail -f storage/logs/laravel.log"
echo "  Run queue:    php artisan queue:work --tries=3 --timeout=60"
