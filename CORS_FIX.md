# CORS Configuration Fix

## Issues Identified

1. **Development (localhost:3000)**: Requests failing due to missing CORS headers
2. **Production**: OPTIONS preflight requests being redirected from HTTP to HTTPS (browsers block this)

## Solution Applied

### 1. Updated Nginx Configuration

The HTTP server block now:
- Proxies all `/api` requests (including OPTIONS) directly to Laravel on HTTP
- This allows Laravel to handle CORS preflight without redirect
- Redirects all other HTTP traffic to HTTPS

### 2. CORS Configuration

The `config/cors.php` already includes:
- Production: `https://robotic-dashboard.nexussolutions.tech`
- Development: `http://localhost:3000` and `http://localhost:5173`

## Next Steps on Backend Server

1. **Upload updated files:**
   ```bash
   # Upload nginx-vps.conf and config/cors.php to your server
   ```

2. **Update Nginx config:**
   ```bash
   cd ~/Attendence-Project
   sudo cp nginx-vps.conf /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com
   sudo nginx -t
   sudo systemctl reload nginx
   ```

3. **Clear Laravel config cache:**
   ```bash
   cd ~/Attendence-Project
   php artisan config:clear
   php artisan config:cache
   ```

4. **Restart PHP-FPM (if using):**
   ```bash
   sudo systemctl restart php8.2-fpm  # or your PHP version
   ```

## How It Works

- **HTTP /api requests**: Proxied directly to Laravel (port 8000), Laravel handles CORS including OPTIONS
- **HTTP other requests**: Redirected to HTTPS
- **HTTPS /api requests**: Proxied to Laravel, Laravel handles CORS
- **HTTPS other requests**: Proxied to Laravel

This ensures:
- OPTIONS requests are never redirected (handled directly by Laravel on HTTP)
- Laravel CORS middleware properly handles all CORS headers
- No duplicate CORS headers (only Laravel sends them)

