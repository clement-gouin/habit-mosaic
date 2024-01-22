ENV_FILE=.env

include ${ENV_FILE}
export

lint:
	npm run lint
	vendor/bin/pint --test
	vendor/bin/phpstan --configuration=./code_quality/phpstan.neon
	vendor/bin/phpmd app ansi ./code_quality/phpmd.xml

fix:
	npm run fix
	vendor/bin/pint

baseline:
	vendor/bin/phpstan --configuration=./code_quality/phpstan.neon --generate-baseline=./code_quality/phpstan-baseline.neon

test:
	php artisan test

install:
	npm ci
	composer install

update:
	npm update
	composer update
