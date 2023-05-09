@echo off
rem -------------------------------------------------------------------------
rem Correr Sistema Modo Desarrollo
rem -------------------------------------------------------------------------


 call php artisan optimize:clear
 call php artisan route:clear 
 call php artisan cache:clear
 call php artisan config:clear

IF [%1]==[] (
	php artisan serve
) ELSE (
    php artisan serve --port=%1
)