<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// ========================================
// PROGRAMACIÓN DE TAREAS (SCHEDULER)
// ========================================

/**
 * Actualizar prioridades de tareas automáticamente
 * 
 * Este comando se ejecuta diariamente a las 02:00 AM para actualizar las prioridades
 * de las tareas según el porcentaje de tiempo transcurrido:
 * - 25% del tiempo: prioridad media
 * - 50% del tiempo: prioridad alta
 * - 75% del tiempo: prioridad urgente
 * 
 * Solo aumenta la prioridad, nunca la disminuye.
 */
Schedule::command('tareas:actualizar-prioridad')
    ->dailyAt('02:00')
    ->withoutOverlapping() // Evita que se ejecute si ya hay una instancia corriendo
    ->onOneServer() // Solo se ejecuta en un servidor si tienes múltiples
    ->runInBackground(); // Se ejecuta en segundo plano

// Alternativas de programación (descomenta la que prefieras):

// Ejecutar cada 30 minutos:
// Schedule::command('tareas:actualizar-prioridad')->everyThirtyMinutes();

// Ejecutar cada 15 minutos:
// Schedule::command('tareas:actualizar-prioridad')->everyFifteenMinutes();

// Ejecutar cada 6 horas:
// Schedule::command('tareas:actualizar-prioridad')->everySixHours();

// Ejecutar diariamente a las 00:00:
// Schedule::command('tareas:actualizar-prioridad')->daily();

// Ejecutar diariamente a una hora específica (ejemplo: 08:00):
// Schedule::command('tareas:actualizar-prioridad')->dailyAt('08:00');

// Ejecutar solo en días laborables:
// Schedule::command('tareas:actualizar-prioridad')->hourly()->weekdays();

// Ejecutar solo en horario laboral (8:00 - 18:00):
// Schedule::command('tareas:actualizar-prioridad')
//     ->hourly()
//     ->between('8:00', '18:00')
//     ->weekdays();
