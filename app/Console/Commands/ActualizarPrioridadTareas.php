<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubTarea;
use App\Models\User;
use App\Models\Alert;
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
                // Si la tarea está completada, restaurar su prioridad base y eliminar de Google Calendar
                if (in_array($tarea->estado, ['Completada', 'Completado'])) {
                    $resultado = $this->restaurarPrioridadBase($tarea);
                    if ($resultado['restaurado']) {
                        $restauradas++;
                        $this->line("🔄 Tarea #{$tarea->id}: {$tarea->nombre}");
                        $this->line("   Prioridad restaurada: {$resultado['prioridad_anterior']} → {$resultado['prioridad_base']} (Completada)");
                    } else {
                        $sinCambios++;
                    }
                    
                    // Eliminar de Google Calendar
                    $this->eliminarDeGoogleCalendar($tarea);
                    continue;
                }

                // Si la fecha de inicio es futura, restaurar a prioridad base
                $fechaInicio = Carbon::parse($tarea->fecha_inicio);
                $ahora = Carbon::now();
                
                if ($ahora->lt($fechaInicio)) {
                    $resultado = $this->restaurarPrioridadBase($tarea, 'Fecha de inicio futura');
                    if ($resultado['restaurado']) {
                        $restauradas++;
                        $this->line("🔄 Tarea #{$tarea->id}: {$tarea->nombre}");
                        $this->line("   Prioridad restaurada: {$resultado['prioridad_anterior']} → {$resultado['prioridad_base']} (Fecha de inicio: {$fechaInicio->format('d/m/Y')})");
                        
                        // Sincronizar cambio con Google Calendar
                        $this->sincronizarConGoogleCalendar($tarea);
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
                        
                        // Crear alerta para el usuario
                        $this->crearAlertaPrioridad($tarea, $resultado['prioridad_anterior'], $resultado['prioridad_nueva']);
                        
                        // Sincronizar cambio con Google Calendar
                        $this->sincronizarConGoogleCalendar($tarea);
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
     * @param String $razon Razón de la restauración (para logging)
     * @return array
     */
    private function restaurarPrioridadBase(SubTarea $tarea, string $razon = 'Completada'): array
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
                'prioridad_base' => $prioridadBase,
                'razon' => $razon
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

    /**
     * Crea una alerta para notificar al usuario sobre el cambio de prioridad
     *
     * @param SubTarea $tarea
     * @param string $prioridadAnterior
     * @param string $prioridadNueva
     * @return void
     */
    private function crearAlertaPrioridad(SubTarea $tarea, string $prioridadAnterior, string $prioridadNueva): void
    {
        try {
            // Emojis según la nueva prioridad
            $iconos = [
                'urgente' => '🔴',
                'alta' => '🟡',
                'media' => '🟢',
                'baja' => '🔵'
            ];

            $icono = $iconos[strtolower($prioridadNueva)] ?? '⚠️';

            Alert::create([
                'user_id' => $tarea->user_id,
                'title' => "{$icono} Prioridad de tarea actualizada",
                'description' => "La tarea \"{$tarea->nombre}\" ha aumentado su prioridad de {$prioridadAnterior} a {$prioridadNueva}.",
                'type' => 'priority_change',
                'date' => now(),
                'priority' => strtolower($prioridadNueva),
            ]);

            $this->line("   🔔 Alerta creada para el usuario");

        } catch (\Exception $e) {
            Log::warning("No se pudo crear la alerta", [
                'tarea_id' => $tarea->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Elimina la tarea de Google Calendar
     *
     * @param SubTarea $tarea
     * @return void
     */
    private function eliminarDeGoogleCalendar(SubTarea $tarea): void
    {
        try {
            // Obtener el usuario de la tarea
            $user = User::find($tarea->user_id);
            
            if (!$user || !$user->google_calendar_token) {
                return; // Usuario no tiene Google Calendar conectado
            }

            // Configurar cliente de Google
            $client = new \Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessToken($user->google_calendar_token);

            // Verificar y renovar token si es necesario
            if ($client->isAccessTokenExpired()) {
                if ($user->google_calendar_refresh_token) {
                    $client->fetchAccessTokenWithRefreshToken($user->google_calendar_refresh_token);
                    $newToken = $client->getAccessToken();
                    
                    $user->update([
                        'google_calendar_token' => $newToken['access_token'],
                        'google_calendar_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                    ]);
                } else {
                    return; // No se puede renovar el token
                }
            }

            $service = new \Google_Service_Calendar($client);

            // Buscar el evento en Google Calendar por nombre y fecha
            $events = $service->events->listEvents('primary', [
                'q' => $tarea->nombre,
                'timeMin' => Carbon::parse($tarea->fecha_inicio)->startOfDay()->toRfc3339String(),
                'timeMax' => Carbon::parse($tarea->fecha_limite)->endOfDay()->toRfc3339String(),
                'singleEvents' => true,
            ]);

            // Si encontramos el evento, eliminarlo
            foreach ($events->getItems() as $event) {
                if ($event->getSummary() === $tarea->nombre) {
                    $service->events->delete('primary', $event->getId());
                    
                    $this->line("   🗑️  Eliminado de Google Calendar");
                    Log::info("Tarea completada eliminada de Google Calendar", [
                        'tarea_id' => $tarea->id,
                        'evento_id' => $event->getId()
                    ]);
                    break;
                }
            }

        } catch (\Exception $e) {
            // Log del error pero no detener el proceso
            Log::warning("No se pudo eliminar de Google Calendar", [
                'tarea_id' => $tarea->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Sincroniza el cambio de prioridad con Google Calendar
     *
     * @param SubTarea $tarea
     * @return void
     */
    private function sincronizarConGoogleCalendar(SubTarea $tarea): void
    {
        try {
            // Obtener el usuario de la tarea
            $user = User::find($tarea->user_id);
            
            if (!$user || !$user->google_calendar_token) {
                return; // Usuario no tiene Google Calendar conectado
            }

            // Configurar cliente de Google
            $client = new \Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessToken($user->google_calendar_token);

            // Verificar y renovar token si es necesario
            if ($client->isAccessTokenExpired()) {
                if ($user->google_calendar_refresh_token) {
                    $client->fetchAccessTokenWithRefreshToken($user->google_calendar_refresh_token);
                    $newToken = $client->getAccessToken();
                    
                    $user->update([
                        'google_calendar_token' => $newToken['access_token'],
                        'google_calendar_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                    ]);
                } else {
                    return; // No se puede renovar el token
                }
            }

            $service = new \Google_Service_Calendar($client);

            // Buscar el evento en Google Calendar por nombre y fecha
            $events = $service->events->listEvents('primary', [
                'q' => $tarea->nombre,
                'timeMin' => Carbon::parse($tarea->fecha_inicio)->startOfDay()->toRfc3339String(),
                'timeMax' => Carbon::parse($tarea->fecha_limite)->endOfDay()->toRfc3339String(),
                'singleEvents' => true,
            ]);

            // Si encontramos el evento, actualizar su color y agregar recordatorio
            $eventosEncontrados = count($events->getItems());
            Log::info("Búsqueda de evento en Google Calendar", [
                'tarea_id' => $tarea->id,
                'tarea_nombre' => $tarea->nombre,
                'eventos_encontrados' => $eventosEncontrados
            ]);
            
            foreach ($events->getItems() as $event) {
                if ($event->getSummary() === $tarea->nombre) {
                    // Actualizar color según nueva prioridad
                    $event->setColorId($this->getPriorityColor($tarea->prioridad));
                    
                    // Actualizar descripción con información del cambio de prioridad
                    $descripcionActual = $event->getDescription() ?? '';
                    $nuevaDescripcion = $descripcionActual . "\n\n⚠️ ALERTA: La prioridad ha aumentado a " . strtoupper($tarea->prioridad) . " (" . now()->format('d/m/Y H:i') . ")";
                    $event->setDescription($nuevaDescripcion);
                    
                    // Agregar recordatorio inmediato sobre el cambio usando formato de objeto
                    $reminder1 = new \Google_Service_Calendar_EventReminder();
                    $reminder1->setMethod('popup');
                    $reminder1->setMinutes(0);
                    
                    $reminder2 = new \Google_Service_Calendar_EventReminder();
                    $reminder2->setMethod('email');
                    $reminder2->setMinutes(0);
                    
                    $reminders = new \Google_Service_Calendar_EventReminders();
                    $reminders->setUseDefault(false);
                    $reminders->setOverrides([$reminder1, $reminder2]);
                    
                    $event->setReminders($reminders);
                    
                    // Actualizar el evento
                    $service->events->update('primary', $event->getId(), $event);
                    
                    $this->line("   📅 Actualizado en Google Calendar (con notificación)");
                    Log::info("Prioridad actualizada en Google Calendar con notificación", [
                        'tarea_id' => $tarea->id,
                        'evento_id' => $event->getId(),
                        'nueva_prioridad' => $tarea->prioridad
                    ]);
                    break;
                }
            }

        } catch (\Exception $e) {
            // Log del error pero no detener el proceso
            Log::warning("No se pudo sincronizar con Google Calendar", [
                'tarea_id' => $tarea->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mapea las prioridades a colores de Google Calendar
     *
     * @param string $prioridad
     * @return string
     */
    private function getPriorityColor(string $prioridad): string
    {
        $colores = [
            'baja' => '7',      // Turquesa
            'media' => '2',     // Verde claro
            'alta' => '5',      // Amarillo
            'urgente' => '11',  // Rojo
        ];

        return $colores[strtolower($prioridad)] ?? '7';
    }
}
