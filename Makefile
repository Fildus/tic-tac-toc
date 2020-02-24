user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose
drtest := $(dc) -f run --rm # todo: add docker-compose.test.yml

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
	$(drtest) php bin/phpunit
