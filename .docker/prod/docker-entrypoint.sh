#!/bin/sh
echo "Composer dump-autoload"
composer dump-autoload

echo "Run Migration & Seeder"
php artisan migrate
echo "App install was successful"

/usr/bin/supervisord  -c "/etc/supervisor/conf.d/supervisord.conf"
