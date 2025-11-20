#!/usr/bin/env bash
set -euo pipefail

# Copy larapp/.env.prod to project root .env so docker-compose variable
# interpolation (which reads root .env) picks up values like APP_HOST_PORT.
# Usage: ./scripts/sync-env.sh

SRC="$(pwd)/larapp/.env.prod"
DST="$(pwd)/.env"

if [ ! -f "$SRC" ]; then
  echo "Source env file not found: $SRC"
  exit 1
fi

cp "$SRC" "$DST"
chmod 600 "$DST" || true

echo "Synced $SRC -> $DST"
echo "Note: .env is in .gitignore and will not be committed."