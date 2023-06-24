APP_VERSION=$$(git describe --tags `git rev-list --tags --max-count=1` | cut -c 2- ) # Get latest tag without the "v" prefix

start:
	docker compose --profile web up -d
	@echo "visit http://localhost:8888"

start-worker:
	docker compose --profile worker up -d

stop:
	docker compose --profile web --profile worker stop

stop-worker:
	docker compose --profile worker stop

down:
	docker compose --profile web --profile worker down

restart:
	make stop
	make start
	make data-migration

cache:
	docker exec -it cmp-app bin/console

cache-clear:
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf var/mail-panel-latte

db-clear:
	rm -rf var/postgres-data/*

build:
	@echo "Building version: $(APP_VERSION)\n-----------------------"
	make install
	make cache-clear
	./vendor/bin/tracy-git-version export-repository --output-file ./var/git-version/repository.json -vv
	make cache
	make db-clear
	docker build -f ./docker/app/prod/Dockerfile -t 68publishers/cmp:latest .

rebuild:
	make build
	make restart

install:
	make cache-clear
	make install-composer
	make install-assets
	# Duplicity with init
	make data-migration

install-composer:
	docker exec -it cmp-app composer install --no-interaction --no-ansi --prefer-dist --no-progress --optimize-autoloader

install-assets:
	docker exec -it cmp-app yarn install --no-progress --non-interactive
	docker exec -it cmp-app yarn run encore prod

init:
	make stop
	make db-clear
	make start
	make install
	make data

data:
	make data-migration
	docker exec -it cmp-app bin/console doctrine:fixtures:load --no-interaction

data-migration:
	docker exec -it cmp-app bin/console migrations:migrate --no-interaction

.PHONY: tests
tests:
	docker exec cmp-app vendor/bin/tester -C -s ./tests

qa:
	@echo "not implemented" >&2

cs:
	docker exec -it cmp-app ./vendor/bin/php-cs-fixer fix -v

coverage:
	@echo "not implemented" >&2

version:
	@echo ${APP_VERSION}
