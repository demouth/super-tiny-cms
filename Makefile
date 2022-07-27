.PHONY: run-docker open run
run-docker:
	docker run --rm -it -p 80:80 -v `pwd`:/var/www/html php:7.4-apache

open:
	open -a '/Applications/Google Chrome.app' http://localhost/src/

run: open run-docker
