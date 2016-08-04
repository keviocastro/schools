#!/bin/bash
set -e

if [ -z "$APP_URL" ]; then
	APP_URL="http://localhost/api/health"
	echo "Using APP_URL = http://localhost/api/health"
fi

cmd="$1"

until [ $( curl --write-out "%{http_code}" --silent --output /dev/null "${APP_URL}") -eq "200" ]; do
  echo "WS-CORE is unavailable - sleeping..."
  sleep 1
done

>&2 echo "WS-CORE is up - executing command: $cmd"
($cmd)