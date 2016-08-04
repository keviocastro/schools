#!/bin/bash
set -e

source .env

if [ -z "$APP_URL" ]; then
	APP_URL="http://localhost"
	echo "Using APP_URL = http://localhost"
fi

cmd="$1"

until [ $( curl --write-out "%{http_code}" --silent --output /dev/null "${APP_URL}/api/health/db") -eq "200" ]; do
  echo "Schools API is unavailable - Checking ${APP_URL}/api/health/db. 
	Sleeping..."
  sleep 1
done

>&2 echo "Schools API is up - executing command: $cmd"
($cmd)