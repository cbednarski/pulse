all: init test
init:
	composer install
test:
	php vendor/phpunit/phpunit/phpunit.php
dev:
	php -S 127.0.0.1:5003 healthcheck-sample.php
