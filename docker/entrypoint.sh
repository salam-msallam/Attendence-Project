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

# Ensure APP_KEY line exists in .env
if ! grep -q "APP_KEY=" /var/www/html/.env; then
    echo "APP_KEY=" >> /var/www/html/.env
fi

# Generate application key
echo "Generating application key..."

# Check if APP_KEY is already set and not empty
APP_KEY_LINE=$(grep "APP_KEY=" /var/www/html/.env)
if [ -n "$APP_KEY_LINE" ] && [ "$APP_KEY_LINE" != "APP_KEY=" ]; then
    echo "Application key already exists"
else
    # Generate APP_KEY manually
    APP_KEY=$(php artisan key:generate --show 2>/dev/null | grep "base64:" | cut -d: -f2 | tr -d ' ')

    if [ -z "$APP_KEY" ]; then
        # Fallback: generate manually using openssl
        APP_KEY="base64:$(openssl rand -base64 32)"
        echo "Generated APP_KEY manually: $APP_KEY"
    else
        echo "Generated APP_KEY via artisan: $APP_KEY"
    fi

    # Write APP_KEY to .env file
    if grep -q "APP_KEY=" /var/www/html/.env; then
        sed -i "s/APP_KEY=.*/APP_KEY=$APP_KEY/" /var/www/html/.env
        echo "Updated existing APP_KEY line"
    else
        echo "APP_KEY=$APP_KEY" >> /var/www/html/.env
        echo "Added new APP_KEY line"
    fi

    # Verify the write was successful
    sleep 1
    if grep -q "APP_KEY=$APP_KEY" /var/www/html/.env; then
        echo "Application key written successfully to .env file"
    else
        echo "ERROR: Failed to write APP_KEY to .env file"
        exit 1
    fi
fi

# Generate JWT secret if not exists
if ! grep -q "JWT_SECRET=" /var/www/html/.env || grep -q "JWT_SECRET=$" /var/www/html/.env; then
    echo "Generating JWT secret..."

    # Generate JWT secret manually (artisan command is unreliable in Docker)
    JWT_SECRET=$(openssl rand -base64 32)
    echo "Generated JWT secret: $JWT_SECRET"

    # Write JWT secret to .env file
    if grep -q "JWT_SECRET=" /var/www/html/.env; then
        # Replace existing empty JWT_SECRET line
        sed -i "s/JWT_SECRET=.*/JWT_SECRET=$JWT_SECRET/" /var/www/html/.env
        echo "Updated existing JWT_SECRET line"
    else
        # Add new JWT_SECRET line
        echo "JWT_SECRET=$JWT_SECRET" >> /var/www/html/.env
        echo "Added new JWT_SECRET line"
    fi

    # Verify the write was successful
    sleep 1
    if grep -q "JWT_SECRET=$JWT_SECRET" /var/www/html/.env; then
        echo "JWT secret written successfully to .env file"
    else
        echo "ERROR: Failed to write JWT secret to .env file"
        exit 1
    fi

    echo "JWT secret generation completed"
else
    echo "JWT secret already exists"
fi

# Verify JWT secret is set (check if it exists and is not empty)
echo "Verifying JWT secret..."
echo "Debug: Contents of .env file:"
grep -E "(APP_KEY|JWT_SECRET)" /var/www/html/.env || echo "No APP_KEY or JWT_SECRET found in .env"

JWT_SECRET_LINE=$(grep "JWT_SECRET=" /var/www/html/.env 2>/dev/null)
echo "Debug: JWT_SECRET_LINE = '$JWT_SECRET_LINE'"

# Check if JWT_SECRET line exists and has a value
if [ -z "$JWT_SECRET_LINE" ]; then
    echo "ERROR: JWT_SECRET line not found in .env file!"
    exit 1
elif [ "$JWT_SECRET_LINE" = "JWT_SECRET=" ]; then
    echo "ERROR: JWT_SECRET is empty!"
    exit 1
elif [ ${#JWT_SECRET_LINE} -lt 20 ]; then
    echo "ERROR: JWT_SECRET appears to be too short!"
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
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link

echo "Laravel application is ready!"

# Execute the main command
exec "$@"
