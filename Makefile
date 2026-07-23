.PHONY: setup dev test build serve migrate fresh clean docker-dev

# First-time project setup
setup:
	composer install
	npm install
	@if [ ! -f .env ]; then cp .env.example .env; fi
	php artisan key:generate
	php artisan migrate --force
	npm run build

# Start all dev services
dev:
	composer run dev

# Run test suite
test:
	composer run test

# Production asset build
build:
	npm run build

# PHP dev server only
serve:
	php artisan serve

# Run pending migrations
migrate:
	php artisan migrate

# Fresh database with seeds
fresh:
	php artisan migrate:fresh --seed

# Clear all Laravel caches
clean:
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	php artisan cache:clear
	php artisan clear-compiled

# Start the Docker dev environment (Dockerfile.dev + docker-compose.yml)
docker-dev:
	docker compose up