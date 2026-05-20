#!/bin/sh


# If volume is empty (first run), files are already here from image build
# Clear bootstrap cache if it exists (prevents host-to-container pollution)
echo "Clearing bootstrap cache..."
rm -f /var/www/bootstrap/cache/config.php
rm -f /var/www/bootstrap/cache/packages.php
rm -f /var/www/bootstrap/cache/services.php
rm -f /var/www/bootstrap/cache/routes-v7.php

# If public/index.php is missing, something is wrong
if [ ! -f "/var/www/public/index.php" ]; then
    echo "ERROR: /var/www/public/index.php not found!"
    exit 1
fi

# Check if vendor exists
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "ERROR: /var/www/vendor/autoload.php not found!"
    echo "It seems the vendor directory is missing. Ensure you are not overriding /var/www with an empty volume."
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

# ✅ Fix storage permissions (ignore errors if volumes are root-owned)
echo "Setting storage permissions..."
chmod -R 775 /var/www/storage || echo "Warning: Could not set permissions for /var/www/storage"
chmod -R 775 /var/www/bootstrap/cache || echo "Warning: Could not set permissions for /var/www/bootstrap/cache"

# Copy public assets to shared volume if it exists (for Nginx)
if [ -d "/var/www/public_shared" ]; then
    echo "Syncing public assets to shared volume..."
    cp -ru /var/www/public/. /var/www/public_shared/
fi

# Wait for DB to be ready
echo "Waiting for database connection (DB_HOST: $DB_HOST)..."
MAX_TRIES=30
COUNT=0
# Try to connect to the DB port first (faster than artisan)
until nc -z "$DB_HOST" "$DB_PORT"; do
    COUNT=$((COUNT+1))
    if [ $COUNT -gt $MAX_TRIES ]; then
        echo "ERROR: Database host $DB_HOST:$DB_PORT unreachable after $MAX_TRIES attempts."
        # Continue anyway to allow FPM to start (app will show DB error in browser)
        break
    fi
    echo "Database port not reachable (Attempt $COUNT/$MAX_TRIES), retrying in 2s..."
    sleep 2
done

echo "Attempting artisan migration..."

if [ "$DB_SEED" = "true" ]; then
    php artisan db:wipe --force || echo "Wipe failed, continuing..."
    php artisan migrate --force || echo "Migration failed, continuing..."
    php artisan db:seed --force || echo "Seeding failed, continuing..."
else
    php artisan config:cache || echo "Config cache failed, continuing..."
    php artisan route:cache  || echo "Route cache failed, continuing..."
    php artisan view:cache   || echo "View cache failed, continuing..."
    php artisan migrate --force || echo "Migration failed, continuing..."
fi


# Cache for production
if [ "$APP_ENV" = "production" ]; then
    echo "Running filament upgrade..."
    php artisan filament:upgrade || echo "Filament upgrade failed, continuing..."

    echo "Caching configuration..."
    php artisan config:cache || echo "Config cache failed, continuing..."
    php artisan route:cache  || echo "Route cache failed, continuing..."
    php artisan view:cache   || echo "View cache failed, continuing..."
fi

echo "Starting container process: $@"
exec "$@"
