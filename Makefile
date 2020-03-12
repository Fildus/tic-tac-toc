user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose
dr := $(dc) run --rm
de := docker-compose exec
sy := $(de) php bin/console
drtest := $(dc) -f docker-compose.test.yml run

.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: dev
dev: install ## Lance le serveur de développement
	$(dc) up -d

.PHONY: clean
clean: ## Nettoie les containers
	$(dc) down --volumes

.PHONY: test
test: ## Lance les tests
	$(dc) -f docker-compose.test.yml up --build -d
	$(drtest) php-test bin/console app:database-ready
	$(drtest) php-test bin/console d:d:c --env=test --if-not-exists
	$(drtest) php-test bin/console d:s:u --env=test --force
	$(drtest) php-test bin/console d:f:l --env=test --group=test -n
	$(drtest) php-test vendor/bin/phpunit
	$(dc) -f docker-compose.test.yml down

.PHONY: lint
lint: vendor/autoload.php ## Analyse le code
	docker run -v $(PWD):/app --rm phpstan/phpstan analyse

.PHONY: fix
fix: ## Lance php-cs-fixer
	$(dc) exec php vendor/bin/php-cs-fixer fix

vendor:
	$(dr) --no-deps php composer install

node_modules:
	$(dr) --no-deps node yarn

public/build:
	$(dr) --no-deps node yarn run build

.PHONY: install
install: vendor node_modules public/build ## Installe les dependances node et php
