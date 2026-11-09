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
