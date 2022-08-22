start:
	docker compose up -d
	echo "visit http://localhost:8888"

stop:
	docker compose stop

down:
	docker compose down

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
	make install
	make cache-clear
	./vendor/bin/tracy-git-version export-repository --output-file ./var/git-version/repository.json -vv
	make cache
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

tests:
	echo "not implemented"

qa:
	echo "not implemented"

cs:
	./vendor/bin/php-cs-fixer fix -v

coverage:
	echo "not implemented"
