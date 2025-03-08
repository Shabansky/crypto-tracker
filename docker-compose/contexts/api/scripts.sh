#!/bin/sh

# Start cron service
service cron start

# Start PHP-FPM
php-fpm
