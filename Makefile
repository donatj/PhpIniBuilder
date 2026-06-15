SRC_FILES = $(shell find example src -type f -name '*.php')

.PHONY: test
test: cs phpstan
	vendor/bin/phpunit

.PHONY: cs
cs:
	vendor/bin/phpcs

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan

.PHONY: cbf
cbf:
	vendor/bin/phpcbf

.PHONY: fix
fix: cbf
	vendor/bin/php-cs-fixer fix
