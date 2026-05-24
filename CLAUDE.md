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
| JS | Alpine.js (countdown + cookie banner + back-to-top), Livewire (Filament) | 3.x |
| DB local | SQLite | — |

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

# Code style
./vendor/bin/pint
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
- **Media**: Spatie MediaLibrary, polymorphic `media` tablosu. Her modelde `registerMediaCollections()` (cover, hero, gallery, documents). `registerMediaConversions()` ile `thumb` (400px), `web` (1280px) WebP ve `og` (1200×630) JPG üretiliyor (Sprint 21). View'lar `*_thumb_url` / `*_web_url` accessor'larıyla responsive `srcset` yayar; OG meta tag detay sayfalarında `og` dönüşümünü kullanır.
- **Form motoru**: `Form` → `FormField` (type select, options) → `FormSubmission` (data json + Spatie media attachments). Dynamic validation `FormField::validationRules()`'ten.
- **Sayfa SEO**: `layouts/app.blade.php` head'i @yield/@section tabanlı; detay sayfalar kendi `og_image`, `og_type`, `meta_description`'ını set eder.
- **Sitemap**: `/sitemap.xml` dinamik route, tüm aktif/yayında içerikten oluşturulur.
- **Accessibility**: Skip-to-content link, aria-current="page", global focus-visible ring, lang-aware aria-label'lar.

## Sprint durumu

- [x] **Sprint 0–5** — MVP'nin tamamı (kurulum → SEO/KVKK)
- [x] **Sprint 6** — Mobile menu, iletişim sayfası, form mail bildirimi, detay sayfa i18n cleanup
- [x] **Sprint 7** — Alumni Registry + SiteMetric
- [x] **Sprint 8** — Sponsor Lead Gen (Faz 2 tamam)
- [x] **Sprint 9** — Site-wide search
- [x] **Sprint 10** — 47-test feature suite + 2 production bug fix
- [x] **Sprint 11** — Pint format pass + accessibility + back-to-top
- [x] **Sprint 12** — Admin dashboard widget'ları
- [x] **Sprint 13** — Spatie Activitylog + ActivityResource + RecentActivityWidget
- [x] **Sprint 14** — Editör rolü permissions + RBAC testleri
- [x] **Sprint 15** — RSS feeds (haberler + etkinlikler)
- [x] **Sprint 16** — Public REST API v1 (5 endpoint, throttle 60/min)
- [x] **Sprint 17** — Bulk CSV import (Sponsor + Alumni, TR/EN translatable)
- [x] **Sprint 18** — Newsletter (double opt-in subscribe + Filament campaign sender)
- [x] **Sprint 19** — Admin bildirim merkezi (Filament database notifications, 3 trigger)
- [x] **Sprint 20** — Etkinlik kayıt sistemi (RSVP + kapasite + iCal + admin bildirim)
- [x] **Sprint 21** — Görsel pipeline: MediaLibrary conversions (thumb/web/og) + WebP + responsive img
- [x] **Sprint 22** — Public etkinlik takvimi (`/etkinlikler/takvim`, ay görünümü, locale-aware)

## SRS özeti

**Hedefler**: kurumsal prestij, Ar-Ge arşivi, operasyonel kolaylık.
**Hedef kitle**: sektör temsilcileri/sponsorlar, öğrenciler, mezunlar, admin/PR ekibi.
**Ana sayfa**: hero, metrik sayaç, sonsuz sponsor bandı, yaklaşan etkinlik geri sayım CTA.
**Ar-Ge sayfaları**: teknik inovasyon özeti, spec tablosu, takım şeması (LinkedIn'li), medya/rapor PDF galerisi.
**Form sistemi**: üyelik, komisyon, Ar-Ge takım alımları — admin'den aç/kapat, CV upload, Excel export.

## Notlar / Bilinen pürüzler

- Tailwind v4 değil v3 — Filament 3 uyumluluğu için
- Filament Select::make options hâlâ TR — admin TR locale'de açılır (LocaleSwitcher ile EN'e geçilebilir)
- KVKK metni şablon halinde — production öncesi avukat revizyonu önerilir
- `public/images/og-default.png` (1200×630) ve `public/docs/sponsorship-kit.pdf` admin tarafından sonradan yüklenecek

## Dosya/klasör haritası

```
app/
├─ Filament/Resources/         # 13 resource: Sponsor, TeamMember, TimelineEvent,
│                              #              Project, Form, Event, NewsPost,
│                              #              Alumni, SiteMetric, SponsorLead,
│                              #              NewsletterSubscriber, NewsletterCampaign,
│                              #              EventRegistration
├─ Filament/Widgets/           # Dashboard KPI + table widget'ları
├─ Filament/Imports/           # SponsorImporter, AlumniImporter (CSV bulk import)
├─ Http/Controllers/           # ApplicationForm, Contact, SponsorLead, Search
├─ Http/Controllers/Api/       # 5 read-only API controller (v1)
├─ Http/Middleware/            # SetLocaleFromSession, SetApiLocale
├─ Http/Resources/             # 5 JSON Resource (Event, News, Project, Alumni, Sponsor)
├─ Concerns/                   # LogsFillableActivity trait
├─ Models/                     # 18 model
├─ Policies/ProjectPolicy.php  # Captain izolasyonu
├─ Mail/                       # Contact, FormSubmission, SponsorLead, Newsletter (confirm+campaign),
│                              # EventRegistration (confirmation+confirmed-with-ics) Mailable
├─ Notifications/              # NewSponsorLead, NewFormSubmission, NewNewsletterSubscriber,
│                              # NewEventRegistration (Filament DB)
├─ Support/                    # AdminNotifier (sends to super_admin + editor), IcsGenerator
└─ Exports/                    # FormSubmissionsExport (Maatwebsite)

public/templates/              # sponsors-template.csv, alumni-template.csv (import örnekleri)

resources/views/
├─ layouts/app.blade.php       # OG/Twitter meta, nav+switcher, footer, cookie banner
├─ partials/                   # cookie-banner, back-to-top
├─ home.blade.php              # Hero, metrics, countdown, sponsor strip
├─ about.blade.php             # Mission/vision, team, timeline, alumni CTA
├─ projects/{index,show}.blade.php
├─ events/{index,show,_card,calendar,registration-feedback}.blade.php
├─ news/{index,show}.blade.php
├─ forms/{index,show}.blade.php
├─ alumni/index.blade.php
├─ sponsor/show.blade.php
├─ contact/show.blade.php
├─ legal/privacy.blade.php     # KVKK
├─ errors/{404,500,503}.blade.php
├─ search/index.blade.php
├─ emails/{contact,form-submission,sponsor-lead}.blade.php
└─ sitemap.blade.php           # XML

lang/{tr,en}/
├─ site.php   # nav, footer, common CTA
├─ pages.php  # page heros, sections, KVKK, errors, cookie banner, contact, sponsor, alumni, search
└─ models.php # categories, statuses, tiers, alumni sectors

database/
├─ migrations/                 # 17 migration
└─ seeders/                    # 8 seeder, idempotent

tests/Feature/                 # 116 test, in-memory SQLite, ~9s
├─ PublicSmokeTest.php
├─ FormSubmissionTest.php
├─ ContactFlowTest.php
├─ SponsorLeadFlowTest.php
├─ AdminAccessTest.php         # super_admin + team_captain isolation
├─ EditorAccessTest.php        # editor rol kısıtlamaları
├─ ActivityLogTest.php
├─ RssFeedTest.php
├─ ApiTest.php
├─ ImporterTest.php            # Sponsor + Alumni CSV import + TR/EN translatable
├─ NewsletterTest.php          # Double opt-in subscribe + campaign dispatch
├─ NotificationsTest.php       # Admin DB notification triggers
├─ EventRegistrationTest.php   # RSVP + capacity + iCal + admin notification
└─ MediaConversionTest.php     # MediaLibrary thumb/web/og + WebP dönüşümleri

tests/fixtures/                # sample.png (400×300, conversion testleri için)
```
