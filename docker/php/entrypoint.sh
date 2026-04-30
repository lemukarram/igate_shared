#!/bin/sh


# If volume is empty (first run), files are already here from image build
# If public/index.php is missing, something is wrong
if [ ! -f "/var/www/public/index.php" ]; then
    echo "ERROR: /var/www/public/index.php not found!"
    exit 1
fi

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

# Run seeders (only on first run — check if tables are empty)
echo "Running seeders..."
php artisan db:seed --force || echo "Seeding failed, continuing..."

# Cache for production
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache || echo "Config cache failed, continuing..."
    php artisan route:cache  || echo "Route cache failed, continuing..."
    php artisan view:cache   || echo "View cache failed, continuing..."
fi

echo "Starting php-fpm..."
exec "$@"
