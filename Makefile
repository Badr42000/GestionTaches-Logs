.PHONY: dev phpstan test

RUN_DEV = docker compose -f docker-compose.yml -f docker-compose.dev.yml run --rm --entrypoint "" web

dev:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build

phpstan:
	$(RUN_DEV) composer install --no-interaction
	$(RUN_DEV) ./vendor/bin/phpstan analyse --configuration phpstan.neon

test:
	$(RUN_DEV) composer install --no-interaction
	$(RUN_DEV) ./vendor/bin/phpunit
