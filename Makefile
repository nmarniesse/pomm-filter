.PHONY: install
install:
	composer install

.PHONY: unit-test
unit-test:
	php ./vendor/bin/atoum --directories tests/Unit --no-code-coverage
