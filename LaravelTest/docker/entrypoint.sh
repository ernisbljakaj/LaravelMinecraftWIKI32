#!/bin/sh
set -e

echo "Running Laravel bootstrapping..."

php artisan package:discover
php artisan filament:upgrade --no-interaction
php artisan optimize
php artisan about --only=version,environment

if [ "${APP_ENV}" != "production" ] || [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force --no-interaction
fi

if [ "${APP_KEY}" ]; then
    echo "Starting php-fpm..."
    exec php-fpm
else
    echo "!! APP_KEY is missing. Generate one: php artisan key:generate" >&2
    exit 1
fi