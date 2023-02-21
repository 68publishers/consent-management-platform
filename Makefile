APP_VERSION=$$(git describe --tags `git rev-list --tags --max-count=1` | cut -c 2- ) # Get latest tag without the "v" prefix
DOCKER_COMPOSE_FILE=docker-compose.yml # Use docker-compose.prod.yml to test the built images

start:
	docker compose -f ${DOCKER_COMPOSE_FILE} --profile web up -d
	make visit

start-worker:
	docker compose -f ${DOCKER_COMPOSE_FILE} --profile worker up -d

stop:
	docker compose -f ${DOCKER_COMPOSE_FILE} --profile web --profile worker stop

stop-worker:
	docker compose -f ${DOCKER_COMPOSE_FILE} --profile worker stop

down:
	docker compose -f ${DOCKER_COMPOSE_FILE} --profile web --profile worker down

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
	make start
	make install
	make cache-clear
	docker exec -it cmp-app ./vendor/bin/tracy-git-version export-repository --output-file ./var/git-version/repository.json -vv
	make cache
	make db-clear
	docker build -f ./docker/app/Dockerfile -t 68publishers/cmp:latest -t "registry.hptronic.cz/dev/cmp/cmp:latest" -t "registry.hptronic.cz/dev/cmp/cmp:"${APP_VERSION} .
	docker build -f ./docker/worker/Dockerfile -t 68publishers/cmp:worker-latest -t "registry.hptronic.cz/dev/cmp/cmp/worker:latest" -t "registry.hptronic.cz/dev/cmp/cmp/worker:"${APP_VERSION} .

rebuild:
	make build
	make restart

push:
	@echo "Pushing to docker registry: $(APP_VERSION)\n-------------"
	docker push registry.hptronic.cz/dev/cmp/cmp:$(APP_VERSION)
	docker push registry.hptronic.cz/dev/cmp/cmp/worker:$(APP_VERSION)

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
	make visit

data:
	make data-migration
	docker exec -it cmp-app bin/console doctrine:fixtures:load --no-interaction

data-migration:
	docker exec -it cmp-app bin/console migrations:migrate --no-interaction

tests:
	@echo "not implemented" >&2

qa:
	@echo "not implemented" >&2

cs:
	./vendor/bin/php-cs-fixer fix -v

coverage:
	@echo "not implemented" >&2

visit:
	@echo "visit http://localhost:8888"

info:
	@echo APP_VERSION=${APP_VERSION}
	@echo DOCKER_COMPOSE_FILE=${DOCKER_COMPOSE_FILE}
	@echo IMAGE_REGISTRY=${IMAGE_REGISTRY}
