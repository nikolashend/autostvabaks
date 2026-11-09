# AUTOSTVABAKS — CURRENT STATE AUDIT V1 (Stage 1, 2026-11-09)

## 1. Repository / environment baseline (Phase A)

| Item | Value |
|---|---|
| Repository | `github.com/nikolashend/autostvabaks`, branch `main` |
| Pre-Stage-1 commit | `e675a2d` (worktree clean, no untracked files) |
| Local path | `C:\Users\nikol\Documents\projects\motionmotors2` (to be renamed `autostvabaks`) |
| Framework | Laravel 12.x (`laravel/framework` ^12), Filament 3.3.x, Livewire 3 |
| PHP | 8.2.12 local (XAMPP) · 8.3.36 hosting (`/opt/php/8.3/bin/php`, fcgid) |
| DB | MySQL 8.0.36 local · Percona MySQL 5.7.44 hosting (`u1579543_autostvabaks`) |
| Composer | 2.7.2; `composer audit` → no advisories |
| Node | 18.20.7 — only for local tooling (Lighthouse/puppeteer); no front-end build step |
| Deployment | reg.ru shared hosting, `~/www/autostvabaks.adme.ee` (docroot `public/`), `deploy.sh` (git pull → composer → migrate → caches) |
| Template source | `themeforest-…carento…zip` kept in project root, git-ignored, SHA-256 prefix `96f2bfd220d01333`, 68 706 904 bytes. Only `theme.css`, Bootstrap, Swiper, hero/banner images were taken into `public/assets`. Original zip untouched. |
| Brand assets | Approved SVG logo / colours **not present** in repo or on disk — `PENDING` |

## 2. Existing implementation discovered (Phase B)

- **Routing/i18n**: `routes/web.php` registers the same closure twice (prefixed `{locale}` ru|en named `l.*`, then unprefixed ET). `SetLocale` middleware. `Site::route()` picks the twin.
- **SEO**: canonical/hreflang/OG in `site/layout.blade.php`; `SitemapController` (sitemap + robots); JSON-LD partial. Before Stage 1: alternates built separately in two controllers; AutoDealer entity; `isset($noindex)` bug (false still rendered noindex).
- **Form**: vanilla JS two-step (`public/assets/js/autostvabaks.js`), `StoreLeadRequest`, `LeadController@store` (honeypot, private photos, mail with attachments, mail-failure safe), rate limiter `leads`. CSRF exempt by design.
- **Admin**: Filament resources Leads (read-only + delete), Testimonials, FAQ, Pages (3-language, slugs), Settings page, Users; widgets LeadStats/LatestLeads; Russian UI.
- **Models**: Lead, Testimonial, Faq, Page (JSON translations), Setting (cached KV), User (hashed password, FilamentUser).
- **Tests**: 14 feature tests before Stage 1 (now 23 / 246 assertions).
- **Libraries**: Bootstrap 5.3 CSS/JS, Swiper 6.5.6, Carento `theme.css` (main.css minus imports), Urbanist (Google Fonts) + self-hosted Manrope Cyrillic subset. No jQuery, no animation libs, no theme `main.js`.

## 3. Template debt (Carento)

| Piece | Class | Note |
|---|---|---|
| Bootstrap 5 CSS/JS | REQUIRED | Grid, modal, dropdown, collapse |
| `theme.css` (264 KB, ~97 % unused per Lighthouse) | USED → REMOVE LATER | Header/footer/button base; Stage 2: purge or replace with own tokens |
| Swiper | USED (conditional) | Reviews only; now loaded only when real reviews exist |
| Rental/booking/inventory/listing/search/checkout/agent/account pages | UNUSED / DEMO ONLY | Never copied; buyer-intent architecture excluded |
| Theme `main.js`, icon packs, demo images | REMOVED | Not in `public/assets` except hero/banner conversions |
| `public/assets/imgs/template/*`, `bg-faq*.png` | DEMO ONLY | Stage 2: delete after redesign |

## 4. Visual baseline

`docs/autostvabaks/baseline/pre-stage1/` — ET/RU/EN at 1440 and 390 (full page), Step 1 validation, Step 2 modal (1440 + 390), admin dashboard, leads list. `post-stage1/` — ET 1440/390 after Stage 1 changes.

## 5. Performance baseline (local `php artisan serve`, no gzip — absolute numbers are pessimistic; relative deltas are valid)

| Metric | Before (mobile / desktop) | After Stage 1 (mobile / desktop) |
|---|---|---|
| Lighthouse performance | 66 / 95 | 69 / 97 |
| Accessibility | 95 / 95 | **100 / 100** |
| Best practices · SEO | 100 · 100 | 100 · 100 |
| LCP | 6.3 s / 1.3 s | 5.2 s / 1.0 s |
| CLS | 0.034 / 0.041 | 0.034 / 0.041 |
| TBT | 0 ms | 0 ms |
| Requests | 12 / 32 | 10 / 10 |
| Transfer | 1.02 MB / 1.59 MB | 0.76 MB / 0.83 MB |
| Third-party requests | 2 / 21 (Google Maps iframe) | 2 / 2 (fonts only) |

LCP element: hero `<img>` (preloaded WebP). Remaining weight: `theme.css` 264 KB + `bootstrap.min.css` 232 KB uncompressed (≈90 KB gzipped on nginx). Field CWV: not available (no traffic). Lighthouse JSON kept out of the repo (scratch).

## 6. Third-party inventory

| Dependency | Purpose | Cost | Privacy | Decision |
|---|---|---|---|---|
| Google Fonts (Urbanist) | Headings/body Latin | 2 requests, ~29 KB | IP to Google on every view | KEEP now; Stage 2: self-host WOFF2 (OFL licence permits) after visual check |
| Manrope Cyrillic (self-hosted) | RU text | 1 request | none | KEEP |
| Google Maps embed | Location proof | 16–20 requests, ~500 KB | IP + cookies to Google | **Click-to-load facade** (done) + direct Maps link |
| Swiper 6 | Reviews carousel | 244 KB JS + 14 KB CSS | none | Conditional (done); Stage 2: consider CSS scroll-snap |
| Bootstrap 5 | Layout/modal | 232 KB CSS + 81 KB JS | none | KEEP; Stage 2: purge unused CSS |
| Carento theme.css | Base styles | 264 KB | none | REMOVE LATER (Stage 2) |
| Mailtrap SMTP (staging) | Mail sandbox | — | test only | Replace with production SMTP at go-live |

## 7. Accessibility smoke

Fixed in Stage 1: footer heading order (h6→h3), language toggle accessible name mismatch, Swiper bullet touch targets (24×24), modal focus restoration to the opening button. Verified: labels, skip link, `role="alert"` error containers, Escape closes modal/menu (Bootstrap), focus trap (Bootstrap modal), `aria-invalid`, keyboard FAQ. Lighthouse a11y 100.

## 8. Security review

| Area | Finding |
|---|---|
| CSRF | Admin/Livewire protected; lead endpoint intentionally exempt (anonymous, honeypot + rate limit + duplicate guard) |
| XSS | Blade escaping everywhere; FAQ answers `nl2br(e())`; page content from admin RichEditor rendered `{!! !!}` — admin-only authors (accepted; sanitiser is a Stage 2 hardening candidate); `head_scripts` setting intentionally raw (analytics tags) |
| SQLi | Eloquent/query builder only |
| Mass assignment | `$fillable` on all models |
| Uploads | `file` + `image` + `mimes:jpg,jpeg,png,webp` (content sniffing), size/count limits, generated names, private disk |
| Path traversal | photo route uses lead id + index into stored array, never user paths |
| Admin brute force | Filament login rate limit (5/min) |
| IDOR | photos require auth (`auth` middleware → `/admin/login`); Filament policies not needed (single admin role) |
| Secrets | `.env` git-ignored; `.env.example` has no secrets; staging `.env` 600 |
| Debug | `APP_DEBUG=false` on staging; `site:release-check` blocks debug on production |
| Headers | `X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options`, `Permissions-Policy`, `X-Robots-Tag` (admin/staging) — no CSP yet |
| Dependencies | `composer audit` clean; `npm audit` n/a (no lockfile / no runtime JS deps) |
| Mail flooding | rate limit + duplicate guard; attachments capped at 18 MB per mail |

## 9. Admin module audit

| Module | Verdict |
|---|---|
| Authentication | KEEP |
| Leads list/detail/photos | KEEP + HARDEN (attribution fields shown; no statuses) |
| Dashboard widgets | KEEP (lightweight counters) |
| Testimonials | KEEP + HARDEN (placeholder guard) |
| FAQ | KEEP (single source for visible FAQ + schema) |
| Pages | ADAPT (+ `is_indexable`; sitemap only for indexable) |
| Settings | KEEP (contacts, lead e-mail, head scripts) |
| Administrators | KEEP (no self-registration) |
| Lead statuses / CRM / Telegram | DEFER (out of scope) |
