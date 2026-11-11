# AUTOSTVABAKS — STAGE 3 QUERY → COMPONENT MAP

Post-launch GSC optimisation baseline (2026-11-11). Principle: **one intent → one strongest canonical answer** (`AUTOSTVABAKS_SEARCH_INTENT_OWNERSHIP_V1.md`). No search-volume numbers are listed: no reliable Estonian volume data was available during Stage 3, and inventing them is prohibited. Ranking of queries below follows SERP prevalence (live Google results reviewed 2026-11-11), intent, business relevance and language naturalness.

## 1. Research summary

**ET SERP (`autode kokkuost`, `auto kokkuost`, `autode ost`, `auto väljaost`, `müü auto kiiresti`, `müü oma auto`, `auto hindamine`, `avariilise auto müük`, `liisinguauto müük`)**
- Commercial SERP dominated by direct buyers: Carbuy, NSADAuto, Autorahaks, CarMoney, Autodeost, Maxcar, AutoDiil, OstameAutod, Autoost24, Alsautod, ES Autod, Rallimüük, Ostanautod. Titles are category-first ("Autode kokkuost …", "Autode kokkuost Eestis — müü auto kiirelt, raha kohe"). `üle Eesti` / `Eestis` is a stable modifier.
- `auto hindamine` is mostly informational / mixed (valuation tools, dealers); `avariilise auto müük` and `liisinguauto müük` are served by segment pages of the same buyers → answered on `/` by the direct-answer cards + FAQ, no new URLs (page gate not met).

**RU SERP (`выкуп авто Эстония/Таллинн`, `срочный выкуп авто`, `продать авто быстро`, `продать машину Таллинн`, `автовыкуп Таллинн`, `оценка авто перед продажей`)**
- Same buyers' RU versions (rahaeest24, ostameauto, autoost24, rahaautoeest, carprice, carbuy/ru, esautod/ru) plus Ukrainian-plate niches. Vocabulary: *выкуп авто*, *скупка автомобилей*, *продать машину/авто быстро*, *по всей Эстонии*, *в лизинге*, *после ДТП*.

**EN SERP (`car buyback Estonia`, `sell my car Estonia`, `sell car fast Estonia`, `car valuation Estonia`, `used car buyback Estonia`)**
- Thin: consignment (Nordauto), marketplaces (auto24), generic guides. Few buyers publish a real EN page → `/en` with concise native copy is sufficient.

## 2. Competitor matrix (2026-11-11, homepages fetched live)

| Intent | Competitor strength | Competitor weakness | Autostvabaks opportunity | Target component |
|---|---|---|---|---|
| H1 / message match | Category-first H1s (NSADAuto: "Müü oma auto kiirelt. Raha kätte juba täna."; Carbuy: "Auto rahaks 24 tunniga") | Speed claims not verifiable; CarMoney contradicts itself (90 % vs 85 %) | Natural H1 + category eyebrow, title category-first, no time promise (F11) | hero eyebrow/H1, `<title>` |
| Form friction | Plate + "continue without number" (Carbuy, Autorahaks, CarMoney, NSAD) | Autodeost ≈ 16 fields + 6 photos before contact; Carbuy ≈ 7 steps | 2 steps, 3 required fields in step 1, photos optional | hero form, step-2 modal |
| Speed claims | "15 min", "1 h", "24 h", "same day" everywhere | Unsupported; no process evidence | Honest wording "esimesel võimalusel"; speed = fewer steps, one buyer | form note, steps, FAQ `response` |
| Valuation transparency | CarMoney lists factors; NSAD explains market vs parts value | Most publish "up to 90 %" with no explanation | 7 named factors + expectation message (market value, not asking prices) | `#hindamine`, FAQ `valuation` |
| Leasing content | NSAD, CarMoney, Carbuy: dedicated pages | Autodeost silent; procedures sometimes overpromised | Direct answer card + FAQ, process agreed individually (no invented bank steps) | `#vastused` card 1, FAQ `leasing` |
| Damaged cars | Segment pages (NSAD: damaged / non-running / scrap) | "buy any car" absolutes | "hindame … teeme pakkumise seisukorra põhjal" | `#vastused` card 2, FAQ `damaged` |
| Payment | Cash/transfer at signing (NSAD, Carbuy) | Cash over-promoted | Bank transfer primary, cash on request | `#vastused` card 3, FAQ `payment` |
| Trust / proof | Carbuy: 3 testimonials, Google reviews (CarMoney 29) | Unsourced "#1", "94 %", "21 000 recommend" | Only real reviews (hidden until supplied); legal operator line; address + map | reviews (conditional), footer operator, `#kontakt` |
| Mobile UX | Plate-first forms | Long pages, repeated CTA blocks (CarMoney) | Form above fold, sticky CTA, compact answer cards | hero, sticky CTA |
| FAQ | Present on most | Generic, not objection-based | 9 objections, self-contained answers (AEO) | `#kkk` + FAQPage schema |
| Local relevance | Addresses + phone on all | — | Meistri 20 + map facade; phone pending (P1) | `#kontakt` |
| Original evidence | Stats (unsourced) | No documentary proof | Real photos / reviews when the client supplies them | reviews, location card |

## 3. Query → component map

Columns: query/intent · locale · owner URL · SERP message (title / description) · on-page answer · proof · CTA.

### ET — `/` (owner of the transactional cluster)

| Query / intent | SERP message | On-page answer | Proof | CTA |
|---|---|---|---|---|
| autode kokkuost · auto kokkuost · autode ost | Title "Autode kokkuost üle Eesti – müü oma auto lihtsalt \| Autostvabaks"; description names service, area, conditions, payment | Hero eyebrow "Autode kokkuost üle Eesti" + H1 + subtitle; footer about | Fact strip (F4–F7), legal operator line (F2), address (F3) | Alusta päringut (hero form) |
| auto väljaost · auto müük kiiresti | same title | Steps section "Auto väljaost nelja sammuga" | 4 concrete steps, no time promise | Alusta päringut |
| müü auto kiiresti · müü oma auto | same | H1 "Müü oma auto kiirelt ja mugavalt"; why-card 1 (no listing / strangers) | one buyer, 2-step form | Alusta päringut |
| auto kokkuost üle Eesti · väljaspool Tallinna | description "üle kogu Eesti" | Fact strip 1; FAQ `area` | address + "mitte ainult Tallinn ja Harjumaa" | Alusta päringut |
| auto hindamine · mis mõjutab auto hinda | — (informational, on-page) | `#hindamine`: direct answer + 7 factors + expectation message; FAQ `valuation` | factors named, price optional | Küsi pakkumist |
| avariilise / rikkega auto müük | description "ka rikkega, avariilised" | `#vastused` card 2; FAQ `damaged`; why-card 3 | photos → more precise offer | Alusta päringut |
| liisinguauto müük · liisingus auto müük | description "liisingus autod" | `#vastused` card 1; FAQ `leasing`; fact strip 3 | process agreed individually (no invented procedure) | Alusta päringut |
| auto eest raha · sularaha / ülekanne | description "Makse ülekandega või sularahas" | `#vastused` card 3; FAQ `payment`; fact strip 4 | payment at completion | Alusta päringut |

### RU — `/ru`

| Query / intent | SERP message | On-page answer | Proof | CTA |
|---|---|---|---|---|
| выкуп авто Эстония · автовыкуп Таллинн · скупка автомобилей | Title "Выкуп авто по всей Эстонии – продать машину просто \| Autostvabaks" | Eyebrow "Выкуп автомобилей по всей Эстонии", H1, subtitle | fact strip, operator line | Начать заявку |
| продать авто/машину быстро · срочный выкуп | same | H1 "Продайте машину быстро и без хлопот"; why-card 1; steps "Выкуп авто за четыре шага" | 2-step form, one buyer | Начать заявку |
| выкуп битых авто · после ДТП · неисправный | description | `#vastused` card 2, FAQ `damaged` | photos | Начать заявку |
| продать машину в лизинге / кредите | description "в лизинге" | `#vastused` card 1, FAQ `leasing` | individual process | Начать заявку |
| оценка авто перед продажей | — | `#hindamine` + FAQ `valuation` | factors | Запросить предложение |
| продать авто Таллинн / по всей Эстонии | description | fact strip 1, FAQ `area` | address | Начать заявку |

### EN — `/en`

| Query / intent | SERP message | On-page answer | Proof | CTA |
|---|---|---|---|---|
| car buyback Estonia · used car buyback Estonia | Title "Car buyback across Estonia – sell your car the simple way \| Autostvabaks" | Eyebrow "Car buyback across Estonia", H1, steps "Car buyback in four steps" | fact strip, operator line | Start request |
| sell my car Estonia · sell car fast Estonia / Tallinn | same | H1 "Sell your car quickly and easily"; why-card 1 | 2-step form | Start request |
| car valuation Estonia | — | `#hindamine`, FAQ `valuation` | factors | Ask for an offer |
| sell damaged car Estonia · sell leased car Estonia | description | `#vastused` cards 1–2, FAQ `leasing` / `damaged` | — | Start request |

## 4. Title / description / H1 / opening statement / CTA per locale

| Locale | Primary intent | Title | Meta description | H1 | Opening value statement | Primary CTA |
|---|---|---|---|---|---|---|
| ET | autode kokkuost (transactional) | Autode kokkuost üle Eesti – müü oma auto lihtsalt \| Autostvabaks (64) | Autostvabaks ostab autosid üle kogu Eesti – igas seisukorras, ka rikkega, avariilised ja liisingus autod. Saada lühike päring ja saa pakkumine. Makse ülekandega või sularahas. (170) | Müü oma auto kiirelt ja mugavalt | Üks lühike päring asendab kuulutuse, proovisõidud ja läbirääkimised. Hindame igas seisukorras autosid – ka rikkega, avariilisi ja liisingus olevaid. | Alusta päringut |
| RU | выкуп авто Эстония | Выкуп авто по всей Эстонии – продать машину просто \| Autostvabaks (63) | Autostvabaks выкупает автомобили по всей Эстонии: в любом состоянии, в том числе неисправные, после ДТП и в лизинге. Короткая заявка – предложение – сделка. Оплата переводом или наличными. (180) | Продайте машину быстро и без хлопот | Одна короткая заявка вместо объявления, показов и торга. Оцениваем автомобили в любом состоянии – неисправные, после ДТП и в лизинге. | Начать заявку |
| EN | car buyback Estonia / sell my car Estonia | Car buyback across Estonia – sell your car the simple way \| Autostvabaks (69) | Autostvabaks buys cars all over Estonia – any condition, including faulty, damaged and leased cars. Send a short request, get an offer, close the deal. Payment by bank transfer or cash. (176) | Sell your car quickly and easily | One short request instead of listings, viewings and haggling. We evaluate cars in any condition – faulty, damaged or under leasing. | Start request |

H1 decision (ET): the Stage-2 H1 "Müü oma auto kiirelt ja mugavalt" is kept. It is natural Estonian, matches the `müü oma auto` / `müü auto kiiresti` cluster, and reads as a seller promise rather than a category label. The category phrase `autode kokkuost üle Eesti` is carried by the eyebrow directly above the H1, the `<title>`, the Service schema name and the footer, so exact-match stuffing into the H1 was rejected. RU/EN H1s are native equivalents, not translations.

## 5. Page creation gate — status after Stage 3

| Candidate | QUERY_DEMAND | DISTINCT_INTENT | BUSINESS_VALUE | ORIGINAL_CONTENT | LOW_CANNIBALISATION | Decision |
|---|---|---|---|---|---|---|
| Damaged / faulty car page | unknown (no GSC data yet) | yes | yes | would repeat `#vastused` card 2 + FAQ | risk vs `/` | DO_NOT_CREATE_YET – measure in GSC after launch |
| Leasing car page | unknown | yes | yes | same | risk | DO_NOT_CREATE_YET |
| Valuation guide | unknown (informational) | partly | medium | `#hindamine` already answers | — | DO_NOT_CREATE_YET |
| City pages | — | no | — | — | doorway | DO_NOT_CREATE |

## 6. Post-launch GSC checklist (Stage 4+)

1. Verify property for `https://autostvabaks.ee`, submit `/sitemap.xml`.
2. Compare queries per owner URL with the tables above; a query that lands on the wrong locale page = hreflang / copy issue.
3. Open a candidate page only when the gate in §5 passes with real impressions/clicks for the distinct intent.
4. Re-check title/description CTR per locale after 4–6 weeks; adjust wording, not structure.
