#!/usr/bin/env bash
# ==============================================================================
# GEMDTEK — first-time server bootstrap (run ONCE per host)
# ==============================================================================
# Usage on the server (after `git clone`):
#   cd ~/gemdtek.com && bash deploy/first-time-setup.sh
#
# Bu script ilk deployment için her şeyi sıfırdan kurar:
#   - Composer prod dependencies
#   - .env'i .env.production.example'dan klonla (eğer yoksa)
#   - APP_KEY üret
#   - Storage symlink
#   - migrate:fresh --seed  (DİKKAT: var olan DB'yi siler! ilk kurulumda OK)
#   - shield:generate (Filament permissions)
#   - PWA ikonları üret
# ==============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$(dirname "$SCRIPT_DIR")"
cd "$APP_DIR"

echo "==> First-time bootstrap for: $APP_DIR"

# Confirm intent — this is destructive
read -r -p "Bu komut DB'yi SİLER ve sıfırdan seedler. Devam? (yes/no) " confirm
if [ "$confirm" != "yes" ]; then
    echo "İptal edildi."
    exit 1
fi

# ----- 1) Composer -----------------------------------------------------------
echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ----- 2) .env ---------------------------------------------------------------
if [ ! -f .env ]; then
    echo "==> Creating .env from .env.production.example..."
    cp .env.production.example .env
    echo "DİKKAT: .env'i şimdi düzenle (DB credentials, MAIL, ADMIN_PASSWORD)"
    echo "       Sonra bu scripti tekrar çalıştır."
    exit 0
fi

# ----- 3) APP_KEY ------------------------------------------------------------
if ! grep -q '^APP_KEY=base64:' .env; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# ----- 4) Storage symlink ----------------------------------------------------
if [ ! -L public/storage ]; then
    echo "==> Linking storage..."
    php artisan storage:link
fi

# ----- 5) DB schema + seed ---------------------------------------------------
echo "==> Running migrate:fresh --seed (will wipe DB)..."
php artisan migrate:fresh --seed --force --no-interaction

# ----- 6) Shield permissions -------------------------------------------------
echo "==> Generating Filament Shield permissions..."
php artisan shield:generate --all --panel=admin --option=permissions || true

# Re-seed roles to make sure super_admin has every permission Shield just made
php artisan db:seed --class=RolesAndAdminSeeder --force || true
php artisan db:seed --class=EditorRolePermissionsSeeder --force || true

# ----- 7) PWA icons (regenerate with current GD/font config) -----------------
echo "==> Regenerating PWA icons..."
php artisan app:generate-pwa-icons || true

# ----- 8) Optimise -----------------------------------------------------------
echo "==> Caching config/routes/views..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ----- 9) Permissions --------------------------------------------------------
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

echo ""
echo "✓ İlk kurulum tamamlandı."
echo ""
echo "  Sonraki adımlar:"
echo "   1. https://gemdtek.com/admin  →  giriş yap"
echo "   2. Şifreni değiştir"
echo "   3. Sponsor logoları, takım fotoları, etkinlik cover'larını yükle"
echo "   4. public/images/og-default.png yükle (1200x630)"
echo "   5. Sonraki deploy'lar için: bash deploy/deploy.sh"
