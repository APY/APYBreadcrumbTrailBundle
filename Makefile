DIR := ${CURDIR}

test-php72:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.2 make test

test-php73:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.3 make test

test-php74:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.4 make test

test-php80:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:8.0 make test

test-php81:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:8.1 make test

test-php82:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:8.2 make test

test:
	composer update --prefer-dist --no-interaction ${COMPOSER_PARAMS}
	composer test

test-lowest:
	COMPOSER_PARAMS='--prefer-lowest' make test

test-php72-lowest:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.2 make test-lowest

test-php73-lowest:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.3 make test-lowest

test-php74-lowest:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.4 make test-lowest

cs: composer.lock
	docker run --rm -v $(DIR):/project -w /project jakzal/phpqa:php8.1 php-cs-fixer fix -vv ${CS_PARAMS}

test-cs: composer.lock
	CS_PARAMS='--dry-run' make cs

static: composer.lock
	docker run --rm -v $(DIR):/project -w /project jakzal/phpqa phpstan analyze -c .phpstan.neon

composer.lock:
	docker run --rm -v $(DIR):/project -w /project jakzal/phpqa composer install
