#!/bin/sh
set -eu

APP_DIR="${APP_DIR:-/app}"
HOT_FILE="$APP_DIR/public/hot"
VITE_HOST="${VITE_HOST:-0.0.0.0}"
VITE_PORT="${VITE_PORT:-5173}"

cleanup() {
    rm -f "$HOT_FILE"
}

forward_signal() {
    if [ -n "${VITE_PID:-}" ] && kill -0 "$VITE_PID" 2>/dev/null; then
        kill "$VITE_PID" 2>/dev/null || true
        wait "$VITE_PID" 2>/dev/null || true
    fi

    exit 0
}

trap cleanup EXIT
trap forward_signal INT TERM HUP

# Always clear a previous hot file before starting a new dev server.
cleanup

cd "$APP_DIR"

npm run dev -- --host "$VITE_HOST" --port "$VITE_PORT" --strictPort &
VITE_PID=$!

wait "$VITE_PID"
