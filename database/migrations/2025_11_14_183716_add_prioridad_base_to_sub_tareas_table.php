<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migración: Agregar campo prioridad_base a subtareas
 * 
 * PROPÓSITO:
 * Habilita el sistema de prioridades automáticas que escala las prioridades
 * de las tareas según el tiempo transcurrido, pero permite restaurarlas a su
 * valor original cuando la tarea se completa.
 * 
 * FUNCIONAMIENTO:
 * - prioridad: La prioridad actual de la tarea (puede ser escalada automáticamente)
 * - prioridad_base: La prioridad original/base de la tarea (nunca cambia por escalación)
 * 
 * FLUJO:
 * 1. Tarea creada con prioridad="media" → prioridad_base="media"
 * 2. Sistema escala por tiempo → prioridad="alta", prioridad_base="media" (sin cambios)
 * 3. Usuario completa tarea → prioridad="media" (restaurada desde prioridad_base)
 * 
 * COMANDOS RELACIONADOS:
 * - php artisan tareas:actualizar-prioridad (escalación automática)
 * - php artisan tareas:corregir-prioridad-base (corrección de datos)
 * 
 * Fecha: Noviembre 2025
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subtareas', function (Blueprint $table) {
            // Agregar columna para guardar la prioridad original de la tarea
            $table->string('prioridad_base')->default('baja')->after('prioridad');
        });
        
        // Copiar las prioridades actuales como prioridades base para tareas existentes
        // NOTA: Algunas tareas existentes ya estaban escaladas, por lo que se requirió
        // ejecutar el comando 'tareas:corregir-prioridad-base' posteriormente
        DB::table('subtareas')->update(['prioridad_base' => DB::raw('prioridad')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subtareas', function (Blueprint $table) {
            $table->dropColumn('prioridad_base');
        });
    }
};
