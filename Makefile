# Default values
env = dev

ifeq ($(ci),true)
    dryrun = --dry-run --diff
endif

cs:
	docker-compose exec -T php php vendor/bin/php-cs-fixer fix ${dryrun}
	docker-compose exec -T php php vendor/bin/phpstan analyse --memory-limit 512M

	docker-compose exec -T php php bin/console lint:twig templates/
	docker-compose exec -T php php bin/console lint:yaml --parse-tags config/
	docker-compose exec -T php php bin/console doctrine:schema:validate --env=${env}
	docker-compose exec -T php php bin/console doctrine:mapping:info --env=${env}

admin:
	docker-compose exec php php bin/console app:permissions:make-admin --full-permissions

db:
	docker-compose exec -T php php bin/console doctrine:database:drop --if-exists --force --env=${env}
	docker-compose exec -T php php bin/console doctrine:database:create --if-not-exists --env=${env}
	docker-compose exec -T php php bin/console doctrine:migration:migrate --no-interaction --env=${env}

setup:
	@make db
	docker-compose exec -T php php bin/console app:import:modlists var/import

test:
	@make db env=test
	@make test-ci

test-ci:
	docker-compose exec -T php php bin/console doctrine:fixtures:load --no-interaction --env=test

	docker-compose exec -T php php bin/phpunit --testdox
