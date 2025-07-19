.PHONY: run-docker open run
run-docker:
	docker run --rm -it -p 80:80 -v `pwd`/src:/var/www/html php:5.6-apache

open:
	open -a '/Applications/Google Chrome.app' http://localhost/public/admin/

run: open run-docker
