#!/bin/sh
# ---------------------------------------------------------------
# MyAPIs - PHP-FPM container entrypoint
# ---------------------------------------------------------------
# Renders the PHP ini template with values from the environment,
# then starts php-fpm in the foreground.
# ---------------------------------------------------------------
set -eu

CONF_DIR="/usr/local/etc/php/conf.d"
TPL="/usr/local/etc/php/php.ini.tpl"
OUT="${CONF_DIR}/zz-app.ini"

# Default values mirror the defaults in example.env
: "${PHP_MEMORY_LIMIT:=256M}"
: "${PHP_UPLOAD_MAX_FILESIZE:=10M}"
: "${PHP_POST_MAX_SIZE:=10M}"
: "${PHP_DATE_TIMEZONE:=UTC}"
: "${APP_ENV_DISPLAY_ERRORS:=On}"

sed \
    -e "s|__PHP_MEMORY_LIMIT__|${PHP_MEMORY_LIMIT}|g" \
    -e "s|__PHP_UPLOAD_MAX_FILESIZE__|${PHP_UPLOAD_MAX_FILESIZE}|g" \
    -e "s|__PHP_POST_MAX_SIZE__|${PHP_POST_MAX_SIZE}|g" \
    -e "s|__PHP_DATE_TIMEZONE__|${PHP_DATE_TIMEZONE}|g" \
    -e "s|__APP_ENV_DISPLAY_ERRORS__|${APP_ENV_DISPLAY_ERRORS}|g" \
    "$TPL" > "$OUT"

echo "[entrypoint] PHP config written to $OUT"
cat "$OUT"

# ---------------------------------------------------------------
# Provision runtime storage for the in-PHP rate limiter / abuse
# tracker. The directory lives outside the read-only project
# mount (Nginx mounts the project :ro) so we put it under /var
# inside the container. Persistent host volumes can be mapped
# to these paths in docker-compose if desired.
# ---------------------------------------------------------------
RATELIMIT_DIR="${RATELIMIT_STORAGE_DIR:-/var/www/myapis-storage/ratelimit}"
LOG_DIR="${MYAPIS_LOG_DIR:-/var/www/myapis-storage/logs}"
for d in "$RATELIMIT_DIR" "$LOG_DIR"; do
    if [ ! -d "$d" ]; then
        mkdir -p "$d"
    fi
    chown -R www-data:www-data "$d" 2>/dev/null || true
    chmod 0775 "$d" 2>/dev/null || true
done

# Point PHP at the directory via environment so the RateLimiter
# picks it up.
export RATELIMIT_STORAGE_DIR="$RATELIMIT_DIR"
export MYAPIS_LOG_DIR="$LOG_DIR"

echo "[entrypoint] Rate-limit storage: $RATELIMIT_DIR"
echo "[entrypoint] Logs:             $LOG_DIR"

exec "$@"
