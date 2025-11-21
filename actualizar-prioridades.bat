@echo off
REM Script para probar el comando de actualización de prioridades
REM Ejecuta este archivo haciendo doble clic o desde la terminal

echo ========================================
echo   ACTUALIZACION DE PRIORIDADES DE TAREAS
echo ========================================
echo.
echo Ejecutando comando...
echo.

php artisan tareas:actualizar-prioridad

echo.
echo ========================================
echo   PROCESO COMPLETADO
echo ========================================
echo.
pause
