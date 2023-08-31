#!/bin/bash
php artisan cache:clear
 echo "se limpio cache correctamente"
php artisan config:clear
 echo "se limpio config correctamente"
php artisan route:clear
 echo "se limpio ruta correctamente"
php artisan view:clear
 echo "se limpio vista correctamente"
php artisan event:clear
 echo "se limpio evento correctamente"
# php -r 'opcache_reset();'
 echo "servidor actualizado correctamente"
exit
