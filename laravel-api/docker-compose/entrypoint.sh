# #!/bin/sh

# # Exit immediately if a command exits with a non-zero status
# set -e

# # 1. Install composer dependencies if they don't exist
# # This allows the vendor folder to persist on your host machine
# if [ ! -d "vendor" ]; then
#     echo "🚀 Installing composer dependencies..."
#     composer install --no-interaction --prefer-dist --optimize-autoloader
# fi

# # 2. Ensure the .env file exists for the app
# if [ ! -f ".env" ]; then
#     echo "📝 Creating .env file from example..."
#     cp .env.example .env
#     php artisan key:generate
# fi

# # 3. Wait for MySQL to be ready
# # It tries to connect to the 'mysql' service on port 3306
# echo "⏳ Waiting for MySQL to be ready..."
# until timeout 1s bash -c "true < /dev/tcp/mysql/3306" 2>/dev/null; do
#   sleep 2
# done
# echo "✅ MySQL is up!"

# # 4. Run migrations
# # The --force flag is required to run migrations in 'production' environments
# # which some Docker containers mimic.
# echo "🔄 Running migrations..."
# php artisan migrate:fresh --seed

# # 5. Start the main command defined in the Dockerfile (php artisan serve)
# echo "🌐 Starting Laravel Development Server..."
# exec "$@"


#!/bin/sh

# Exit immediately if a command exits with a non-zero status
set -e

# 1. ALWAYS install composer dependencies
# Since you don't want to persist the vendor folder, we run this every time.
# We use --no-dev for production-like builds, but omit it if you need tests inside Docker.
echo "🚀 Installing composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# 2. Ensure the .env file exists
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file from example..."
    cp .env.example .env
    php artisan key:generate
fi

# 3. Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
until timeout 1s bash -c "true < /dev/tcp/mysql/3306" 2>/dev/null; do
  sleep 2
done
echo "✅ MySQL is up!"

# 4. Refresh Database & Seed Roles/Admin
# Note: migrate:fresh is fine for dev, but use 'migrate --force' for production!
echo "🔄 Preparing database..."
php artisan migrate:fresh --seed

# 5. Link Storage for Filament/User uploads
echo "🔗 Linking storage..."
php artisan storage:link

# 6. Clear and Cache Configs (Important for performance in Docker)
php artisan config:clear
php artisan route:clear

# 7. Start the Queue Worker in the background
# This ensures your 'ProcessUnitFile' jobs actually run.
echo "👷 Starting Queue Worker..."
php artisan queue:work --verbose --tries=3 --timeout=90 &

# 8. Start the main command (the web server)
echo "🌐 Starting Application Server..."
exec "$@"
