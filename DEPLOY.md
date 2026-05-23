# Natro Deploy Kılavuzu — gemdtek.com

Production deploy adım adım. Toplam ~30-40 dakika sürer (ilk deploy).

## Ön gereksinim kontrolü

cPanel > Seçenekler > **PHP Sürümü**: 8.2 veya 8.3 seçili olmalı.
cPanel > **SSH Erişimi**: SSH key'in ekli (yoksa cPanel'den ekle).
cPanel > **MySQL Veritabanları**: Yeni DB + user oluştur, kullanıcıya tüm yetkileri ver.

Bu yetenekler Sprint 0'da doğrulandı (PHP 8.2.12, Composer 2.9, SSH+Composer mevcut).

## 1. Server'a SSH

```bash
ssh kullanici@gemdtek.com -p 22   # cPanel SSH port'una göre değişir
cd ~/public_html
```

## 2. Repoyu klonla

```bash
git clone https://github.com/mecityazici/gemdtek.com.git .
```

## 3. .env hazırla

```bash
cp .env.production.example .env
nano .env
# DB_*, MAIL_*, ADMIN_PASSWORD alanlarını doldur
```

Ardından `APP_KEY` üret:

```bash
php artisan key:generate
```

## 4. Composer (production bağımlılıkları)

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

## 5. Frontend asset'leri

Build adımını **yerelde** yap, `public/build/` klasörünü server'a SCP'le:

```bash
# Yerel makine
npm run build

# Yerel'den server'a
scp -r public/build kullanici@gemdtek.com:~/public_html/public/
```

> Alternatif: Node Natro'da varsa server'da `npm ci && npm run build`. Çoğu Natro paketi Node sunmaz.

## 6. Database migration + seed

```bash
php artisan migrate --force
php artisan db:seed --force      # İlk kurulum — bir kez çalıştır
```

İlk admin oluşturulduktan sonra `/admin/login`'den giriş yap ve şifreyi değiştir.

## 7. Storage symlink

```bash
php artisan storage:link
```

## 8. Önbellek yarat (performans)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> Not: `route:cache` closure-based route'larla çalışmaz. Tüm route'larımız closure (basit route dosyamız) — bu komutu **atla** ya da route'ları controller'a taşı.

Doğrusu:

```bash
php artisan config:cache
php artisan view:cache
php artisan event:cache
# route:cache atla (closure route'lar uyumsuz)
```

## 9. cPanel — Document Root

cPanel > **Domains** > gemdtek.com > Document Root'u `~/public_html/public/` olarak ayarla.

Eğer cPanel bu seçeneği vermiyorsa, alternatif:

```bash
cd ~/public_html
mv public/* ./
mv public/.htaccess ./
# index.php içinde require yolunu güncelle:
# require __DIR__.'/../bootstrap/app.php'  →  require __DIR__.'/bootstrap/app.php'
```

## 10. SSL / HTTPS

cPanel > **SSL/TLS Status** > **Run AutoSSL** — Let's Encrypt otomatik kurulur.
.env'de `APP_URL=https://gemdtek.com` ve `SESSION_SECURE_COOKIE=true` olduğundan emin ol.

## 11. Smoke test

Tarayıcıdan kontrol et:

- https://gemdtek.com — Ana sayfa açılıyor, sponsor strip dönüyor mu?
- https://gemdtek.com/admin/login — Filament açılıyor, login çalışıyor mu?
- https://gemdtek.com/sitemap.xml — XML dönüyor mu?
- https://gemdtek.com/lang/en — EN versiyon açılıyor mu?

## 12. Sonraki deploy'lar (kod güncellemesi)

```bash
ssh kullanici@gemdtek.com
cd ~/public_html
git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
# yerelden npm run build + scp public/build/
php artisan config:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart   # queue worker'lar varsa
```

## Bilinen Natro tuhaflıkları

- **Composer yolu**: bazen `/usr/local/bin/composer` yerine `~/composer.phar`. Path'i doğrula.
- **PHP CLI versiyonu**: cPanel default'u eski olabilir. SSH'ta `php -v` ile kontrol et; eski ise `/opt/cpanel/ea-php82/root/usr/bin/php` kullan.
- **Cron**: cPanel > Cron Jobs altından Laravel scheduler ekle:
  ```
  * * * * * cd /home/kullanici/public_html && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
  ```

## Canlıya çıkış öncesi son kontrol listesi

- [ ] `.env` APP_ENV=production, APP_DEBUG=false
- [ ] `APP_KEY` üretildi (boş değil)
- [ ] DB credentials .env'de, MySQL'de DB+user oluşturuldu
- [ ] `php artisan migrate --force` başarılı çıktı
- [ ] İlk admin password sıfırlandı (default `ChangeMe!2026` değil)
- [ ] SSL aktif, https zorunlu
- [ ] `public/images/og-default.png` (1200×630, marka kimliğine uygun) yüklendi
- [ ] cPanel Cron tanımlı (zamanlı görevler için)
- [ ] KVKK aydınlatma metni (`/kvkk`) bir avukatla revize edildi
- [ ] Google Search Console'a `gemdtek.com` eklendi, `sitemap.xml` submit edildi
- [ ] Yedek planı: cPanel Backup Wizard ile haftalık otomatik backup açıldı
