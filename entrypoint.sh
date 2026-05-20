#!/bin/sh

# error handling
set -e

echo "Starting container..."

if [ ! -f .env ]; then
  echo "Creating .env..."
  cp .env.example .env
fi

if [ ! -d vendor ]; then
  echo "Installing dependencies (composer install)..."
  composer install
fi

if ! grep -q "^APP_KEY=base64:" .env; then
  echo "Generating APP_KEY..."
  php artisan key:generate
fi

echo "Waiting for database..."
until php -r "new PDO('mysql:host=db;port=3306;dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null 1>/dev/null; do
    sleep 2
done
echo "   Database available."

if ! php -r "
\$pdo = new PDO('mysql:host=db;port=3306;dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');
\$r = \$pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=\"${DB_DATABASE}\" AND table_name=\"users\"');
exit(\$r->fetchColumn() > 0 ? 0 : 1);
" ; then
    echo "   Running migrations..."
    php artisan migrate --force
    echo "   Running seeders..."
    php artisan db:seed --force
fi

echo "   Starting colecifebens..."
exec php artisan serve --host=0.0.0.0 --port=8000