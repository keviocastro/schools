#!/bin/bash
set -e

# Dependencies install
if [[ ! -f /var/app/vendor/autoload.php ]]; then
	echo 'File vendor/autoload.php does not exist.'
	echo 'Running composer install'
	composer install
	cp .env.example .env
	php artisan key:generate
fi

exec "$@"