#!/bin/sh
# Runs on every deploy/start on Render: prepare the database, cache config, then start Apache.
set -e

# Render sets RENDER_EXTERNAL_URL (https://<name>.onrender.com); use it unless APP_URL is set.
export APP_URL="${APP_URL:-$RENDER_EXTERNAL_URL}"

php artisan migrate --force
# Reference data (halls, price list) and the Head of Residence Life; safe to run on every start.
php artisan db:seed --force

php artisan config:cache
php artisan view:cache
php artisan route:cache || echo "Route cache skipped"

exec apache2-foreground
