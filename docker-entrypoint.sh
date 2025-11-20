#!/bin/bash
set -e

# This entrypoint will:
# - if vendor missing, run composer install
# - wait for DB to be reachable
# - run migrations and db:seed
# - exec the provided CMD (apache2-foreground)

: ${DB_HOST:=db}
: ${DB_PORT:=3306}

echo "Entrypoint: APP_ENV=$APP_ENV"

# If vendor not present (because host volume overlays /var/www/html), install dependencies
if [ ! -d /var/www/html/vendor ]; then
  echo "vendor not found, running composer install..."
  cd /var/www/html
  composer install --no-interaction || true
fi

# If a required package is missing from the checked-in vendor (common when vendor
# is present on the host but incomplete), ensure Composer installs missing deps.
if [ ! -d /var/www/html/vendor/laravel/fortify ]; then
  echo "Detected missing package laravel/fortify, running composer install to restore dependencies..."
  cd /var/www/html
  # Avoid running Composer scripts while dependencies are missing (they may bootstrap
  # the app and reference classes that aren't installed yet). Install without
  # scripts, then dump-autoload and run discovery.
  # Temporarily disable composer scripts to avoid bootstrapping the app during
  # install (config files may reference classes not yet present). After install
  # we dump autoload and run discovery.
  # If Fortify config exists it may reference classes that aren't installed yet.
  # Move it out of the way while running composer, then put it back.
  FORTIFY_MOVED=0
  if [ -f /var/www/html/config/fortify.php ]; then
    echo "Temporarily moving config/fortify.php to avoid bootstrap-time errors..."
    mv /var/www/html/config/fortify.php /var/www/html/config/fortify.php.disabled || true
    FORTIFY_MOVED=1
  fi

  export COMPOSER_NO_SCRIPTS=1
  composer install --no-interaction --prefer-dist || true
  composer dump-autoload --no-interaction || true
  unset COMPOSER_NO_SCRIPTS

  # Restore Fortify config if we moved it
  if [ "$FORTIFY_MOVED" = "1" ]; then
    echo "Restoring config/fortify.php"
    mv /var/www/html/config/fortify.php.disabled /var/www/html/config/fortify.php || true
  fi

  # Now run package discovery (requires installed packages)
  php artisan package:discover --ansi || true

fi

# Publish and generate L5 Swagger docs at runtime (do this after composer install
# and package discovery so required classes are available)
echo "Publishing L5 Swagger assets..."
php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider" --force || true
echo "Generating L5 Swagger docs..."
php artisan l5-swagger:generate || true

# Wait for DB to be available
if [ -n "$DB_HOST" ]; then
  echo "Waiting for DB $DB_HOST:$DB_PORT..."
  while ! nc -z "$DB_HOST" "$DB_PORT"; do
    echo "Waiting for database..."
    sleep 1
  done
fi

# Run migrations and seed (force to run in non-interactive)
cd /var/www/html
# Ensure public storage symlink exists so uploaded files are web-accessible
echo "Ensuring storage symlink (public/storage -> storage/app/public)..."
php artisan storage:link || true
echo "Running migrations..."
php artisan migrate --force || true

echo "Running db:seed..."
# Only run seed if users table is empty to avoid duplicate-key errors when container restarts
echo "Checking if seeding required (users table)..."
cat > /tmp/check_seed.php <<'PHP'
<?php
try {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: 3306;
    $db = getenv('DB_DATABASE');
    $user = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');
    $dsn = "mysql:host={$host};port={$port};dbname={$db}";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $count = 0;
    try {
        $stmt = $pdo->query('select count(*) from users');
        $count = (int) $stmt->fetchColumn();
    } catch (Throwable $e) {
        // table might not exist yet
        $count = 0;
    }
    // echo a code we can grep for: NEED_SEED or SKIP_SEED
    echo ($count === 0) ? "NEED_SEED" : "SKIP_SEED";
    exit(0);
} catch (Throwable $e) {
    // on any error, print SKIP_SEED and exit
    echo "SKIP_SEED";
    exit(0);
}
PHP

RESULT=$(php /tmp/check_seed.php || true)
if [ "$RESULT" = "NEED_SEED" ]; then
  echo "Seeding database (no users detected)..."
  php artisan db:seed --force || true
else
  echo "Users exist or check failed — skipping seed to avoid duplicates."
fi
rm -f /tmp/check_seed.php || true

# Exec original command (apache foreground from base image)
exec "$@"
