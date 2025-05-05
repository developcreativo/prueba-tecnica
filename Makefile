.PHONY: setup start stop restart build bash-php bash-mysql logs logs-php logs-mysql db-init test composer-install cs-fix

# Colors
COLOR_RESET=\033[0m
COLOR_INFO=\033[32m
COLOR_COMMENT=\033[33m

## Setup the entire environment
setup: build composer-install db-init

## Start all containers
start:
	@echo "${COLOR_INFO}Starting containers...${COLOR_RESET}"
	docker-compose up -d

## Stop all containers
stop:
	@echo "${COLOR_INFO}Stopping containers...${COLOR_RESET}"
	docker-compose down

## Restart all containers
restart: stop start

## Build containers
build:
	@echo "${COLOR_INFO}Building containers...${COLOR_RESET}"
	docker-compose build

## Enter PHP container shell
bash-php:
	@echo "${COLOR_INFO}Entering PHP container...${COLOR_RESET}"
	docker-compose exec php bash

## Enter MySQL container shell
bash-mysql:
	@echo "${COLOR_INFO}Entering MySQL container...${COLOR_RESET}"
	docker-compose exec mysql bash

## Display logs for all containers
logs:
	docker-compose logs -f

## Display logs for PHP container
logs-php:
	docker-compose logs -f php

## Display logs for MySQL container
logs-mysql:
	docker-compose logs -f mysql

## Initialize database schema using Doctrine
db-init:
	@echo "${COLOR_INFO}Creating database schema...${COLOR_RESET}"
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	docker-compose exec php php bin/console doctrine:schema:update --force

## Run tests
test:
	@echo "${COLOR_INFO}Running tests...${COLOR_RESET}"
	docker-compose exec php vendor/bin/phpunit

## Install composer dependencies
composer-install:
	@echo "${COLOR_INFO}Installing Composer dependencies...${COLOR_RESET}"
	docker-compose exec php composer install

## PHP code style fix
cs-fix:
	@echo "${COLOR_INFO}Fixing code style...${COLOR_RESET}"
	docker-compose exec php vendor/bin/php-cs-fixer fix src/

## Show this help
help:
	@echo "${COLOR_COMMENT}Usage:${COLOR_RESET}"
	@echo " make [target]"
	@echo ""
	@echo "${COLOR_COMMENT}Available targets:${COLOR_RESET}"
	@grep -E '^## [a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = "## "}; {printf " ${COLOR_INFO}%-20s${COLOR_RESET} %s\n", $$1, $$2}'

.DEFAULT_GOAL := help
