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

# ✅ Add these two lines
echo "Linking storage..."
php artisan storage:link --force || echo "Storage link failed, continuing..."

# ✅ Fix storage permissions
chmod -R 755 /var/www/storage
chmod -R 755 /var/www/public/storage

# Wait for DB to be ready
echo "Waiting for database connection..."
MAX_TRIES=20
COUNT=0
until php artisan db:show > /dev/null 2>&1; do
    COUNT=$((COUNT+1))
    if [ $COUNT -gt $MAX_TRIES ]; then
        echo "ERROR: Database connection timed out after $MAX_TRIES attempts."
        php artisan db:show # Run one last time without redirection to show the error
        exit 1
    fi
    echo "Database not ready (Attempt $COUNT/$MAX_TRIES), retrying in 3s..."
    sleep 3
done
echo "Database connected!"

if [ "$DB_SEED" = "true" ]; then
    php artisan db:wipe --force || echo "Wipe failed, continuing..."
    php artisan migrate --force || echo "Migration failed, continuing..."
    php artisan db:seed --force || echo "Seeding failed, continuing..."
else
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
