# Deployment Guide: iGate on Dokploy

This guide explains how to deploy the iGate application on Dokploy using Docker Compose.

## 1. Prerequisites
- A Dokploy instance installed and running.
- Access to your Dokploy dashboard.

## 2. Environment Configuration
Create a `.env` file on your server (or add these variables in the Dokploy UI) based on the provided `.env-server`.

**Important:**
- Set `APP_KEY`: Generate one using `php artisan key:generate --show`.
- Set `APP_URL`: Your production domain (e.g., `https://igate.yourdomain.com`).
- Ensure `DB_PASSWORD` matches between the `app` and `db` service environment variables.

## 3. Docker Compose Setup
In Dokploy, create a new **Compose** project and use the `docker-compose.yml` from the root of this repository.

### Key Configuration in `docker-compose.yml`:
- **Shared Volume:** The `app-files` named volume is shared between `app` (PHP-FPM) and `web` (Nginx). This ensures Nginx can serve the static files from the `public` directory.
- **Port Mapping:** The `web` service is exposed on port `8001` (default). You can change this via the `WEB_PORT` environment variable.
- **Container Names:** Prefixed with `${APP_NAME:-igate}` to avoid conflicts with other projects.

## 4. Dokploy Specific Settings

### Volume Mapping
When configuring the Compose project in Dokploy:
- The `igate_db_data` volume will persist your database.
- The `app-files` volume will persist the application code and vendor files.

**⚠️ Warning on Code Updates:**
Because `app-files` is a named volume mounted at the application root (`/var/www`), Docker will only populate it from the image if the volume is **empty**. When you push new code and Dokploy rebuilds the image, the existing `app-files` volume will still contain the **old code**.

To ensure your code updates:
1.  **Option A (Manual):** Clear the `app-files` volume in Dokploy after a deployment if changes are not reflecting.
2.  **Option B (Recommended for CI/CD):** Use a bind mount instead of a named volume for `/var/www` if Dokploy allows it (e.g., `./:/var/www`). However, the current setup follows your request for a named volume.
3.  **Option C (Entrypoint Sync):** We can update `docker/php/entrypoint.sh` to sync code from a temporary directory in the image to `/var/www` on every start, but this adds overhead.

### Port Configuration
- In Dokploy's "Domains" section for the `web` service:
  - Point your domain to the container `igate_web` (or whatever you named it).
  - Use port `80` (the internal port of the Nginx container).

## 5. First-Time Setup
After the containers are started for the first time:
1. Access the `app` container terminal via Dokploy.
2. Run migrations:
   ```bash
   php artisan migrate --force
   ```
3. (Optional) Seed the database:
   ```bash
   php artisan db:seed --force
   ```

## 6. Troubleshooting

### Permissions
If you encounter permission issues with the `storage` or `bootstrap/cache` directories, run:
```bash
chown -R www-data:www-data storage bootstrap/cache
```

### Trusted Proxies
Since Dokploy uses a reverse proxy, you might need to trust all proxies in `app/Http/Middleware/TrustProxies.php` (for Laravel 10/11) or via the `TRUSTED_PROXIES` env var if supported by your Laravel version.

For Laravel 12 (as seen in `composer.json`):
In `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->trustProxies(at: '*');
})
```

### Static Files Not Loading
Ensure the `web` container has the `app-files` volume mounted at `/var/www` and the `root` in `docker/nginx/default.conf` points to `/var/www/public`.
