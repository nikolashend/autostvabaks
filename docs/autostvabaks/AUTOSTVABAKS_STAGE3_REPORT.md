# AUTOSTVABAKS — STAGE 3 / 4 REPORT

**Content × Organic Search Dominance × AI/AEO × Trust × Analytics × Production-Candidate Hardening** · 2026-11-11 · staging https://autostvabaks.adme.ee · commits `df19bda`, `381d4da` (+ this report)

## 1. Executive verdict

**STAGE_3 = PASS** · **READY_FOR_STAGE_4 = YES** (technical) — with two explicitly listed **human launch blockers**: the temporary hero visual (`HERO_ASSET_STATUS=temporary`) and unapproved legal texts (`LEGAL_CONTENT_STATUS=draft`). Both are enforced by `site:release-check` on the production host, so Stage 4 cannot launch past them by accident. No technical P0/P1 remains open. Tests: 38 passed / 527 assertions (was 27 / 350). Staging Lighthouse: mobile 98 / desktop 100 / a11y 100 — no regression.

## 2. Query / competitor research

- Live SERP review (2026-11-11) for the ET/RU/EN query lists of the TZ; no search-volume data was available → none invented. Prioritisation by SERP prevalence, intent, business relevance, naturalness.
- Competitors fetched live: Carbuy, Autorahaks, Autodeost, NSADAuto, CarMoney (autoost.ee is a parked domain, autokokkuost.ee does not resolve); SERP also surfaced Maxcar, AutoDiil, OstameAutod, Autoost24, Alsautod, ES Autod, Rallimüük, Ostanautod, rahaeest24, carprice.
- Patterns: category-first titles ("Autode kokkuost …"), unverifiable speed claims (15 min / 1 h / 24 h), "up to 90 %" price claims without explanation (CarMoney contradicts itself 90 % vs 85 %), unsourced proof ("#1", "94 %", "21 000 recommend"), plate-first forms with "continue without number"; Autodeost ≈ 16 fields before contact, NSADAuto the lightest (6 inputs) and the only one explaining valuation logic.
- Compact matrix (intent · strength · weakness · opportunity · component): `AUTOSTVABAKS_STAGE3_QUERY_COMPONENT_MAP.md` §2.

## 3. Final intent ownership

Unchanged architecture: `/` owns the ET transactional cluster (autode kokkuost · auto kokkuost · auto väljaost · müü auto kiiresti · müü oma auto + avariiline / liisingus / hindamine as on-page answers); `/ru` and `/en` own their clusters. No new URLs: the page gate (§5 of the map) is not met for damaged / leasing / valuation until GSC shows distinct demand. Query → component table per locale: map §3. Intent ownership doc updated (§1, §2, §4, §5, §6).

## 4. ET content changes

- Title/description rewritten category-first (see §7). H1 kept (ADR-31); eyebrow carries "Autode kokkuost üle Eesti"; subtitle now states the mechanism ("Üks lühike päring asendab kuulutuse, proovisõidud ja läbirääkimised …").
- Steps: "Auto väljaost nelja sammuga", step 3 states that the final offer follows the inspection.
- Why: four seller objections → factual resolutions (no listing/strangers · don't know the value · leasing/faulty/damaged · how deal & payment work); "helistajaid" avoided (phone pending).
- Valuation: direct answer (market price of similar cars in Estonia + actual condition, final after inspection, "main factors" not the full method) + **expectation message** (F14) + 7 factors + optional-price note.
- Answers (`#vastused`): self-contained Q/A/detail for leasing (request accepted, lender optional, process agreed individually), damaged ("hindame … pakkumine seisukorra põhjal", no "buy every car"), payment (bank transfer normal, cash on request).
- FAQ: 9 keyed questions (vehicles · how · response · photos · valuation · area · leasing · damaged · payment), every answer names Autostvabaks and stands alone.
- Footer: about line + "Autostvabaks teenuse osutaja: Motion Motors OÜ". Removed unused `hero.ticks`.

## 5. RU content changes

Independent native copy, not a translation: title "Выкуп авто по всей Эстонии – продать машину просто", H1 "Продайте машину быстро и без хлопот", seller vocabulary (выкуп авто, после ДТП, в лизинге, по всей Эстонии), no «гарантированно / самая высокая цена / лучший выкуп / обязательно купим». Same structure and facts as ET; FAQ ×9; operator line «Услугу Autostvabaks оказывает Motion Motors OÜ».

## 6. EN content changes

Concise native copy for residents/international owners: title "Car buyback across Estonia – sell your car the simple way", H1 "Sell your car quickly and easily", steps "Car buyback in four steps", FAQ ×9, operator line. Not expanded for SEO.

## 7. Titles / meta / H1

Per-locale table (primary intent · title · description · H1 · opening statement · CTA) with lengths: map §4. Titles are unique per locale, H1 ≠ title, one H1 per page, canonical self, hreflang reciprocal + x-default, OG per locale with `og:locale:alternate`. H1 decision documented (ADR-31).

## 8. AEO / direct-answer architecture

QUESTION → DIRECT ANSWER → DETAIL → PROOF → CTA in `#hindamine`, `#vastused` and the FAQ; answers avoid pronoun-only replies ("Jah. Liisingus või laenuga autole saab päringu saata …"). No hidden copy, no llms.txt/facts.json, no AI-only pages; server-rendered HTML, strong headings, FAQPage schema from the same records. Snippet restrictions: none (release-check scans `nosnippet` / `max-snippet` / `noimageindex`). Production robots: `*` allowed except `/admin`, `OAI-SearchBot` allowed explicitly, GPTBot at the default (ADR-25) — Stage-4 verification item after DNS switch.

## 9. Trust / claims / legal

- Fact register extended (F13–F15) and pending list (P8 no-obligation, P9 documents, P10 hero, P11 reviews/photo); full copy audit §5 of the register. Prohibited-claim scanner (26 fragments, 3 languages) in config, test and release-check: no hits.
- Documents answer: **omitted** (P9 unconfirmed). "Päringu saatmine ei kohusta …": **not published** (P8); test asserts absence; the seeded draft Terms contain the sentence — final Terms and landing must agree.
- Legal operator footer line in ET/RU/EN (F2), no Motion Motors branding.
- Legal pages: Privacy = DRAFT, Terms = DRAFT (seeded structural drafts, "Mustand" markers, text unchanged). **LEGAL_CONTENT_READY = NO** → `LEGAL_CONTENT_STATUS=draft` blocks production.
- Reviews: none supplied → section hidden (conditional, tested). Location photo: none → map facade kept. No fakes.

## 10. Hero / image decision

- No client-approved hero asset was supplied → the Stage-2 composition stays with the template photo, **HERO_ASSET_STATUS=temporary** (gate, ADR-26). Directional gradient lightened over the car (0.5 → 0.42 at 66 %, right edge 0.62 → 0.52) — car and facade are now recognisable at 1440 while copy stays on ≥ 0.84 graphite (`hero-et-1440.jpg`, `hero-et-1024.jpg`). Plate still sits under the form card.
- Mobile hierarchy unchanged (H1 → value → form → facts → visual); the supporting visual is now a 16:9 derivative `hero-mobile-card.{webp,jpg}` (800×450, 28 KB) instead of the 1000×465 file — note: the Stage-2 markup declared 900×1200 for a 1000×465 file; fixed.
- OG: brand compositions `og-{et,ru,en}.jpg` (1200×630, 55–61 KB) generated from the design tokens (logo-white, graphite, magenta glow, locale tagline); `og:image:width/height/alt` set; schema `primaryImageOfPage` = OG image; release-check blocks any og/schema reference to the temporary hero. Generation brief for the real hero remains Stage-2 report §13; AI-image quality gate (§76 of the TZ) applies when it arrives.

## 11. Analytics implementation

- No GTM/GA4 IDs supplied → **ANALYTICS_VENDOR_STATUS = READY_NOT_CONFIGURED**. Implemented: `ANALYTICS_GTM_ID` + `ANALYTICS_ENABLED` (default: production host only), container injected in `<head>` with the CSP nonce + `<noscript>`, `window.dataLayer` initialised before it; `head_scripts` still available (nonced). Tests cover off / enabled / invalid ID / no duplicates / order.
- Event map verified on staging (real submission with 2 photos): `form_start → step1_complete → step2_view → photo_add → submit → success`; `map_load`, `faq_open`, `cta_click {cta_location}` verified; errors tracked separately (`buyback_form_error {step, status, fields}` — field names only). Fix: `step2_view` now fires on `show.bs.modal` (before the transition), so it always precedes `photo_add` / `submit` (`381d4da`).
- PII audit: dataLayer payload contains no plate / phone / e-mail / text / filenames (checked on staging after a real submission). GTM "form auto-capture" must stay off in the container — listed in the Stage-4 handoff.
- Consent: no banner shipped; GA4 consent approach is a client decision before enabling tags (ADR-28).

## 12. Lead-email / photo reliability

- Attachment budget lowered to **10 MB** (`LEADS_MAIL_ATTACHMENTS_MAX_MB`; ≈ 14 MB on the wire), photos attached in order until the budget is hit; the e-mail states "N of M attached, the rest available in the admin panel" and links to the authenticated lead page (never to `admin/lead-photo/*`). Originals stay in `storage/app/private/leads/{id}`. Mail template colours moved to the approved brand.
- Tests: cap exceeded → e-mail still sent, 0 attachments, `skippedPhotos = 2`, admin link present; normal budget → 2 attachments; mail failure never loses the lead (existing test, green).
- Staging E2E: lead #3 (`999S3E`, RU, 2 photos, `utm_source=stage3`, `gclid=G3`) stored, `mail_sent_at` set, no mail error.

## 13. SEO technical checks

Canonical self per locale · hreflang et/ru/en + x-default reciprocal · OG per locale · sitemap = home ×3 + indexable pages (release-check now renders every `<loc>`: 200, self-canonical, not noindex, not a utility route) · production simulation (`APP_ENV=production APP_URL=https://autostvabaks.ee SITE_NOINDEX=false STAGING_AUTH=` + `--strict`): **only** the human blockers fail (HERO_ASSET_STATUS, LEGAL_CONTENT_STATUS, draft markers of the same legal pages); with both statuses set to approved and final texts, the strict check passes (test). Staging keeps noindex meta + `X-Robots-Tag` + `Disallow: /`. Image SEO: semantic OG filenames, explicit dimensions, decorative hero images `alt=""`.

## 14. Schema

Graph unchanged in model (Organization = Motion Motors OÜ → Brand Autostvabaks → Service car buyback, WebSite, WebPage, FAQPage). Changes: `Organization.logo` and `Brand.logo` = approved wordmark SVG; `WebPage.primaryImageOfPage` = brand OG image; FAQPage = 9 records = visible accordion (test). No ratings, no opening hours, no telephone until configured.

## 15. Security / CMS sanitisation / CSP

- **HTML sanitiser** (`App\Support\HtmlSanitizer`, ADR-30): allow-list h2–h4/p/br/hr/strong/b/em/i/u/s/sub/sup/ul/ol/li/blockquote/a[href title target rel]; drops script/style/iframe/object/embed/svg/form/…, all `on*`/`style` attributes, `javascript:`/`data:`/protocol-relative URLs, comments; `_blank` → `rel=noopener`. Applied in `Page::saving` and on render. Tests: stored XSS vectors removed, legitimate formatting kept, legacy rows cleaned at render.
- **CSP** (ADR-24): nonce-based policy on public HTML, `CSP_MODE=report-only` by default (enforce/off available), admin/livewire excluded. Every executable `<script>` carries the nonce (test), admin head scripts get it injected. Report-only run through map → FAQ → form → success on local and staging: **0 violations**, 0 console errors. Decision: keep report-only through the first production weeks, then `CSP_MODE=enforce`.
- `composer audit`: no advisories. Stage-1/2 headers unchanged; admin auth / upload / photo-route tests green. npm: not applicable (no JS build in the repo).

## 16. Performance / cache / compression

| Run (staging, gzip) | Perf | A11y | BP | LCP | CLS | TBT | Req | Transfer |
|---|---|---|---|---|---|---|---|---|
| Stage 2 mobile | 98 | 100 | 100 | 1.7 s | 0 | 70 ms | 13 | 131 KB |
| **Stage 3 mobile** | **98** | 100 | 100 | **1.6 s** | 0 | 20 ms | 13 | 119 KB |
| Stage 2 desktop | 100 | 100 | 100 | 0.4 s | 0.001 | — | 12 | 199 KB |
| **Stage 3 desktop** | **100** | 100 | 100 | 0.4 s | 0 | 0 ms | 12 | 192 KB |

(SEO 61 on staging = noindex by design.) Minified twins: CSS 56.8 → 43.7 KB, JS 25.5 → 12.7 KB (`scripts/build-assets.cjs`, served only when fresh; release-check warns when stale). Caching: reg.ru nginx serves static files with `Cache-Control: max-age=3888000` + gzip (verified on the `.min.css`), HTML `no-cache, private`; `.htaccess` adds the Apache fallback (immutable 1 y, deflate). **Brotli: not available on the host** (request with `br` → gzip) — not claimed. Mobile page length: ET 8 343 → 9 356 px, RU 9 066 → 10 240 px at 390 (three extra FAQ items + expectation paragraph) — reviewed, no awkward wraps; sticky CTA behaviour unchanged.

## 17. Accessibility

Lighthouse a11y 100 (mobile + desktop, staging). New elements: footer operator line (muted on graphite, 13 px, contrast ≥ 7:1), expectation paragraph (`--asv-text-on-dark-muted`), OG `og:image:alt`, decorative images `alt=""`; no new consent UI.

## 18. Tests / E2E

- `php artisan test` → **38 passed, 527 assertions**: `LeadFormTest` 14, `SeoFoundationTest` 9 (release-check test now sets the Stage-3 statuses), `BrandFrontendTest` 4 (prohibited list from config, new copy), **`Stage3HardeningTest` 11** (operator line, OG image, intent copy + no "ei kohusta", FAQ release idempotence + schema parity, GTM switch, CSP nonce/admin exclusion/head scripts, sanitiser save+render, mail cap + admin link, robots OAI-SearchBot, strict release-check gates + claim scanner, minified assets freshness).
- `site:release-check` staging: pass with the 9 expected warnings (hero, legal, no GTM, 6 draft markers). Strict production simulation: see §13.
- Staging E2E (headless Chrome): CSP report-only clean, dataLayer order correct, no PII, no console errors; real lead with photos stored + mailed.

## 19. P0 / P1 fixed

| Sev | Finding | Fix |
|---|---|---|
| P0 | Temporary template hero could reach production unnoticed; og:image pointed at it | `HERO_ASSET_STATUS` gate + og/schema check in release-check; brand OG images |
| P0 | Stored XSS possible through admin RichEditor HTML (`{!! !!}` render) | allow-list sanitiser on save + render, tests |
| P1 | Lead e-mail could carry up to 80 MB of attachments | 10 MB budget, admin deep-link, test |
| P1 | Draft legal text could be mistaken for approved | `LEGAL_CONTENT_STATUS` gate; LEGAL_CONTENT_READY = NO documented |
| P1 | Copy: "Kiire vastus", "ostame iga auto", vague leasing wording, prohibited-claim list only in a test | final copy against the register; scanner in config + release-check |
| P1 | Legal operator missing from public UI | footer line in 3 locales |
| P1 | No analytics vendor path, no CSP | config-driven GTM, nonce CSP report-only |
| P1 | `step2_view` could fire after `photo_add`/`submit` | tracked on `show.bs.modal` |
| P1 | Stage-2 mobile visual declared 900×1200 for a 1000×465 file; full file loaded for a small card | 16:9 800×450 derivative, correct dimensions |
| P1 | Sitemap entries never validated | release-check renders every `<loc>` |

## 20. Remaining human blockers (Stage 4)

1. **Hero asset** — approved photo/render per Stage-2 brief §13 → replace files, pass the AI-image quality gate, set `HERO_ASSET_STATUS=approved`.
2. **Legal texts** — final Terms/Privacy (incl. company registry code, decision on the no-obligation sentence P8) → publish in admin, set `LEGAL_CONTENT_STATUS=approved`.
3. Non-blocking inputs: phone/WhatsApp/Telegram, real reviews, Meistri 20 photo, documents answer (P9), GTM container ID + consent approach, GPTBot decision.

## 21. Visual evidence

`docs/autostvabaks/baseline/post-stage3/` (13 files, from staging at `df19bda`): `hero-et-1440.jpg`, `hero-et-1024.jpg`, `hero-et-390.jpg`, `public-et-1440.jpg`, `public-et-390.jpg`, `public-ru-1440.jpg`, `public-ru-390.jpg`, `public-en-1440.jpg`, `public-en-390.jpg`, `form-step2-modal-1440.jpg`, `form-step2-modal-390.jpg`, `form-success-1440.jpg`, `form-success-390.jpg`. Before: `baseline/post-stage2/`.

## 22. Git evidence

`df19bda` feat(autostvabaks): Stage 3 search content, AEO layer, trust and production-candidate hardening (43 files) · `381d4da` fix(analytics): track buyback_form_step2_view when the modal starts opening · this report as a docs commit. All pushed to `main`; staging deployed with `deploy.sh` (`Deployed 381d4da`, release check passed); FAQ release published on staging (`db:seed --class=FaqSeeder --force`). Worktree clean after the docs commit. No secrets, no debug files.

## 23. Acceptance matrix

| Gate | Result |
|---|---|
| MASTER_BASELINE_READ | PASS |
| STAGE2_NO_REGRESSION | PASS (tests, DOM contract, Lighthouse) |
| FINAL_ET_COPY / FINAL_RU_COPY / FINAL_EN_COPY | PASS |
| TITLE_META_H1_ALIGNMENT | PASS |
| QUERY_COMPONENT_MAP | PASS |
| INTENT_OWNERSHIP | PASS |
| AEO_ANSWER_LAYER | PASS |
| VALUATION_CONTENT / LEASING_CONTENT / DAMAGED_CONTENT / PAYMENT_CONTENT | PASS |
| FAQ_FINAL | PASS (9, keyed, schema-synced) |
| FACT_CLAIM_AUDIT | PASS |
| LEGAL_OPERATOR_FOOTER | PASS |
| LEGAL_CONTENT_STATUS | BLOCKED (human: drafts; gate enforced) |
| HERO_ASSET_STATUS | BLOCKED (human: temporary; gate enforced) |
| HERO_DESKTOP_VISUAL | PARTIAL (gradient improved; asset temporary) |
| HERO_MOBILE_VISUAL | PASS |
| OG_IMAGE_STATUS | PASS (brand composition) |
| ANALYTICS_VENDOR_STATUS | PARTIAL (ready, no ID supplied) |
| DATALAYER_NO_PII | PASS |
| ATTRIBUTION | PASS (utm/gclid stored on staging lead) |
| LEAD_MAIL_SIZE_SAFETY | PASS |
| ADMIN_DEEP_LINK | PASS |
| PHOTO_STORAGE | PASS (private, unchanged) |
| HTML_SANITISER | PASS |
| CSP_DECISION | PASS (report-only, 0 violations; enforce in Stage 4) |
| CACHE_HEADERS | PASS (host nginx + .htaccess fallback) |
| COMPRESSION | PASS (gzip; Brotli NOT_APPLICABLE on host) |
| MINIFIED_ASSETS | PASS |
| CANONICAL / HREFLANG / ROBOTS / SITEMAP / SCHEMA | PASS |
| PRODUCTION_SIMULATION | PASS (only human blockers fail) |
| STRICT_RELEASE_CHECK | PASS (expanded, tested) |
| OAI_SEARCHBOT_POLICY | PASS |
| GOOGLEBOT_READINESS | PASS |
| PERFORMANCE | PASS (98 / 100) |
| A11Y | PASS (100) |
| SECURITY | PASS |
| ALL_TESTS | PASS (38 / 527) |
| E2E | PASS (staging) |
| VISUAL_EVIDENCE | PASS |
| WORKTREE_CLEAN | PASS |
| READY_FOR_STAGE_4 | YES (technical) — launch blocked by HERO_ASSET_STATUS and LEGAL_CONTENT_STATUS |

## 24. Stage-4 production handoff

1. Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://autostvabaks.ee`, `SITE_NOINDEX=false`, `STAGING_AUTH=`, real SMTP + SPF/DKIM, `HERO_ASSET_STATUS=approved`, `LEGAL_CONTENT_STATUS=approved` (only after real replacements), `ANALYTICS_GTM_ID` (+ consent decision), `CSP_MODE=report-only` → `enforce` after a clean period.
2. Content: `php artisan migrate --force`, `php artisan db:seed --class=FaqSeeder --force`, publish final legal pages in admin, deactivate placeholder testimonials.
3. Gate: `php artisan site:release-check --strict` must pass with 0 FAIL (deploy.sh runs it and rolls back to maintenance on failure).
4. After DNS: verify `robots.txt` (production variant, OAI-SearchBot), `sitemap.xml`, canonical/hreflang on the production host, `X-Robots-Tag` absent on public pages, GSC property + sitemap submission, GTM preview (event order, no form auto-capture), CSP console clean, Lighthouse re-run.
5. Preserve: DOM/JS contract (`#lead-step1/#lead-step2/#lead-modal`, `[data-field]`, `[data-sticky-cta]`, `[data-focus-form]`, `window.ASV`, dataLayer events), section ids, tokens, master baseline §15–17.
