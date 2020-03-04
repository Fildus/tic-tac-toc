user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose

.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: dev
dev: ## Lance le serveur de développement
	$(dc) up -d --build --force

.PHONY: clean
clean: ## Nettoie les containers
	$(dc) down --volumes

.PHONY: test
test: ## Lance les tests
	$(dc) exec php bin/phpunit

.PHONY: fix
fix: ## Lance php-cs-fixer
	$(dc) exec php vendor/bin/php-cs-fixer fix

vendor:
	env USER_ID=1000 GROUP_ID=1000 docker-compose exec php composer install

node_modules:
	docker run --user (id -u):(id -g) -it -v $PWD:/usr/src/app:rw -w /usr/src/app node:12-alpine yarn

.PHONY: install
install: vendor node_modules ## Installe les dependances node et php
