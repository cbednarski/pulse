all: init test
init:
	composer install
test:
	phpunit