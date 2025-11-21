@echo off
REM Script para ejecutar el scheduler de Laravel continuamente
REM IMPORTANTE: Deja esta ventana abierta para que funcione

echo ========================================
echo   SCHEDULER DE LARAVEL - MODO CONTINUO
echo ========================================
echo.
echo Este script ejecutara el scheduler cada minuto.
echo MANTENER ESTA VENTANA ABIERTA.
echo.
echo Presiona Ctrl+C para detener.
echo.
echo ========================================
echo.

:loop
echo [%date% %time%] Ejecutando scheduler...
php artisan schedule:run
timeout /t 60 /nobreak >nul
goto loop
