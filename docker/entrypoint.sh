#!/bin/sh
set -e

echo "🍳 Recip — starting up..."

# Sync built public assets into the volume (overwrites stale files from previous builds)
if [ -d /var/www/html/public-build ]; then
    cp -a /var/www/html/public-build/. /var/www/html/public/
fi

# Cache config, routes, and views for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run any pending migrations
php artisan migrate --force

# Ensure storage link exists
php artisan storage:link --force 2>/dev/null || true

echo "✅ Ready to cook."

# Execute the CMD (php-fpm)
exec "$@"
