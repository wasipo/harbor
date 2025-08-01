# Harbor - Laravel 12 Project Makefile

.PHONY: help install up down restart logs test lint fix stan

# Default target
help:
	@echo "Harbor - Laravel 12 Project Commands"
	@echo ""
	@echo "Setup:"
	@echo "  make install    Install dependencies and setup environment"
	@echo "  make up         Start all containers"
	@echo "  make down       Stop all containers"
	@echo ""
	@echo "Development:"
	@echo "  make restart    Restart all containers"
	@echo "  make logs       Show container logs"
	@echo "  make shell      Access PHP container shell"
	@echo ""
	@echo "Testing & Code Quality:"
	@echo "  make test       Run PHPUnit tests"
	@echo "  make lint       Check code style with Pint"
	@echo "  make fix        Fix code style with Pint"
	@echo "  make stan       Run PHPStan static analysis"
	@echo ""
	@echo "Laravel Commands:"
	@echo "  make migrate    Run database migrations"
	@echo "  make seed       Run database seeders"
	@echo "  make fresh      Fresh migrate with seeding"

# Setup commands
install:
	cp .env.example .env
	docker-compose build
	docker-compose up -d
	docker-compose exec php composer install
	docker-compose exec php php artisan key:generate
	docker-compose exec php php artisan config:clear
	@echo "✅ Installation complete! Access: http://localhost:8080"

install-simple:
	cp .env.example .env
	docker-compose up -d mysql php nginx
	@echo "✅ Simple installation complete! MySQL + PHP + Nginx only"

install-full:
	$(MAKE) install
	@echo "✅ Full installation with all services"

up:
	docker-compose up -d
	@echo "✅ Containers started"

down:
	docker-compose down
	@echo "✅ Containers stopped"

restart:
	docker-compose restart
	@echo "✅ Containers restarted"

# Development commands
logs:
	docker-compose logs -f

status:
	docker-compose ps
	@echo ""
	@echo "Container status:"
	@docker ps -a | grep harbor

shell:
	docker-compose exec php bash

# Testing & Code Quality
test:
	docker-compose exec php php artisan test

lint:
	docker-compose exec php ./vendor/bin/pint --test

fix:
	docker-compose exec php ./vendor/bin/pint

stan:
	docker-compose exec php ./vendor/bin/phpstan analyse --memory-limit=256M

# Laravel commands
migrate:
	docker-compose exec php php artisan migrate

seed:
	docker-compose exec php php artisan db:seed

fresh:
	docker-compose exec php php artisan migrate:fresh --seed

# Cache commands
clear:
	docker-compose exec php php artisan optimize:clear
	@echo "✅ Cache cleared"

optimize:
	docker-compose exec php php artisan optimize
	@echo "✅ Application optimized"