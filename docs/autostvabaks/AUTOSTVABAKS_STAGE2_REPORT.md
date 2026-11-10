# AUTOSTVABAKS — STAGE 2 REPORT

**STAGE_2 = PASS (image asset and client content remain PENDING, non-blocking) · READY_FOR_STAGE_3 = YES** · 2026-11-10

## 1. Executive verdict

The prototype became a brand-specific, conversion-first landing: approved wordmark and magenta/graphite identity applied through tokens, every public component owned by `autostvabaks.css`, Carento `theme.css` gone, Bootstrap purged to 25 KB, fonts self-hosted. New CRO layer: fact strip in the hero, objection-based "why", valuation transparency, direct-answer cards (leasing / damaged / payment), final CTA, mobile sticky CTA, success state with next steps. Stage 1 invariants (routes, hreflang, noindex guards, form contract, analytics, security headers) untouched and still tested. Lighthouse on staging (gzip): mobile 98 / desktop 100 / a11y 100 / best-practices 100. 27 tests, 350 assertions.

## 2. Brand assets used

- Originals (unchanged): `resources/brand/source/svg/logo-av-01…06.svg` (+ eps/png/jpeg), commit `411a804`. Colours read from the SVG fills: `#E6007E`, `#141414`, `#FFFFFF`.
- Web derivatives (svgo + viewBox crop, no redrawing): `public/assets/imgs/brand/logo.svg` (← logo-av-04, light backgrounds), `logo-white.svg` (← logo-av-05, dark backgrounds), `favicon.svg` (the split "a" glyph from logo-av-04 on a white tile, 954 B).
- Variant decision: horizontal wordmark is the primary logo (header 30 px / 26 px phones, footer, admin login). Stacked 01–03 are kept for print/social. No separate compact mark exists in the package; the favicon is the only derived mark. Logo-av-06 (white + graphite on magenta) is not used — no magenta surfaces on the site.
- Component: `resources/views/components/asv-logo.blade.php` (`variant="dark|light"`, explicit width/height → no CLS). `partials/logo.blade.php` delegates to it.

## 3. Design system

- **Tokens** (`:root` in `autostvabaks.css`, mirrored in `config('autostvabaks.brand')`): `--asv-brand-primary #E6007E`, `--asv-brand-primary-hover #C9006E`, `--asv-brand-text #C9006E` (small text on light), `--asv-brand-primary-on-dark #FF4FAE` (small text on graphite), `--asv-brand-dark #141414`, `--asv-surface #FFF`, `--asv-surface-muted #F5F5F7`, `--asv-surface-dark-2 #1F1F22`, `--asv-text #141414`, `--asv-text-muted #5B5B61`, `--asv-border #E4E4E9`, `--asv-success #1B7F3B`, `--asv-warning #9A6700`, `--asv-danger #C8102E`, `--asv-focus #E6007E`.
- **Typography**: Urbanist variable 400–800 (latin + latin-ext, self-hosted) + Manrope Cyrillic subsets under the same family. Scale: display `clamp(2.25rem, 1.4rem+3.2vw, 3.9rem)`, H2 `clamp(1.75rem, 1.25rem+1.9vw, 2.75rem)`, H3 1.25rem/700, lead 1.125rem, body 1rem/1.6, small 0.875rem, xs 0.8125rem, labels 0.875rem/700, buttons 1rem/700. Line length capped (`max-width: 48–68ch`).
- **Spacing / shape**: section padding `clamp(64px, 5vw+32px, 112px)`, gutter 24/16 px, radii 10/14/20/28 px, two shadows (card, float), one easing.
- **Icons**: one 24-px line set (`x-asv-icon`, stroke 1.8, 36 glyphs, inline SVG, no icon font). Brand icons (WhatsApp/Telegram) filled.
- **Motion**: hover/focus transitions 180 ms, accordion/modal (Bootstrap), small reveal on scroll via IntersectionObserver; everything collapses under `prefers-reduced-motion`.
- **Buttons**: `.asv-btn` primary (magenta/white), dark (graphite), ghost, ghost-light; sizes default 48 px / large 56 px.

## 4. Hero

Three compositions were compared: **A** copy → form → visual, **B** form → copy, **C** integrated panel. **A** chosen: natural reading flow (promise before the ask), the form stays the most saturated object on the screen, the car can sit behind the form instead of competing with the headline, and the stacked mobile order follows without extra markup. Desktop grid `1.15fr : 1fr`, form card max 520 px, graphite wash over the image (solid behind the copy, lighter where the car is). Below the grid: a four-cell **fact strip** (F4–F7) on a glass panel — the micro-trust layer. Phones (< 992 px): no backdrop image at all, H1 → short value line → form → 2×2 facts → lazy supporting visual (16:9). At 390 × 844 the plate, phone and e-mail fields and the CTA are inside the first viewport. Eyebrow hidden on phones; H1 2rem. LCP element on mobile is the H1 (text), on desktop the hero image.

## 5. Form UX

- **Step 1**: card head with title + "Samm 1 / 2" label; plate-styled registration input (EST tab, 30 px bold, tolerant of spaces/dashes, hint "Tühikud ja sidekriipsud ei ole olulised"); phone + e-mail side by side (≥ 576 px); desired price marked optional with a hint that it is not binding ("Ei ole kohustuslik – kui Sul on ootus, anna sellest teada"); consent unchecked, link to Terms; 56-px magenta CTA; note "Võtame Sinuga ühendust esimesel võimalusel". Focus ring magenta + 4-px glow; hover border; errors with icon, red border, `aria-invalid`, first invalid field focused.
- **Step 2 modal**: back pill + title + "Samm 2 / 2" + close; vehicle chip with car icon; segmented language; mileage and info marked optional; photo **drop zone** (camera icon, "Lisa fotod", why photos help; hint JPG/PNG/WEBP · up to 10 · up to 8 MB each; drag & drop added; thumbnails with remove); submit with spinner; same note under the button. Focus trap, Escape, focus restoration (verified on staging: focus returns to "Alusta päringut").
- **Success**: green check (semantic colour), title, text, "Mis edasi?" with three factual steps (review → contact via the e-mail/phone given → agree inspection time), close button. No response-time promise.
- **Errors**: 422 per-field mapping preserved; 413/429/network states unchanged; duplicate guard untouched.

## 6. Page architecture (final order)

1 header (logo · anchors · ET/RU/EN pill · CTA · burger) → 2 hero + form + fact strip → 3 how it works (numbered timeline, 4 steps, verbs) → 4 why (4 objection cards: no ads/strangers, all Estonia, any condition incl. leasing, clear deal) → 5 valuation transparency (dark; 7 factors; note "desired price optional"; CTA) → 6 direct answers (3 cards: leasing/credit, damaged/faulty, payment; each question → answer → detail → CTA) → 7 reviews (conditional; grid 1–3, slider > 3) → 8 FAQ (divider list, plus/minus) → 9 location card + click-to-load map → 10 final CTA panel → 11 footer (brand · contact · links · legal · © + top). Ten visual sections, no text walls. Documents answer slot intentionally not filled (process not confirmed).

## 7. Trust / CRO improvements

- The four confirmed facts are visible before the first scroll on every viewport (fact strip) instead of three small ticks on desktop only.
- "Why" no longer lists adjectives; each card answers a seller objection with a register fact.
- Valuation section lowers unrealistic expectations and removes the "I must know my price" blocker (explicit note + optional price hint).
- Leasing and damaged cars — the two highest-intent objections — get direct answers with a CTA each; payment reassurance states bank transfer or cash without over-emphasising cash.
- One dominant action everywhere (`Alusta päringut`): header, hero, steps, valuation, answers, final CTA, sticky bar — all scroll to and focus the same form; tracked per location.
- Success state tells the seller what happens next; sticky CTA catches readers who scrolled past the form.
- Nothing unsupported was published: no "no obligation" claim (needs client confirmation, §67), no opening hours, no phone, no review schema.

## 8. Multilingual QA

ET/RU/EN share one template and one component structure (`BrandFrontendTest::test_conversion_components_render_in_every_locale`). RU strings (+20–30 %) checked at 390/768/1024/1440: Cyrillic renders in Manrope under the Urbanist family, no clipped cards (no fixed heights), longest RU titles wrap inside answer cards and fact cells, RU checkbox label wraps to two lines without overflow. EN verified at 1440/390. Language switcher: desktop pill dropdown with the current item marked (`aria-current`), mobile segmented row.

## 9. Mobile / tablet / desktop QA

- 375/390: no horizontal overflow (measured: all elements ≤ viewport; an early overflow caused by the plate input's intrinsic width was fixed with `width:0` and `minmax(0,1fr)` tracks); form in first viewport; sticky CTA appears after the form leaves view, hides under the modal/menu and near the footer; footer bottom padding reserves space for it; safe-area insets respected.
- 768: stacked hero, form capped at 640 px, 2-column steps/benefits/factors, 2×2 facts.
- 1024: two-column hero, car behind the form; nav collapses to burger (< 1200), CTA stays visible (≥ 768).
- 1440: full composition; fact strip four columns; answer cards three columns.
- Modal on phones is full-screen with safe-area footer padding; photos grid 3 per row.

## 10. Performance

Same Lighthouse version/flags as Stage 1 (lab, simulated throttling). Local = PHP dev server, no gzip; staging = nginx + gzip, Basic Auth header passed.

| Run | Perf | A11y | BP | LCP | CLS | TBT | Requests | Transfer |
|---|---|---|---|---|---|---|---|---|
| Stage 1 local mobile | 69 | 100 | 100 | 5.2 s | 0.034 | 0 ms | 10 | 758 KB |
| **Stage 2 local mobile** | **92** | 100 | 100 | 2.9 s | 0.011 | 170 ms | 14 | 365 KB |
| Stage 1 local desktop | 97 | 100 | 100 | 1.0 s | 0.041 | 0 ms | 10 | 826 KB |
| **Stage 2 local desktop** | **100** | 100 | 100 | 0.6 s | 0.001 | 0 ms | 13 | 433 KB |
| **Stage 2 staging mobile** | **98** | 100 | 100 | 1.7 s | 0 | 70 ms | 13 | 131 KB |
| **Stage 2 staging desktop** | **100** | 100 | 100 | 0.4 s | 0.001 | 0 ms | 12 | 199 KB |

Payload changes: `theme.css` 264 KB → removed; Bootstrap 232 KB → 25 KB; Google Fonts (2 third-party requests) → self-hosted 28 KB latin variable (+17 KB latin-ext only when needed); hero desktop image only ≥ 992 px, phones load a 34 KB supporting WebP lazily; Swiper only > 3 reviews; third-party requests before interaction: 0. Remaining advice (P2): a smaller mobile variant of the supporting image (16 KB), minified copies of `autostvabaks.css/js` (2 KB gzipped), longer cache TTL on hosting. Staging SEO score 61 is the intended noindex/robots state.

## 11. Accessibility

Lighthouse a11y 100 on mobile and desktop (local and staging). Contrast audited for the new palette: brand magenta is 4.6:1 on white (OK for buttons/large text) but 4.1:1 on graphite and 4.15:1 on the muted surface → small text uses `--asv-brand-text` (#C9006E, ≥ 5:1) on light and `--asv-brand-primary-on-dark` (#FF4FAE, 6.1:1) on dark; placeholders #8A8A91 (3.9:1 — placeholder, label always present); muted body #5B5B61 (7:1). Keyboard: visible 3-px magenta focus ring everywhere, FAQ buttons, language menu, sticky link gets `tabindex=-1` while hidden, modal trap/Escape/restore verified, mobile menu dialog with focus on close. Touch targets ≥ 42 px (pills 42, nav rows 48+, bullets 24 with 40-px active). Headings: one H1, H2 per section, H3 cards/questions. Reduced motion honoured.

## 12. Template debt

Removed: `public/assets/css/theme.css` (264 KB), `public/assets/imgs/template/*` (1.1 MB, 130 files), `imgs/page/*`, `imgs/page-header/*` (banner no longer used on inner pages). Retained: Bootstrap 5 as a generated subset (grid, modal, collapse/accordion, dropdown, spinner, `visually-hidden`, `d-none`, `pt-0`, `mb-0`, `gx-3`, `col-sm-6`) — regenerated by `scripts/purge-bootstrap.cjs` from the rendered pages with a runtime safelist; Bootstrap JS bundle (80 KB, modal/collapse/dropdown behaviour and a11y) and Swiper files (conditional). The ThemeForest zip in the project root is untouched (git-ignored). No `.btn-*`, `.header-*`, `.menu-footer` or other Carento classes remain in templates.

## 13. Images

**Decision:** the current hero (`hero.webp/jpg`, `hero-mobile.*`) is a template-package image (licence not production-grade, visible Audi badge and plate). It stays as a *temporary* backdrop: heavily graded on desktop, hidden on phones, used below the form as a supporting visual on phones/tablets. No decorative images were added anywhere else; the map facade is the location proof. OG image still points to `hero.jpg` (swap together with the hero).

**Replacement brief (generate or shoot one asset, two crops):**

- *Scene*: a contemporary European car (e.g. mid-size estate or SUV, 2–6 years old, dark grey / silver / deep blue — never pink or lime) parked in a calm, modern service or showroom forecourt at dusk; subtle wet asphalt reflections; no people in the frame or only an out-of-focus handshake / keys in the lower right.
- *Subject position*: car occupies the right 55 % of the frame, three-quarter front view from the left, headlights off or soft; left 45 % is calm dark background (wall, blurred glass) so white headline text stays legible; keep the lower 18 % of the frame simple (fact strip overlays it).
- *Camera*: 35–50 mm equivalent, eye level slightly below the bonnet line, no wide-angle distortion.
- *Lighting*: soft, directional, cool-neutral (5500–6500 K) with a faint warm rim light; overall exposure low-key — the image will be multiplied with a graphite gradient; avoid blown highlights.
- *Environment*: neutral, premium, Nordic; no dealership flags, no price tags, no signage, no visible brand logos on the building, **no readable or fake number plate** (blank plate holder or angle that hides it), no badges on the car or retouched off.
- *Colour*: desaturated graphite/charcoal palette with one cool accent at most; must sit next to #E6007E without clashing.
- *Desktop deliverable*: 1920 × 893 px (2.15:1), WebP ≤ 180 KB + JPEG fallback, `object-position` reference "car right of centre".
- *Mobile deliverable*: 900 × 1200 px (3:4) crop of the same scene with the car centred and the lower 40 % calm, WebP ≤ 90 KB + JPEG; used below the form at 16:9 (object-position 50 % 72 %).
- *Forbidden*: supercars/sports cars, racing stripes, neon, motion blur, rain streaks over the lens, stock-photo smiles, documents with readable text, any element that could read as a real location or dealer.
- *Later documentary assets (client only)*: Meistri 20 facade 1200 × 800, inspection photo 800 × 600, handover 800 × 600 — rendered in the location card and the "how it works" steps when supplied; never AI-generated.

## 14. Tests

- `php artisan test` → **27 passed (350 assertions)**: `LeadFormTest` (14), `SeoFoundationTest` (9, reviews test now covers grid vs slider), `BrandFrontendTest` (4: approved logo/favicon served, no lime/theme.css/Google Fonts, components + single H1 in every locale, prohibited-claim scan).
- `php artisan site:release-check` → local: pass with warnings; staging (in `deploy.sh`): pass with the 6 expected draft-page warnings.
- Headless Chrome (staging): map click-to-load (0 → 1 iframe), Back restores focus to "Alusta päringut", real submission → lead #2 stored with `utm_source=google`, `gclid=G123`, mail sent (no mail error), dataLayer `map_load → form_start → step1_complete → step2_view → … → submit → success`, no PII, no console errors. Local mocked flow at 1440 and 390: errors → step 2 → success, events in order.
- Overflow scan at 390 (ET/RU): no element wider than the viewport except the off-canvas menu.

## 15. P0 / P1 found and fixed

| Sev | Finding | Fix |
|---|---|---|
| P0 | Plate input's intrinsic width (20 chars × 30 px) widened the grid on phones → horizontal clipping of copy and form | `width:0` on the input, `minmax(0,1fr)` tracks |
| P1 | Brand magenta fails AA for 13-px text on graphite/muted | text tokens `--asv-brand-text` / `--asv-brand-primary-on-dark` |
| P1 | Latin font requested twice (preload with cache-busting query vs `@font-face` URL) | preload uses the plain asset URL |
| P1 | `Testimonial::published()` hid real reviews without a title (`NULL NOT LIKE`) | null-safe filter + test |
| P1 | Swiper loaded for a single review | grid for 1–3, carousel only > 3 |
| P1 | Template feeling: Carento header/footer/button classes, lime palette, text logo | own components, tokens, approved logo |
| P1 | Google Fonts third-party request on every view | self-hosted WOFF2 |
| P1 | Inner page header used the template banner image | graphite page head, banner assets deleted |
| P1 | Mobile: no CTA after scrolling past the form | sticky CTA with visibility rules |

## 16. Deferred to Stage 3

Final hero/location photography or generated image per §13 brief · phone/WhatsApp/Telegram (conditional UI ready) · real reviews (component ready) · final legal texts and the "no obligation" statement (client confirmation) · documents answer slot copy · final SEO/AEO copy and metadata · minified CSS/JS copies and cache headers on hosting · smaller mobile variant of the supporting image · legal-entity line in the footer (placement to confirm) · optional static map tile · HTML sanitiser for admin page content · CSP audit.

## 17. Screenshots

`docs/autostvabaks/baseline/post-stage2/`: `hero-et-1440.jpg`, `hero-et-390.jpg`, `public-et-1440.jpg`, `public-et-390.jpg`, `public-et-768.jpg`, `public-et-1024.jpg`, `public-ru-1440.jpg`, `public-ru-390.jpg`, `public-en-1440.jpg`, `public-en-390.jpg`, `form-step1-errors-1440.jpg`, `form-step1-errors-390.jpg`, `form-step2-modal-1440.jpg`, `form-step2-modal-390.jpg`, `form-success-1440.jpg`, `form-success-390.jpg`, `sticky-cta-390.jpg`, `mobile-menu-390.jpg`, `page-terms-et-1440.jpg`. Before: `baseline/post-stage1/`. Admin: only the login logo changed (verified visually, not stored).

## 18. Git evidence

`411a804` chore(brand): approved logo sources · **`15ea511` feat(autostvabaks): Stage 2 premium brand frontend, CRO layer and performance hardening** (187 files, +2 593 / −14 455) · this report committed as a follow-up docs commit. Deployed to staging with `deploy.sh` (`Deployed 15ea511`), release check passed. Worktree clean after the docs commit.

## 19. Acceptance matrix

| Gate | Result |
|---|---|
| MASTER_BASELINE_READ | PASS |
| APPROVED_BRAND_ASSETS | APPLIED |
| TEMP_LIME_BRANDING | REMOVED_FROM_PUBLIC_BRAND (test) |
| APPROVED_LOGO | APPLIED |
| DESIGN_SYSTEM | PASS |
| HERO_PREMIUM | PASS (image asset temporary, brief delivered) |
| FORM_ABOVE_FOLD | PASS (1440/1024/768/390) |
| FORM_VISUAL_UX | PASS |
| STEP2_MODAL | PASS |
| MOBILE_375_390 | PASS |
| TABLET_768_1024 | PASS |
| DESKTOP_1440 | PASS |
| ET_RU_EN_PARITY | PASS (test) |
| TRUST_LAYER | PASS |
| VALUATION_SLOT | PASS |
| LEASING_DAMAGED_PAYMENT_LAYER | PASS |
| REVIEWS_CONDITIONAL | PASS (test) |
| CONTACT_CONDITIONAL | PASS (test) |
| MOBILE_STICKY_CTA | PASS |
| SUCCESS_ERROR_STATES | PASS |
| FONT_STRATEGY | PASS |
| TEMPLATE_DEBT_REDUCED | PASS |
| THIRD_PARTY_COST | CONTROLLED (0 before interaction) |
| A11Y_NO_REGRESSION | PASS (100) |
| FUNCTIONAL_NO_REGRESSION | PASS (tests + staging E2E) |
| SEO_FOUNDATION_NO_REGRESSION | PASS (tests + live headers) |
| ALL_EXISTING_TESTS | PASS (27 / 350) |
| VISUAL_EVIDENCE | PASS |
| WORKTREE_CLEAN | PASS |
| READY_FOR_STAGE_3 | YES |

## 20. Stage-3 handoff — CONTENT × SEO/AEO/AI × ANALYTICS × PRODUCTION HARDENING

**Read first:** master baseline (§13, §16 updated), this report, `AUTOSTVABAKS_SEARCH_INTENT_OWNERSHIP_V1.md`, `AUTOSTVABAKS_FACT_CLAIM_REGISTER_V1.md`.

**Preserve:** everything in master §15–16; DOM contract (`#lead-step1`, `#lead-step2`, `#lead-modal`, `[data-field]`, `[data-sticky-cta]`, `[data-focus-form]`, `window.ASV`, dataLayer events); section ids `paring · kuidas-tootab · miks · hindamine · vastused · tagasiside · kkk · kontakt`; tokens and components.

**Stage 3 scope:** final ET/RU/EN copy inside the existing components (hero, facts, why, valuation, answers, FAQ, success) with the claim register as the gate; titles/descriptions per locale; FAQ expansion only for real objections (schema follows automatically); documents answer when confirmed; "no obligation" line when confirmed; analytics vendor tag via admin head scripts + GTM event mapping for the existing dataLayer contract; production `.env` and `site:release-check --strict` on the production host; cache/compression headers; hero/location assets per §13; llms.txt/GPTBot decisions remain out of scope unless re-decided.

**Inputs still required from the client (non-blocking):** phone/WhatsApp/Telegram, real reviews, final legal texts, photos, confirmation of the no-obligation statement and footer legal line.
