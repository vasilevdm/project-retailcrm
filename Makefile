up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up retailcrm-init

docker-up:
	docker-compose up

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

retailcrm-init:
	retailcrm-composer-install

retailcrm-composer-install:
	docker-compose run --rm retailcrm-php-cli composer install
