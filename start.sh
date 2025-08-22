#!/bin/bash
set -e

echo "🚀 Starting Client Portal..."

# Generate app key if needed
if ! grep -q "^APP_KEY=" .env || [ -z "$(grep "^APP_KEY=" .env | cut -d'=' -f2)" ]; then
    echo "🔑 Generating app key..."
    php artisan key:generate --force
fi

# Create SQLite database
echo "🗄️ Setting up database..."
mkdir -p database
touch database/database.sqlite

# Run migrations
echo "🔄 Running migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear

echo "✅ Setup complete! Starting server..."
exec php artisan serve --host=0.0.0.0 --port=8001 