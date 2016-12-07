#!/bin/bash
set -e

# Dependencies install
if [[ ! -f /var/app/vendor/autoload.php ]]; then
	source /var/app/.env
else
	composer install
fi