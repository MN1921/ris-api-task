SHELL := /bin/bash 

compose-up: 
	docker compose -f Composefile -p compose up -d;

compose-down:
	docker compose -f Composefile -p compose down;

build-image:
	docker build -f Imagefile -t php:8.4.13-fpm.dev .;
