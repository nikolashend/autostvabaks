# AUTOSTVABAKS — STAGE 4 GO-LIVE PROCEDURE (production activation)

Status (2026-11-12): **TECHNICAL_READY = YES · PRODUCTION_DEPLOYED = NO.** The release candidate is `main` (see the Stage-4 report for the exact hash), verified on staging. Production does not exist yet: `autostvabaks.ee` resolves to a zone.ee "page is under construction" placeholder (A `217.146.69.28`), DNS is hosted at zone.eu (`ns.zone.eu`, `ns2.zone.ee`, `ns3.zonedata.net`), mail is Google Workspace (MX `smtp.google.com`, SPF `v=spf1 a mx include:_spf.zone.eu include:_spf.google.com -all`, DMARC `p=reject`).

Everything below is the smallest exact procedure. Each step names who can do it. Nothing here is optional unless marked so.

## 0. Human inputs that remain

| # | Input | Owner | Needed for |
|---|---|---|---|
| H1 | **Legal approval** of the Terms / Privacy texts as seeded (`database/seeders/PageSeeder.php`, now carrying Motion Motors OÜ, registry code 17341567, VAT EE102908916, Meistri 20). Decision on the no-obligation sentence (P8): it stays in Terms §3 as a description of the request; it is not published on the landing. | client (Alan / Motion Motors OÜ) | `LEGAL_CONTENT_STATUS=approved` |
| H2 | **Hero sign-off**: Pexels photo 20695262 (Erik Mclean, Pexels licence) — technically applied and QA'd in Stage 4 (`HERO_ASSET_STATUS=approved` on staging/local). If the client rejects the picture, replace the five `hero-coast.*` files and keep the status. | client (visual acceptance) | none (already approved technically) |
| H3 | **Hosting panel access** (ispmanager `https://server144.hosting.reg.ru:1500`, user `u1579543`) to add the WWW-domain and the database — SSH cannot do it (`mgrctl` needs interactive auth). | Nikolai | §2 |
| H4 | **DNS access at zone.ee** for autostvabaks.ee (A/AAAA records; later the GSC TXT record). | client / Nikolai | §5, §8 |
| H5 | **Production SMTP**: a Google Workspace mailbox or SMTP relay for `info@autostvabaks.ee` (app password or `smtp-relay.gmail.com` with the server IP allow-listed). reg.ru's own SMTP is **not** in the SPF record → DMARC `p=reject` would discard lead e-mails. | client (Workspace admin) | §3 `.env` |
| H6 | Optional: GTM container ID + consent decision (`ANALYTICS_GTM_ID`, `ANALYTICS_ENABLED`); phone / WhatsApp / Telegram (admin → Settings); real reviews; Meistri 20 photo. | client | not blocking |

## 1. Pre-flight (SSH, no panel) — recorded 2026-11-12

| Item | Value |
|---|---|
| Host | `server144.hosting.reg.ru` (31.31.196.109), shared hosting, ispmanager |
| PHP | 8.3.36 (`/opt/php/8.3/bin/php`), web SAPI cgi-fcgi with `upload_max_filesize=256M`, `post_max_size=256M`, `memory_limit=1024M`; modules gd, imagick, intl, pdo_mysql, zip, opcache |
| Composer | 2.10.1 (`~/bin/composer`) |
| MySQL | Percona 8.0.25; one DB/user per site (`u1579543_<name>`), grants limited to the own schema → the production DB must be created in the panel |
| Disk | 407 GB free; staging site 124 MB |
| Web server | nginx in front of Apache (`.htaccess` honoured; static files served by nginx with gzip and `max-age=3888000`) |
| TLS | Let's Encrypt is issued per domain from the panel after DNS points here |
| Cron | not required (queue `sync`, no scheduler) |
| Staging | `~/www/autostvabaks.adme.ee` at the release commit, Basic Auth + `SITE_NOINDEX=true` — stays untouched |

## 2. Create the production site (panel, ~10 min)

1. ispmanager → **WWW-domains → Add**: name `autostvabaks.ee`, alias `www.autostvabaks.ee`, document root `www/autostvabaks.ee/public`, PHP 8.3 (same mode as `autostvabaks.adme.ee`), charset UTF-8, "Redirect HTTP → HTTPS" off for now (Laravel 301s foreign/insecure hosts itself; enable after TLS if desired).
2. ispmanager → **Databases → Add**: `u1579543_asv` (MySQL, user with the same name, strong password). Keep the password for `.env` only.
3. SSH — put the code in place (the panel pre-creates the folder, so clone into it rather than onto it):

```bash
ssh autostvabaks
cd ~/www/autostvabaks.ee
git init && git remote add origin git@github.com-autostvabaks:nikolashend/autostvabaks.git
git fetch origin main && git checkout -f main      # exact release hash: git log -1 --oneline
cp .env.example .env
```

## 3. Production `.env` (never committed)

```
APP_NAME=Autostvabaks
APP_ENV=production
APP_DEBUG=false
APP_URL=https://autostvabaks.ee
APP_LOCALE=et

SITE_NOINDEX=false
STAGING_AUTH=

HERO_ASSET_STATUS=approved          # asset replaced + QA'd in Stage 4 (ADR-32)
LEGAL_CONTENT_STATUS=approved       # ONLY after H1 – the seeder then publishes the texts without the draft marker

LOG_CHANNEL=daily
LOG_LEVEL=warning
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=u1579543_asv
DB_USERNAME=u1579543_asv
DB_PASSWORD=<panel>

MAIL_MAILER=smtp                    # Google Workspace (H5) – matches SPF/DMARC
MAIL_HOST=smtp.gmail.com            # or smtp-relay.gmail.com (IP allow-list 31.31.196.109)
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=info@autostvabaks.ee
MAIL_PASSWORD=<app password>
MAIL_FROM_ADDRESS=info@autostvabaks.ee
MAIL_FROM_NAME="Autostvabaks"
LEADS_MAIL_TO=alan@autostvabaks.ee
LEADS_MAIL_ATTACHMENTS_MAX_MB=10

ANALYTICS_GTM_ID=                   # empty = no vendor analytics (ANALYTICS = DISABLED_PENDING_APPROVAL)
ANALYTICS_ENABLED=false
CSP_MODE=report-only                # ADR-35: keep report-only at launch
ASSETS_MINIFIED=true
```

Then: `php artisan key:generate --force` (PHP = `/opt/php/8.3/bin/php`).

## 4. First deployment (SSH, before DNS)

```bash
cd ~/www/autostvabaks.ee
mkdir -p ~/backups && /usr/bin/mysqldump -u u1579543_asv -p u1579543_asv > ~/backups/asv-$(date +%F-%H%M)-empty.sql   # DB_BACKUP (empty schema baseline)
bash deploy.sh                      # composer --no-dev, migrate --force, filament:assets, optimize, up, site:release-check (blocks on FAIL)
/opt/php/8.3/bin/php artisan db:seed --class=PageSeeder --force   # legal pages with operator facts; without draft marker when LEGAL_CONTENT_STATUS=approved
/opt/php/8.3/bin/php artisan db:seed --class=FaqSeeder --force    # 9 keyed FAQ entries
/opt/php/8.3/bin/php artisan make:filament-user                   # admin account (name, e-mail, strong password)
/opt/php/8.3/bin/php artisan site:release-check --strict          # must print "Release check passed" (0 FAIL)
```

Smoke test before DNS (host header against the server IP):

```bash
curl -sk -H "Host: autostvabaks.ee" https://31.31.196.109/ -o /dev/null -w "%{http_code}\n"      # 200 (cert warning expected before LE)
curl -sk -H "Host: autostvabaks.ee" https://31.31.196.109/robots.txt                              # Disallow: /admin + OAI-SearchBot + Sitemap
```

## 5. DNS switch (zone.ee) — record before/after

| Record | Before (2026-11-12) | After |
|---|---|---|
| `autostvabaks.ee` A | `217.146.69.28` (zone.ee placeholder) | `31.31.196.109` |
| `www.autostvabaks.ee` | A `217.146.69.28` | A `31.31.196.109` (or CNAME `autostvabaks.ee`) |
| AAAA | none | none (server IPv6 `2a00:f940:2:2:1:1:0:144` only if the panel binds it) |
| MX / SPF / DMARC / Google TXT | unchanged | **unchanged** |

TTL is zone.ee's default; expect propagation within the hour. Then ispmanager → SSL → **Let's Encrypt** for `autostvabaks.ee` + `www`, and switch the "Redirect HTTP → HTTPS" option on (or rely on Laravel's 301).

## 6. Live verification (copy/paste)

```bash
for u in / /ru /en /kasutustingimused /privaatsuspoliitika /ru/usloviya-ispolzovaniya /en/terms-of-use; do
  curl -sI https://autostvabaks.ee$u | grep -iE "^HTTP|x-robots|content-security|cache-control" ; done
curl -sI http://autostvabaks.ee/ru | grep -iE "^HTTP|location"        # 301 → https://autostvabaks.ee/ru
curl -sI https://www.autostvabaks.ee/en | grep -iE "^HTTP|location"   # 301 → https://autostvabaks.ee/en
curl -s https://autostvabaks.ee/ | grep -oE '<link rel="canonical"[^>]+|<link rel="alternate"[^>]+|<meta property="og:image"[^>]+|<meta name="robots"[^>]+'
curl -s https://autostvabaks.ee/robots.txt; curl -s https://autostvabaks.ee/sitemap.xml | grep -c "<loc>"
curl -s https://autostvabaks.ee/ | grep -c "autostvabaks.adme.ee"     # 0
curl -sI https://autostvabaks.ee/admin | grep -i x-robots            # noindex, nofollow
curl -sI https://autostvabaks.ee/.env | head -1                         # 403/404
curl -sI -u x:y https://autostvabaks.adme.ee/ | head -1                 # staging still 401 (unchanged)
```

Then: one labelled test lead (ET form, 1 photo) → DB row → e-mail in alan@autostvabaks.ee → admin lead page; Lighthouse mobile/desktop on the live host; browser console = 0 errors; `storage/logs/laravel-*.log` has no PII dump.

## 7. Rollback

| Layer | Action |
|---|---|
| Visibility | `php artisan down` (maintenance page) — instant |
| Code | `git checkout -f <previous hash> && bash deploy.sh` (deploy.sh pulls `main`; for a hotfix: fix in git → push → deploy, never edit on the server) |
| DB | `mysql -u u1579543_asv -p u1579543_asv < ~/backups/<dump>.sql` (dump taken before every `bash deploy.sh`) |
| Photos | `storage/app/private` is included in `tar czf ~/backups/asv-files-$(date +%F).tgz ~/www/autostvabaks.ee/storage` |
| DNS | A records back to `217.146.69.28` (placeholder) — only if the host itself fails |

## 8. Search Console (after the site is public)

1. GSC → Add property → **Domain** `autostvabaks.ee` → TXT record at zone.ee (`google-site-verification=…`). Fallback: URL-prefix property `https://autostvabaks.ee/` verified by HTML file in `public/` (commit it).
2. Sitemaps → submit `https://autostvabaks.ee/sitemap.xml` once.
3. URL inspection → Live test + Request indexing for `/`, `/ru`, `/en`.
4. Record `AUTOSTVABAKS_GSC_T0 = <date of submission>` in the Stage-4 report; first query review 4–6 weeks later against `AUTOSTVABAKS_STAGE3_QUERY_COMPONENT_MAP.md`. No new pages before that.

## 9. Status vocabulary

LOCAL PASS → STAGING PASS → PRODUCTION DEPLOYED → PRODUCTION VERIFIED → GSC VERIFIED → INDEXED. Stage 4 PASS requires PRODUCTION VERIFIED; INDEXED is Google's timing.
