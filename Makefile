user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose
drit := docker run --user $(user):$(group) -it

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
	$(drit) -v $(PWD):/app:rw --workdir /app composer install

node_modules:
	$(drit) -v $(PWD):/usr/src/app:rw -w /usr/src/app node:12-alpine yarn

.PHONY: install
install: vendor node_modules dev## Installe les dependances node et php
	$(dc) exec php bin/console d:s:u --force
