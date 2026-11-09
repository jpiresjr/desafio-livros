#!/bin/sh
set -e

# Prepara o diretório de recursos do KoolReport.
ASSETS_DIR="/var/www/html/public/koolreport_assets"

mkdir -p "$ASSETS_DIR"
chown -R www-data:www-data "$ASSETS_DIR"
chmod -R u+rwX,go+rX,go-w "$ASSETS_DIR"

# Inicia o comando principal do container.
exec "$@"