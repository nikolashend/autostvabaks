# AUTOSTVABAKS — SEARCH INTENT OWNERSHIP V1

Principle: **one intent → one strongest canonical answer.** Qualified seller leads > traffic. No page per synonym.

## 1. Ownership map

| Owner URL | Intent cluster (examples) | Language |
|---|---|---|
| `/` (ET root) | autode kokkuost · auto kokkuost · auto väljaost · müü auto kiiresti · müü oma auto · kasutatud auto väljaost · sõiduki kokkuost · auto müük kiiresti · autode kokkuost üle Eesti · avariilise auto müük (until a dedicated page is justified) · liisingus auto müük (same) | ET |
| `/ru` | выкуп авто Эстония · скупка автомобилей Таллинн/Эстония · продать авто быстро · выкуп битых авто · продать машину в лизинге | RU |
| `/en` | sell my car Estonia · car buyback Estonia · sell car fast Tallinn · we buy any car Estonia · sell damaged car Estonia | EN |
| `/kasutustingimused`, `/privaatsuspoliitika` (+ RU/EN) | legal — indexable but no commercial intent | ET/RU/EN |

Synonyms are covered **on the owner page** via H1/H2, hero copy, answer components and FAQ — not via extra URLs.

## 2. Future candidate pages (ONLY when the gate passes)

| Candidate | Distinct intent? | Status |
|---|---|---|
| Damaged / faulty vehicle sale | yes (condition-specific objections, photos, towing) | NOT YET — answer block + FAQ live on `/` (Stage 3); measure in GSC first |
| Vehicle under leasing / credit | yes (process with the lessor, payoff) | NOT YET — answer block + FAQ live on `/` (Stage 3) |
| Valuation / what affects the offer | partly (informational) | NOT YET — `#hindamine` + FAQ live on `/` (Stage 3) |
| Fast-sale guide | informational | NOT YET |
| City pages (Tartu, Pärnu …) | **no** — same service, same process | DO_NOT_CREATE (doorway) |
| Make/model pages | **no** | DO_NOT_CREATE |

## 3. Page creation gate (mandatory)

```
QUERY_DEMAND  +  DISTINCT_INTENT  +  BUSINESS_VALUE
+ ORIGINAL_USEFUL_CONTENT  +  LOW_CANNIBALISATION_RISK   → create
any one fails                                            → DO_NOT_CREATE_PAGE
```
Also required: a real Search Console / Ads query signal, a unique answer the owner page cannot host without becoming a text wall, and a CTA to the same form. Admin "Pages" with `is_indexable` is the implementation path; no code.

## 4. SERP promise → page proof

| Query / intent | SERP promise (title/description) | Visible proof on page | CTA |
|---|---|---|---|
| autode kokkuost üle Eesti | nationwide car buyback, any condition | hero ticks (any condition · all Estonia · transfer/cash) · "why" block · address + map | Alusta päringut |
| müü oma auto kiiresti | fast, simple request | form above the fold · 4-step process · "Võtame ühendust esimesel võimalusel" (no time promise) | Alusta päringut |
| avariilise auto müük | damaged cars accepted | "Ostame iga auto … rikkega ja avariilisi" · FAQ item | Alusta päringut |
| liisingus auto müük | leasing possible | `#vastused` card 1 + FAQ `leasing` (Stage 3 copy) | Alusta päringut |
| auto hindamine / mis mõjutab hinda | transparent valuation | `#hindamine` (7 factors + expectation message) + FAQ `valuation` | Küsi pakkumist |
| выкуп авто Эстония | same in RU | identical blocks in RU | Начать заявку |
| sell my car Estonia | same in EN | identical blocks in EN | Start request |

Metadata must never promise what the page cannot show: no "best price", no "in 5 minutes", no counts.

## 5. AI / AEO answer component (pattern frozen)

```
QUESTION / INTENT → DIRECT ANSWER (1–2 sentences) → SHORT DETAIL → PROOF (if any) → NEXT ACTION (form)
```
Slots on `/` after Stage 3: what is car buyback (hero + steps) · what affects the offer (`#hindamine`, FAQ) · leasing/credit car (`#vastused`, FAQ) · damaged/faulty car (`#vastused`, FAQ) · payment (`#vastused`, FAQ) · response / photos / area / how (FAQ). **Empty by decision:** documents (P9 unconfirmed), obligation-free (P8 unconfirmed). Every FAQ answer names the service ("Autostvabaks …") so it stays understandable when extracted alone. Full query → component table: `AUTOSTVABAKS_STAGE3_QUERY_COMPONENT_MAP.md`.

## 6. Crawler policy (production, later)

Implemented in Stage 3 (ADR-25): production `robots.txt` = `User-agent: *` / `Disallow: /admin`, explicit `User-agent: OAI-SearchBot` / `Allow: /`, `Sitemap:`; `GPTBot` follows `*` (allowed) until the client decides otherwise. No snippet/preview restrictions (release-check scans for `nosnippet`, `max-snippet`, `noimageindex`). Staging stays `Disallow: /` + noindex + Basic Auth. Stage-4 verification item: fetch `https://autostvabaks.ee/robots.txt` after the DNS switch and confirm the production variant is served.
