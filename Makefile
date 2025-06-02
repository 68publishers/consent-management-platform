APP_VERSION = $$(git describe --tags `git rev-list --tags --max-count=1` | cut -c 2- ) # Get latest tag without the "v" prefix
IMAGE ?= 68publishers/cmp
COMPOSE_ENV := "local"# "local" or "stage"

ifneq (,$(wildcard ./.env.dist))
	include .env.dist
	export
endif

ifneq (,$(wildcard ./.env))
	include .env
	export
endif

.PHONY: tests

start:
	docker compose --profile web up -d
	@echo "\033[1;92mvisit https://${NGINX_DOMAIN_NAME}\033[0m"

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
	make database-migrate

mkcert:
	@if [ "stage" = "${COMPOSE_ENV}" ]; then \
  		docker run -it --rm --name certbot \
  			-v ./docker/nginx/certs:/etc/letsencrypt \
  			-v ./docker/certbot/www:/var/www/certbot \
  			-v ./docker/certbot/secrets:/.secrets \
  			certbot/dns-digitalocean certonly \
  			--dns-digitalocean \
  			--dns-digitalocean-credentials /.secrets/digitalocean.ini \
  			--dns-digitalocean-propagation-seconds 60 \
  			--cert-name "${NGINX_DOMAIN_NAME}" \
  			-d "${NGINX_DOMAIN_NAME}" \
  			--text --agree-tos --email "${CERTBOT_EMAIL}" --rsa-key-size 4096 --verbose; \
	else \
		cd ./docker/nginx/certs && mkdir -p "live/${NGINX_DOMAIN_NAME}" && cd "./live/${NGINX_DOMAIN_NAME}" && mkcert -key-file privkey.pem -cert-file fullchain.pem ${NGINX_DOMAIN_NAME}; \
	fi
	@echo "certificates successfully created"

# Stage only
certs-renew:
	@if [ "stage" != "${COMPOSE_ENV}" ]; then \
  		echo "\033[1;91mError: The command certs-renew can be called in the stage environment only.\033[0m"; \
  		exit 1; \
  	fi
	@docker run -it --rm --name certbot \
		-v ./docker/nginx/certs:/etc/letsencrypt \
		-v ./docker/certbot/www:/var/www/certbot \
		-v ./docker/certbot/secrets:/.secrets \
		-v ./docker/certbot/renew:/renew-hook \
		certbot/dns-digitalocean renew \
		--post-hook "touch /renew-hook/renewed.txt";
	@make certs-renew.post-hook

certs-renew.post-hook:
ifneq (,$(wildcard ./docker/certbot/renew/renewed.txt))
	@rm ./docker/certbot/renew/renewed.txt
	@docker exec -it cmp-nginx nginx -s reload
	@echo "\033[0;34mNginx reloaded\033[0m"
endif

cache:
	docker exec -it cmp-app bin/console

cache-clear:
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf var/mail-panel-latte
	rm -rf var/mail-panel-mails

redis-clear:
	docker exec -it cmp-redis redis-cli -a redis_pass flushall

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
	make redis-clear
	make install-composer
	make install-assets
	make database-migrate
	docker exec -it cmp-app bin/console messenger:stop-workers

install-composer:
	docker exec -it cmp-app composer install --no-interaction --no-ansi --prefer-dist --no-progress --optimize-autoloader

install-assets:
	docker exec -it cmp-app yarn install --no-progress --non-interactive
	docker exec -it cmp-app yarn run encore prod

init-with-certs:
	@echo "\033[1;94mDo you want to setup the application on a domain ${NGINX_DOMAIN_NAME} with \"${COMPOSE_ENV}\" environment? [y/n]\033[0m"
	@read line; if [ $$line != "y" ]; then echo "aborting"; exit 1 ; fi
	@if [ "stage" != "${COMPOSE_ENV}" ]; then \
		make mkcert; \
		make init; \
	else \
	  	NGINX_TEMPLATE_DIR=/etc/nginx/templates/nossl docker compose --profile web stop; \
	  	NGINX_TEMPLATE_DIR=/etc/nginx/templates/nossl docker compose --profile web up -d; \
	  	make install; \
	  	make mkcert; \
	  	NGINX_TEMPLATE_DIR=/etc/nginx/templates/nossl docker compose --profile web stop; \
	  	make init; \
	fi

init:
	make stop
	make stop-worker
	make start
	make install
	make start-worker

data:
	make data-migration
	docker exec -it cmp-app bin/console doctrine:fixtures:load --no-interaction

database-migrate:
	docker exec -it cmp-app bin/console migrations:migrate --no-interaction

fixtures:
	@echo "\033[1;91mWarning: running this command without `--append` option will remove all existing data in the database!\033[0m"
	docker exec -it cmp-app bin/console doctrine:fixtures:load --append --no-interaction

tests:
	docker exec cmp-app vendor/bin/tester -C -s ./tests

qa:
	@echo "not implemented" >&2

cs:
	docker exec -it -e PHP_CS_FIXER_IGNORE_ENV=1 cmp-app ./vendor/bin/php-cs-fixer fix -v

coverage:
	@echo "not implemented" >&2

version:
	@echo ${APP_VERSION}
