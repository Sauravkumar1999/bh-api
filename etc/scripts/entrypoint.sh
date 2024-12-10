#!/bin/bash
su appuser
# export homepath=/var/www/html/erp/bh-dev
for env in $(printenv); do
    if [[ $env == "AWS"* ]]; then
        echo "$env" >> .env
    fi;
done;


chown appuser:appuser  /var/www/html/laravel -R

composer install --no-dev
composer update
composer dump-autoload
php artisan key:generate
php artisan optimize:clear
#php artisan config:clear
php artisan migrate --force --no-interaction
#php artisan config:cache
#php artisan storage:link
php artisan l5-swagger:generate
php artisan passport:install
# npm install
# npm run development
php artisan serve --host 0.0.0.0
