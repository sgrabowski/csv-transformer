# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up down logs sh composer-install phpmd phpcs phpstan validate tests

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## —— Composer 🤖 ————————————————————————————————————————————————————————————--
composer-install: ## install composer dependencies
	@$(COMPOSER) install --prefer-dist --no-progress --no-interaction

## —— Validation 🤠 ————————————————————————————————————————————————————————————
qa: fixers validate tests

validate: phpmd phpstan phpcs ## run all code validators

phpmd: ## run php mess detector
	@$(PHP_CONT) vendor/bin/phpmd src/ text phpmd.xml

phpstan: ## run php static analysis
	@$(PHP_CONT) vendor/bin/phpstan analyse

phpcs: ## run php code sniffer
	@$(PHP_CONT) vendor/bin/phpcs -p --colors

## -- Fixers 🔧 ----------------------------------------------------------------
fixers: phpcbf phpcsfixer phpcsfixer-tests ## run all code fixers

phpcbf: ## Run PHP Code Beautifier and Fixer using PHPCS rules
	-@$(PHP_CONT) vendor/bin/phpcbf

phpcsfixer: ## Run php-cs-fixer
	-@$(PHP_CONT) vendor/bin/php-cs-fixer fix src

phpcsfixer-tests: ## Run php-cs-fixer for tests
	-@$(PHP_CONT) vendor/bin/php-cs-fixer fix tests

## -- Tests 🤯 ————————————————————————————————————————————————————————————-----
tests: tests-unit ## run all tests

tests-unit: ## run unit tests only
	@$(PHP_CONT) vendor/bin/phpunit