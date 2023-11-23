#!/bin/sh
echo "Composer dump-autoload"
composer dump-autoload
echo "Run scout sync index"
php artisan scout:sync-index-settings
echo "Run Migration & Seeder"
php artisan migrate --force
php artisan module:migrate --force
echo "App install was successful"

/usr/bin/supervisord  -c "/etc/supervisor/conf.d/supervisord.conf"
