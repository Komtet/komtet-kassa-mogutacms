SHELL:=/bin/bash
VERSION=$(shell grep -o '^[0-9]\+\.[0-9]\+\.[0-9]\+' CHANGELOG.rst | head -n1)
DIST_MARKET_DIR="dist/$(VERSION)"
PROJECT_DIR="komtet-kassa"
PROJECT_ZIP="$(PROJECT_DIR).zip"

# Colors
COLOR_OFF=\033[0m
RED=\033[1;31m
YELLOW=\e[33m
CYAN=\033[1;36m


version:  ## Версия проекта
	@echo -e "${RED}Version:${COLOR_OFF} $(VERSION)"

help:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[0;36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST) | sort

build:  ## Собрать контейнер
	@docker-compose build

stop: ## Остановить все контейнеры
	@docker-compose down

start: stop  ## Запустить контейнер
	@docker-compose up

update:  ## Установить/Обновить модуль
	@rm -rf php/mg-plugins/komtet-kassa/* &&\
	cp -r komtet-kassa/. php/mg-plugins/komtet-kassa/

release: ## Создать архив для загрузки в маркет
	@mkdir -p $(DIST_MARKET_DIR)
	@zip -r -q $(DIST_MARKET_DIR)/$(PROJECT_ZIP) $(PROJECT_DIR)

	@echo -e "${CYAN}Сборка обновлений завершена. ${COLOR_OFF}"
	@echo -e "${CYAN}Для маркета: ${COLOR_OFF} ${YELLOW}${DIST_MARKET_DIR}/${PROJECT_ZIP}${COLOR_OFF}"

.PHONY: help build start stop version release
.DEFAULT_GOAL := help
