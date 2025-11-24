# VPS Deployment Guide for Attendance Project

This guide will help you deploy the Attendance Project to your VPS with a subdomain managed by Cloudflare.

## Prerequisites

- VPS with Ubuntu/Debian (or similar Linux distribution)
- Domain managed by Cloudflare
- Docker and Docker Compose installed on VPS
- Nginx installed on VPS
- SSH access to your VPS

## Step 1: Prepare Your VPS

### Install Required Software

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose -y

# Install Nginx
sudo apt install nginx -y

# Install Certbot (for SSL certificates, optional if using Cloudflare SSL)
sudo apt install certbot python3-certbot-nginx -y
```

## Step 2: Configure Cloudflare

1. **Add Subdomain A Record:**
   - Log in to Cloudflare dashboard
   - Go to DNS settings
   - Add an A record:
     - **Type:** A
     - **Name:** api-cards-robotic-club
     - **IPv4 address:** Your VPS IP address
     - **Proxy status:** Proxied (orange cloud) - recommended for DDoS protection
     - **TTL:** Auto

2. **SSL/TLS Settings:**
   - Go to SSL/TLS settings in Cloudflare
   - Choose one of these options:
     - **Full (strict):** Requires SSL certificate on your server (recommended)
     - **Full:** Works with self-signed certificates
     - **Flexible:** No SSL needed on server (simpler but less secure)

## Step 3: Deploy Your Application

### Upload Project to VPS

```bash
# On your local machine, use SCP or SFTP to upload the project
scp -r "D:\Robotic club\Attendence-Project" user@your-vps-ip:/home/user/

# Or use Git if your project is in a repository
git clone your-repo-url /home/user/Attendence-Project
```

### Configure Environment Variables

```bash
# SSH into your VPS
ssh user@your-vps-ip

# Navigate to project directory
cd /home/user/Attendence-Project

# Copy environment example
cp docker.env.example .env

# Edit .env file with your settings
nano .env
```

Update these important variables in `.env`:
```env
APP_NAME="Attendance Project"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api-cards-robotic-club.tech-sauce.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=attendance_user
DB_PASSWORD=your_secure_password

# Generate these using: php artisan key:generate
APP_KEY=base64:your-generated-key
JWT_SECRET=your-jwt-secret
```

## Step 4: Configure Nginx on VPS

### Option A: Using Cloudflare Flexible SSL (Easier)

```bash
# Copy the nginx config
sudo cp nginx-vps.conf /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com

# Edit the config file (if needed)
sudo nano /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com
```

Replace `attendance.yourdomain.com` with your actual subdomain, and **remove or comment out** the SSL certificate lines (lines starting with `ssl_certificate`).

Modify the HTTP server block to not redirect to HTTPS (since Cloudflare handles SSL):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name attendance.yourdomain.com;
    
    # Cloudflare Real IP (uncomment if using Cloudflare proxy)
    # ... (keep the real_ip settings)
    
    location / {
        proxy_pass http://127.0.0.1:8000;
        # ... (rest of proxy settings)
    }
}
```

### Option B: Using Let's Encrypt SSL (More Secure)

```bash
# Copy the nginx config
sudo cp nginx-vps.conf /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com

# Edit the config file (if needed)
sudo nano /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com
```

Replace `attendance.yourdomain.com` with your actual subdomain.

```bash
# Create symlink
sudo ln -s /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com /etc/nginx/sites-enabled/

# Test nginx configuration
sudo nginx -t

# Get SSL certificate
sudo certbot --nginx -d api-cards-robotic-club.tech-sauce.com

# Certbot will automatically configure SSL
```

### Enable the Site

```bash
# Create symlink (if not done by certbot)
sudo ln -s /etc/nginx/sites-available/api-cards-robotic-club.tech-sauce.com /etc/nginx/sites-enabled/

# Test nginx configuration
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx
```

## Step 5: Start Docker Containers

```bash
# Navigate to project directory
cd /home/user/Attendence-Project

# Start containers
docker-compose up -d

# Check logs
docker-compose logs -f

# Check if containers are running
docker-compose ps
```

## Step 6: Verify Deployment

1. **Check Docker containers:**
   ```bash
   docker ps
   ```

2. **Check Nginx status:**
   ```bash
   sudo systemctl status nginx
   ```

3. **Test your API:**
   ```bash
   curl https://api-cards-robotic-club.tech-sauce.com/api/hello
   ```

4. **Access in browser:**
   - Visit: `https://api-cards-robotic-club.tech-sauce.com`
   - Test API endpoint: `https://api-cards-robotic-club.tech-sauce.com/api/login`

## Step 7: Firewall Configuration

```bash
# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow SSH (if not already allowed)
sudo ufw allow 22/tcp

# Enable firewall
sudo ufw enable
```

## Troubleshooting

### Check Nginx Logs
```bash
sudo tail -f /var/log/nginx/api-cards-robotic-club-error.log
sudo tail -f /var/log/nginx/api-cards-robotic-club-access.log
```

### Check Docker Logs
```bash
docker-compose logs app
docker-compose logs mysql
```

### Test Nginx Configuration
```bash
sudo nginx -t
```

### Restart Services
```bash
# Restart Nginx
sudo systemctl restart nginx

# Restart Docker containers
docker-compose restart
```

### Common Issues

1. **502 Bad Gateway:**
   - Check if Docker container is running: `docker ps`
   - Check if port 8000 is accessible: `curl http://127.0.0.1:8000`
   - Verify proxy_pass URL in nginx config

2. **SSL Certificate Errors:**
   - If using Cloudflare Flexible SSL, remove SSL certificate lines from nginx config
   - If using Let's Encrypt, ensure certbot ran successfully

3. **Database Connection Issues:**
   - Check database credentials in `.env`
   - Verify MySQL container is running: `docker-compose ps`
   - Check database logs: `docker-compose logs mysql`

## Maintenance

### Update Application
```bash
cd /home/user/Attendence-Project
git pull  # if using git
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Backup Database
```bash
docker exec attendance_mysql mysqldump -u attendance_user -p attendance_db > backup.sql
```

### Renew SSL Certificate (Let's Encrypt)
```bash
sudo certbot renew
```

## Security Recommendations

1. **Use strong passwords** for database and application
2. **Keep system updated:** `sudo apt update && sudo apt upgrade`
3. **Configure firewall** properly
4. **Use Cloudflare's security features** (WAF, DDoS protection)
5. **Regular backups** of database and files
6. **Monitor logs** regularly
7. **Use Full (strict) SSL mode** in Cloudflare if possible

## API Endpoints

Your API will be available at:
- Base URL: `https://api-cards-robotic-club.tech-sauce.com`
- API Routes: `https://api-cards-robotic-club.tech-sauce.com/api/*`

Example endpoints:
- `POST https://api-cards-robotic-club.tech-sauce.com/api/login`
- `POST https://api-cards-robotic-club.tech-sauce.com/api/Transaction/{code}`
- `GET https://api-cards-robotic-club.tech-sauce.com/api/Attendance_Records_By_UserId/{user_id}`
- And more as defined in `routes/api.php`


