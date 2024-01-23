ENV_FILE=.env

include ${ENV_FILE}
export

.env:
	cp .env.example .env

vendor: composer.lock .env
	composer install

node_modules: package-lock.json .env
	npm ci

.PHONY: install
install: vendor node_modules

.PHONY: update
update:
	npm update
	composer update

.PHONY: db
create-db: .env
	sudo -u postgres psql -c 'DROP DATABASE IF EXISTS "${DB_DATABASE}"'
	sudo -u postgres createdb --owner ${DB_USERNAME} ${DB_DATABASE}

.PHONY: build
build: node_modules
	npm run build

.PHONY: reset
reset: vendor
	php artisan db:wipe
	php artisan migrate --seed

.PHONY: migrate
migrate: vendor
	php artisan migrate --seed

.PHONY: test
test: vendor
	php artisan db:wipe --env=testing
	php artisan migrate --seed --env=testing
	vendor/bin/phpunit --log-junit "reports/phpunit.xml" --testdox

.PHONY: coverage
coverage: vendor
	php artisan db:wipe --env=testing
	php artisan migrate --seed --env=testing
	XDEBUG_MODE=coverage php vendor/bin/phpunit --log-junit "reports/phpunit.xml" --coverage-clover "reports/coverage.xml" --coverage-html "reports/coverage" --testdox


.PHONY: lint
lint: vendor node_modules
	npm run lint
	vendor/bin/pint --test --config=pint.json
	vendor/bin/phpstan --configuration=phpstan.neon
	vendor/bin/phpmd app ansi phpmd.xml

.PHONY: fix
fix: vendor node_modules
	npm run fix
	vendor/bin/pint --config=pint.json

.PHONY: baseline
baseline: vendor
	vendor/bin/phpstan --configuration=phpstan.neon --generate-baseline=phpstan-baseline.neon
