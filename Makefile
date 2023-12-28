include docker-compose.env
-include docker-compose-dev.env

#export COMPOSE_FILE=docker-compose-dev.yml
export COMPOSE_PROJECT_NAME?=compose
export DOCKER_BUILDKIT?=1
export COMPOSE_CONVERT_WINDOWS_PATHS?=1
export TZ?=UTC
export BUILD_DATE?=$(shell TZ=":UTC" date '+%Y-%m-%d %H:%M:%S (%Z)')
export BUILD_FINGERPRINT?=N/A
.EXPORT_ALL_VARIABLES:
.PHONY: *
.DEFAULT_GOAL := help

THIS_FILE := $(abspath $(lastword $(MAKEFILE_LIST)))
CURRENT_DIR := $(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))

ifeq (,$(shell command docker compose 2> /dev/null))
  DOCKER_COMPOSE_COMMAND = docker-compose
else
  DOCKER_COMPOSE_COMMAND = docker compose
endif

help: ## Show this help
	@echo "Make Application Docker Images and Containers using Docker-Compose files in 'docker' Dir."
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m (default: help)\n\nTargets:\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

up: ## Docker compose up
	$(DOCKER_COMPOSE_COMMAND) pull php
	$(DOCKER_COMPOSE_COMMAND) up --build --no-deps --detach --remove-orphans

start:
	make up

down: ## Docker compose down
	$(DOCKER_COMPOSE_COMMAND) down --remove-orphans

stop: ## Docker compose stop
	$(DOCKER_COMPOSE_COMMAND) stop

restart: ## Restart containers
	make down
	make up
	$(info Restart completed)

update: ## Update containers
	$(DOCKER_COMPOSE_COMMAND) pull
	make up

destroy: ## Destroy containers/volumes (keep sources app folders)
	make stop
	$(DOCKER_COMPOSE_COMMAND) down --rmi all --remove-orphans

rebuild: ## Rebuild docker container (destroy & upgrade)
	make destroy
	make up

state: ## Show current state
	docker ps --format=table

logs: ## Show docker logs
	$(DOCKER_COMPOSE_COMMAND) logs -f --tail=100 $(ARGS)

php: ## PHP Container shell
	$(DOCKER_COMPOSE_COMMAND) exec php bash

php-root: ## PHP Container shell (root)
	$(DOCKER_COMPOSE_COMMAND) exec -u root php bash

idehelper: ## Generate Laravel Ide Helper
	@if [ -f /.dockerenv ]; then\
		composer run-script post-update-cmd;\
	else\
		$(DOCKER_COMPOSE_COMMAND) exec php sh -c 'composer run-script post-update-cmd';\
	fi

migrate: ## Run migrations
	@if [ -f /.dockerenv ]; then\
		php artisan migrate;\
	else\
		$(DOCKER_COMPOSE_COMMAND) exec php sh -c 'php artisan migrate';\
	fi

cs: ## Execute PHP CS Fixer
	@if [ -f /.dockerenv ]; then\
		php-cs-fixer fix --diff --verbose;\
	else\
		$(DOCKER_COMPOSE_COMMAND) exec php sh -c 'php-cs-fixer fix --diff --verbose';\
	fi

rector: ## Execute Rector PHP
	@if [ -f /.dockerenv ]; then\
		rector process;\
	else\
		$(DOCKER_COMPOSE_COMMAND) exec php sh -c 'rector process';\
	fi

stan: ## Execute PHPStan
	@if [ -f /.dockerenv ]; then\
		phpstan analyse;\
	else\
		$(DOCKER_COMPOSE_COMMAND) exec php sh -c 'phpstan analyse';\
	fi

nodejs: ## nodejs Container shell
	$(DOCKER_COMPOSE_COMMAND) exec nodejs bash

