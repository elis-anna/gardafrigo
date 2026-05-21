#!/usr/bin/env bash
set -euo pipefail

if [ ! -f .env ]; then
  cp .env.example .env
  echo "Creato .env da .env.example. Aggiorna le credenziali prima di usare l'ambiente in modo condiviso."
fi

set -a
source .env
set +a

site_url="http://localhost:${WORDPRESS_PORT:-8080}"

docker compose up -d

echo "Attendo MariaDB..."
until docker compose run --rm wpcli wp db check >/dev/null 2>&1; do
  sleep 3
done

echo "Attendo WordPress..."
if ! docker compose run --rm wpcli wp core is-installed >/dev/null 2>&1; then
  docker compose run --rm wpcli wp core install \
    --url="$site_url" \
    --title="${WORDPRESS_TITLE:-Garda Frigor}" \
    --admin_user="${WORDPRESS_ADMIN_USER:-admin}" \
    --admin_password="${WORDPRESS_ADMIN_PASSWORD:-change-admin-me}" \
    --admin_email="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}" \
    --skip-email
fi

if [ -f avada-packages/Avada.zip ]; then
  docker compose run --rm wpcli wp theme install /avada-packages/Avada.zip --force
else
  echo "Avada.zip non presente in avada-packages/. Salto installazione parent theme."
fi

docker compose run --rm wpcli wp theme activate gardafrigo-cawipa || true
docker compose run --rm wpcli wp option update permalink_structure '/%postname%/'
docker compose run --rm wpcli wp rewrite flush
docker compose run --rm wpcli wp eval 'do_action("gardafrigo_seed_content");'

echo "Pronto: $site_url"
