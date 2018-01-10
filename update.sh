#!/bin/bash
env='dev'
if [ -a .env ] ; then
  printf "Load env from .env file\n"
  env=`cat .env`
else
  printf "No .env file found, assuming dev\n"
fi
printf "env is set to $env\n"

git pull
php composer.phar install
php bin/console cache:clear --no-warmup --env=$env
php bin/console cache:warmup --env=$env
php bin/console assets:install
php bin/console doc:mig:mig

