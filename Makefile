SRC_FILES = $(shell find example src -type f -name '*.php')

.PHONY: test
test:
	vendor/bin/phpunit
