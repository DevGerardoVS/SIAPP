@echo off
rem -------------------------------------------------------------------------
rem Correr Sistema Modo Desarrollo
rem -------------------------------------------------------------------------

call   php artisan cache:clear
   echo "se limpio cache correctamente"
call  php artisan config:clear
 echo "se limpio config correctamente"
call  php artisan route:clear
  echo "se limpio ruta correctamente"
call  php artisan view:clear
  echo "se limpio vista correctamente"
call  php artisan event:clear
echo "se limpio evento correctamente"

 echo "servidor actualizado correctamente"

IF [%1]==[] (
	php artisan serve
) ELSE (
    php artisan serve --port=%1
)