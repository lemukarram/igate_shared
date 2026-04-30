#!/bin/sh

cd /var/www

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Wait for DB to be ready
echo "Waiting for database..."
until php artisan db:show > /dev/null 2>&1; do
    echo "Database not ready, retrying in 3s..."
    sleep 3
done

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

# Cache for production
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache || echo "Config cache failed, continuing..."
    php artisan route:cache  || echo "Route cache failed, continuing..."
    php artisan view:cache   || echo "View cache failed, continuing..."
fi

echo "Starting php-fpm..."
exec "$@"
