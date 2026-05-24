# GEMDTEK — Production Deployment

Hedef: **https://gemdtek.com** üzerinde, Natro **shared + SSH** paketinde
çalışan Laravel 11 + Filament 3 uygulaması.

Bu doküman sıfırdan canlıya çıkışı kapsar. Bir defalık kurulum + sonraki
deploy'lar ayrı bölümlerde.

---

## Tek bakışta gerekenler

| Gerekli | Nereden |
|---|---|
| Natro Linux shared hosting | Mevcut |
| SSH erişimi açık | Natro cPanel → SSH Access (gerekirse SSH key yükle) |
| MySQL DB + user + password | cPanel → MySQL Databases — hazır ✓ |
| PHP 8.2+ aktif | cPanel → MultiPHP Manager (gemdtek.com için 8.2 seç) |
| Composer (sunucuda) | Çoğu Natro paketi `composer` veya `composer2` olarak sağlar |
| Git | Hemen her shared'da kuruludur (`git --version`) |
| SSL sertifikası | cPanel → SSL/TLS Status → Let's Encrypt (ücretsiz, otomatik) |
| SMTP hesabı | `info@gemdtek.com` Natro mail kutusu **veya** [Brevo](https://brevo.com) (ücretsiz 300/gün) |

---

## 1. Sunucu tarafı ön hazırlık (cPanel)

### 1.1 Document Root'u Laravel'in `public/` klasörüne çevir

Bu **çok önemli** — Laravel'in app kodu web root'un DIŞINDA olmalı.

cPanel → **Domains** → `gemdtek.com` satırının yanındaki **Edit** veya
**Manage** → **Document Root** alanını şuna çevir:

```
/home/<cpanel-user>/gemdtek.com/public
```

> Natro paketinde "Document Root" alanı kilitliyse, **Subdomain** olarak
> `gemdtek.com`'u silip yeniden ekle ve doc root'u doğru yere ver. Veya
> destek bileti aç — 5 dakikalık değişiklik.

### 1.2 SSH key ekle (opsiyonel ama önerilir)

cPanel → **SSH Access** → **Manage SSH Keys** → **Generate a New Key**
(veya kendi local key'ini import et) → **Authorize**.

Local'inden bağlanmak için:
```bash
ssh <cpanel-user>@gemdtek.com -p 22   # bazı paketlerde port farklı olur
```

### 1.3 PHP version & extension kontrolü

cPanel → **MultiPHP Manager** → gemdtek.com için **PHP 8.2** seç.

Gereken extension'lar (cPanel → **Select PHP Version** → Extensions):

```
bcmath  ctype  curl  dom  fileinfo  gd  iconv  intl
mbstring  mysqli  openssl  pcre  pdo_mysql  tokenizer  xml  zip
```

GD'nin **WebP** ve **JPEG** desteği aktif olmalı (MediaLibrary conversions için).

### 1.4 Cron job hazırlığı

cPanel → **Cron Jobs** → her dakika çalışan Laravel scheduler:

```cron
* * * * * cd /home/<cpanel-user>/gemdtek.com && php artisan schedule:run >> /dev/null 2>&1
```

Queue worker (mail kuyruğu için) — daha seyrek bir cron yerine **screen** /
**tmux** ile sürekli çalıştırmak daha sağlam:

```cron
*/5 * * * * cd /home/<cpanel-user>/gemdtek.com && pgrep -f "queue:work" > /dev/null || nohup php artisan queue:work --tries=3 --timeout=60 --sleep=3 >> storage/logs/queue.log 2>&1 &
```

Bu satır 5 dakikada bir kontrol eder; queue:work düşmüşse yeniden başlatır.

---

## 2. Repo'yu sunucuya çek

SSH'la bağlandıktan sonra:

```bash
cd ~
git clone https://github.com/mecityazici/gemdtek.com.git gemdtek.com
cd gemdtek.com
```

> Repo public; eğer ileride private'a alırsan SSH deploy key yükle ya da
> GitHub PAT kullan.

---

## 3. İlk kurulum (sadece bir kere)

### 3.1 `.env` hazırla

```bash
cp .env.production.example .env
nano .env   # veya vi/vim
```

Aşağıdakileri **mutlaka** doldur:

```dotenv
APP_KEY=                # boş bırak, script üretecek
APP_URL=https://gemdtek.com

DB_DATABASE=gemdtek_main
DB_USERNAME=gemdtek_app
DB_PASSWORD=<cpanel-mysql-password>

MAIL_HOST=mail.gemdtek.com
MAIL_USERNAME=info@gemdtek.com
MAIL_PASSWORD=<cpanel-email-password>

ADMIN_EMAIL=admin@gemdtek.com
ADMIN_PASSWORD=<openssl rand -base64 24 ile üret>
```

### 3.2 Bootstrap script'i çalıştır

```bash
bash deploy/first-time-setup.sh
```

Script seni adım adım götürür:
- Composer paketleri (no-dev, optimized autoload)
- `APP_KEY` üretir
- `storage:link` (yüklenen medya için)
- `migrate:fresh --seed` (admin, editor, kaptan kullanıcıları + örnek içerik)
- Filament Shield permissions
- PWA ikonları
- Config/route/view cache

Bittikten sonra https://gemdtek.com erişilebilir olmalı.

### 3.3 SSL sertifikası

cPanel → **SSL/TLS Status** → gemdtek.com ve www.gemdtek.com'u seç →
**Run AutoSSL** (Let's Encrypt otomatik kurulur, 5 dk içinde aktif).

`public/.htaccess` zaten HTTP → HTTPS redirect ediyor — sertifika kurulunca
otomatik çalışır.

### 3.4 İlk admin girişi

1. https://gemdtek.com/admin
2. `admin@gemdtek.com` + `.env`'e yazdığın `ADMIN_PASSWORD`
3. Sağ üst avatar → Profil → **şifreyi değiştir**
4. Email/şifre dışında sana ait bilgi varsa güncelle

---

## 4. Sonraki deploy'lar (rutin)

Local'de değişiklik yap → commit → push → sunucuda:

```bash
cd ~/gemdtek.com
bash deploy/deploy.sh
```

Script şunları yapar (idempotent, ~15 sn):
1. `git pull` (origin/main)
2. `composer install --no-dev`
3. `php artisan down` (maintenance mode, kullanıcıya 503 sayfası)
4. `php artisan migrate --force`
5. Cache'leri tazele (config / route / view / event)
6. `php artisan up`
7. Queue worker'a restart sinyali

---

## 5. Content yükleme (ilk gün)

Admin paneli → her resource'a 1-2 görsel/içerik:

| Ne | Nerede | Beklenen boyut |
|---|---|---|
| Sponsor logoları | Kurumsal → Sponsorlar | 400×200 PNG/SVG (otomatik 160×80 + 320×160 thumb üretir) |
| Takım fotoğrafları | Kurumsal → Yönetim Kurulu | 800×800 JPG (160×160 + 400×400 thumb otomatik) |
| Etkinlik cover | İçerik → Etkinlikler | 1600×900 JPG (400×225 + 1280×720 + 1200×630 og otomatik) |
| Haber cover | İçerik → Haberler | Aynı |
| Proje hero/gallery | Ar-Ge → Projeler | 1600×900 hero + galeri |
| Site OG default | SFTP ile `public/images/og-default.png` | 1200×630 PNG |
| KVKK linki | Avukatla revize → KVKK Aydınlatma sayfası | — |
| Sponsorship kit | SFTP ile `public/docs/sponsorship-kit.pdf` | PDF |

---

## 6. SMTP seçimi

### Seçenek A — Natro mail (en kolay)

cPanel → **Email Accounts** → `info@gemdtek.com` oluştur. Sonra:

```dotenv
MAIL_HOST=mail.gemdtek.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=info@gemdtek.com
MAIL_PASSWORD=<email-account-password>
```

Limit: günlük ~500 mail. Newsletter kampanyasında yetebilir ama tutarlılık zayıf.

### Seçenek B — Brevo (önerilen, ücretsiz 300/gün)

1. brevo.com'da hesap aç → Senders → `info@gemdtek.com` doğrula (DKIM TXT
   kaydı cPanel DNS Zone Editor'dan eklenir)
2. SMTP & API → SMTP Keys → bir key oluştur

```dotenv
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=<brevo-account-email>
MAIL_PASSWORD=<brevo-smtp-key>
MAIL_FROM_ADDRESS=info@gemdtek.com
```

Delivery rate %99+, dashboard'dan açılma/tıklama metrikleri görürsün.

---

## 7. Yedekleme

cPanel → **Backup Wizard** → tam yedek (haftalık). Otomatik için:

```cron
0 3 * * 0 cd /home/<cpanel-user>/gemdtek.com && mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE | gzip > ~/backups/gemdtek-$(date +\%Y\%m\%d).sql.gz && find ~/backups -name "gemdtek-*.sql.gz" -mtime +30 -delete
```

DB credentials cron komutuna düz yazılırsa cPanel başkasıyla paylaşılmıyorsa OK.
Daha temizi: `~/.my.cnf` oluştur `[client] user=... password=...` ile, sonra
`mysqldump $DB`. Medya yedeği için `storage/app/public` klasörünü cron'a ekle.

---

## 8. İzleme

### Log'lar

```bash
tail -f storage/logs/laravel.log         # uygulama log'u
tail -f storage/logs/queue.log           # queue worker
tail -f ~/logs/gemdtek.com.error.log     # Apache error log (cPanel'de yer farklı olabilir)
```

### Admin paneli içi izleme

- Dashboard → **Son Aktiviteler** widget'ı (Spatie Activitylog)
- Site Ayarları → **Aktivite Logları** (full audit trail, filtreli)
- Header zil ikonu → admin bildirimleri (yeni sponsor lead, form, abone)

---

## 9. Production smoke check (deploy sonrası ~3 dk)

```bash
# Sunucuda
php artisan about              # PHP/Laravel/cache/env hep doğru mu
php artisan route:list          # Tüm route'lar yüklü mü
php artisan migrate:status      # Tüm migration'lar ran mı
```

Tarayıcıda:

- [ ] https://gemdtek.com → anasayfa açılıyor
- [ ] http://gemdtek.com → https'e redirect ediyor
- [ ] https://www.gemdtek.com → apex'e redirect ediyor
- [ ] https://gemdtek.com/etkinlikler → liste + kart
- [ ] https://gemdtek.com/etkinlikler/cfd-atolyesi → RSVP formu görünür
- [ ] https://gemdtek.com/sitemap.xml → XML
- [ ] https://gemdtek.com/manifest.json → JSON (PWA install prompt)
- [ ] https://gemdtek.com/sw.js → JavaScript (service worker)
- [ ] https://gemdtek.com/admin/login → login formu
- [ ] Admin'e giriş + dashboard widget'ları render oluyor
- [ ] Public formdan test gönderim → admin paneline bildirim düşüyor
- [ ] Etkinliğe RSVP → confirmation e-postası inbox'a düşüyor

---

## 10. Olası problemler & çözümleri

| Problem | Çözüm |
|---|---|
| **500 Internal Server Error** | `tail -f storage/logs/laravel.log` ve Apache error log'a bak. Genelde: yanlış PHP version, `.env` yok, `APP_KEY` boş, `storage/` yazılabilir değil. |
| **mixed content / asset 404** | `APP_URL=https://gemdtek.com` doğru mu? `php artisan config:clear && config:cache` |
| **CSRF token mismatch** | `SESSION_DRIVER=file`, `storage/framework/sessions` writable, `SESSION_DOMAIN=null` |
| **Yüklenen görseller görünmüyor** | `php artisan storage:link` çalıştırıldı mı? `public/storage` symlink var mı? |
| **Mail gönderilmiyor** | `php artisan tinker` → `Mail::raw('test', fn($m)=>$m->to('seninmail@x.com')->subject('test'));` ile dene. Hata log'u kontrol. |
| **Filament admin login sonsuz redirect** | `SESSION_SECURE_COOKIE=false` yap geçici, sonra HTTPS'i kontrol et. |
| **Pencere darken / dark mode bozuk** | `php artisan filament:assets` + `optimize:clear` |
| **`composer install` yavaş veya OOM** | `--prefer-dist`, `--no-dev`, swap aktif (Natro'da bazı paketlerde 512MB limit) |
| **Queue mail bekliyor, gönderilmiyor** | Cron çalışıyor mu? `ps aux \| grep queue:work` |

---

## 11. Rollback

Bir deploy işleri kırdıysa:

```bash
cd ~/gemdtek.com
git log --oneline -5                       # son commit'leri gör
git reset --hard <önceki-commit-sha>
composer install --no-dev --optimize-autoloader
php artisan migrate:rollback --step=1      # sadece son deploy migration eklediyse
php artisan optimize:clear
php artisan optimize
```

Veya basitçe: önceki commit hash'i ile `bash deploy/deploy.sh` yerine
elle pull et + cache temizle.

---

## 12. Güvenlik son kontrolü

- [ ] `.env` web'den erişilemiyor (test: `curl https://gemdtek.com/.env` → 403)
- [ ] `composer.json` web'den erişilemiyor (`.htaccess` block ediyor)
- [ ] `storage/`, `bootstrap/cache/` 775; içerik 664; `.env` 600
- [ ] `ADMIN_PASSWORD` panelden değiştirildi, .env'deki artık kullanılmıyor
- [ ] Test kullanıcıları (`editor@`, `kaptan@`) — production'da gerek yoksa sil veya şifrelerini değiştir
- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] HSTS header çalışıyor (browser dev tools → Network → Response Headers)
- [ ] Google Search Console'a gemdtek.com property eklendi, sitemap submit edildi
- [ ] Cloudflare proxy aktif değilse direkt origin IP açık (DDoS koruma istersen ücretsiz Cloudflare ekleyebilirsin)

---

## 13. Süreklilik

| Frekans | Ne |
|---|---|
| **Her gün** | Sponsor lead / form / RSVP bildirimleri — admin panel zilden takip |
| **Haftalık** | `storage/logs/laravel.log` taraması, anormal hata var mı |
| **Aylık** | Backup downloadla local'e bir kopya çek (cPanel veya rsync) |
| **3 ayda bir** | `composer update --with-dependencies` → local'de test → push → deploy |
| **6 ayda bir** | Laravel patch release güncel mi (security advisories) |
| **Yıllık** | SSL sertifikası otomatik yenileniyor mu, KVKK metni revize gerek mi |

---

İyi seyirler. Bir aksaklık olursa `tail -f storage/logs/laravel.log` ilk
bakacağın yer.
