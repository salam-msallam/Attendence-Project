# Attendance System - Docker Deployment Guide

This guide will help you deploy your Laravel Attendance System to a VPS using Docker with MySQL database.

## Prerequisites

- VPS with Ubuntu 20.04+ or CentOS 8+
- Docker and Docker Compose installed
- Domain name (optional, for production)
- SSL certificate (for production)

## Quick Start

### 1. Prepare Your VPS

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker
```

### 2. Upload Your Project

```bash
# Clone or upload your project to VPS
git clone <your-repository-url>
cd Attendence-Project

# Or upload via SCP/SFTP
scp -r ./Attendence-Project user@your-vps-ip:/home/user/
```

### 3. Configure Environment

```bash
# Copy the Docker environment file
cp docker.env .env

# Edit the environment file
nano .env
```

Update the following variables in `.env`:
- `APP_URL`: Your domain or VPS IP
- `DB_PASSWORD`: Strong password for database
- `DB_ROOT_PASSWORD`: Strong root password for MySQL
- `JWT_SECRET`: Will be auto-generated

### 4. Deploy with Docker

```bash
# Build and start containers
docker-compose up -d

# Check container status
docker-compose ps

# View logs
docker-compose logs -f app
```

### 5. Access Your Application

- **Application**: http://your-vps-ip:8000
- **API**: http://your-vps-ip:8000/api/
- **Health Check**: http://your-vps-ip:8000/health

## Production Deployment

### 1. SSL Certificate Setup

```bash
# Install Certbot
sudo apt install certbot

# Generate SSL certificate
sudo certbot certonly --standalone -d yourdomain.com

# Copy certificates to project
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem docker/ssl/cert.pem
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem docker/ssl/key.pem
sudo chown $USER:$USER docker/ssl/*.pem
```

### 2. Production Configuration

```bash
# Update docker-compose.yml to use production Nginx
# Uncomment the nginx service section

# Update environment for production
nano .env
```

Set these production values:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 3. Start Production Services

```bash
# Start all services including Nginx with SSL
docker-compose up -d

# Check all services
docker-compose ps
```

## Management Commands

### Container Management

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Restart services
docker-compose restart

# View logs
docker-compose logs -f [service_name]

# Execute commands in container
docker-compose exec app php artisan [command]
```

### Database Management

```bash
# Access MySQL container
docker-compose exec mysql mysql -u root -p

# Run migrations
docker-compose exec app php artisan migrate

# Run seeders
docker-compose exec app php artisan db:seed

# Backup database
docker-compose exec mysql mysqldump -u root -p attendance_db > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u root -p attendance_db < backup.sql
```

### Application Management

```bash
# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Optimize for production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Generate application key
docker-compose exec app php artisan key:generate

# Generate JWT secret
docker-compose exec app php artisan jwt:secret
```

## Monitoring and Maintenance

### Health Checks

```bash
# Check application health
curl http://your-vps-ip:8000/health

# Check container status
docker-compose ps

# Check resource usage
docker stats
```

### Log Monitoring

```bash
# Application logs
docker-compose logs -f app

# Database logs
docker-compose logs -f mysql

# Nginx logs
docker-compose logs -f nginx
```

### Backup Strategy

```bash
# Create backup script
cat > backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/user/backups"

mkdir -p $BACKUP_DIR

# Database backup
docker-compose exec -T mysql mysqldump -u root -p$DB_ROOT_PASSWORD attendance_db > $BACKUP_DIR/db_backup_$DATE.sql

# Application files backup
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz --exclude=node_modules --exclude=vendor --exclude=.git .

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
EOF

chmod +x backup.sh

# Schedule daily backups
crontab -e
# Add: 0 2 * * * /home/user/Attendence-Project/backup.sh
```

## Troubleshooting

### Common Issues

1. **Container won't start**
   ```bash
   # Check logs
   docker-compose logs [service_name]
   
   # Check port conflicts
   netstat -tulpn | grep :8000
   ```

2. **Database connection issues**
   ```bash
   # Check MySQL container
   docker-compose exec mysql mysql -u root -p
   
   # Verify environment variables
   docker-compose exec app env | grep DB_
   ```

3. **Permission issues**
   ```bash
   # Fix storage permissions
   docker-compose exec app chown -R www-data:www-data storage
   docker-compose exec app chmod -R 775 storage
   ```

4. **SSL certificate issues**
   ```bash
   # Check certificate files
   ls -la docker/ssl/
   
   # Test SSL configuration
   openssl s_client -connect yourdomain.com:443
   ```

### Performance Optimization

1. **Enable file-based caching**
   - Already configured in docker-compose.yml
   - Uses Laravel's file cache driver for simplicity

2. **Optimize PHP-FPM**
   - Adjust `pm.max_children` in `docker/php-fpm.conf`
   - Monitor with: `docker-compose exec app php-fpm -t`

3. **Database optimization**
   - Add indexes for frequently queried columns
   - Monitor slow queries: `docker-compose exec mysql mysql -u root -p -e "SHOW PROCESSLIST;"`

## Security Considerations

1. **Change default passwords**
   - Update `DB_PASSWORD` and `DB_ROOT_PASSWORD`
   - Use strong, unique passwords

2. **Firewall configuration**
   ```bash
   # Allow only necessary ports
   sudo ufw allow 22    # SSH
   sudo ufw allow 80    # HTTP
   sudo ufw allow 443   # HTTPS
   sudo ufw enable
   ```

3. **Regular updates**
   ```bash
   # Update Docker images
   docker-compose pull
   docker-compose up -d
   
   # Update system packages
   sudo apt update && sudo apt upgrade
   ```

## API Endpoints

Your application provides the following API endpoints:

- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user_info` - Get user information (requires auth)
- `GET /api/Attendance_Records` - Get attendance records (requires auth)
- `GET /api/Profile` - Get user profile (requires auth)
- `POST /api/Transaction/{code}` - Create card transaction (ESP endpoint)

Admin endpoints (require admin middleware):
- `POST /api/User` - Create user
- `GET /api/User` - Get all users
- `GET /api/User/{id}` - Get specific user
- `PUT /api/User/{id}` - Update user
- `DELETE /api/User/{id}` - Delete user
- `POST /api/Card/{user_id}` - Create card for user
- `GET /api/Card` - Get all cards
- `GET /api/Card/{card_id}` - Get specific card
- `PUT /api/Card/{card_id}` - Update card
- `DELETE /api/Card/{card_id}` - Delete card

## Support

For issues or questions:
1. Check the logs: `docker-compose logs -f`
2. Verify environment configuration
3. Ensure all required ports are open
4. Check database connectivity

## Scaling

To scale your application:

1. **Horizontal scaling**: Use multiple app containers behind a load balancer
2. **Database scaling**: Consider MySQL master-slave replication
3. **Caching**: Use file-based caching or consider database caching for high availability
4. **File storage**: Use cloud storage (S3, etc.) for file uploads

Remember to monitor resource usage and adjust container limits as needed.
