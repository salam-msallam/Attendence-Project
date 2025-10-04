# Makefile for Attendance System Docker Deployment

.PHONY: help build up down restart logs shell migrate seed backup clean

# Default target
help: ## Show this help message
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Development commands
build: ## Build Docker images
	docker-compose build

up: ## Start all services
	docker-compose up -d

down: ## Stop all services
	docker-compose down

restart: ## Restart all services
	docker-compose restart

logs: ## Show logs for all services
	docker-compose logs -f

logs-app: ## Show logs for app service
	docker-compose logs -f app

logs-mysql: ## Show logs for MySQL service
	docker-compose logs -f mysql


# Application commands
shell: ## Access app container shell
	docker-compose exec app sh

shell-mysql: ## Access MySQL container shell
	docker-compose exec mysql mysql -u root -p

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migration with seeding
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

# Cache commands
cache-clear: ## Clear all caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

cache-optimize: ## Optimize caches for production
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

# Key generation
key-generate: ## Generate application key
	docker-compose exec app php artisan key:generate

jwt-secret: ## Generate JWT secret
	docker-compose exec app php artisan jwt:secret

# Storage commands
storage-link: ## Create storage symlink
	docker-compose exec app php artisan storage:link

# Backup commands
backup-db: ## Backup database
	docker-compose exec mysql mysqldump -u root -p$$(grep DB_ROOT_PASSWORD docker.env | cut -d '=' -f2) attendance_db > backup_$$(date +%Y%m%d_%H%M%S).sql

backup-app: ## Backup application files
	tar -czf app_backup_$$(date +%Y%m%d_%H%M%S).tar.gz --exclude=node_modules --exclude=vendor --exclude=.git --exclude=storage/logs .

# Production commands
prod-build: ## Build production images
	docker-compose -f docker-compose.prod.yml build

prod-up: ## Start production services
	docker-compose -f docker-compose.prod.yml up -d

prod-down: ## Stop production services
	docker-compose -f docker-compose.prod.yml down

prod-restart: ## Restart production services
	docker-compose -f docker-compose.prod.yml restart

prod-logs: ## Show production logs
	docker-compose -f docker-compose.prod.yml logs -f

# Maintenance commands
clean: ## Clean up unused Docker resources
	docker system prune -f
	docker volume prune -f

clean-all: ## Clean up all Docker resources (WARNING: removes all containers, images, volumes)
	docker system prune -a -f --volumes

# Health check
health: ## Check application health
	@echo "Checking application health..."
	@curl -f http://localhost:8000/health || echo "Application is not responding"
	@echo "Checking MySQL connection..."
	@docker-compose exec mysql mysql -u root -p$$(grep DB_ROOT_PASSWORD docker.env | cut -d '=' -f2) -e "SELECT 1;" > /dev/null && echo "MySQL is healthy" || echo "MySQL connection failed"

# Status
status: ## Show container status
	docker-compose ps

# Install dependencies
install: ## Install PHP dependencies
	docker-compose exec app composer install --no-dev --optimize-autoloader

# Update dependencies
update: ## Update PHP dependencies
	docker-compose exec app composer update --no-dev --optimize-autoloader
