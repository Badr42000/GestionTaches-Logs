.PHONY: dev phpstan test

dev:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build

phpstan:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml run --rm web composer install --no-interaction
	docker compose -f docker-compose.yml -f docker-compose.dev.yml run --rm web ./vendor/bin/phpstan analyse --configuration phpstan.neon

test:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml run --rm web composer install --no-interaction
	docker compose -f docker-compose.yml -f docker-compose.dev.yml run --rm web ./vendor/bin/phpunit
