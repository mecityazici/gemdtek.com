# GEMDTEK Platform — Geliştirici Notları

Gemi İnşaatı ve Deniz Teknolojileri Kulübü kurumsal web platformu.

## Stack

| Katman | Seçim | Versiyon |
|---|---|---|
| Framework | Laravel | 11.53 |
| Admin panel | Filament | 3.3 |
| RBAC | Filament Shield + Spatie Permission | 3.9 / 6.25 |
| Medya | Spatie MediaLibrary | 11.22 |
| i18n | Spatie Translatable + Filament plugin | 6.11 / 3.3 |
| Export | Maatwebsite Excel | 3.1 |
| CSS | Tailwind 3 + forms + typography | 3.4 |
| JS | Alpine.js (countdown + cookie banner), Livewire (Filament) | 3.x |
| DB local | SQLite | — |
| DB prod | MySQL (Natro) | — |

PHP 8.2.12 (XAMPP), Composer 2.9, Node 24, Vite 5.

## Yerel geliştirme

```powershell
# DB ve seed
php artisan migrate:fresh --seed

# Backend + asset watcher (iki ayrı terminal)
php artisan serve --port=8080      # 8000 Windows'ta bloke
npm run dev

# Production build
npm run build

# Testler (47 feature test, in-memory SQLite, ~2s)
php artisan test
# veya:
composer test
```

## Erişim

- Public site: http://localhost:8080/
- Admin panel: http://localhost:8080/admin
- Varsayılan admin: `admin@gemdtek.com` / `ChangeMe!2026` — **ilk girişte değiştir**
- Test takım kaptanı: `kaptan@gemdtek.com` / `Captain!2026` — sadece kendi atandığı projeyi görür
- Admin email/şifre `.env` üzerinden `ADMIN_EMAIL`/`ADMIN_PASSWORD` ile özelleştirilebilir; seed yeniden çalıştırılırsa kullanılır.

## RBAC

Spatie Permission rolleri:
- `super_admin` — Shield bypass ile tüm yetkiler
- `editor` — Haber, etkinlik, blog içeriği
- `team_captain` — Sadece kendi atandığı Ar-Ge projesini görür/günceller (ProjectPolicy + ProjectResource::getEloquentQuery filter)

Filament panel erişimi `User::canAccessPanel()` üzerinden bu üç rolden birine sahip olma şartına bağlı.

## Brand palette

Tailwind config (`tailwind.config.js`):
- `navy-800` `#0B2545` — birincil (deep navy)
- `petrol` `#13315C` — ikincil (gövde mavisi)
- `brass-500` `#B87333` — vurgu (gemi pirinci)
- `graphite` `#1F2937`, `cream` `#F4F4F2` — nötr

Fontlar Google Fonts: **Inter** (gövde), **Space Grotesk** (başlık), **JetBrains Mono** (teknik tablolar).

## Mimari kararlar

- **Çeviri stratejisi**: Spatie HasTranslations (JSON kolon) — Astrotomic'ten Sprint 4'te geçildi (Filament entegrasyonu için). Translatable alanlar `public array $translatable = [...]` ile işaretlenir.
- **Filament resource'larda** `Translatable` concern + `LocaleSwitcher` header action her Create/Edit/List page'inde — TR/EN düzenleme bağlamı toggle'lanır.
- **Locale switcher** (public): `SetLocaleFromSession` middleware web grubunda, `/lang/{locale}` route session'a yazar.
- **Media**: Spatie MediaLibrary, polymorphic `media` tablosu. Her modelde `registerMediaCollections()` (cover, hero, gallery, documents).
- **Form motoru**: `Form` → `FormField` (type select, options) → `FormSubmission` (data json + Spatie media attachments). Dynamic validation `FormField::validationRules()`'ten.
- **Sayfa SEO**: `layouts/app.blade.php` head'i @yield/@section tabanlı; detay sayfalar kendi `og_image`, `og_type`, `meta_description`'ını set eder.
- **Sitemap**: `/sitemap.xml` dinamik route, tüm aktif/yayında içerikten oluşturulur.

## Sprint durumu

- [x] **Sprint 0** — Kurulum, Filament+Shield, brand palette
- [x] **Sprint 1** — Layout, ana sayfa, hakkımızda, sponsor/team/timeline
- [x] **Sprint 2** — Ar-Ge proje portfolyo + team_captain RBAC izolasyonu
- [x] **Sprint 3** — Dinamik form motoru + Excel export
- [x] **Sprint 4** — Event/News CMS + i18n stack swap + locale switcher
- [x] **Sprint 4.5** — Mevcut modellerin i18n retrofit'i (full bilingual)
- [x] **Sprint 5** — SEO, sitemap, KVKK, error pages, performans, deploy kılavuzu

## SRS özeti

**Hedefler**: kurumsal prestij, Ar-Ge arşivi, operasyonel kolaylık.
**Hedef kitle**: sektör temsilcileri/sponsorlar, öğrenciler, mezunlar, admin/PR ekibi.
**Ana sayfa**: hero, metrik sayaç, sonsuz sponsor bandı, yaklaşan etkinlik geri sayım CTA.
**Ar-Ge sayfaları**: teknik inovasyon özeti, spec tablosu, takım şeması (LinkedIn'li), medya/rapor PDF galerisi.
**Form sistemi**: üyelik, komisyon, Ar-Ge takım alımları — admin'den aç/kapat, CV upload, Excel export.

## Faz 2 vizyon (planlı, henüz başlanmadı)

- **Mezunlar Ağı** (Alumni Registry): mezunların sektörel haritası, mentorluk eşleştirmesi
- **Sponsor Lead Gen**: PDF sponsorluk dosyası indirme + lead form

## Notlar / Bilinen pürüzler

- Tailwind v4 değil v3 — Filament 3 uyumluluğu için
- Original SRS dökümanında `gemtek.com` typo'ları var; doğru domain `gemdtek.com`
- Filament Select::make options hâlâ TR — admin TR locale'de açılır (LocaleSwitcher ile EN'e geçilebilir)
- Detay sayfalarındaki bazı bölüm başlıkları (Spesifikasyonlar, Takım yapısı) hâlâ TR — temizleme Faz 2'ye bırakıldı
- Closure-based route'lar `route:cache` ile uyumsuz — DEPLOY.md'de not düşülmüş
- `public/images/og-default.png` (1200×630) deploy öncesi eklenmeli
- KVKK metni şablon halinde — canlıya çıkmadan avukat revizyonu önerilir

## Deploy

Detaylı adımlar için `DEPLOY.md`. Özet:
1. SSH'la repo clone et
2. `.env.production.example` → `.env` doldur + `php artisan key:generate`
3. `composer install --no-dev`
4. Yerelde `npm run build`, `public/build`'i SCP'le
5. `php artisan migrate --force && db:seed --force`
6. `php artisan storage:link`
7. `php artisan config:cache view:cache event:cache`  (route:cache ATLA — closure route'lar uyumsuz)
8. cPanel Document Root → `public/`
9. AutoSSL + smoke test

## Dosya/klasör haritası

```
app/
├─ Filament/Resources/         # 7 resource: Sponsor, TeamMember, TimelineEvent,
│                              #             Project, Form, Event, NewsPost
├─ Http/Controllers/           # ApplicationFormController
├─ Http/Middleware/            # SetLocaleFromSession
├─ Models/                     # 11 model (+ User, ProjectSpec, ProjectMember, FormField, FormSubmission)
├─ Policies/ProjectPolicy.php  # Captain izolasyonu
└─ Exports/                    # FormSubmissionsExport (Maatwebsite)

resources/views/
├─ layouts/app.blade.php       # OG/Twitter meta, nav+switcher, footer, cookie banner
├─ partials/cookie-banner.blade.php
├─ home.blade.php              # Hero, metrics, countdown, sponsor strip
├─ about.blade.php             # Mission/vision, team, timeline
├─ projects/{index,show}.blade.php
├─ events/{index,show,_card}.blade.php
├─ news/{index,show}.blade.php
├─ forms/{index,show}.blade.php
├─ legal/privacy.blade.php     # KVKK
├─ errors/{404,500,503}.blade.php
└─ sitemap.blade.php           # XML

lang/{tr,en}/
├─ site.php   # nav, footer, common CTA
├─ pages.php  # page heros, sections, KVKK, errors, cookie banner
└─ models.php # categories, statuses, tiers

database/
├─ migrations/                 # 14 migration (forms timestamp'ları manuel ayarlandı)
└─ seeders/                    # 6 seeder, idempotent (firstOrCreate)
```
