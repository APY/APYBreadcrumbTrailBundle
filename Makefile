test:
	composer update --prefer-dist --no-interaction ${COMPOSER_PARAMS}
	composer test

test-sf4:
	composer require symfony/framework-bundle:"^4.0" --no-update
	$(MAKE) test-lowest

test-lowest:
	COMPOSER_PARAMS='--prefer-lowest' $(MAKE) test
