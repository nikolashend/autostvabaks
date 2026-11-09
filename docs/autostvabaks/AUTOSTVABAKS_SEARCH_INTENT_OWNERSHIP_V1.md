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
| Damaged / faulty vehicle sale | yes (condition-specific objections, photos, towing) | NOT YET — first measure demand; start as an answer block on `/` |
| Vehicle under leasing / credit | yes (process with the lessor, payoff) | NOT YET — answer block first |
| Valuation / what affects the offer | partly (informational) | NOT YET — answer block on `/` (valuation transparency slot) |
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
| liisingus auto müük | leasing possible | FAQ item (Stage 4 copy) | Alusta päringut |
| выкуп авто Эстония | same in RU | identical blocks in RU | Начать заявку |
| sell my car Estonia | same in EN | identical blocks in EN | Start request |

Metadata must never promise what the page cannot show: no "best price", no "in 5 minutes", no counts.

## 5. AI / AEO answer component (pattern frozen)

```
QUESTION / INTENT → DIRECT ANSWER (1–2 sentences) → SHORT DETAIL → PROOF (if any) → NEXT ACTION (form)
```
Reserved slots on `/` (Stage 2 layout, Stage 4 copy): what is car buyback · what affects the offer · leasing/credit car · damaged/faulty car · payment · documents/data needed · obligation-free (only if client confirms). Delivered through the FAQ (schema-backed) and short answer blocks — not six long SEO sections.

## 6. Crawler policy (production, later)

`robots.txt`: allow all except `/admin`; `OAI-SearchBot` allowed; `GPTBot` decision deferred to the client. Staging stays fully blocked.
