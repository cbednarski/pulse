all: init test
init:
	composer install
test:
	php vendor/phpunit/phpunit/phpunit.php