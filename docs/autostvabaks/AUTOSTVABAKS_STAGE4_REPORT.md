# AUTOSTVABAKS — STAGE 4 REPORT (production release × live QA × launch readiness)

Date: 2026-11-12 · Release candidate: `45d748d` (main) · Staging: https://autostvabaks.adme.ee at `45d748d` · Production: not deployed (see §1–3).

## 1. Executive verdict

| Item | Value |
|---|---|
| STAGE_4 | **PARTIAL** |
| TECHNICAL_READY | **YES** |
| PRODUCTION_DEPLOYED | **NO** |
| PRODUCTION_VERIFIED | NO |
| GSC_VERIFIED | NO |
| READY_FOR_CLIENT_HANDOFF | **YES — for the activation decision**, with the exact procedure in `AUTOSTVABAKS_STAGE4_GO_LIVE.md` |

Why PARTIAL and not forced: production does not exist as an environment. `autostvabaks.ee` points to a zone.ee placeholder, the hosting panel (not SSH) is needed to create the WWW-domain and the database on reg.ru, the DNS zone lives at zone.eu, and lead mail must go through Google Workspace SMTP that we have no credentials for. The legal texts are still not client-approved. Everything that does not depend on those inputs is done, verified on staging and committed: the licensed hero (gate now `approved`), the operator facts (registry code, VAT) in footer / schema / legal drafts, the one-step legal approval workflow, extended release gates, the production procedure with backup/rollback, and 42 green tests.

BLOCKER (exact): **H1 legal approval** (`LEGAL_CONTENT_STATUS=approved` + `db:seed --class=PageSeeder --force`), **H3 panel access** (WWW-domain + DB), **H4 DNS at zone.ee** (A → 31.31.196.109), **H5 Google Workspace SMTP credentials**. Hero (H2) is applied and QA'd; client visual sign-off is recorded, not required to flip the gate again.

## 2. Human input closure

| Input | Status | Evidence |
|---|---|---|
| Hero asset | **CLOSED technically** — Pexels 20695262 (Erik Mclean, Pexels licence: commercial use, modification, no attribution) replaces the template Audi; six template files deleted; `HERO_ASSET_STATUS=approved` on local and staging; release-check verifies files + references (ADR-32) | §6, `baseline/post-stage4/hero-et-*.jpg` |
| Legal texts | **OPEN (human)** — drafts rewritten with the authoritative operator block in ET/RU/EN, consistent across Terms §1 / Privacy §6, still marked Mustand/Черновик/Draft; `LEGAL_CONTENT_STATUS=draft` | §7 |
| Registry code / VAT | **CLOSED** — F16 `17341567`, F17 `EE102908916` (TZ §12) | footer, JSON-LD, legal drafts |
| Phone / WhatsApp / Telegram, reviews, Meistri 20 photo, GTM ID | not supplied → conditional modules stay hidden; not blocking | §9, §19 |

## 3. Production environment (pre-flight, SSH)

Host `server144.hosting.reg.ru` (31.31.196.109), PHP 8.3.36 (web SAPI cgi-fcgi, upload/post 256 MB, memory 1 GB, gd/imagick/intl/zip/opcache/pdo_mysql), Composer 2.10.1, Percona MySQL 8.0.25 (one DB per site, panel-created), 407 GB free, nginx → Apache (`.htaccess` honoured), Let's Encrypt per domain from the panel, no cron needed. `mgrctl` (ispmanager CLI) refuses non-interactive use; the DB user has grants on its own schema only → **production site + DB can only be created in the panel** (H3). Differences from staging assumptions: none for the application; the only environment delta is the mail transport (§17).

Domain facts: `autostvabaks.ee` A `217.146.69.28` (`sn-69-28.tll07.zoneas.eu`, "page is under construction" frameset), `www` alias same; NS `ns.zone.eu`, `ns2.zone.ee`, `ns3.zonedata.net`; MX `smtp.google.com`; SPF `v=spf1 a mx include:_spf.zone.eu include:_spf.google.com -all`; DMARC `v=DMARC1; p=reject`; no `google._domainkey` DKIM record found (selector may differ).

## 4. Backup / rollback evidence

Nothing exists in production to back up (placeholder page at zone.ee, not ours). The procedure fixes: DB dump before every `deploy.sh`, `storage/` tarball (private photos), exact commit hash, DNS before/after table, `artisan down` as the instant switch, code rollback by hash, DNS rollback to `217.146.69.28`. Staging DB untouched by Stage 4 except the seeder refresh of the two draft pages and test lead #5.

## 5. Deployment

Staging: `bash deploy.sh` → `Deployed 45d748d` (composer --no-dev, migrate, filament:assets, optimize, up, release-check passed with 8 expected warnings after `HERO_ASSET_STATUS=approved` + `db:seed --class=PageSeeder --force`). Production: not executed. Order for production is fixed in the go-live doc §4–6: backup → clone exact hash → `.env` → `deploy.sh` → seeders → admin user → `release-check --strict` → smoke by Host header → DNS → Let's Encrypt → live verification.

## 6. Final hero

- Source: Pexels photo 20695262 "Volvo XC60 on dirt road in winter" by Erik Mclean, 6000×4000 real photograph; inspected at 100 %: no plate (pure side profile), no readable badge, no people, no AI geometry, no compression artefacts. Scene: dark-blue contemporary SUV, misty Nordic coast — realistic, premium, relevant to ordinary owners, not a dealership cliché.
- Desktop crop 1920×1080: car on the right 36–92 % of the frame, bottom at 84 %; left third = blurred mirrored mist (invisible under the ≥ 0.86 wash). `object-fit: cover; object-position: 80% 72%` keeps the zoom ≤ 1.2 at 1440×900. Overlay directional: 0.96 → 0.86 (28 %) → 0.52 (46 %) → 0.26 (62 %+); vertical 0.5 / 0.06 / 0.22 / 0.66.
- Files: `hero-coast.avif` 103 KB (preloaded, `type="image/avif"`), `hero-coast.webp` 154 KB, `hero-coast.jpg` 227 KB, mobile 16:9 `hero-coast-mobile.webp` 45 KB / `.jpg` 65 KB (800×450, below the form, hierarchy unchanged). Explicit `width/height`, CLS 0.
- QA at 1440: car readable between the copy and the form, wheel and front visible right of the card, form clearly dominant, white text on the ≥ 0.86 graphite side. At 1024: car sits left of the form without clashing. At 768/390/375: unchanged mobile hierarchy, card below the fact strip. Compared with Stage 3: car visibility ↑, premium feel ↑ (real Nordic scene vs. template dealership), realism ↑, form prominence =, legibility =.
- OG images and `primaryImageOfPage` remain the brand compositions; release-check blocks any temporary file reference and their presence on disk.

## 7. Legal

Operator facts: Motion Motors OÜ · registry code 17341567 · VAT EE102908916 · Meistri tn 20, Tallinn 13517, Estonia (TZ §12; config `facts`). Footer line in all locales: `Autostvabaks teenuse osutaja: Motion Motors OÜ (registrikood 17341567)` (RU/EN equivalents). JSON-LD Organization: `vatID`, `identifier` (PropertyValue "Registrikood"). Terms §1 and Privacy §6 (ET/RU/EN) carry the same operator block; consistency checked across operator/controller, contact (info@autostvabaks.ee), purposes, form data incl. photos, e-mail notifications, retention wording, rights/AKI, service terms, request nature. P8 ("ei kohusta") stays in Terms §3 only, absent on the landing (test). No machine-translated text was declared approved: **LEGAL_CONTENT_STATUS = draft**, `LEGAL_CONTENT_READY = NO`. Activation = one `.env` line + one seeder run (ADR-33); release-check also verifies that both legal pages resolve 200 in every locale, name the operator in their own text and are linked from the home page (footer + consent line), and scans page content for placeholder markers.

## 8. Form / mail / admin (staging E2E, `45d748d`)

Lead #5: RU form at 390, plate `999S4E`, `utm_source=stage4`, `gclid=G4`, 2 photos (1.3 MB total) → stored, photos on the private disk (`public-reachable: no`), `mail_sent_at` set, `mail_error` null (Mailtrap sandbox), dataLayer `form_start → step1_complete → step2_view → photo_add → submit → success`, no PII in events, 0 console errors, 0 CSP violations (report-only). Attachment cap (10 MB) and the no-photo / invalid-photo paths are covered by tests (`Stage3HardeningTest`, `LeadFormTest`), unchanged in Stage 4. Admin login/leads/photos were verified in Stage 3; no admin code changed. Production E2E: pending deployment (§1).

## 9. Analytics

**ANALYTICS = DISABLED_PENDING_APPROVAL.** No GTM ID, no consent decision → production `.env` ships `ANALYTICS_GTM_ID=` and `ANALYTICS_ENABLED=false`; the dataLayer contract keeps firing (ADR-28/35). Conversion functionality does not depend on analytics.

## 10. SEO technical

Staging (noindex by design): canonical/hreflang/OG per locale unchanged from Stage 3; sitemap 9 URLs (3 home + 6 legal), every `<loc>` 200 and self-canonical (release-check); robots `Disallow: /`; `X-Robots-Tag: noindex, nofollow`; Basic Auth 401. Production simulation (`APP_URL=https://autostvabaks.ee`, `--strict`, local): the only FAIL set is the legal human gate (status + 6 draft markers); with the texts published the check passes. Host normalisation (http/www → canonical https), robots production variant, indexability: covered by tests; live verification commands are in the go-live doc §6.

## 11. Schema / entity

Graph unchanged (Organization = Motion Motors OÜ, Brand = Autostvabaks, WebSite, WebPage, Service, FAQPage 9) + Organization `vatID`/`identifier`. No AutoDealer, ratings, opening hours, phone, temporary image. JSON-LD parsed on staging for all three locales (values verified by grep in §8 live check).

## 12. OpenAI / AI search readiness

Unchanged: public crawlable HTML, direct-answer blocks, no snippet restrictions (release-check), production robots allow `OAI-SearchBot` explicitly, GPTBot allowed by default (owner decision documented, ADR-25). No AI-only files. Server/WAF: reg.ru nginx anti-bot page was observed only on `/admin/login` for curl; public routes answer normally — to be re-verified on the production host after DNS.

## 13. GSC

Not possible before the site is public. Procedure (Domain property via TXT at zone.ee, sitemap submission once, URL inspection for `/`, `/ru`, `/en`, `AUTOSTVABAKS_GSC_T0`) in the go-live doc §8. GSC_PROPERTY / GSC_SITEMAP / GSC_URL_INSPECTION = BLOCKED.

## 14. Performance (staging, Basic Auth, Lighthouse 12, same environment as Stage 3)

| | Perf | A11y | BP | SEO* | LCP | CLS | TBT | Requests | Transfer |
|---|---|---|---|---|---|---|---|---|---|
| Stage 3 mobile | 98 | 100 | 100 | 61 | 1.6 s | 0 | 20 ms | 13 | 119 KB |
| **Stage 4 mobile** | **98** | 100 | 100 | 61 | **1.7 s** | 0 | 50 ms | 13 | 137 KB |
| Stage 3 desktop | 100 | 100 | 100 | 61 | 0.4 s | 0 | 0 ms | 12 | 192 KB |
| **Stage 4 desktop** | **100** | 100 | 100 | 61 | 0.4 s | 0 | 0 ms | 12 | 195 KB |

\*SEO 61 = staging noindex (intended). Mobile LCP element is the hero subtitle text (image not loaded on phones; +18 KB = mobile card); desktop LCP element is the AVIF hero at 0.4 s. Within target (LCP ≤ 2.5 s, CLS < 0.1). Cache (live headers): hero AVIF/WebP `max-age=31536000, immutable` (Apache), CSS/JS via nginx gzip with 45-day max-age (Stage 3), HTML no-cache. Brotli not available on the host (not claimed). No double loading of desktop/mobile hero (data-URI `<img>` below 992 px).

## 15. Accessibility

Lighthouse 100 (mobile + desktop). Hero contrast: copy sits on the ≥ 0.86 graphite side; footer operator line unchanged colour. Single H1 per page, no overflow at 375/390/768/1024 (scroll width = viewport; only the off-canvas menu is wider by design).

## 16. Security

Headers on public pages: nosniff, Referrer-Policy, X-Frame-Options SAMEORIGIN, Permissions-Policy, CSP-Report-Only (nonce + strict-dynamic). `.env`, storage, admin, lead photos: unchanged protections (auth, private disk — verified `public-reachable: no`). `composer audit`: no advisories. No stack traces, debug off in production `.env` template. CSP decision: **keep `report-only` at launch** (ADR-35), enforce after a clean production week.

## 17. DNS / SSL / redirects / mail

Current DNS and mail facts in §3. Required change: A records → 31.31.196.109 (table with before/after in the go-live doc §5); MX/SPF/DMARC untouched. TLS: Let's Encrypt from the panel after DNS. Redirect matrix (http → https, www → apex, RU/EN path retention) is enforced by the application (tests) and listed for live re-verification. **MAIL_DNS_STATUS = SPF+DMARC present (Google), DKIM not found; production must send via Google Workspace SMTP** — a P1 until credentials exist (ADR-34).

## 18. P0 / P1 found and fixed

| Sev | Finding | Fix |
|---|---|---|
| P0 | Hero template asset (licence, Audi badge/plate) still the production candidate | licensed photo, template files deleted, gate verifies files + references |
| P0 | Lead mail from production would be rejected (DMARC p=reject, reg.ru SMTP not in SPF) | documented transport requirement + `.env` template (Google SMTP); blocks MAIL_DELIVERY until credentials |
| P1 | Legal drafts lacked registry code/VAT; approval path was manual admin editing | facts in config/seeder, seeder-owned drafts, one-step approval, release-check legal gates |
| P1 | `HERO_ASSET_STATUS=approved` was a bare switch | contradiction checks (temporary file on disk, missing production file, reference in HTML) |
| P1 | Stale `<link rel=preload>` to the deleted hero would 404 | AVIF preload with `type` |
| P1 | Staging E2E script used deleted files (test tooling) | updated |
| P2 | Release-check did not scan legal pages for placeholders / links | added |

## 19. P2 backlog (post-launch)

Real reviews, phone/messengers, Meistri 20 photo, GTM + consent banner, CSP enforce, Brotli (host), DKIM check in Workspace, mobile page length (ET 390: 9 376 px), GSC-driven page gate (damaged/leasing/valuation pages only with data).

## 20. Production screenshots

`docs/autostvabaks/baseline/production-v1/` is **not created** (no production). Stage-4 staging evidence (13 files, `45d748d`): `docs/autostvabaks/baseline/post-stage4/` — `hero-et-1440.jpg`, `hero-et-1024.jpg`, `hero-et-390.jpg`, `public-{et,ru,en}-{1440,390}.jpg`, `form-step2-modal-{1440,390}.jpg`, `form-success-{1440,390}.jpg`. Page heights 1440: ET 6 168, RU 6 520, EN 6 200; 390: ET 9 376, RU 10 260, EN 9 377.

## 21. Test results

`php artisan test` → **42 passed (626 assertions)**: Stage4ReleaseTest (4) added; Stage-1/3 release-check fixtures updated to name the operator. `composer audit` → clean. `site:release-check` staging → passed (8 warnings: legal gate + analytics). `--strict` production simulation → FAIL only on the legal human gate. Minified twins rebuilt (CSS 43.7 KB).

## 22. Git evidence

`45d748d` feat(autostvabaks): Stage 4 production release candidate — licensed hero, legal operator facts, release gates, go-live procedure (35 files) · this report + evidence as a docs commit. Pushed to `main`; staging `Deployed 45d748d`; worktree clean; `.env` files untouched in git.

## 23. Final acceptance matrix

| Gate | Result | | Gate | Result |
|---|---|---|---|---|
| MASTER_BASELINE_READ | PASS | | CANONICAL | PASS (staging/tests) · live pending |
| HERO_ASSET_APPROVED | PASS | | HREFLANG | PASS · live pending |
| HERO_PRODUCTION_APPLIED | PASS (code/staging) · prod BLOCKED | | ROBOTS | PASS (tests) · live pending |
| HERO_VISUAL_QA | PASS | | SITEMAP | PASS · live pending |
| OG_PRODUCTION_ASSET | PASS | | SCHEMA | PASS |
| LEGAL_CONTENT_APPROVED | BLOCKED (human H1) | | NO_STAGING_URLS | PASS (simulation) |
| LEGAL_OPERATOR_ACCURATE | PASS | | NO_DEMO_ASSETS | PASS (template hero deleted, scan clean) |
| LEGAL_PAGES_LIVE | PASS (staging) · prod BLOCKED | | OAI_SEARCHBOT_ALLOWED | PASS (prod robots variant) |
| PRODUCTION_ENV | PARTIAL (template ready, secrets H5) | | GPTBOT_POLICY_DOCUMENTED | PASS |
| APP_DEBUG_OFF | PASS (template) | | ANALYTICS_STATE | DISABLED_PENDING_APPROVAL |
| STAGING_AUTH_OFF_PROD | PASS (template) | | DATALAYER_NO_PII | PASS |
| SITE_NOINDEX_OFF_PROD | PASS (template) | | CONSENT_STATE | PARTIAL (no vendor tags → no consent needed yet) |
| DB_BACKUP | NOT_APPLICABLE (no prod) · procedure PASS | | GSC_PROPERTY | BLOCKED |
| FILES_BACKUP | NOT_APPLICABLE · procedure PASS | | GSC_SITEMAP | BLOCKED |
| ROLLBACK_READY | PASS (documented) | | GSC_URL_INSPECTION | BLOCKED |
| MIGRATIONS | PASS (staging) | | MOBILE_QA | PASS |
| RELEASE_CHECK_STRICT | PARTIAL (only legal gate fails) | | DESKTOP_QA | PASS |
| ET_LIVE / RU_LIVE / EN_LIVE | PASS (staging) · prod BLOCKED | | RU_QA / EN_QA | PASS |
| FORM_LIVE_E2E | PASS (staging) | | PERFORMANCE | PASS |
| DB_LEAD | PASS | | CWV_LAB_TARGET | PASS |
| PRIVATE_PHOTOS | PASS | | A11Y | PASS |
| MAIL_DELIVERY | PARTIAL (Mailtrap OK; prod SMTP H5) | | SECURITY | PASS |
| ADMIN_LEAD | PASS (Stage 3, unchanged) | | CSP_DECISION | PASS (report-only, ADR-35) |
| MAIL_SIZE_CAP | PASS (tests) | | MAIL_DNS_STATUS | PARTIAL (SPF/DMARC Google; DKIM not found) |
| PHONE_CONDITIONAL | PASS | | ALL_TESTS | PASS (42/626) |
| REVIEWS_CONDITIONAL | PASS | | COMPOSER_AUDIT | PASS |
| CONSOLE_ERRORS | PASS (0) | | BROKEN_LINKS | PASS (16 internal + sitemap 200, maps 200) |
| WORKTREE_CLEAN | PASS | | PRODUCTION_DEPLOYED | NO |
| PRODUCTION_VERIFIED | NO | | READY_FOR_CLIENT_HANDOFF | YES (activation procedure) |

## 24. Post-launch measurement baseline

`AUTOSTVABAKS_GSC_T0` = date of the GSC sitemap submission (to be recorded at activation). Review after 4–6 weeks by query × page × locale × device against `AUTOSTVABAKS_STAGE3_QUERY_COMPONENT_MAP.md`; primary KPI qualified organic seller leads; supporting: non-branded impressions/clicks/CTR/position, form starts, submits, qualified ratio. No new pages before the page gate passes.
