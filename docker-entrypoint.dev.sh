#!/usr/bin/env bash
# Bootstrap the bind-mounted app so `composer dev` works on a fresh checkout,
# then exec the passed command (default: `composer dev`, set in Dockerfile.dev).
#
# Idempotent: once vendor/, node_modules/, and APP_KEY are present it does
# nothing, so repeated `docker compose up` starts have no install overhead.
set -euo pipefail

cd /var/www/html

# 1) .env + APP_KEY (artisan needs a key to boot)
if [ ! -f .env ]; then
    cp .env.example .env
fi
if [ -z "$(grep -E '^APP_KEY=' .env | cut -d= -f2-)" ]; then
    php artisan key:generate --ansi
fi

# 2) PHP dependencies
if [ ! -d vendor ]; then
    composer install --no-interaction --no-progress
fi

# 3) Node dependencies (Vite). node_modules is a named volume (see
#    docker-compose.yml) so it holds the container's own Linux-native build;
#    install when absent OR empty (fresh volume). `npm ci` installs the
#    platform-appropriate optional deps (e.g. rolldown's linux native binding)
#    from the committed lockfile WITHOUT rewriting it (npm install would mutate
#    the bind-mounted lockfile).
if [ ! -d node_modules ] || [ -z "$(ls -A node_modules 2>/dev/null)" ]; then
    npm ci --no-audit --no-fund
fi

exec "$@"
