ENV_FILE=.env

include ${ENV_FILE}
export

lint:
	npm run lint
	vendor/bin/phpcs
	vendor/bin/phpstan -cphpstan.neon
	vendor/bin/phpmd app ansi phpmd.xml

fix:
	npm run fix
	composer phpcbf

baseline:
	vendor/bin/phpstan -cphpstan.neon --generate-baseline=./code_quality/phpstan/phpstan-baseline.neon

test:
	php artisan test

install:
	npm ci
	composer install

update:
	npm update
	composer update
