include .env

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
	php vendor/bin/phpunit --log-junit "reports/phpunit.xml" --testdox-html "reports/phpunit.html" --no-coverage
	open reports/phpunit.html &> /dev/null &

.PHONY: coverage
coverage: vendor
	XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-clover "reports/coverage.xml" --coverage-html "reports/coverage"
	open reports/coverage/index.html &> /dev/null &

.PHONY: lint
lint: vendor node_modules
	npm run lint
	php vendor/bin/pint --test --config=pint.json
	php vendor/bin/phpstan --configuration=phpstan.neon
	php vendor/bin/phpmd app ansi phpmd.xml

.PHONY: fix
fix: vendor node_modules
	npm run fix
	php vendor/bin/pint --config=pint.json

.PHONY: metrics
metrics: vendor
	php vendor/bin/phpunit --log-junit "reports/phpunit.xml" --no-coverage
	php vendor/bin/phpmetrics --config=phpmetrics.json --junit=reports/phpunit.xml
	open reports/phpmetrics/index.html &> /dev/null &

.PHONY: phpstan
phpstan: vendor
	php vendor/bin/phpstan --configuration=phpstan.neon

.PHONY: baseline
baseline: vendor
	php vendor/bin/phpstan --configuration=phpstan.neon --generate-baseline=phpstan-baseline.neon
