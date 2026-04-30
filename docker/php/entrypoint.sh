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

# Fresh wipe + migrate + seed only if DB_SEED=true
if [ "$DB_SEED" = "true" ]; then
    echo "DB_SEED=true detected — wiping database..."
    php artisan db:wipe --force || echo "Wipe failed, continuing..."
    echo "Running migrations..."
    php artisan migrate --force || echo "Migration failed, continuing..."
    echo "Running seeders..."
    php artisan db:seed --force || echo "Seeding failed, continuing..."
else
    # Normal deploy — just migrate, never touch existing data
    echo "Running migrations..."
    php artisan migrate --force || echo "Migration failed, continuing..."
fi

# Cache for production
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache || echo "Config cache failed, continuing..."
    php artisan route:cache  || echo "Route cache failed, continuing..."
    php artisan view:cache   || echo "View cache failed, continuing..."
fi

echo "Starting php-fpm..."
exec "$@"
