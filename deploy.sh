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
$PHP artisan up
echo "Deployed $(git rev-parse --short HEAD)"
