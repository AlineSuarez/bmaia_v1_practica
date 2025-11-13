<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RecalculateTaskPriorities;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        RecalculateTaskPriorities::class,
    ];

    protected function schedule(Schedule $schedule)
    {

        // Opción: ejecución diaria (ej. a las 02:00)
        // $schedule->command('tareas:recalcular-prioridades')->dailyAt('02:00');

        // Opción: cada 6 horas (0:00, 6:00, 12:00, 18:00)
        // $schedule->command('tareas:recalcular-prioridades')->cron('0 */6 * * *');

        // Opción: cada 4 horas (si prefieres más frecuente)
        // $schedule->command('tareas:recalcular-prioridades')->everyFourHours();

        // Opción: cada hora
        // $schedule->command('tareas:recalcular-prioridades')->hourly();

        // Opción: cada 30 minutos
        // $schedule->command('tareas:recalcular-prioridades')->everyThirtyMinutes();

        // Ejecutar cada minuto (o ajustar según necesidad)
        $schedule->command('tareas:recalcular-prioridades')->everyMinute();
    }
}