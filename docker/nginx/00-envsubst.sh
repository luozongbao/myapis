#!/bin/sh
# =============================================================
# MyAPIs - nginx entrypoint helper (runs before nginx starts)
# -------------------------------------------------------------
# Renders /etc/nginx/conf.d/default.conf from default.conf.template
# by replacing __NGINX_CLIENT_MAX_BODY_SIZE__ and
# __RATE_LIMIT_PER_MINUTE__ placeholders with values from the
# container environment (set by docker-compose from .env).
#
# Then removes the stock "Welcome to nginx" default.conf from the
# base image so it does not shadow our rendered config.
# =============================================================
set -eu

TPL="/etc/nginx/conf.d/default.conf.template"
OUT="/etc/nginx/conf.d/default.conf"

: "${NGINX_CLIENT_MAX_BODY_SIZE:=10M}"
: "${RATE_LIMIT_PER_MINUTE:=100}"

# Remove the stock default.conf from the base nginx image so our
# rendered config is the only server block loaded.
rm -f /etc/nginx/conf.d/default.conf

sed \
  -e "s|__NGINX_CLIENT_MAX_BODY_SIZE__|${NGINX_CLIENT_MAX_BODY_SIZE}|g" \
  -e "s|__RATE_LIMIT_PER_MINUTE__|${RATE_LIMIT_PER_MINUTE}|g" \
  "$TPL" > "$OUT"

echo "[nginx-entrypoint] rendered $OUT (body=${NGINX_CLIENT_MAX_BODY_SIZE}, rate=${RATE_LIMIT_PER_MINUTE}r/m)"
