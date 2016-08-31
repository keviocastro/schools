#!/bin/bash
set -e

source .env

if [ -z "$APP_URL" ]; then
	APP_URL="http://localhost"
	echo "Using APP_URL = http://localhost"
fi

cmd="$1"
wait_for=60
i=0

until [ $( curl --write-out "%{http_code}" --silent --output /dev/null "${APP_URL}/api/health") -eq "200" ]; do
  	echo "Schools API is unavailable - Checking ${APP_URL}/api/health. 
		Sleeping..."
	
	i=$((i+1))

	if [ "$i" -gt "$wait_for" ]; then
		echo >&2 "schools api is unavailable for $wait_for seconds. The command was not executed."
		exit 1
	fi


  	sleep 1
done

>&2 echo "Schools API is up - executing command: $cmd"
($cmd)