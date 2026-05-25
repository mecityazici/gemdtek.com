#!/usr/bin/env bash
# ==============================================================================
# GEMDTEK — Bash backup script (shared hosting friendly)
# ==============================================================================
# Natro shared hosting'te PHP'nin proc_open/exec fonksiyonları kapalı olduğu
# için Spatie laravel-backup çalışmaz. Bu script onun yerini alır:
#   - mysqldump ile DB dump → gzip
#   - storage/app/public (yüklenen medya) → tar.gz
#   - 30 günden eski yedekleri otomatik siler
#
# Kurulum (tek seferlik):
#   chmod +x deploy/backup.sh
#   mkdir -p ~/backups
#
# Manuel çalıştırma:
#   bash deploy/backup.sh
#
# cPanel Cron Jobs ile otomatik:
#   0 2 * * *  /home/gemdtekc/public_html/deploy/backup.sh >> /home/gemdtekc/backups/backup.log 2>&1
# ==============================================================================

set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKUP_DIR="${BACKUP_DIR:-$HOME/backups}"
RETENTION_DAYS="${RETENTION_DAYS:-30}"

mkdir -p "$BACKUP_DIR"
cd "$APP_DIR"

# .env'den credentials oku (boşlukları + tırnakları trim et)
env_value() {
    grep -E "^$1=" .env | head -1 | cut -d '=' -f2- | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}
DB_NAME=$(env_value DB_DATABASE)
DB_USER=$(env_value DB_USERNAME)
DB_PASS=$(env_value DB_PASSWORD)

if [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo "ERROR: .env'de DB_DATABASE veya DB_USERNAME bulunamadı."
    exit 1
fi

DATE=$(date +%Y%m%d-%H%M)
DB_FILE="$BACKUP_DIR/db-$DB_NAME-$DATE.sql.gz"
MEDIA_FILE="$BACKUP_DIR/media-$DATE.tar.gz"

# ---- DB dump ----
echo "[$(date +'%Y-%m-%d %H:%M:%S')] DB dump başlıyor: $DB_NAME"
mysqldump \
    --no-tablespaces \
    --single-transaction \
    --quick \
    --lock-tables=false \
    -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
    | gzip > "$DB_FILE"
DB_SIZE=$(du -h "$DB_FILE" | cut -f1)
echo "[$(date +'%Y-%m-%d %H:%M:%S')] DB dump tamam: $DB_FILE ($DB_SIZE)"

# ---- Media archive ----
if [ -d "$APP_DIR/storage/app/public" ]; then
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] Medya arşivleniyor..."
    tar -czf "$MEDIA_FILE" -C "$APP_DIR/storage/app" public 2>/dev/null
    MEDIA_SIZE=$(du -h "$MEDIA_FILE" | cut -f1)
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] Medya tamam: $MEDIA_FILE ($MEDIA_SIZE)"
else
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] storage/app/public yok, medya atlandı."
fi

# ---- Cleanup ----
DELETED=$(find "$BACKUP_DIR" -maxdepth 1 \( -name "db-*.sql.gz" -o -name "media-*.tar.gz" \) -mtime +"$RETENTION_DAYS" -print -delete | wc -l)
echo "[$(date +'%Y-%m-%d %H:%M:%S')] $DELETED eski dosya silindi (>$RETENTION_DAYS gün)."

# ---- Summary ----
echo ""
echo "Son yedekler:"
ls -lht "$BACKUP_DIR" | head -6
echo ""
echo "✓ Backup tamamlandı."
