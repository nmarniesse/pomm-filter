.PHONY: install
install:
	composer install

.PHONY: unit-tests
unit-test:
	php ./vendor/bin/atoum --directories tests/Unit --no-code-coverage
