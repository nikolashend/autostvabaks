# AUTOSTVABAKS — X999 MASTER BASELINE V1

**Status: FROZEN (Stage 1, 2026-11-09; §2, §11, §13, §15 amended by Stage 2 on 2026-11-10 per ADR-17…22; §4, §5, §12 amended and §17 added by Stage 3 on 2026-11-11 per ADR-23…31; §1 amended and §18 added by Stage 4 on 2026-11-12 per ADR-32…35).** Every later task (Stage 3–5) starts by reading this file. It is the project constitution: change it only by a deliberate, documented decision (append to `AUTOSTVABAKS_ARCHITECTURE_DECISIONS_V1.md`).

## 1. Identity

| Fact | Value | Status |
|---|---|---|
| Public brand | **Autostvabaks** | CONFIRMED |
| Legal operator / data controller | **Motion Motors OÜ** | CONFIRMED |
| Address | Meistri 20, Tallinn 13517, Estonia | CONFIRMED |
| Registry code | 17341567 | CONFIRMED (Stage 4) |
| VAT number | EE102908916 | CONFIRMED (Stage 4) |
| Service | Car buyback — *autode kokkuost / auto väljaost* | CONFIRMED |
| Service area | All Estonia | CONFIRMED |
| Vehicles accepted | Any make/age/condition, incl. damaged/faulty, incl. under leasing/credit | CONFIRMED (client brief) |
| Payment | Bank transfer or cash | CONFIRMED |
| Public e-mail | info@autostvabaks.ee | CONFIRMED |
| Lead e-mail / owner | alan@autostvabaks.ee / Alan | CONFIRMED |
| Phone / WhatsApp / Telegram | — | **PENDING** — never invent; UI, schema and copy stay phone-free until set in admin → Settings |
| Production domain | https://autostvabaks.ee | CONFIRMED (not yet live) |
| Staging | https://autostvabaks.adme.ee (Basic Auth + noindex) | CONFIRMED |
| Languages | ET (default), RU, EN | CONFIRMED |

Motion Motors branding must not dominate marketing UI; it appears in Privacy/Terms, in the Organization node of the JSON-LD and wherever the law requires.

## 2. Stack (do not rebuild)

Laravel 12 · PHP 8.2 (local) / 8.3 (hosting) · MySQL 8 (local) / 5.7 (hosting) · Filament 3 admin at `/admin` · public frontend = own design system `public/assets/css/autostvabaks.css` + `autostvabaks.js` on a purged Bootstrap 5 subset (`scripts/purge-bootstrap.cjs`; grid/modal/collapse/dropdown only) · Swiper only for > 3 real reviews · Carento `theme.css` and template assets removed (Stage 2, ADR-18). No Vite build step is required for the public site. Tests: PHPUnit (SQLite in-memory), `php artisan test`.

## 3. Route & URL architecture (FROZEN)

```
ET  = /            RU = /ru            EN = /en
x-default = /      pages: /{slug_et}  /ru/{slug_ru}  /en/{slug_en}
POST paring  (+ /{locale}/paring)     sitemap.xml   robots.txt
admin/*  livewire/*  (noindex, auth)
```

- No `/et/` prefix migration. No automatic language redirect. Manual switcher only.
- One canonical origin = `APP_URL`. `ForceHttps` 301-redirects http:// and any other host (www., IP, old alias) to it when `APP_URL` is https. `URL::forceRootUrl(APP_URL)` makes canonical/hreflang/OG/schema independent of the Host header.
- Every public page: self-canonical; hreflang et/ru/en + x-default (= ET), generated only by `Site::alternates()` / `Site::routeAlternates()` / `Page::alternates()`. No hand-written hreflang arrays in templates.
- Admin-created pages have `is_active` (published) **and** `is_indexable`; only both → sitemap, otherwise `noindex`.

## 4. SEO environment rules (P0)

| Env | `SITE_NOINDEX` | `STAGING_AUTH` | Result |
|---|---|---|---|
| local | false | empty | plain |
| staging (autostvabaks.adme.ee) | **true** | user:pass | `<meta robots noindex,nofollow>` + `X-Robots-Tag` + `robots.txt Disallow: /` + Basic Auth |
| production (autostvabaks.ee) | **false** | **empty** | indexable, `robots.txt` allows all except `/admin`, sitemap advertised |

Guards: `php artisan site:release-check` (fails on the production host when noindex/debug/placeholders/drafts/staging host/prohibited claims/snippet restrictions/broken sitemap entries are present, or when `HERO_ASSET_STATUS` / `LEGAL_CONTENT_STATUS` are not `approved`, or when og:image/schema point at the temporary hero; `--strict` forces failure mode anywhere) and `tests/Feature/SeoFoundationTest.php` + `Stage3HardeningTest.php` (production simulation with `APP_URL=https://autostvabaks.ee`). `deploy.sh` runs the check. Production `robots.txt` allows `OAI-SearchBot` explicitly (ADR-25). Public pages carry a nonce-based CSP (`CSP_MODE`, ADR-24).

## 5. Lead form (FROZEN)

- Visible immediately in the hero. Step 1: reg number*, phone*, e-mail*, desired price, terms consent → **Alusta päringut** (nothing sent). Step 2 (modal): communication language (default = page locale), mileage, info, photos (≤10, ≤8 MB each, JPG/PNG/WEBP, downscaled client-side) → **Saada päring**.
- Server validation is authoritative (`StoreLeadRequest`); client validation is UX. Honeypot `website`; rate limit 8/10 min/IP; duplicate guard (same reg + e-mail within 2 min = one lead); CSRF-exempt endpoint by design (anonymous, stateless).
- DB is the lead record. Flow: save → store photos privately (`storage/app/private/leads/{id}`) → e-mail with attachments up to `LEADS_MAIL_ATTACHMENTS_MAX_MB` (10 MB, ADR-27) + authenticated admin deep-link; photos beyond the budget are announced as "available in the admin panel"; mail failure is recorded on the lead, never loses it.
- Attribution fields `utm_source/medium/campaign/content/term`, `gclid` — optional, never required.
- External registration/vehicle API: **OUT OF SCOPE V1**; the form must stay fully functional without it.

## 6. Admin boundaries

KEEP: auth, leads list/detail/photos, testimonials, FAQ, pages, settings, administrators, dashboard widgets. NOT in V1: CRM, statuses/pipeline, Telegram, reminders, automated follow-up, AI scoring, marketing automation.

Security invariants: hashed passwords, Filament login throttling, no public registration, `/admin` + `livewire` noindex, lead photos only via authenticated `admin/lead-photo/{lead}/{index}`, uploads validated by content (finfo), filenames generated server-side, no SVG/HEIC/executables.

## 7. Central facts & claims

- Static facts → `config/autostvabaks.php` (`facts.*`, `brand.*`); mutable contacts → admin Settings (`Site::setting()`); channel status derived: empty = PENDING = not rendered.
- Claim policy: **no proof → no claim.** Forbidden: #1, best/highest price, guaranteed purchase, fastest, guaranteed reply time, invented counts/years. Register: `AUTOSTVABAKS_FACT_CLAIM_REGISTER_V1.md`.
- Contact copy is conditional (`site.contact.lead` vs `lead_with_phone`): "call" is only promised when a phone exists.

## 8. Structured data policy

One `@graph`: `Organization` (Motion Motors OÜ, address, e-mail, phone only when set) → `brand` `Brand` (Autostvabaks) ; `WebSite`; `WebPage`; `Service` (car buyback, provider = Organization, areaServed Estonia, availableLanguage ET/RU/EN); `FAQPage` from the same FAQ records as the visible accordion. No `AutoDealer`/`LocalBusiness` (not a dealership page, no opening hours), no ratings/reviews schema, nothing invented. Built in `App\Support\Schema`.

## 9. Search-intent ownership & page gate

ET `/` owns the broad transactional cluster; `/ru`, `/en` own their language equivalents. New commercial URL only when QUERY_DEMAND + DISTINCT_INTENT + BUSINESS_VALUE + ORIGINAL_USEFUL_CONTENT + LOW_CANNIBALISATION_RISK all hold (`AUTOSTVABAKS_SEARCH_INTENT_OWNERSHIP_V1.md`). No doorway/location/model/AI-article pages.

## 10. Reviews & placeholders

Reviews section renders only with ≥1 real published review; Swiper CSS/JS load only then. Seeded samples are inactive and matched by `placeholder_markers`; `Testimonial::published()` hides them outside `local`. No fake reviews, no Review/AggregateRating schema.

## 11. Performance targets

LCP ≤ 2.5 s · INP < 200 ms · CLS < 0.1 (stretch ≤ 0.05). Baseline numbers: `AUTOSTVABAKS_CURRENT_STATE_AUDIT_V1.md`; Stage 2 numbers: `AUTOSTVABAKS_STAGE2_REPORT.md` §10. Premium appearance beats an arbitrary PSI 100. Map is click-to-load; fonts are self-hosted (ADR-19) — zero third-party requests before user interaction.

## 12. Analytics contract (no PII)

Events pushed to `window.dataLayer` via `window.asvTrack(name, params)`:
`buyback_form_start`, `buyback_form_step1_complete`, `buyback_form_step2_view`, `buyback_photo_add {photo_count}`, `buyback_form_submit {photo_count, has_price}`, `buyback_form_success`, `buyback_form_error {step, status?, fields}`, `buyback_cta_click {cta_location}`, `buyback_phone_click`, `buyback_whatsapp_click`, `buyback_telegram_click`, `buyback_faq_open {faq_index}`, `buyback_map_load`.
Never send: plate, phone, e-mail, free text, file names, images. Vendor (Stage 3, ADR-28): GTM container from `ANALYTICS_GTM_ID`, injected on the production host only unless `ANALYTICS_ENABLED=true`; `window.dataLayer` is initialised before the container; extra snippets via admin → Settings → head scripts (nonced). Consent approach for GA4 is a client decision before enabling tags.

## 13. Brand / design tokens

**Applied (Stage 2, ADR-17).** Approved identity from `resources/brand/source/svg` (Oleg, 2026-10-27): magenta `#E6007E`, graphite `#141414`, white. Tokens: `:root --asv-brand-primary / --asv-brand-dark / --asv-surface* / --asv-text* / --asv-border* / --asv-success / --asv-warning / --asv-danger / --asv-focus` in `public/assets/css/autostvabaks.css` + `config('autostvabaks.brand')` (admin colour, theme-color, logo paths). Text-sized magenta: `--asv-brand-text` (#C9006E, light surfaces) and `--asv-brand-primary-on-dark` (#FF4FAE, graphite) — contrast first. Logo: `<x-asv-logo variant="dark|light">` → `public/assets/imgs/brand/logo.svg | logo-white.svg`; favicon = split "a" glyph. Magenta is an accent for brand/conversion moments, never a surface colour (no "pink site"). Lime no longer exists in the public frontend (`BrandFrontendTest`).

## 14. Out of scope (all stages until re-decided)

Telegram bot, reminders, CRM, registry API, Google Ads setup, city/model SEO pages, inventory/marketplace, AI chatbot/scoring, blog farm, GBP, llms.txt/facts.json, strict CSP without audit.

## 15. Stage-2 protected strengths (do not regress)

Hero form above the fold on all viewports · 2-step flow with retained data · ET/RU/EN parity · per-field localized errors · private photos · mail-failure resilience · conditional contacts · reciprocal hreflang · production simulation test · release gate · accessibility 100 (Lighthouse) · zero third-party requests before user action (fonts self-hosted since Stage 2).

## 16. Stage-2 protected strengths (added 2026-11-10; do not regress in Stage 3+)

Approved logo component and tokens · hero composition copy → form → fact strip (desktop) / H1 → form → facts → visual (mobile) · valuation transparency section (`#hindamine`) · direct-answer cards (`#vastused`: leasing, damaged, payment) · success state with "what happens next" · mobile sticky CTA (`[data-sticky-cta]`) · reviews grid for 1–3, Swiper only > 3 · no `theme.css`, Bootstrap subset regenerated by `scripts/purge-bootstrap.cjs` · Lighthouse (local, no gzip) mobile ≥ 90 / desktop 100 / a11y 100 · `BrandFrontendTest` green.

## 17. Stage-3 protected strengths (added 2026-11-11; do not regress in Stage 4+)

Final ET/RU/EN copy in `lang/*/site.php` written against the fact register (no prohibited claims; scanner in test + release-check) · category-first titles, native H1s (ADR-31) · 9 keyed FAQ records = visible accordion = FAQPage schema (`FaqSeeder` content release, ADR-23) · valuation section with expectation message (F13/F14) · leasing / damaged / payment direct answers (F6/F5/F15) · legal operator line in the footer (F2) · brand OG images per locale, release gates `HERO_ASSET_STATUS` / `LEGAL_CONTENT_STATUS` (ADR-26) · production robots with `OAI-SearchBot` (ADR-25) · nonce CSP report-only on public pages (ADR-24) · HTML sanitiser for CMS pages (ADR-30) · lead-mail attachment budget + admin deep-link (ADR-27) · GTM switch (ADR-28) · minified twins + host caching (ADR-29) · 38 tests / 527 assertions green.

## 18. Stage-4 production release candidate (added 2026-11-12; do not regress)

Licensed real-photo hero `hero-coast.*` (AVIF/WebP/JPEG 1920×1080 + 16:9 mobile card; template files deleted; gate verifies files and references, ADR-32) · operator facts F16/F17 in footer, schema and legal drafts; seeder-owned drafts with one-step approval (ADR-33) · release-check extended: hero files on disk, legal pages 200/operator/linked, placeholder scan on page content · production activation procedure `AUTOSTVABAKS_STAGE4_GO_LIVE.md` (pre-flight, `.env`, deploy order, DNS before/after, verification, rollback, GSC) · launch states: CSP report-only, analytics disabled pending approval (ADR-35), mail must go through Google Workspace SMTP (ADR-34) · 42 tests / 626 assertions green.
