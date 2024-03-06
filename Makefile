APP_VERSION = $$(git describe --tags `git rev-list --tags --max-count=1` | cut -c 2- ) # Get latest tag without the "v" prefix
IMAGE ?= 68publishers/cmp

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
	make stop-worker
	make start
	make start-worker

cache:
	docker exec -it cmp-app bin/console

cache-clear:
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf var/mail-panel-latte

db-clear:
	rm -rf var/postgres-data/*

build.local.%:
	@echo "building a local image $(IMAGE):app-$* ..."
	@docker build -t $(IMAGE):app-$* -f ./docker/build/Dockerfile --target app .
	@echo "done"
	@echo "building a local image $(IMAGE):worker-$* ..."
	@docker build -t $(IMAGE):worker-$* -f ./docker/build/Dockerfile --target worker .
	@echo "done"

build.multiarch.%:
	@docker buildx inspect multi_arch_builder > /dev/null 2>&1; \
	if [ $$? -ne 0 ]; then \
		echo "builder multi_arch_builder does not exist, initialization starting..."; \
		docker buildx create --name multi_arch_builder --driver docker-container --bootstrap; \
		echo "done"; \
	else \
		echo "builder multi_arch_builder does exist, skipping initialization."; \
	fi
	@echo "building multiplatform [linux/arm64/v8, linux/amd64] image $(IMAGE):app-$* ..."
	@docker buildx build \
		-f ./docker/build/Dockerfile \
		--pull \
		--push \
		--builder multi_arch_builder \
		--platform linux/arm64/v8,linux/amd64 \
		--provenance=false \
		--target app \
		-t ${IMAGE}:app-$* \
		.
	@echo "done"
	@echo "building multiplatform [linux/arm64/v8, linux/amd64] image $(IMAGE):worker-$* ..."
	@docker buildx build \
		-f ./docker/build/Dockerfile \
		--pull \
		--push \
		--builder multi_arch_builder \
		--platform linux/arm64/v8,linux/amd64 \
		--provenance=false \
		--target worker \
		-t $(IMAGE):worker-$* \
		.
	@echo "done"

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
	make start-worker

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
