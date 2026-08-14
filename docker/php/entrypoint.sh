#!/bin/sh
set -e

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

if [ -f "artisan" ]; then
    if [ ! -L "public/storage" ]; then
        php artisan storage:link || true
    fi
fi

exec "$@"
