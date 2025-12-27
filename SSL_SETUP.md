# SSL Certificate Setup for API Backend

## Problem
Nginx test fails because SSL certificate doesn't exist yet:
```
cannot load certificate "/etc/letsencrypt/live/api-cards-robotic-club.tech-sauce.com/fullchain.pem": No such file or directory
```

## Solution: Obtain SSL Certificate

### Step 1: Create a Temporary HTTP-only Config (for initial certificate request)

If you can't test Nginx config because SSL cert doesn't exist, temporarily comment out SSL certificate paths:

```bash
sudo nano /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com
```

Temporarily comment out the SSL certificate lines:
```nginx
# ssl_certificate /etc/letsencrypt/live/api-cards-robotic-club.tech-sauce.com/fullchain.pem;
# ssl_certificate_key /etc/letsencrypt/live/api-cards-robotic-club.tech-sauce.com/privkey.pem;
```

And temporarily change HTTPS server to listen on port 80:
```nginx
server {
    listen 80;  # Temporarily use HTTP
    # listen 443 ssl http2;  # Comment this out temporarily
    server_name api-cards-robotic-club.tech-sauce.com;
    
    # ... rest of config without SSL lines ...
}
```

### Step 2: Test and Reload Nginx (with temporary HTTP config)

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Step 3: Obtain SSL Certificate

**Option A: Using Nginx plugin (Recommended if port 80 is accessible)**

```bash
sudo certbot certonly --nginx -d api-cards-robotic-club.tech-sauce.com
```

**Option B: Using Standalone (if port 80 is blocked)**

```bash
sudo systemctl stop nginx
sudo certbot certonly --standalone -d api-cards-robotic-club.tech-sauce.com
sudo systemctl start nginx
```

**Option C: Using DNS validation (if port 80 is not accessible)**

```bash
sudo certbot certonly --manual --preferred-challenges dns -d api-cards-robotic-club.tech-sauce.com
# Follow prompts to add TXT record to DNS
```

### Step 4: Restore Full HTTPS Configuration

After obtaining the certificate, restore the full config:

```bash
sudo nano /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com
```

Uncomment the SSL certificate lines and restore HTTPS listener:
```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api-cards-robotic-club.tech-sauce.com;
    
    ssl_certificate /etc/letsencrypt/live/api-cards-robotic-club.tech-sauce.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api-cards-robotic-club.tech-sauce.com/privkey.pem;
    
    # ... rest of config ...
}
```

### Step 5: Final Test and Reload

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Step 6: Verify SSL is Working

```bash
curl -I https://api-cards-robotic-club.tech-sauce.com/api/health
# or check in browser
```

## Alternative: Quick Test Without SSL

If you just want to test the config syntax without SSL:

1. Comment out SSL certificate paths
2. Change `listen 443 ssl http2;` to `listen 80;`
3. Test: `sudo nginx -t`
4. Don't reload if you want to keep SSL working for other sites
5. Restore config after obtaining certificate

