# AUTOSTVABAKS — STAGE 1 REPORT

**STAGE_1 = PASS (with documented PENDING items) · READY_FOR_STAGE_2 = YES** · 2026-11-09

## 1. Executive verdict

The existing Laravel 12 + Filament 3 prototype was preserved and audited; no rebuild. P0/P1 defects in SEO safety, entity model, fact centralisation, placeholder risk, CMS indexation and headers were fixed; no-regression tests (23 tests / 246 assertions) and a production release gate exist; the project constitution is in `AUTOSTVABAKS_X999_MASTER_BASELINE_V1.md`. Blocked only by client inputs (phone, brand assets, legal texts, real reviews) — none of them blocks Stage 2.

## 2. Repository / environment baseline

See `AUTOSTVABAKS_CURRENT_STATE_AUDIT_V1.md` §1. Pre-Stage-1 commit `e675a2d`, clean worktree; template zip checksum recorded; brand assets PENDING.

## 3. Existing implementation discovered

Laravel 12 / Filament 3 / Bootstrap 5 / Swiper / Carento `theme.css`; routes `/`, `/ru`, `/en`; two-step form; private photos; admin with leads, reviews, FAQ, pages, settings, users (audit §2).

## 4. Intentionally preserved

Route model, hero form and flow, JS form behaviour, admin modules, legal page system, mail pipeline, Basic Auth + noindex on staging, Mailtrap on staging, all visual design (tokens added underneath, no re-skin).

## 5. KEEP / HARDEN / ADAPT / REMOVE matrix

Audit §9 (admin) and §3 (template). Summary: KEEP auth/leads/dashboard/settings/users; KEEP+HARDEN reviews (placeholder guard), leads (attribution); ADAPT pages (`is_indexable`); REMOVE LATER `theme.css` bulk, demo images; DEFER CRM/Telegram/statuses.

## 6. Architecture decisions

ADR-1…16 in `AUTOSTVABAKS_ARCHITECTURE_DECISIONS_V1.md`.

## 7. Brand reconciliation

Prototype lime palette identified as temporary. Design-token layer (`--asv-color-*`, `config('autostvabaks.brand')`) now drives public CSS, admin primary colour and `theme-color`. Approved SVG logo/colour files are **not available** in repo/disk → `BRAND_ASSET_AUDIT = PARTIAL` (architecture ready, assets pending from client/Oleg).

## 8. SEO / routes / hreflang / indexation

Frozen `/`, `/ru`, `/en`, x-default `/`; `URL::forceRootUrl(APP_URL)`; `ForceHttps` scheme+host 301; single `Site::alternates()` generator; `is_indexable` on pages; sitemap only indexable; `X-Robots-Tag` on staging and `/admin`; `isset($noindex)` bug fixed. Matrix: `AUTOSTVABAKS_ROUTE_INDEXATION_MATRIX_V1.md`.

## 9. Intent ownership

`AUTOSTVABAKS_SEARCH_INTENT_OWNERSHIP_V1.md` — ET root owns the transactional cluster; RU/EN own their languages; page gate; SERP-promise map; AEO answer pattern.

## 10. Entity / schema

`App\Support\Schema`: Organization (Motion Motors OÜ) + Brand (Autostvabaks) + WebSite + WebPage + Service + FAQPage (same source as visible FAQ). AutoDealer removed. Telephone only when configured. Verified by test (valid JSON, entity names, no ratings/hours).

## 11. Form / DB / photos / mail

Audit confirmed: server validation authoritative, honeypot, 8/10 min rate limit, DB first, private photos, mail-failure safe. Added: `image` rule (content sniff), duplicate guard (2 min), attribution fields (`utm_*`, `gclid`), analytics events without PII, modal focus restoration. HEIC: rejected server-side (documented; iOS converts to JPEG on upload in most cases — Stage 2 may add a hint).

## 12. Admin / security

Hashed passwords, Filament login throttling, no registration, auth-only photo route, `X-Robots-Tag` on admin, security headers (nosniff/referrer/frame/permissions), `composer audit` clean, `.env` ignored, debug off on staging. CSP deferred (ADR-15). Page RichEditor HTML is admin-authored (sanitiser = Stage 2 candidate).

## 13. UI / UX / CRO gap map

`AUTOSTVABAKS_UI_UX_GAP_MAP_V1.md` (14 gaps, image spec).

## 14. Performance / third parties

Audit §5–6. Stage 1 changes: click-to-load map (−16–20 requests, −~500 KB on desktop), Swiper conditional (−258 KB when no reviews), bullets/headings fixes. Lighthouse local: mobile 66→69, desktop 95→97, a11y 95→100. Remaining weight is template CSS (Stage 2).

## 15. Accessibility

Lighthouse a11y 100 on mobile and desktop after fixing heading order, toggle name mismatch, bullet target size; focus restoration added; Bootstrap modal trap/Escape verified in headless Chrome.

## 16. Tests and evidence

- `php artisan test` → 23 passed (246 assertions): `LeadFormTest` (14) + `SeoFoundationTest` (9: production simulation, hreflang reciprocity, schema entities, phone conditionality, placeholder guard, CMS index safety, headers, duplicate + attribution, release-check gate).
- `php artisan site:release-check` locally: PASS with warnings (placeholders/drafts); `--strict`: 18 blocking problems as designed.
- Headless Chrome E2E: map facade loads iframe on click; Back restores focus to "Alusta päringut"; full submission with UTM/gclid stored; dataLayer sequence `buyback_form_start → step1_complete → step2_view → submit → success`; no PII in dataLayer; no console errors.
- Lighthouse before/after JSON in session scratch; numbers in audit §5.
- Screenshots: `baseline/pre-stage1/*.jpg` (11), `baseline/post-stage1/*.jpg` (2).

## 17. P0 / P1 defects fixed

| Sev | Defect | Fix |
|---|---|---|
| P0 | `noindex` rendered whenever `$noindex` variable existed (even `false`) | `! empty($noindex)` |
| P0 | No production fail-safe against inherited staging noindex / host | `site:release-check` + `SeoFoundationTest::test_production_simulation…` + `deploy.sh` gate |
| P0 | Canonical/hreflang followed request Host (www/IP/alias leak possible) | `URL::forceRootUrl(APP_URL)` + host redirect |
| P0 | Staging noindex only via meta + robots | + `X-Robots-Tag` header |
| P1 | AutoDealer schema naming the brand as legal entity, address hard-coded | Organization/Brand/Service graph from central facts |
| P1 | "write or call" with phone PENDING | conditional copy (ET/RU/EN) |
| P1 | Placeholder reviews active & renderable in production | seeded inactive, `published()` filter, release scan, Swiper conditional |
| P1 | Any admin page auto-indexed / in sitemap | `is_indexable` toggle, sitemap filter, noindex meta |
| P1 | Hreflang arrays built in two controllers | single generator |
| P1 | Brand colours hard-wired; admin colour separate | token layer + config |
| P1 | No security headers; `/admin` not explicitly noindex | `SecurityHeaders` middleware |
| P1 | Google Maps iframe loaded on every view (perf + privacy) | click-to-load facade |
| P1 | a11y: heading order, toggle name, 9 px bullets, focus after modal | fixed |
| P1 | Double submit could create two leads + two mails | 2-minute duplicate guard |

## 18. Deferred P2 / P3

P2: self-host Urbanist; purge `theme.css`/Bootstrap; HTML sanitiser for page content; CSP; sticky mobile CTA; typography scale; final copy; final hero image; HEIC hint. P3: Telegram, CRM/statuses, registry API, city/model pages, AI extras, llms.txt.

## 19. Git evidence

Pre-Stage-1: `e675a2d`. Stage 1: `450afdb feat(autostvabaks): harden prototype and freeze X999 foundation` + follow-ups found while verifying on staging: `6c553d2` (release-check renders behind Basic Auth), `f71c355` (deploy runs the check after `artisan up`), `675ac68` (staging-host marker only on production). Deployed to staging via `deploy.sh`; live checks: `X-Robots-Tag`/security headers present, `noindex` meta, canonical/hreflang on staging host, `robots.txt Disallow: /`, `www.` → 301, migration ran, `site:release-check` passes with only the 6 expected draft-legal-page warnings. Worktree clean.

## 20. Acceptance matrix

| Gate | Result | Evidence |
|---|---|---|
| REPOSITORY_BASELINE | PASS | audit §1 |
| SOURCE_PRESERVED | PASS | zip untouched, checksum recorded |
| CURRENT_IMPLEMENTATION_AUDITED | PASS | audit §2–9 |
| VISUAL_BASELINE | PASS | baseline/pre-stage1 |
| STAGING_NOINDEX | PASS | meta + header + robots; tests |
| PRODUCTION_NOINDEX_GUARD | PASS | release-check + simulation test |
| ENV_AWARE_CANONICAL | PASS | forceRootUrl + test |
| HREFLANG | PASS | reciprocity test |
| ET_RU_EN_ROUTES | PASS | tests |
| SEARCH_INTENT_OWNERSHIP | PASS | doc |
| PAGE_CREATION_GATE | PASS | doc §3 |
| CENTRAL_FACT_SOURCE | PASS | config facts + Settings |
| FACT_CLAIM_REGISTER | PASS | doc |
| BRAND_ASSET_AUDIT | PARTIAL | assets not supplied; tokens ready |
| DESIGN_TOKEN_FOUNDATION | PASS | CSS + config tokens |
| PLACEHOLDER_REVIEW_GUARD | PASS | scope + scan + test |
| CONTACT_CONDITIONALITY | PASS | test |
| ENTITY_SCHEMA_ARCHITECTURE | PASS | Schema class + test |
| FORM_BACKEND_AUDIT | PASS | audit §8 + tests |
| LEAD_DB_RELIABILITY | PASS | mail-failure test |
| PHOTO_SECURITY | PASS | rules + auth route test |
| ADMIN_SECURITY | PASS | audit §8 |
| CMS_INDEX_SAFETY | PASS | is_indexable + test |
| THIRD_PARTY_AUDIT | PASS | audit §6 |
| PERFORMANCE_BASELINE | PASS (lab only) | audit §5 |
| ACCESSIBILITY_SMOKE | PASS | Lighthouse 100 + E2E |
| SECURITY_BASELINE | PASS | headers, audit, no CSP (documented) |
| PRODUCTION_SIMULATION | PASS | test |
| OUT_OF_SCOPE_FROZEN | PASS | master §14 |
| WORKTREE_CLEAN | PASS | after commit |
| READY_FOR_STAGE_2 | YES | handoff below |

## 21. Stage-2 handoff — PREMIUM BRAND UI/UX × FULL LANDING FRONTEND × CRO HARDENING

**Read first:** `AUTOSTVABAKS_X999_MASTER_BASELINE_V1.md`, then `AUTOSTVABAKS_UI_UX_GAP_MAP_V1.md`.

**Preserve:** hero form above the fold on all viewports; 2-step flow and JS contract (`#lead-step1`, `#lead-step2`, `window.ASV`, dataLayer events); ET/RU/EN parity; conditional contacts/reviews; `Site::alternates()`; all 23 tests green; a11y 100.

**Improve:** apply approved magenta/graphite identity by changing `--asv-color-*` tokens + `config brand.*` + `logo.blade.php` (needs SVG from client); replace Carento look (purge/replace `theme.css`, own type scale, self-hosted Urbanist); stronger hierarchy and section rhythm; trust layer (legal/location card, valuation transparency slot, leasing/damaged/payment/documents answer blocks with CTA); sticky mobile CTA; success-state "what happens next"; review layout for 1–3 real reviews; hero/location images per spec (gap map §3).

**Inputs required from client (do not block layout work):** SVG logo + colour codes; phone/WhatsApp/Telegram; final legal texts; real reviews; photos.

**Definition of done for Stage 2:** tokens applied, no lime remains, Lighthouse a11y 100 kept, mobile perf ≥ 85 on hosting (gzip), all tests green, `site:release-check --strict` passes on a production-like `.env` except for client-content warnings, screenshots in `baseline/post-stage2/`.
