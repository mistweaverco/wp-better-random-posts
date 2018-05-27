all: coverage-text

install-dev:
	composer install --dev

coverage-text:
	./vendor/bin/phpunit tests/wp-better-random-posts --coverage-text

coverage-html:
	./vendor/bin/phpunit tests/wp-better-random-posts --coverage-html coverage

