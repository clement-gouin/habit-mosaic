ENV_FILE=.env

include ${ENV_FILE}
export

lint:
	npm run lint
	composer exec phpcs
	composer exec phpstan
	composer exec phpmd app ansi phpmd.xml

fix:
	npm run fix
	composer phpcbf

test:
	php artisan test

install:
	npm ci
	composer install

update:
	npm update
	composer update
