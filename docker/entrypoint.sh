#!/bin/sh

# Exit on any error
set -e

echo "Starting Laravel application setup..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! nc -z mysql 3306; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done
echo "MySQL is up - continuing"

# Wait for Redis to be ready
echo "Waiting for Redis to be ready..."
while ! nc -z redis 6379; do
    echo "Redis is unavailable - sleeping"
    sleep 2
done
echo "Redis is up - continuing"

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from docker.env..."
    cp docker.env .env
fi

# Generate application key if not set
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Generate JWT secret if not set
if ! grep -q "JWT_SECRET=" .env || grep -q "JWT_SECRET=$" .env; then
    echo "Generating JWT secret..."
    php artisan jwt:secret --force
fi

# Clear and cache configuration
echo "Optimizing Laravel configuration..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed database if needed (uncomment if you have seeders)
# echo "Seeding database..."
# php artisan db:seed --force

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "Laravel application setup completed!"

# Start supervisor
exec "$@"
