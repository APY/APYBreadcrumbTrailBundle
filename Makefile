DIR := ${CURDIR}

test-php73:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.3 $(MAKE) test

test-php74:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.4 $(MAKE) test

test:
	composer update --prefer-dist --no-interaction ${COMPOSER_PARAMS}
	composer test

test-lowest:
	COMPOSER_PARAMS='--prefer-lowest' $(MAKE) test

test-php73-lowest:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.3 $(MAKE) test-lowest

test-php74-lowest:
	docker run --rm -v $(DIR):/project -w /project webdevops/php:7.4 $(MAKE) test-lowest

cs:
	docker run --rm -v $(DIR):/project -w /project jakzal/phpqa php-cs-fixer fix

static:
	docker run --rm -v $(DIR):/project -w /project jakzal/phpqa phpstan analyze -c .phpstan.neon
