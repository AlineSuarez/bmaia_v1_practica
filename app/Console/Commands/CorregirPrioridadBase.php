<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubTarea;
use App\Models\TareasPredefinidas;

class CorregirPrioridadBase extends Command
{
    protected $signature = 'tareas:corregir-prioridad-base';
    protected $description = 'Corrige la prioridad_base de tareas que se guardaron incorrectamente';

    /**
     * COMANDO DE MANTENIMIENTO HISTÓRICO
     * 
     * Este comando corrige prioridades base que fueron guardadas incorrectamente
     * durante la implementación inicial del sistema (nov 2025).
     * 
     * USO NORMAL: No es necesario ejecutarlo (problema ya resuelto)
     * 
     * USAR SOLO SI:
     * - Importas datos de backups antiguos
     * - Sospechas corrupción de prioridades base
     * - Migras tareas de otro sistema
     * 
     * Seguro ejecutar múltiples veces (idempotente)
     */

    public function handle()
    {
        $this->info('🔧 Iniciando corrección de prioridades base...');
        
        // Obtener todas las subtareas
        $subtareas = SubTarea::all();
        $corregidas = 0;
        $sinCorreccion = 0;
        
        foreach ($subtareas as $tarea) {
            // Buscar si existe una tarea predefinida con el mismo nombre
            $tareaPredefinida = TareasPredefinidas::where('nombre', 'like', '%' . trim($tarea->nombre) . '%')
                ->orWhere('nombre', 'like', trim($tarea->nombre) . '%')
                ->first();
            
            if ($tareaPredefinida) {
                $prioridadOriginal = strtolower($tareaPredefinida->prioridad ?? 'baja');
                $prioridadBaseActual = strtolower($tarea->prioridad_base ?? 'baja');
                
                // Si la prioridad_base es diferente a la original de la predefinida
                if ($prioridadBaseActual !== $prioridadOriginal) {
                    $tarea->update([
                        'prioridad_base' => $prioridadOriginal
                    ]);
                    
                    $this->line("✅ Corregida: {$tarea->nombre}");
                    $this->line("   Base anterior: {$prioridadBaseActual} → Nueva base: {$prioridadOriginal}");
                    $corregidas++;
                } else {
                    $sinCorreccion++;
                }
            } else {
                // Si no es predefinida, usar 'baja' como base si no tiene
                if (!$tarea->prioridad_base) {
                    $tarea->update(['prioridad_base' => 'baja']);
                    $this->line("ℹ️  Establecida base en 'baja': {$tarea->nombre}");
                    $corregidas++;
                } else {
                    $sinCorreccion++;
                }
            }
        }
        
        $this->newLine();
        $this->info("📊 Resumen:");
        $this->table(
            ['Estado', 'Cantidad'],
            [
                ['Tareas corregidas', $corregidas],
                ['Sin corrección necesaria', $sinCorreccion],
                ['Total procesadas', $subtareas->count()]
            ]
        );
        
        $this->info('✅ Corrección completada');
        return 0;
    }
}
