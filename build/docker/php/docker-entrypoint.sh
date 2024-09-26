#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	if [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-interaction
	fi

	if grep -q ^DATABASE_URL= .env; then
		echo "Waiting for db to be ready..."
		ATTEMPTS_LEFT_TO_REACH_DATABASE=30
		until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(bin/console dbal:run-sql "SELECT 1" 2>&1); do
			sleep 1
			ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
			echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
		done

		if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
			echo "The database is not up or not reachable:"
			echo "$DATABASE_ERROR"
			exit 1
		else
			echo "The db is now ready and reachable"
		fi

		if [ "$( find ./migrations -iname '*.php' -print -quit )" ]; then
			bin/console doctrine:migrations:migrate --no-interaction
		fi
	fi

  if grep -q ^MONGODB_URL= .env; then
    MAX_ATTEMPTS=30
    ATTEMPTS_LEFT_TO_CREATE_SCHEMA=MAX_ATTEMPTS
    until [ $ATTEMPTS_LEFT_TO_CREATE_SCHEMA -eq 0 ]; do
        echo "Creating MongoDB schema (attempt $((MAX_ATTEMPTS + 1 - ATTEMPTS_LEFT_TO_CREATE_SCHEMA)) of $MAX_ATTEMPTS)"
        bin/console doctrine:mongodb:schema:create
        if [ $? -eq 0 ]; then
            echo "MongoDB schema created successfully."
            break
        else
            echo "Failed to create MongoDB schema. Retrying..."
            ATTEMPTS_LEFT_TO_CREATE_SCHEMA=$((ATTEMPTS_LEFT_TO_CREATE_SCHEMA - 1))
            sleep 1
        fi
    done
    if [ $ATTEMPTS_LEFT_TO_CREATE_SCHEMA -eq 0 ]; then
        echo "Failed to create MongoDB schema after $MAX_ATTEMPTS attempts."
    fi
  fi

	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

if [ "$APP_ENV" != 'prod' ]; then
  #this will let the build pipeline know that the entrypoint has finished
  php -S 0.0.0.0:9876 entrypoint-finished.php
fi

exec docker-php-entrypoint "$@"
