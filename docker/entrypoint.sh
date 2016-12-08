#!/bin/bash
set -e

# Dependencies install
if [[ ! -f /var/app/vendor/autoload.php ]]; then
	source /var/app/.env
else
	echo 'File vendor/autoload.php does not exist.'
	echo 'Running composer install'
	composer install
fi

exec "$@"