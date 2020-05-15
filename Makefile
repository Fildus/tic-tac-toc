user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose
dr := $(dc) run --rm
de := docker-compose exec
sy := $(de) php bin/console
drtest := $(dc) -f docker-compose.test.yml

.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: dev
dev: install clean ## Lance le serveur de développement
	$(dc) up -d
	$(dc) exec php bin/console app:database-ready dev
	$(dc) exec php bin/console d:d:c --env=dev --if-not-exists
	$(dc) exec php bin/console d:s:u --env=dev --force
	$(dc) exec php bin/console d:f:l --env=dev -n

.PHONY: clean
clean: ## Nettoie les containers
	$(dc) down --volumes

.PHONY: build-test
build-test:
	$(dc) -f docker-compose.test.yml up --build -d
	$(drtest) exec php-test bin/console app:database-ready test
	$(drtest) exec php-test bin/console d:d:c --env=test --if-not-exists
	$(drtest) exec php-test bin/console d:s:u --env=test --force
	$(drtest) exec php-test bin/console d:f:l --env=test -n

.PHONY: test
test: install build-test ## Lance les tests
	$(drtest) exec php-test vendor/bin/phpunit
	$(dc) -f docker-compose.test.yml down

test-coverage: install build-test ## Lance les tests avec coverage
	$(drtest) exec php-test vendor/bin/phpunit --coverage-html="var/test"
	$(dc) -f docker-compose.test.yml down

.PHONY: tt
tt: install build-test ## Lance le watcher phpunit
	$(drtest) run php-test vendor/bin/phpunit-watcher watch --filter="nothing"
	$(dc) -f docker-compose.test.yml down

.PHONY: lint
lint: ## Analyse le code
	docker run -v $(PWD):/app --rm phpstan/phpstan analyse

.PHONY: fix
fix: ## Lance php-cs-fixer
	$(dc) run --rm php vendor/bin/php-cs-fixer fix --allow-risky=yes

vendor:
	$(dr) --no-deps php composer install

node_modules:
	$(dr) --no-deps node yarn

.PHONY: public/build
public/build:
	$(dr) --no-deps node yarn run build

.PHONY: reload_database
reload_database: ## relance la base de données
	$(drtest) exec php-test bin/console app:database-ready test
	$(drtest) exec php-test bin/console d:d:c --env=test --if-not-exists
	$(drtest) exec php-test bin/console d:s:u --env=test --force
	$(drtest) exec php-test bin/console d:f:l --env=test -n

.PHONY: install
install: vendor node_modules public/build ## Installe les dependances node et php
