#!/bin/sh
set -eu

APP_DIR="${APP_DIR:-/app}"
HOT_FILE="$APP_DIR/public/hot"
BUILD_MANIFEST="$APP_DIR/public/build/manifest.json"

if [ -f "$HOT_FILE" ]; then
    echo "Removing stale Vite hot file so Docker uses built assets."
    rm -f "$HOT_FILE"
fi

mkdir -p \
    "$APP_DIR/storage/framework/cache/data" \
    "$APP_DIR/storage/framework/sessions" \
    "$APP_DIR/storage/framework/views" \
    "$APP_DIR/bootstrap/cache"

if [ ! -f "$BUILD_MANIFEST" ]; then
    echo "Warning: $BUILD_MANIFEST is missing."
    echo "Run 'docker compose exec app npm run build' for production assets"
    echo "or 'docker compose exec app npm run dev' for the Vite dev server."
fi

exec "$@"
