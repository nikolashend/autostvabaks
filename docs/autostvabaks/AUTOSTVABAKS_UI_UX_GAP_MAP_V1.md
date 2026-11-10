# AUTOSTVABAKS — UI / UX / CRO GAP MAP V1 (input for Stage 2)

Baseline screenshots: `docs/autostvabaks/baseline/pre-stage1/`. Priorities: P1 = fix in Stage 2 first, P2 = polish, P3 = later/optional.

**Stage 2 status (2026-11-10, evidence in `baseline/post-stage2/`):** G1 CLOSED (approved logo + magenta/graphite tokens, template look replaced) · G2 PARTIAL (new composition, mobile hierarchy; image itself still temporary → brief in `AUTOSTVABAKS_STAGE2_REPORT.md` §13) · G3 CLOSED for Stage 2 (fact strip, objection-based "why", location card; real photos pending) · G4 CLOSED (valuation section + answer cards for leasing/damaged/payment; documents slot reserved) · G5 CLOSED (grid 1–3, slider > 3) · G6 CLOSED (sticky CTA) · G7 CLOSED (type scale tokens, self-hosted fonts) · G8 CLOSED (timeline markers, vertical on phones) · G9 PARTIAL (clean footer; legal-entity line still needs client confirmation) · G10 OPEN (client assets) · G11 CLOSED (`theme.css` removed, Bootstrap purged) · G12 OPEN (P3) · G13 CLOSED (what-happens-next) · G14 CLOSED (restyled).

## 1. What is already strong (PRESERVE)

- Form in the hero on every viewport; step 1 is 4 fields + consent; step 2 modal keeps data on Back.
- Per-field localized errors, success state, photo previews with removal, client-side downscale.
- Clean 4-step process block, benefits grid, FAQ accordion, contact with address proof.
- Language switcher without auto-redirect; header CTA; mobile menu; floating messengers (conditional).
- Accessibility: labels, skip link, alert containers, focus management, Lighthouse a11y 100.

## 2. Gaps

| # | Area | Gap | Priority |
|---|---|---|---|
| G1 | Brand | Lime/green prototype palette, placeholder car-mark logo, generic Carento look ("template feeling"). Approved magenta #E6007E / graphite #141414 + SVG logo not applied. | P1 |
| G2 | Hero | Stock-style background; no real product/location photo; text hierarchy OK but generic. Mobile hides eyebrow/subtitle (<576) — verify value proposition still reads. | P1 |
| G3 | Trust layer | Only generic benefit icons. Missing: valuation transparency block, leasing/damaged answers, legal/location proof card (Motion Motors OÜ + address), real photos. No decorative badges allowed. | P1 |
| G4 | Answer components | Reserved slots (what is buyback · what affects offer · leasing · damaged · payment · documents) exist only as FAQ items; need compact answer blocks with CTA. | P1 (layout) / Stage 4 (copy) |
| G5 | Reviews | Hidden until real reviews exist (correct). Design for 1–3 reviews, not only carousel; show source/date when available. | P2 |
| G6 | Mobile CTA strategy | No sticky bottom CTA after scrolling past the hero form; floating messengers appear only when configured. Decide: sticky "Alusta päringut" bar below fold. | P1 |
| G7 | Typography/spacing | Urbanist via Google Fonts; sizes/spacing inherited from theme; headings fine but section rhythm uneven (tint sections). Self-host fonts, define type scale tokens. | P2 |
| G8 | Steps block | Arrow pseudo-elements fragile on tablet widths (2-column); number badges small. | P2 |
| G9 | Footer | Four columns OK; "Kasulik info" row appears only with info pages; add legal entity line (Motion Motors OÜ) once client confirms placement. | P2 |
| G10 | Images | No images except hero; need: hero desktop 1920×900 + mobile 900×1200 (real car/handover), 1 location/team photo 1200×800, optional 3 process photos 800×600. Deliver WebP + JPG fallback. | P1 (spec) / client (assets) |
| G11 | Performance | `theme.css` 264 KB (97 % unused) + Bootstrap 232 KB; purge/replace during redesign. | P2 |
| G12 | Map | Facade done; consider static map tile (needs API key) for premium feel. | P3 |
| G13 | Success state | Modal success text only; add "what happens next" (3 lines) and optional messenger links. | P2 |
| G14 | 404 | Plain; fine. | P3 |

## 3. Stage 2 image specification

| Image | Purpose | Ratio / size | Crop |
|---|---|---|---|
| Hero desktop | emotional proof (car handover / keys) | 1920×900 (≈2.13:1), ≤180 KB WebP | subject right/centre; left 45 % stays calm for form overlay on lg+ |
| Hero mobile | same | 900×1200 (3:4), ≤90 KB | subject top; bottom 40 % calm |
| Location / team | trust | 1200×800 (3:2) | real office at Meistri 20 |
| Process ×3 (optional) | steps | 800×600 (4:3) | request → inspection → payment |
| OG image | sharing | 1200×630 | brand + headline |

No stock photos presented as "our team". All images are client-supplied or clearly generic backgrounds.
