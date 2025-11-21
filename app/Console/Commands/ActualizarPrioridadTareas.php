<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubTarea;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActualizarPrioridadTareas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tareas:actualizar-prioridad';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza automáticamente la prioridad de las tareas según el tiempo transcurrido desde su fecha de inicio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando actualización de prioridades de tareas...');

        // Obtener todas las subtareas no archivadas
        $subtareas = SubTarea::where('archivada', false)->get();

        $actualizadas = 0;
        $restauradas = 0;
        $sinCambios = 0;
        $errors = 0;

        foreach ($subtareas as $tarea) {
            try {
                // Si la tarea está completada, restaurar su prioridad base
                if (in_array($tarea->estado, ['Completada', 'Completado'])) {
                    $resultado = $this->restaurarPrioridadBase($tarea);
                    if ($resultado['restaurado']) {
                        $restauradas++;
                        $this->line("🔄 Tarea #{$tarea->id}: {$tarea->nombre}");
                        $this->line("   Prioridad restaurada: {$resultado['prioridad_anterior']} → {$resultado['prioridad_base']} (Completada)");
                    } else {
                        $sinCambios++;
                    }
                    continue;
                }

                // Si no está completada ni vencida, actualizar prioridad según tiempo transcurrido
                if (!in_array($tarea->estado, ['Vencida'])) {
                    $resultado = $this->actualizarPrioridadTarea($tarea);
                    
                    if ($resultado['actualizado']) {
                        $actualizadas++;
                        $this->line("✅ Tarea #{$tarea->id}: {$tarea->nombre}");
                        $this->line("   Prioridad: {$resultado['prioridad_anterior']} → {$resultado['prioridad_nueva']} ({$resultado['porcentaje']}%)");
                    } else {
                        $sinCambios++;
                    }
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("❌ Error en tarea #{$tarea->id}: {$e->getMessage()}");
                Log::error("Error actualizando prioridad de tarea {$tarea->id}", [
                    'error' => $e->getMessage(),
                    'tarea' => $tarea->toArray()
                ]);
            }
        }

        $this->newLine();
        $this->info("📊 Resumen de actualización:");
        $this->table(
            ['Estado', 'Cantidad'],
            [
                ['Tareas actualizadas', $actualizadas],
                ['Tareas restauradas (completadas)', $restauradas],
                ['Sin cambios', $sinCambios],
                ['Errores', $errors],
                ['Total procesadas', $subtareas->count()]
            ]
        );

        Log::info('Actualización de prioridades completada', [
            'actualizadas' => $actualizadas,
            'restauradas' => $restauradas,
            'sin_cambios' => $sinCambios,
            'errores' => $errors,
            'total' => $subtareas->count()
        ]);

        $this->info('✅ Proceso completado exitosamente');
        return 0;
    }

    /**
     * Restaura la prioridad base de una tarea completada
     *
     * @param SubTarea $tarea
     * @return array
     */
    private function restaurarPrioridadBase(SubTarea $tarea): array
    {
        $prioridadActual = strtolower($tarea->prioridad ?? 'baja');
        $prioridadBase = strtolower($tarea->prioridad_base ?? 'baja');

        // Si no tiene prioridad_base definida, establecer la prioridad actual como base
        if (!$tarea->prioridad_base) {
            $tarea->update(['prioridad_base' => $prioridadActual]);
            return [
                'restaurado' => false,
                'motivo' => 'Prioridad base no definida, establecida como actual'
            ];
        }

        // Si la prioridad actual es diferente a la base, restaurar
        if ($prioridadActual !== $prioridadBase) {
            $tarea->update(['prioridad' => $prioridadBase]);
            
            return [
                'restaurado' => true,
                'prioridad_anterior' => $prioridadActual,
                'prioridad_base' => $prioridadBase
            ];
        }

        return [
            'restaurado' => false,
            'motivo' => 'Prioridad ya está en su valor base'
        ];
    }

    /**
     * Actualiza la prioridad de una tarea específica según el tiempo transcurrido
     *
     * @param SubTarea $tarea
     * @return array
     */
    private function actualizarPrioridadTarea(SubTarea $tarea): array
    {
        $fechaInicio = Carbon::parse($tarea->fecha_inicio);
        $fechaLimite = Carbon::parse($tarea->fecha_limite);
        $ahora = Carbon::now();

        // Guardar prioridad base si no existe (primera vez)
        if (!$tarea->prioridad_base) {
            $tarea->update(['prioridad_base' => strtolower($tarea->prioridad ?? 'baja')]);
        }

        // Si la fecha de inicio es futura, no hacer nada
        if ($ahora->lt($fechaInicio)) {
            return [
                'actualizado' => false,
                'motivo' => 'Fecha de inicio en el futuro'
            ];
        }

        // Si ya pasó la fecha límite, marcar como vencida
        if ($ahora->gt($fechaLimite)) {
            $prioridadAnterior = $tarea->prioridad;
            $tarea->update([
                'estado' => 'Vencida',
                'prioridad' => 'urgente'
            ]);
            
            return [
                'actualizado' => true,
                'prioridad_anterior' => $prioridadAnterior,
                'prioridad_nueva' => 'urgente',
                'porcentaje' => 100,
                'motivo' => 'Fecha límite superada'
            ];
        }

        // Calcular el porcentaje de tiempo transcurrido
        $tiempoTotal = $fechaInicio->diffInMinutes($fechaLimite);
        $tiempoTranscurrido = $fechaInicio->diffInMinutes($ahora);
        
        // Evitar división por cero
        if ($tiempoTotal == 0) {
            return [
                'actualizado' => false,
                'motivo' => 'Tiempo total es cero'
            ];
        }

        $porcentajeTranscurrido = ($tiempoTranscurrido / $tiempoTotal) * 100;

        // Obtener prioridad actual y base
        $prioridadActual = strtolower($tarea->prioridad ?? 'baja');
        $prioridadBase = strtolower($tarea->prioridad_base ?? 'baja');
        
        // Mapeo de prioridades a niveles numéricos
        $nivelesPrioridad = [
            'baja' => 1,
            'media' => 2,
            'alta' => 3,
            'urgente' => 4
        ];

        // Determinar nueva prioridad según el porcentaje
        $nuevaPrioridad = $this->determinarPrioridad($porcentajeTranscurrido);
        
        // Obtener niveles
        $nivelActual = $nivelesPrioridad[$prioridadActual] ?? 1;
        $nivelNuevo = $nivelesPrioridad[$nuevaPrioridad] ?? 1;
        $nivelBase = $nivelesPrioridad[$prioridadBase] ?? 1;

        // Solo actualizar si:
        // 1. La nueva prioridad es mayor que la actual
        // 2. La nueva prioridad es mayor o igual a la prioridad base
        if ($nivelNuevo > $nivelActual && $nivelNuevo >= $nivelBase) {
            $tarea->update(['prioridad' => $nuevaPrioridad]);
            
            return [
                'actualizado' => true,
                'prioridad_anterior' => $prioridadActual,
                'prioridad_nueva' => $nuevaPrioridad,
                'porcentaje' => round($porcentajeTranscurrido, 2)
            ];
        }

        return [
            'actualizado' => false,
            'prioridad_actual' => $prioridadActual,
            'prioridad_base' => $prioridadBase,
            'porcentaje' => round($porcentajeTranscurrido, 2),
            'motivo' => 'Prioridad actual es igual o mayor a la calculada, o nueva prioridad es menor a la base'
        ];
    }

    /**
     * Determina la prioridad según el porcentaje de tiempo transcurrido
     *
     * @param float $porcentaje
     * @return string
     */
    private function determinarPrioridad(float $porcentaje): string
    {
        if ($porcentaje >= 75) {
            return 'urgente';
        } elseif ($porcentaje >= 50) {
            return 'alta';
        } elseif ($porcentaje >= 25) {
            return 'media';
        } else {
            return 'baja';
        }
    }
}
