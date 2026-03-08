#!/bin/sh
set -e

echo "🍳 Recip — starting up..."

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
