.PHONY: check
check: lint php-cs-fixer tests phpstan

.PHONY: tests
tests:
	php vendor/bin/phpunit

.PHONY: lint
lint:
	php vendor/bin/parallel-lint --colors \
		src tests oxid-bootstrap.php

.PHONY: install-php-cs-fixer
install-php-cs-fixer:
	wget https://cs.symfony.com/download/php-cs-fixer-v3.phar -O php-cs-fixer && chmod a+x php-cs-fixer && mv php-cs-fixer /usr/local/bin/php-cs-fixer

.PHONY: php-cs-fixer
php-cs-fixer:
	php-cs-fixer fix --dry-run -v

.PHONY: phpstan
phpstan:
	php vendor/bin/phpstan analyse
