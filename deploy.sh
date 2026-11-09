#!/usr/bin/env bash
# Update the site on the hosting server (run from the project root on the server):
#   bash deploy.sh
# Pulls the latest main, installs dependencies, runs migrations and rebuilds caches.
set -euo pipefail
cd "$(dirname "$0")"

PHP="${PHP:-/opt/php/8.3/bin/php}"
COMPOSER="${COMPOSER:-$HOME/bin/composer}"

$PHP artisan down --retry=10 || true
git pull --ff-only origin main
$PHP "$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --no-progress
$PHP artisan migrate --force
$PHP artisan filament:assets
$PHP artisan optimize
# Release gate: warnings on staging, blocking failures on the production host
# (SITE_NOINDEX, APP_DEBUG, STAGING_AUTH, placeholder reviews, draft pages, staging host in SEO output).
# On failure the site stays in maintenance mode (503): fix .env / content, re-run the check, then `artisan up`.
$PHP artisan site:release-check
$PHP artisan up
echo "Deployed $(git rev-parse --short HEAD)"
