#!/bin/bash
set -e

# Generate APP_KEY if not already set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Wait for MySQL to be reachable (belt-and-suspenders beyond healthcheck)
echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
until php artisan db:monitor --databases=mysql 2>/dev/null | grep -q 'OK'; do
    sleep 2
done
echo "MySQL is ready."

# Run migrations (idempotent — Laravel skips already-applied migrations)
php artisan migrate --force

# Seed only once using a marker file
if [ ! -f /var/www/html/storage/.seeded ]; then
    php artisan db:seed --force
    touch /var/www/html/storage/.seeded
    echo "Database seeded."
else
    echo "Database already seeded, skipping."
fi

# Cache configuration for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
