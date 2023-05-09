#!/bin/bash
php artisan optimize
php artisan route:clear 
php artisan cache:clear
php artisan config:clear
echo "servidor actualizado correctamente"
exit
