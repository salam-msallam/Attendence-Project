# Attendance Management System - Docker Deployment

This guide will help you deploy the Attendance Management System using Docker on your VPS.

## Prerequisites

- Docker and Docker Compose installed on your VPS
- Git (to clone the repository)
- Basic knowledge of Linux commands

## Quick Start

1. **Clone the repository** (if not already done):
   ```bash
   git clone <your-repository-url>
   cd Attendence-Project
   ```

2. **Set up environment variables**:
   ```bash
   cp docker.env.example .env
   ```
   
   Edit the `.env` file with your specific configuration:
   - Change database passwords for security
   - Update `APP_URL` to your domain/IP
   - Modify other settings as needed

3. **Build and start the containers**:
   ```bash
   docker-compose up -d --build
   ```

4. **Access your application**:
   - Application: `http://your-server-ip:8000`
   - MySQL: `your-server-ip:3307`

## Configuration

### Environment Variables

Key environment variables you should customize:

- `DB_DATABASE`: Database name (default: attendance_db)
- `DB_USERNAME`: Database user (default: attendance_user)
- `DB_PASSWORD`: Database password (default: attendance_password)
- `DB_ROOT_PASSWORD`: MySQL root password (default: attendance_root_password)
- `APP_URL`: Your application URL
- `APP_KEY`: Laravel application key (auto-generated)

### Ports

- **8000**: Laravel application (HTTP)
- **3307**: MySQL database

### Volumes

- `mysql_data`: Persistent MySQL data storage
- `./storage`: Laravel storage directory
- `./bootstrap/cache`: Laravel cache directory

## Docker Services

### MySQL Database
- **Image**: mysql:8.0
- **Container**: attendance_mysql
- **Port**: 3307 (external) → 3306 (internal)
- **Data**: Persisted in `mysql_data` volume

### Laravel Application
- **Build**: Custom Dockerfile
- **Container**: attendance_app
- **Port**: 8000 (external) → 80 (internal)
- **Features**: PHP 8.2, Nginx, Supervisor

## Useful Commands

### Container Management
```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Rebuild containers
docker-compose up -d --build

# Access application container
docker-compose exec app sh

# Access MySQL container
docker-compose exec mysql mysql -u root -p
```

### Laravel Commands
```bash
# Run Laravel commands inside container
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
```

### Database Management
```bash
# Create database backup
docker-compose exec mysql mysqldump -u root -p attendance_db > backup.sql

# Restore database backup
docker-compose exec -T mysql mysql -u root -p attendance_db < backup.sql
```

## Production Considerations

### Security
1. **Change default passwords** in `.env` file
2. **Use HTTPS** by setting up SSL certificates
3. **Configure firewall** to only allow necessary ports
4. **Regular updates** of Docker images

### Performance
1. **Resource limits**: Add memory/CPU limits in docker-compose.yml
2. **Caching**: Configure Redis for better performance
3. **CDN**: Use a CDN for static assets

### Monitoring
1. **Logs**: Monitor application and database logs
2. **Health checks**: Implement health check endpoints
3. **Backups**: Set up automated database backups

## Troubleshooting

### Common Issues

1. **Permission errors**:
   ```bash
   docker-compose exec app chown -R www-data:www-data /var/www/html/storage
   ```

2. **Database connection issues**:
   - Check if MySQL container is running: `docker-compose ps`
   - Verify database credentials in `.env`

3. **Application not accessible**:
   - Check if port 8000 is open in firewall
   - Verify container is running: `docker-compose logs app`

4. **Migration errors**:
   ```bash
   docker-compose exec app php artisan migrate:fresh --seed
   ```

### Logs
```bash
# Application logs
docker-compose logs app

# Database logs
docker-compose logs mysql

# All logs
docker-compose logs
```

## File Structure

```
Attendence-Project/
├── docker/
│   ├── mysql/
│   │   └── init/          # MySQL initialization scripts
│   ├── nginx.conf         # Nginx configuration
│   ├── php-fpm.conf       # PHP-FPM configuration
│   ├── supervisord.conf   # Supervisor configuration
│   └── entrypoint.sh      # Container startup script
├── Dockerfile            # Application container definition
├── docker-compose.yml    # Multi-container setup
└── docker.env.example    # Environment variables template
```

## Support

For issues or questions:
1. Check the logs: `docker-compose logs`
2. Verify configuration in `.env` file
3. Ensure all required ports are accessible
4. Check Docker and Docker Compose versions
