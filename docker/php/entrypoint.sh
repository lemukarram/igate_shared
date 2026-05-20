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

# ✅ Recreate storage link as root
echo "Recreating storage link..."
rm -rf public/storage
su -s /bin/sh www-data -c "php artisan storage:link --force" || echo "Storage link failed, continuing..."

# ✅ Fix storage permissions as root
echo "Setting storage permissions..."
mkdir -p storage/app/public/settings
mkdir -p storage/app/livewire-tmp
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ✅ Run artisan commands as www-data
run_as_user() {
    su -s /bin/sh www-data -c "$1"
}

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    run_as_user "php artisan key:generate --force"
fi

# Copy public assets to shared volume
if [ -d "/var/www/public_shared" ]; then
    echo "Syncing public assets to shared volume..."
    cp -ru /var/www/public/. /var/www/public_shared/
    chown -R www-data:www-data /var/www/public_shared
fi

# Wait for DB
echo "Waiting for database connection (DB_HOST: $DB_HOST)..."
MAX_TRIES=30
COUNT=0
until nc -z "$DB_HOST" "$DB_PORT"; do
    COUNT=$((COUNT+1))
    if [ $COUNT -gt $MAX_TRIES ]; then
        echo "ERROR: Database host $DB_HOST:$DB_PORT unreachable."
        break
    fi
    sleep 2
done

echo "Attempting artisan migration..."

if [ "$DB_SEED" = "true" ]; then
    run_as_user "php artisan db:wipe --force"
    run_as_user "php artisan migrate --force"
    run_as_user "php artisan db:seed --force"
else
    run_as_user "php artisan config:cache"
    run_as_user "php artisan route:cache"
    run_as_user "php artisan view:cache"
    run_as_user "php artisan migrate --force"
fi


# Cache for production
if [ "$APP_ENV" = "production" ]; then
    echo "Running filament upgrade..."
    run_as_user "php artisan filament:upgrade"

    echo "Caching configuration..."
    run_as_user "php artisan config:cache"
    run_as_user "php artisan route:cache"
    run_as_user "php artisan view:cache"
fi

echo "Starting container process: $@"
exec "$@"
