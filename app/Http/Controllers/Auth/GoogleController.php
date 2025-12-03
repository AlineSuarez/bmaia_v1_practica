<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SubTarea;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class GoogleController extends Controller
{
    // Redirige a Google para la autenticación
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Maneja la respuesta de Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?: 'Usuario',
                    'password' => bcrypt(Str::random(32)),
                ]
            );

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->intended('/home');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'No se pudo iniciar sesión con Google.');
        }
    }

    /**
     * Redirige a Google para autorizar acceso a Google Calendar
     */
    public function redirectToGoogleCalendar()
    {
        // Construir la URL completa para evitar problemas de protocolo/host
        $redirectUrl = config('app.url') . '/auth/google-calendar/callback';

        Log::info('GoogleCalendar: Iniciando autorización', [
            'redirect_url' => $redirectUrl
        ]);

        // Especificar el redirect URI dinámicamente para Google Calendar
        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->scopes(['https://www.googleapis.com/auth/calendar'])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent', // IMPORTANTE: Forzar consent para obtener refresh token
                'include_granted_scopes' => 'true'
            ])
            ->redirect();
    }

    /**
     * Maneja el callback de autorización de Google Calendar
     */
    public function handleGoogleCalendarCallback()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                Log::error('GoogleCalendar: Usuario no autenticado en callback');
                return redirect()->route('login')->with('error', 'Debe iniciar sesión primero.');
            }

            Log::info('GoogleCalendar: Iniciando callback para usuario ' . $user->id);

            // IMPORTANTE: Debemos especificar el mismo redirectUrl en el callback
            $redirectUrl = config('app.url') . '/auth/google-calendar/callback';

            // Obtener usuario de Google con stateless para evitar problemas de sesión
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->stateless()
                ->user();

            Log::info('GoogleCalendar: Token obtenido de Google', [
                'user_id' => $user->id,
                'has_token' => !empty($googleUser->token),
                'has_refresh' => !empty($googleUser->refreshToken),
                'expires_in' => $googleUser->expiresIn ?? 'N/A',
                'token_preview' => $googleUser->token ? substr($googleUser->token, 0, 20) . '...' : 'NULL'
            ]);

            if (!$googleUser->token) {
                throw new Exception('No se obtuvo token de Google');
            }

            // Guardar los tokens en el usuario
            $updated = $user->update([
                'google_calendar_token' => $googleUser->token,
                'google_calendar_refresh_token' => $googleUser->refreshToken ?? $user->google_calendar_refresh_token,
                'google_calendar_token_expires_at' => now()->addSeconds($googleUser->expiresIn ?? 3600),
            ]);

            if (!$updated) {
                Log::error('GoogleCalendar: No se pudo actualizar el usuario en BD');
                throw new Exception('Error al guardar tokens en base de datos');
            }

            // Refrescar el usuario para asegurar que tiene los datos actualizados
            $user->refresh();

            Log::info('GoogleCalendar: Tokens guardados en BD', [
                'user_id' => $user->id,
                'token_saved' => !empty($user->google_calendar_token),
                'refresh_saved' => !empty($user->google_calendar_refresh_token)
            ]);

            // NO sincronizar aquí, se hará desde el frontend con progreso
            return redirect()->route('tareas')
                ->with('google_calendar_connected', true) // Flag especial para trigger de sincronización
                ->with('success', 'Google Calendar conectado exitosamente.');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('GoogleCalendar: Error de estado inválido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('tareas')
                ->with('error', 'Error de autenticación. Por favor, intenta nuevamente.');
        } catch (Exception $e) {
            Log::error('GoogleCalendar: Error al conectar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('tareas')
                ->with('error', 'No se pudo conectar con Google Calendar. Error: ' . $e->getMessage());
        }
    }

    /**
     * Sincroniza las tareas del usuario con Google Calendar
     * @param User $user Usuario a sincronizar
     * @param int|null $limit Número máximo de tareas a sincronizar (null = todas)
     */
    private function syncTasksToGoogleCalendar(User $user, ?int $limit = null)
    {
        try {
            Log::info('GoogleCalendar: Iniciando sincronización para usuario ' . $user->id, [
                'limit' => $limit ?? 'sin límite'
            ]);

            // Verificar que el usuario tenga token
            if (!$user->google_calendar_token) {
                throw new Exception('No hay token de Google Calendar');
            }

            // Configurar el cliente de Google
            $client = new \Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessToken($user->google_calendar_token);

            Log::info('GoogleCalendar: Cliente de Google configurado');

            // Verificar si el token está expirado y renovarlo si es necesario
            if ($client->isAccessTokenExpired()) {
                Log::warning('GoogleCalendar: Token expirado, intentando renovar');
                if ($user->google_calendar_refresh_token) {
                    $client->fetchAccessTokenWithRefreshToken($user->google_calendar_refresh_token);
                    $newToken = $client->getAccessToken();

                    $user->update([
                        'google_calendar_token' => $newToken['access_token'],
                        'google_calendar_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                    ]);
                    Log::info('GoogleCalendar: Token renovado exitosamente');
                } else {
                    throw new Exception('Token expirado y no hay refresh token');
                }
            }

            $service = new \Google_Service_Calendar($client);
            Log::info('GoogleCalendar: Servicio de Calendar creado');

            // Obtener las tareas del usuario
            $query = SubTarea::where('user_id', $user->id)
                ->where('archivada', false)
                ->orderBy('fecha_inicio', 'asc');

            if ($limit) {
                $query->limit($limit);
            }

            $tareas = $query->get();

            Log::info('GoogleCalendar: Tareas encontradas', [
                'total' => $tareas->count(),
                'user_id' => $user->id,
                'limit_applied' => $limit ?? 'none'
            ]);

            if ($tareas->count() === 0) {
                Log::warning('GoogleCalendar: No hay tareas para sincronizar');
                $user->update(['google_calendar_synced' => true]);
                return;
            }

            $sincronizadas = 0;
            $errores = 0;

            foreach ($tareas as $tarea) {
                try {
                    Log::info("GoogleCalendar: Sincronizando tarea {$tarea->id}: {$tarea->nombre}");

                    // Crear evento en Google Calendar con recordatorios
                    $event = new \Google_Service_Calendar_Event([
                        'summary' => $tarea->nombre,
                        'description' => $tarea->descripcion ?? 'Tarea creada desde BMaia',
                        'start' => [
                            'date' => Carbon::parse($tarea->fecha_inicio)->format('Y-m-d'),
                            'timeZone' => 'America/Santiago',
                        ],
                        'end' => [
                            'date' => Carbon::parse($tarea->fecha_limite)->addDay()->format('Y-m-d'),
                            'timeZone' => 'America/Santiago',
                        ],
                        'colorId' => $this->getPriorityColor($tarea->prioridad),
                        'reminders' => [
                            'useDefault' => false,
                            'overrides' => [
                                ['method' => 'popup', 'minutes' => 0], // Notificación al inicio
                                ['method' => 'email', 'minutes' => 1440], // Email 1 día antes
                            ],
                        ],
                    ]);

                    $createdEvent = $service->events->insert('primary', $event);
                    $sincronizadas++;

                    Log::info("GoogleCalendar: Tarea {$tarea->id} sincronizada exitosamente", [
                        'event_id' => $createdEvent->getId(),
                        'event_link' => $createdEvent->getHtmlLink()
                    ]);

                } catch (Exception $e) {
                    $errores++;
                    Log::error("GoogleCalendar: Error al sincronizar tarea {$tarea->id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            // Marcar como sincronizado
            $user->update(['google_calendar_synced' => true]);

            Log::info("GoogleCalendar: Sincronización completada", [
                'user_id' => $user->id,
                'sincronizadas' => $sincronizadas,
                'errores' => $errores,
                'total' => $tareas->count()
            ]);

        } catch (Exception $e) {
            Log::error('GoogleCalendar: Error en syncTasksToGoogleCalendar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Obtiene el conteo de tareas pendientes de sincronizar
     */
    public function getSyncStatus()
    {
        $user = Auth::user();

        if (!$user || !$user->google_calendar_token) {
            return response()->json(['error' => 'No conectado'], 401);
        }

        $totalTareas = SubTarea::where('user_id', $user->id)
            ->where('archivada', false)
            ->count();

        return response()->json([
            'total' => $totalTareas,
            'connected' => true
        ]);
    }

    /**
     * Sincroniza un lote de tareas (para llamadas AJAX)
     */
    public function syncBatch(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->google_calendar_token) {
            return response()->json(['error' => 'No conectado'], 401);
        }

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);

        try {
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
                    return response()->json(['error' => 'Token expirado'], 401);
                }
            }

            $service = new \Google_Service_Calendar($client);

            // Obtener el lote de tareas
            $tareas = SubTarea::where('user_id', $user->id)
                ->where('archivada', false)
                ->orderBy('fecha_inicio', 'asc')
                ->skip($offset)
                ->take($limit)
                ->get();

            $sincronizadas = 0;
            $errores = 0;
            $detalles = [];

            foreach ($tareas as $tarea) {
                try {
                    $event = new \Google_Service_Calendar_Event([
                        'summary' => $tarea->nombre,
                        'description' => $tarea->descripcion ?? 'Tarea creada desde BMaia',
                        'start' => [
                            'date' => Carbon::parse($tarea->fecha_inicio)->format('Y-m-d'),
                            'timeZone' => 'America/Santiago',
                        ],
                        'end' => [
                            'date' => Carbon::parse($tarea->fecha_limite)->addDay()->format('Y-m-d'),
                            'timeZone' => 'America/Santiago',
                        ],
                        'colorId' => $this->getPriorityColor($tarea->prioridad),
                        'reminders' => [
                            'useDefault' => false,
                            'overrides' => [
                                ['method' => 'popup', 'minutes' => 0],
                                ['method' => 'email', 'minutes' => 1440],
                            ],
                        ],
                    ]);

                    $service->events->insert('primary', $event);
                    $sincronizadas++;

                } catch (Exception $e) {
                    $errores++;
                    $errorMsg = "Error en tarea '{$tarea->nombre}' (ID: {$tarea->id}): " . $e->getMessage();
                    $detalles[] = $errorMsg;

                    Log::error('GoogleCalendar: Error sincronizando tarea', [
                        'tarea_id' => $tarea->id,
                        'tarea_nombre' => $tarea->nombre,
                        'fecha_inicio' => $tarea->fecha_inicio,
                        'fecha_limite' => $tarea->fecha_limite,
                        'prioridad' => $tarea->prioridad,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Si es el último lote, marcar como sincronizado
            $totalTareas = SubTarea::where('user_id', $user->id)
                ->where('archivada', false)
                ->count();

            $isComplete = ($offset + $limit) >= $totalTareas;

            if ($isComplete) {
                $user->update(['google_calendar_synced' => true]);
            }

            return response()->json([
                'success' => true,
                'sincronizadas' => $sincronizadas,
                'errores' => $errores,
                'procesadas' => $tareas->count(),
                'complete' => $isComplete,
                'detalles' => $detalles
            ]);

        } catch (Exception $e) {
            Log::error('Error en syncBatch', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina todos los eventos de Google Calendar del usuario
     */
    public function deleteAllCalendarEvents()
    {
        // Aumentar el tiempo de ejecución para eliminar todos los eventos
        set_time_limit(120); // 2 minutos

        $user = Auth::user();

        if (!$user || !$user->google_calendar_token) {
            return response()->json(['error' => 'No conectado'], 401);
        }

        try {
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
                    return response()->json(['error' => 'Token expirado'], 401);
                }
            }

            $service = new \Google_Service_Calendar($client);

            // Obtener eventos (máximo 250 es suficiente según las tareas esperadas)
            $events = $service->events->listEvents('primary', [
                'maxResults' => 250,
                'singleEvents' => true,
            ]);

            $total = 0;
            $eliminados = 0;
            $errores = 0;
            $omitidos = 0;

            Log::info("GoogleCalendar: Iniciando eliminación de eventos de BMaia", [
                'user_id' => $user->id,
                'total_eventos_en_calendario' => count($events->getItems())
            ]);

            foreach ($events->getItems() as $event) {
                $total++;

                // IMPORTANTE: Solo eliminar eventos creados por BMaia
                $descripcion = $event->getDescription() ?? '';
                $esDeBMaia = str_contains($descripcion, 'BMaia') ||
                    str_contains($descripcion, 'Tarea creada desde BMaia');

                if (!$esDeBMaia) {
                    $omitidos++;
                    Log::debug("GoogleCalendar: Evento omitido (no es de BMaia)", [
                        'evento_id' => $event->getId(),
                        'titulo' => $event->getSummary(),
                        'descripcion' => substr($descripcion, 0, 50)
                    ]);
                    continue;
                }

                try {
                    $service->events->delete('primary', $event->getId());
                    $eliminados++;

                    Log::debug("GoogleCalendar: Evento eliminado", [
                        'evento_id' => $event->getId(),
                        'titulo' => $event->getSummary()
                    ]);
                } catch (Exception $e) {
                    $errores++;
                    Log::warning("GoogleCalendar: Error eliminando evento", [
                        'evento_id' => $event->getId(),
                        'evento_titulo' => $event->getSummary(),
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Marcar como no sincronizado
            $user->update(['google_calendar_synced' => false]);

            Log::info("GoogleCalendar: Eliminación completada", [
                'user_id' => $user->id,
                'total_revisados' => $total,
                'eliminados' => $eliminados,
                'omitidos' => $omitidos,
                'errores' => $errores
            ]);

            return response()->json([
                'success' => true,
                'total' => $total,
                'eliminados' => $eliminados,
                'omitidos' => $omitidos,
                'errores' => $errores
            ]);

        } catch (Exception $e) {
            Log::error('GoogleCalendar: Error eliminando eventos', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapea las prioridades a colores de Google Calendar
     */
    private function getPriorityColor($prioridad)
    {
        $colores = [
            'baja' => '7',      // Turquesa
            'media' => '2',     // Verde claro
            'alta' => '5',      // Amarillo
            'urgente' => '11',  // Rojo
        ];

        return $colores[$prioridad] ?? '7';
    }
}
