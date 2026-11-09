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
| F6 | Buys cars under leasing/credit | FAQ (copy Stage 4) | client |
| F7 | Payment: bank transfer or cash | hero ticks, steps, why | client |
| F8 | Public e-mail info@autostvabaks.ee | contact, footer, schema | client |
| F9 | Lead e-mail alan@autostvabaks.ee (not public) | mail only | client |
| F10 | Languages ET/RU/EN, manual switch | header | client |
| F11 | Response wording "Võtame Sinuga ühendust esimesel võimalusel" (no time promise) | form note, success, steps | client |
| F12 | E-mail required; price, photos optional | form | client |

## 2. Pending facts (must not be invented)

| # | Fact | Current handling |
|---|---|---|
| P1 | Public phone number | empty → no tel: link, no "call", no schema telephone |
| P2 | WhatsApp number | empty → no button |
| P3 | Telegram | empty → no button |
| P4 | Company registry code / VAT (for legal pages) | not shown; legal pages are drafts |
| P5 | Opening hours | deliberately none (client: do not show) |
| P6 | Approved logo / colours | prototype tokens; `brand.assets_status = PENDING` |
| P7 | Final legal texts (Terms, Privacy) | seeded drafts marked "Mustand"; release-check warns |

## 3. Proof-required claims (allowed only with evidence)

| Claim | Required proof |
|---|---|
| "X cars bought" / years in business | client statement + public register |
| Customer reviews | real, identifiable reviews published by admin (no placeholders) |
| "Offer within N hours" | measured process; otherwise keep F11 wording |
| Photos of team/location | real photos supplied by client |

## 4. Prohibited / unsupported

#1 · best price · highest price · guaranteed purchase · fastest in Estonia · guaranteed five-minute (or any-minute) reply · "hundreds/thousands of purchases" · invented experience figures · fake reviews/ratings/AggregateRating · opening hours · temporary phone numbers · "we call you" while P1 is pending.

## 5. Current copy audit (Stage 1)

- ET/RU/EN `site.php` strings reviewed: no prohibited claims found. "Kiire vastus / Быстрый ответ / Fast reply" (why block) is acceptable only with F11 wording underneath (it is). Stage 4 may soften to "Vastame esimesel võimalusel".
- Contact lead text now conditional: "write or call" only with P1 confirmed.
- Legal drafts: name Motion Motors OÜ as controller (correct) — replace with final texts (P7).
