# GEMDTEK Platform — Geliştirici Notları

Gemi İnşaatı ve Deniz Teknolojileri Kulübü kurumsal web platformu. SRS özeti için bkz. `docs/srs.md` (yazılacak); özet aşağıda.

## Stack

| Katman | Seçim | Versiyon |
|---|---|---|
| Framework | Laravel | 11.53 |
| Admin panel | Filament | 3.3 |
| RBAC | Filament Shield + Spatie Permission | 3.9 / 6.25 |
| Medya | Spatie MediaLibrary | 11.22 |
| i18n | Astrotomic Translatable | 11.17 |
| Export | Maatwebsite Excel | 3.1 |
| CSS | Tailwind 3 + forms + typography | 3.4 |
| DB local | SQLite | — |
| DB prod | MySQL (Natro) | — |

PHP 8.2.12 (XAMPP), Composer 2.9, Node 24, Vite 5.

## Yerel geliştirme

```powershell
# DB ve seed (ilk kurulumdan sonra gerekirse yeniden)
php artisan migrate:fresh --seed

# Backend + asset watcher (iki ayrı terminal)
php artisan serve --port=8000
npm run dev

# Production build (deploy öncesi)
npm run build
```

## Erişim

- Public site: http://localhost:8000/
- Admin panel: http://localhost:8000/admin
- Varsayılan admin: `admin@gemdtek.com` / `ChangeMe!2026` — **ilk girişte değiştir**
- Admin email/şifre `.env` üzerinden `ADMIN_EMAIL` / `ADMIN_PASSWORD` ile özelleştirilebilir; seed yeniden çalıştırılırsa kullanılır.

## Rol mimarisi (RBAC)

Spatie Permission rolleri seed ediliyor:
- `super_admin` — Shield bypass ile tüm yetkiler
- `editor` — Haber, etkinlik, blog içeriği
- `team_captain` — Sadece kendi atandığı Ar-Ge proje sayfası (Sprint 2'de policy yazılacak)

Filament panel erişimi `User::canAccessPanel()` üzerinden bu üç rolden birine sahip olma şartına bağlı.

## Brand palette

Tailwind config (`tailwind.config.js`) içinde tanımlı:
- `navy-800` `#0B2545` — birincil (deep navy)
- `petrol` `#13315C` — ikincil (gövde mavisi)
- `brass-500` `#B87333` — vurgu (gemi pirinci)
- `graphite` `#1F2937`, `cream` `#F4F4F2` — nötr

Fontlar Google Fonts üzerinden: **Inter** (gövde), **Space Grotesk** (başlık), **JetBrains Mono** (teknik tablolar).

## Mimari kararlar

- **Localized models**: `astrotomic/laravel-translatable` ile per-row TR/EN çevirileri ayrı bir `*_translations` tablosunda tutulur. JSON kolon değil.
- **Media**: `spatie/laravel-medialibrary` — `media` tablosu polymorphic; modellerde `HasMedia` interface'i ve collection tanımları (`addMediaCollection('gallery')`).
- **Form motoru**: `Form` → `FormField` → `FormSubmission` → `SubmissionAnswer` ilişkisi. Admin panelde Filament Builder field ile no-code form yönetimi (Sprint 3).
- **Shield politikaları**: `php artisan shield:generate --all --resource` ile resource oluşturulduktan sonra çalıştırılır. Otomatik policy + permission üretir.

## Deploy (Natro shared hosting, gemdtek.com)

1. Lokal: `composer install --no-dev --optimize-autoloader` + `npm run build`
2. SCP/SSH ile dosyaları aktar
3. cPanel'de Document Root'u `public/` olarak ayarla (veya `public/` içindekileri root'a, `index.php`'de `__DIR__.'/../..'` patikalarını ayarla)
4. `.env`'de:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `DB_CONNECTION=mysql` + Natro MySQL bilgileri
   - `MAIL_*` Natro SMTP bilgileri
5. `php artisan migrate --force && php artisan db:seed --force`
6. `php artisan storage:link`
7. `php artisan config:cache && php artisan route:cache && php artisan view:cache`

KVKK: aydınlatma metni + cookie banner Sprint 5'te eklenecek.

## Sprint durumu

- [x] **Sprint 0** — Kurulum, Filament+Shield, palet (bu commit)
- [ ] **Sprint 1** — Layout, ana sayfa, hakkımızda, navigation
- [ ] **Sprint 2** — Ar-Ge proje modülü, team_captain policy
- [ ] **Sprint 3** — Dinamik form motoru, CV upload, Excel export
- [ ] **Sprint 4** — Etkinlik/haber CMS, TR/EN tam i18n
- [ ] **Sprint 5** — SEO, performans, KVKK, canlıya çıkış

## SRS özeti

**Hedefler**: kurumsal prestij, Ar-Ge arşivi, operasyonel kolaylık.
**Hedef kitle**: sektör temsilcileri/sponsorlar, öğrenciler, mezunlar, admin/PR ekibi.
**Ana sayfa**: hero video, metrik sayaç, sonsuz sponsor bandı, geri sayım CTA.
**Ar-Ge sayfaları**: teknik inovasyon özeti, spec tablosu, takım şeması (LinkedIn'li), medya/rapor PDF galerisi. **Mühendislik vitrini** — sponsor değerlendirmesinin yapıldığı yer.
**Form sistemi**: üyelik, komisyon, Ar-Ge takım alımları — admin'den aç/kapat, CV upload, Excel/CSV export, opsiyonel Google Sheets sync.
**Faz 2 vizyon**: Mezunlar Ağı (sektör haritası), Sponsor PDF lead-gen.

## Notlar / Bilinen pürüzler

- `bezhansalleh/filament-shield` policy üretimi Sprint 2'de modeller yazıldıktan sonra çalıştırılacak.
- Tailwind v4 değil v3 seçildi — Filament 3 ile uyumluluk için.
- Original SRS dökümanında `gemtek.com` typo'ları var; doğru domain `gemdtek.com`.
