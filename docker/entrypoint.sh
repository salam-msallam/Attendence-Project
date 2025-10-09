#!/bin/sh

# Create necessary directories
mkdir -p /tmp
mkdir -p /var/log/nginx
mkdir -p /var/run/nginx

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! nc -z mysql 3306; do
  sleep 1
done
echo "MySQL is ready!"

# Set proper permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Generate application key if not exists
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate application key
php artisan key:generate --force

# Generate JWT secret if not exists
if ! grep -q "JWT_SECRET=" /var/www/html/.env || grep -q "JWT_SECRET=$" /var/www/html/.env; then
    echo "Generating JWT secret..."
    php artisan jwt:secret --force || {
        echo "Artisan JWT secret generation failed, generating manually..."
        JWT_SECRET=$(openssl rand -base64 32)
        if grep -q "JWT_SECRET=" /var/www/html/.env; then
            sed -i "s/JWT_SECRET=.*/JWT_SECRET=$JWT_SECRET/" /var/www/html/.env
        else
            echo "JWT_SECRET=$JWT_SECRET" >> /var/www/html/.env
        fi
    }
fi

# Verify JWT secret is set
if ! grep -q "JWT_SECRET=" /var/www/html/.env || grep -q "JWT_SECRET=$" /var/www/html/.env; then
    echo "ERROR: JWT_SECRET is still not set!"
    exit 1
fi

echo "JWT secret verification passed"

# Run database migrations with error handling
echo "Running database migrations..."
php artisan migrate --force || {
    echo "Migration failed, trying to reset and migrate fresh..."
    php artisan migrate:fresh --force --seed
}

# Run database seeders (only if no users exist)
echo "Running database seeders..."
php artisan db:seed --force

# Clear and cache configuration
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link

echo "Laravel application is ready!"

# Execute the main command
exec "$@"
