all: deps docker

.PHONY: deps docker phpunit phpunit-coverage-html phpunit-coverage-clover docs tables

deps:
	docker run --rm -ti -v `pwd`:/app composer install

docker:
	docker-compose stop
	docker-compose rm -f
	docker-compose up -d --force-recreate --remove-orphans mysql

phpunit:
	docker-compose run --rm app sh -c 'cd /var/www/html ; ./vendor/bin/phpunit; (exit $?)'

phpunit-coverage-html:
	docker-compose run --rm app sh -c 'cd /var/www/html ; ./vendor/bin/phpunit --coverage-html ./coverage/; (exit $?)'

phpunit-coverage-clover:
	docker-compose run --rm app sh -c 'cd /var/www/html ; ./vendor/bin/phpunit --coverage-clover ./coverage/coverage.xml; (exit $?)'

docs:
	~/.composer/vendor/bin/apigen generate -s ./src -d ./docs

tables:
	docker-compose exec mysql sh -c 'MYSQL_PWD=testpassword mysql oauth2_laravel -utestuser -e "show tables;"; (exit $?)'
