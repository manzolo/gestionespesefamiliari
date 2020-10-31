#!/bin/sh
set -e

echo "Waiting $MYSQL_HOST on $MYSQL_PORT start..."

while ! nc -z $MYSQL_HOST $MYSQL_PORT; do   
  sleep 0.1 # wait for 1/10 of the second before check again
done

echo "mysql started"

bin/console cache:clear --env=prod
symfony server:start --no-tls --port=8030