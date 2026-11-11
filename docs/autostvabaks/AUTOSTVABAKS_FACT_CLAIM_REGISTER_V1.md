# AUTOSTVABAKS — FACT / CLAIM REGISTER V1

Source of truth for every statement in copy, metadata, schema and ads. Classes: **CONFIRMED** (client brief / legal reality) · **CLIENT_APPROVED** (wording accepted by client) · **PROOF_REQUIRED** (may be used only with evidence) · **PROHIBITED**. Where a fact lives in code: `config/autostvabaks.php` (`facts`, `settings` defaults) or admin → Settings.

## 1. Confirmed facts

| # | Fact | Where used | Source |
|---|---|---|---|
| F1 | Brand name "Autostvabaks" | everywhere | client |
| F2 | Legal operator & data controller "Motion Motors OÜ" | Privacy, Terms, JSON-LD Organization | client |
| F3 | Address Meistri 20, Tallinn 13517 | contact, footer, schema | client |
| F4 | Service area: whole Estonia | hero ticks, why, schema areaServed | client |
| F5 | Buys cars in any condition, incl. damaged/faulty | hero, why, FAQ | client |
| F6 | Buys cars under leasing/credit – request accepted, exact settlement process agreed individually (no bank procedure invented) | hero subtitle, fact strip, why, answers card, FAQ `leasing` | client |
| F7 | Payment: bank transfer or cash | hero ticks, steps, why | client |
| F8 | Public e-mail info@autostvabaks.ee | contact, footer, schema | client |
| F9 | Lead e-mail alan@autostvabaks.ee (not public) | mail only | client |
| F10 | Languages ET/RU/EN, manual switch | header | client |
| F11 | Response wording "Võtame Sinuga ühendust esimesel võimalusel" (no time promise) | form note, success, steps, FAQ `response` | client |
| F13 | Offer depends on market price of similar cars in Estonia + actual condition; main factors: make/model/year, mileage, technical condition, damage, equipment, service history, demand; final offer after inspection; not the full internal method | `#hindamine`, FAQ `valuation` | client brief (Stage 2/3) |
| F14 | Direct buyback = simplicity/speed, offer follows market value not asking prices (expectation message; no margin discussion, no "maximum price" claim) | `#hindamine` expectation | Stage-3 TZ §27 |
| F15 | Bank transfer is the normal method, cash possible on request | answers card 3, FAQ `payment` | client (F7) |
| F12 | E-mail required; price, photos optional | form | client |

## 2. Pending facts (must not be invented)

| # | Fact | Current handling |
|---|---|---|
| P1 | Public phone number | empty → no tel: link, no "call", no schema telephone |
| P2 | WhatsApp number | empty → no button |
| P3 | Telegram | empty → no button |
| P4 | Company registry code / VAT (for legal pages) | not shown; legal pages are drafts |
| P5 | Opening hours | deliberately none (client: do not show) |
| P6 | ~~Approved logo / colours~~ | **APPLIED** in Stage 2 (ADR-17) |
| P7 | Final legal texts (Terms, Privacy) | seeded drafts marked "Mustand"; `LEGAL_CONTENT_STATUS=draft` blocks production (ADR-26). **LEGAL_CONTENT_READY = NO** |
| P8 | "Päringu saatmine ei kohusta autot müüma" (no-obligation statement) | **not published** on the landing until the client confirms; the seeded draft Terms contain the sentence – final Terms and landing must agree. Test asserts the landing does not contain "ei kohusta" |
| P9 | Documents needed for the deal | unknown → no documents answer (slot stays empty), FAQ does not mention documents |
| P10 | Hero visual licence / brand asset | template photo, `HERO_ASSET_STATUS=temporary` blocks production; brief in Stage-2 report §13 |
| P11 | Real reviews, real Meistri 20 photo | not supplied; reviews hidden, map facade kept |

## 3. Proof-required claims (allowed only with evidence)

| Claim | Required proof |
|---|---|
| "X cars bought" / years in business | client statement + public register |
| Customer reviews | real, identifiable reviews published by admin (no placeholders) |
| "Offer within N hours" | measured process; otherwise keep F11 wording |
| Photos of team/location | real photos supplied by client |

## 4. Prohibited / unsupported

#1 · best price · highest price · guaranteed purchase · fastest in Estonia · guaranteed five-minute (or any-minute) reply · "hundreds/thousands of purchases" · invented experience figures · fake reviews/ratings/AggregateRating · opening hours · temporary phone numbers · "we call you" while P1 is pending.

## 5. Copy audit (Stage 3, 2026-11-11)

- Final ET/RU/EN `site.php` and the 9 FAQ records were written against F1–F15 only. Scanner (`config('autostvabaks.prohibited_claims')`, 26 fragments in three languages) runs in `BrandFrontendTest` and in `site:release-check` on every rendered home page: no hits.
- "Kiire vastus" removed; every response statement uses F11. No "we call you" while P1 is pending (`SeoFoundationTest`). No counts, years, percentages, "nr 1", "parim/kõrgeim hind", "garanteeritud", "kiireim", "tasuta hindamine".
- Why-cards answer objections with facts (no adjectives such as "usaldusväärne", "premium").
- Draft legal pages: text unchanged, still marked "Mustand/Черновик/Draft"; the draft Terms sentence "ei kohusta" is the only place where P8 appears (not on the landing).

## 6. Previous copy audit (Stage 1)

- ET/RU/EN `site.php` strings reviewed: no prohibited claims found. "Kiire vastus / Быстрый ответ / Fast reply" (why block) is acceptable only with F11 wording underneath (it is). Stage 4 may soften to "Vastame esimesel võimalusel".
- Contact lead text now conditional: "write or call" only with P1 confirmed.
- Legal drafts: name Motion Motors OÜ as controller (correct) — replace with final texts (P7).
