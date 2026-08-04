#!/bin/sh
set -e

if [ -f "artisan" ]; then
    if [ ! -L "public/storage" ]; then
        php artisan storage:link || true
    fi
fi

exec "$@"
