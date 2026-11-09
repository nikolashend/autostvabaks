# AUTOSTVABAKS — ROUTE / INDEXATION MATRIX V1

Hosts: **production** `https://autostvabaks.ee` (SITE_NOINDEX=false) · **staging** `https://autostvabaks.adme.ee` (SITE_NOINDEX=true + Basic Auth → everything below becomes `noindex,nofollow` + 401 without credentials).

| Route | Method | Name | Locale | Index (prod) | Canonical | hreflang | Sitemap | Auth | Notes |
|---|---|---|---|---|---|---|---|---|---|
| `/` | GET | `home` | et | YES | self | et/ru/en + x-default=`/` | YES | — | Owner of ET transactional cluster |
| `/ru` | GET | `l.home` | ru | YES | self | same set | YES | — | RU owner |
| `/en` | GET | `l.home` | en | YES | self | same set | YES | — | EN owner |
| `/{slug_et}` | GET | `page` | et | if `is_active && is_indexable` | self | per-page set | if indexable | — | Legal + future info pages |
| `/ru/{slug_ru}` · `/en/{slug_en}` | GET | `l.page` | ru/en | same rule | self | per-page set | same | — | 404 when slug belongs to another locale |
| `/paring` · `/{locale}/paring` | POST | `leads.store` | any | n/a | — | — | NO | throttle `leads` | JSON only, CSRF-exempt |
| `/sitemap.xml` | GET | `sitemap` | — | n/a | — | — | — | — | Home ×3 + indexable pages, with xhtml:link alternates |
| `/robots.txt` | GET | `robots` | — | n/a | — | — | — | — | prod: `Disallow: /admin` + Sitemap; staging: `Disallow: /` |
| `/admin/**` | * | filament | ru UI | **NO** (`X-Robots-Tag`) | — | — | NO | Filament auth | login throttled |
| `/livewire/**` | * | livewire | — | NO | — | — | NO | — | |
| `/admin/lead-photo/{lead}/{index}` | GET | `admin.lead-photo` | — | NO | — | — | NO | `auth` | private disk stream |
| `/up` | GET | health | — | NO | — | — | NO | — | Laravel health |
| any other | GET | — | — | 404 + noindex | — | — | NO | — | |

## Invariants (tested in `tests/Feature/SeoFoundationTest.php`, `LeadFormTest.php`)

1. Production simulation (`APP_URL=https://autostvabaks.ee`): zero `autostvabaks.adme.ee`, zero `noindex`, no `X-Robots-Tag` on public routes, canonical/OG/hreflang/sitemap on production host.
2. ET/RU/EN never canonicalise to each other; each page self-canonical; hreflang reciprocal incl. x-default.
3. http:// and foreign hosts → 301 to `APP_URL` origin (when APP_URL is https).
4. Non-indexable page: absent from sitemap, renders `noindex,nofollow`, still reachable by URL.
5. `/admin*` always `X-Robots-Tag: noindex, nofollow`.
6. Staging: `noindex` meta + header on every public page, robots `Disallow: /`.
7. `php artisan site:release-check` fails on the production host if any of: SITE_NOINDEX=true, APP_DEBUG=true, APP_ENV≠production, STAGING_AUTH set, active placeholder reviews, draft markers on published pages, placeholder strings or staging host in rendered home pages, canonical not starting with APP_URL.
