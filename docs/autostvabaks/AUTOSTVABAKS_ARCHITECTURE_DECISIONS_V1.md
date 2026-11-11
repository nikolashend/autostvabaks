# AUTOSTVABAKS — ARCHITECTURE DECISIONS V1

Format: **ADR-n — decision** · context · alternatives · consequence. Append new ADRs; never rewrite accepted ones.

**ADR-1 — Keep the existing Laravel 12 + Filament 3 prototype; no greenfield rebuild.**
Working ET/RU/EN landing, form, admin, tests existed. Alternatives: Botble/static PHP (older plan). Rebuilding would cost a full cycle with no KPI gain. Consequence: PRESERVE → AUDIT → HARDEN model.

**ADR-2 — Routes frozen as `/`, `/ru`, `/en`; x-default = `/`.**
Simple, already live on staging, Google Ads-friendly. Alternative `/et/` prefix: needs redirects + canonical migration for nothing. Consequence: no redirect debt.

**ADR-3 — One canonical origin from `APP_URL`, enforced in code.**
`URL::forceRootUrl` + `ForceHttps` host/scheme redirect. Alternative: trust Host header → www/IP/staging aliases could leak into canonical/hreflang. Consequence: tests must set `URL::forceRootUrl` when simulating production.

**ADR-4 — Staging protection = `SITE_NOINDEX` (meta + X-Robots-Tag + robots Disallow) + `STAGING_AUTH` Basic Auth; production fail-safe = `site:release-check` + production-simulation test.**
Robots.txt alone is advisory. Consequence: production `.env` must have `SITE_NOINDEX=false`, `STAGING_AUTH=` empty; `deploy.sh` runs the check.

**ADR-5 — Single hreflang generator (`Site::alternates`).**
Two controllers built arrays independently. Consequence: pages expose `Page::alternates()`; sitemap uses the same output.

**ADR-6 — Entity model: Organization = Motion Motors OÜ, Brand = Autostvabaks, Service = car buyback. No AutoDealer/LocalBusiness.**
AutoDealer implied a dealership with inventory/opening hours and named the brand as the legal entity. Consequence: schema is truthful and stays valid when phone is added (conditional field).

**ADR-7 — Facts centralised: static in `config/autostvabaks.php` (`facts`, `brand`), mutable contacts in admin Settings; PENDING channel = empty value = not rendered.**
Alternative: hard-code in Blade — rejected (drift). Consequence: contact copy has `lead` / `lead_with_phone` variants.

**ADR-8 — Placeholder reviews cannot ship.**
Seeded inactive + `Testimonial::published()` filter outside `local` + release-check scan. Alternative: delete seeds — rejected (admin needs an example of the fields). Consequence: reviews section and Swiper are conditional.

**ADR-9 — CMS pages get `is_indexable`.**
Admin can create arbitrary pages; without a flag every page became a sitemap entry. Consequence: sitemap = `Page::indexable()`, non-indexable pages render `noindex`.

**ADR-10 — Map is click-to-load.**
Google iframe cost 16–20 requests/~500 KB before interaction and sent visitor IPs to Google. The facade keeps the address + Maps link as location proof. Alternative: static map image — needs API key; deferred.

**ADR-11 — Lead endpoint stays CSRF-exempt; protection = honeypot + IP rate limit + 2-minute duplicate guard.**
Anonymous stateless form; a session token would break for long-open tabs and cached pages. Consequence: documented in `bootstrap/app.php`.

**ADR-12 — Attribution (`utm_*`, `gclid`) captured first-touch in `sessionStorage`, stored on the lead, never required.**

**ADR-13 — Analytics: dataLayer events only, no vendor, no PII.** Vendor tags are pasted via admin Settings → head scripts when the client decides.

**ADR-14 — Design tokens before redesign.** `--asv-color-*` tokens + `config('autostvabaks.brand')`; prototype lime stays until approved SVG assets arrive. Stage 2 changes tokens + `logo.blade.php` only.

**ADR-15 — Security headers without CSP.** Nosniff/Referrer/Frame/Permissions are safe; a strict CSP would need nonce support for Livewire, inline `window.ASV`, Google Fonts and Maps — audited later.

**ADR-16 — Fonts unchanged in Stage 1.** Self-hosting Urbanist is allowed (OFL) and planned for Stage 2 together with the typography pass, to avoid a visual regression now.

---

*Stage 2 (2026-11-10) — premium brand UI/UX × CRO × frontend performance.*

**ADR-17 — Approved identity applied through tokens only; the logo is a component, the colours are extracted from the SVG sources.**
Oleg's package (`resources/brand/source/{svg,eps,png,jpeg}`, commit `411a804`) carries exactly `#E6007E` / `#141414` / `#FFFFFF`. Web files are derived without redrawing (`scripts`-free: svgo + viewBox crop → `public/assets/imgs/brand/logo.svg`, `logo-white.svg`, `favicon.svg` = the split "a" glyph on a white tile). One `<x-asv-logo variant="dark|light">` component serves header, mobile menu, footer and Filament. Consequence: `config('autostvabaks.brand')` is `APPLIED`; `brand.primary = #E6007E`, `brand.ink = #141414`; text-sized magenta uses `--asv-brand-text` (#C9006E on light) / `--asv-brand-primary-on-dark` (#FF4FAE on graphite) because the brand magenta is 4.1:1 on `#141414` and 4.15:1 on the muted surface. Brand fidelity never overrides contrast.

**ADR-18 — Carento `theme.css` removed from the public site; Bootstrap kept as a purged subset.**
Stage 1 measured 97 % of `theme.css` unused. Instead of trimming it, every public component is now owned by `autostvabaks.css` (design tokens, type scale, buttons, header, forms, sections). Bootstrap stays for grid/modal/collapse/dropdown but is rebuilt by `scripts/purge-bootstrap.cjs` from the rendered pages (232 KB → 25 KB). Alternatives: keep `theme.css` and override (template feeling persists, 264 KB), drop Bootstrap entirely (modal/collapse a11y would need re-implementation). Consequence: after adding a new Bootstrap class, regenerate the subset (instructions in the script header); 136 unreferenced template files were deleted, the licensed zip stays untouched.

**ADR-19 — Fonts self-hosted: Urbanist variable WOFF2 (latin + latin-ext, OFL) + Manrope Cyrillic subsets, `font-display: swap`, Latin subset preloaded.**
Google Fonts cost 2 third-party requests and sent visitor IPs to Google on every view. The variable file (27.8 KB latin) is lighter than four static weights. Consequence: zero third-party requests before user interaction; the preload URL must equal the `@font-face` URL (no cache-busting query) or the font downloads twice.

**ADR-20 — Reviews: 1–3 real reviews render as a grid, Swiper (CSS+JS ≈ 258 KB) loads only for more than three.**
Also fixes a Stage 1 defect: `Testimonial::published()` excluded reviews whose `title` was NULL (`NULL NOT LIKE` is not true). Consequence: `SeoFoundationTest` now asserts both states.

**ADR-21 — Mobile sticky CTA is a secondary trigger for the same hero form, not a second form.**
Visible only while the hero form is out of view and the footer is not; hidden by CSS while the modal/menu is open; tracks `buyback_cta_click {cta_location: "sticky"}`. Alternative (repeating step 1 at the bottom) was rejected: two forms double the state to keep in sync and dilute the single dominant action.

**ADR-22 — Hero visual stays temporary; replacement is specified, not generated in code.**
The current image comes from the template package (licence not production-grade, visible Audi branding and plate). Stage 2 keeps it as a heavily graded backdrop on desktop only (phones get a lazy supporting visual below the form) and ships an exact generation/photography brief in `AUTOSTVABAKS_STAGE2_REPORT.md` §13. The hero's LCP element on mobile is now the H1, so swapping the image later carries no layout risk (fixed `aspect-ratio` slots).

---

*Stage 3 (2026-11-11) — content × search × AEO × trust × analytics × production-candidate hardening.*

**ADR-23 — Final copy lives in `lang/{et,ru,en}/site.php`; FAQ is a keyed content release.**
Copy is code (reviewed, tested, deployed with the build), the FAQ stays admin-editable but each agreed question has a stable `key` (`faqs.key`, migration `2026_11_11_000001`) and `FaqSeeder` upserts by key (legacy rows adopted by their ET question), so `php artisan db:seed --class=FaqSeeder --force` publishes exactly the agreed list on every host. The prohibited-claim list moved to `config('autostvabaks.prohibited_claims')` and is shared by `BrandFrontendTest` and `site:release-check`. Alternatives: FAQ in lang files (admin loses editing) or free admin FAQ (no content release, schema drift). Consequence: re-running the seeder overwrites admin edits of keyed questions — edit the seeder, not only the panel.

**ADR-24 — Nonce-based CSP on public pages, report-only first; admin excluded.**
Third-party cleanup (self-hosted fonts, click-to-load map, no Carento) made a strict policy feasible: `script-src 'self' 'nonce' 'strict-dynamic'` (+ GTM host for fallback), `style-src 'unsafe-inline'` (Bootstrap/Swiper set element styles), `img-src https:` (tag pixels), `frame-src` Google Maps/GTM, `object-src 'none'`. Every `<script>` the layout emits carries the request nonce, including `window.ASV` and admin "head scripts" (`Csp::withNonce`). `CSP_MODE=off|report-only|enforce`; Filament/Livewire routes keep the Stage-1 headers only. Local and staging runs showed zero violations through map → FAQ → form → success. Consequence: enforce after a report-only period in production; tags that inject scripts outside the nonce chain (not via GTM) will be blocked in enforce mode.

**ADR-25 — Production crawler policy: everything public is crawlable, `/admin` closed, `OAI-SearchBot` allowed explicitly, `GPTBot` left at the `*` default.**
ChatGPT search visibility must not be blocked by accident; model-training access is the client's decision (currently allowed by `*`). No `nosnippet`/`max-snippet`/`noimageindex` anywhere (release-check scans for them). No llms.txt/facts.json — crawlable HTML with self-contained answers is the AI layer.

**ADR-26 — Human sign-offs are explicit release statuses, not inferred.**
`HERO_ASSET_STATUS=temporary|approved` and `LEGAL_CONTENT_STATUS=draft|approved` in `.env`; `site:release-check` blocks on the production host (or `--strict`) unless both are `approved`, and always blocks when `og:image` / `primaryImageOfPage` point at the temporary template photo. The social image is a brand composition (`og-{et,ru,en}.jpg`, 1200×630) generated from the design tokens, so sharing never exposes the Audi/template asset. Consequence: Stage 4 cannot launch with the template hero or draft legal pages by forgetting.

**ADR-27 — Lead e-mail: attachment budget 10 MB (`LEADS_MAIL_ATTACHMENTS_MAX_MB`), originals stay private in admin, e-mail carries the admin deep-link.**
10 photos × 8 MB could produce an undeliverable 80 MB+ message. Photos are attached in order until the budget is reached; the e-mail says how many are available only in the admin panel and links to the authenticated lead page (never to `admin/lead-photo/*`). DB stays the record; mail failure still never loses the lead (test). Alternative (thumbnails only) deferred: the client reads photos in the panel.

**ADR-28 — Analytics vendor = GTM container from config, environment-aware.**
`ANALYTICS_GTM_ID` + `ANALYTICS_ENABLED` (default: production host only). The dataLayer is initialised before the container; event contract unchanged (ADR-13). No consent banner is shipped: the client must decide the consent approach (GA4 in the EU) before enabling tags — a Stage-4 launch item, not a technical blocker. `head_scripts` remains for extra snippets (nonced).

**ADR-29 — Production assets: minified twins built by `scripts/build-assets.cjs` (esbuild), served by `Site::asset()` only while not older than the source; cache headers delegated to the host.**
Readable sources stay the edit surface; the twin is regenerated and committed. Release-check warns when the twin is stale. nginx on reg.ru already serves static files gzip-compressed with a 45-day max-age; `.htaccess` adds an Apache fallback (immutable 1 year for fingerprinted assets, deflate). Brotli is not available on the host (verified: `content-encoding: gzip` for `br` requests) — not claimed.

**ADR-30 — Admin rich text passes an allow-list sanitiser on save and on render.**
`App\Support\HtmlSanitizer` (DOMDocument): headings, paragraphs, lists, links (http/https/mailto/tel/relative), basic inline formatting; drops script/style/iframe/object/embed/svg/form, every attribute except `href/title/target/rel`, `javascript:`/`data:` URLs, comments; `target=_blank` gets `rel=noopener`. Applied in `Page::saving` and in `page.blade.php`. The admin "head scripts" setting is intentionally raw (trusted operator input, nonced).

**ADR-31 — H1 kept, title restructured category-first.**
ET H1 "Müü oma auto kiirelt ja mugavalt" stays (natural, seller-promise, matches the `müü oma auto` cluster); the category `autode kokkuost üle Eesti` moves to the front of the `<title>`, stays in the eyebrow, Service schema name and footer. RU/EN titles follow the same pattern with native wording. Documented in `AUTOSTVABAKS_STAGE3_QUERY_COMPONENT_MAP.md` §4.
