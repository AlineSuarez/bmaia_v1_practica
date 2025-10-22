<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\ColmenaApiController;
use App\Http\Controllers\Api\v1\VisitaApiController;
use App\Http\Controllers\Api\v1\ApiarioApiController;
use App\Http\Controllers\Api\v1\SyncController;
use App\Http\Controllers\Api\v1\VoiceController;
use App\Http\Controllers\Api\v1\AIController;
use App\Http\Controllers\Api\v1\AssistantApiController;
use App\Http\Controllers\Api\v1\StatsController;
use App\Http\Controllers\Api\v1\IndicadoresController;
use App\Http\Controllers\Api\v1\PreferencesController;
use App\Http\Controllers\Api\v1\DeviceController;
use App\Http\Controllers\Api\v1\FileController;
use App\Http\Controllers\Api\v1\ClimaController;
use App\Http\Controllers\Api\v1\NotificacionController;
use App\Http\Controllers\Api\v1\MovimientoController;
use App\Http\Controllers\Api\v1\ProfileController;
use App\Models\Region;
use App\Models\Comuna;
use App\Http\Controllers\Api\v1\ForgotPasswordController;
use App\Http\Controllers\Api\v1\GeoController;

use App\Http\Controllers\Api\v1\VisitaInspeccionApiController;
use App\Http\Controllers\Api\v1\VisitaMedicamentosApiController;
use App\Http\Controllers\Api\v1\VisitaAlimentacionApiController;
use App\Http\Controllers\Api\v1\VisitaReinaApiController;

use App\Models\Apiario;

Route::prefix('v1')->name('api.')->group(function () {

    // ---- Auth públicas ----
    Route::post('login',    [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');

    Route::post('login/google', [AuthController::class, 'loginWithGoogle'])->middleware('throttle:10,1');

    Route::get('health', fn () => response()->json(['ok' => true, 'time' => now()->toIso8601String()]))->name('health');

    Route::post('password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1');
    Route::post('password/reset',  [ForgotPasswordController::class, 'reset'])->middleware('throttle:5,1');

    // ---- Rutas autenticadas ----
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        
        Route::get('user', function (Request $r) {
            $u = $r->user();

            return response()->json([
                'id'              => $u->id,
                'name'            => $u->name,
                'last_name'       => $u->last_name,
                'email'           => $u->email,
                'telefono'        => $u->telefono,
                'created_at'      => $u->created_at,

                'rut'             => $u->rut,
                'razon_social'    => $u->razon_social,
                'direccion'       => $u->direccion,
                'numero_registro' => $u->numero_registro,

                // 👉 lo que la app necesita para resolver nombres
                'id_region'       => $u->id_region,
                'id_comuna'       => $u->id_comuna,

                // opcional: manda también los nombres
                'region'          => optional(Region::find($u->id_region))->nombre,
                'comuna'          => optional(Comuna::find($u->id_comuna))->nombre,

                // avatar si aplica
                'avatar_url'      => $u->profile_picture ? asset('storage/'.$u->profile_picture) : null,
            ]);
        })->name('auth.me');

        Route::patch('me', [ProfileController::class, 'update']); // actualizar nombre/teléfono
        Route::post('me/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::patch('me/password', [ProfileController::class, 'updatePassword']);

        Route::get('geo/resolve', [GeoController::class, 'resolve'])->name('geo.resolve');

        // ===== Lookups para la app móvil =====
        Route::get('/regiones', function () {
            return response()->json(
                // tu tabla tiene columna 'nombre'
                Region::select('id', \DB::raw("nombre as name"))
                    ->orderBy('nombre')
                    ->get()
            );
        });

        Route::get('/comunas', function () {
            return response()->json(
                Comuna::select(
                    'id',
                    \DB::raw("nombre as name"),
                    'region_id' // ✅ en tu tabla se llama region_id
                )
                ->orderBy('nombre')
                ->get()
            );
        });

        Route::get('me/preferences',   [PreferencesController::class, 'show'])->name('me.preferences.show');
        Route::patch('me/preferences', [PreferencesController::class, 'update'])->name('me.preferences.update');

        Route::apiResource('apiarios', ApiarioApiController::class);      // nombres: api.apiarios.index|store|show|update|destroy
        Route::apiResource('colmenas', ColmenaApiController::class);
        Route::apiResource('visitas',  VisitaApiController::class);

        // ---- Visita GENERAL (formulario específico) ----
        Route::post('visitas/general', [VisitaApiController::class, 'storeGeneral'])->name('visitas.general.store');
        Route::put('visitas/general/{visita}', [VisitaApiController::class, 'updateGeneral'])->name('visitas.general.update');

        // ---- Visita de INSPECCIÓN ----
        Route::post('visitas/inspeccion', [VisitaApiController::class, 'storeInspeccion'])->name('visitas.inspeccion.store');
        Route::put('visitas/inspeccion/{visita}', [VisitaApiController::class, 'updateInspeccion'])->name('visitas.inspeccion.update');

        // ---- Visita de USO DE MEDICAMENTOS ----
        Route::post('visitas/medicamentos', [VisitaApiController::class, 'storeMedicamentos'])->name('visitas.medicamentos.store');
        Route::put('visitas/medicamentos/{visita}', [VisitaApiController::class, 'updateMedicamentos'])->name('visitas.medicamentos.update');

        // ---- Registro de ALIMENTACIÓN ----
        Route::post('visitas/alimentacion', [VisitaApiController::class, 'storeAlimentacion'])->name('visitas.alimentacion.store');
        Route::put('visitas/alimentacion/{visita}', [VisitaApiController::class, 'updateAlimentacion'])->name('visitas.alimentacion.update');

        // ---- Registro de REINA ----
        Route::post('visitas/reina', [VisitaApiController::class, 'storeReina'])->name('visitas.reina.store');
        Route::put('visitas/reina/{visita}', [VisitaApiController::class, 'updateReina'])->name('visitas.reina.update');

        Route::get('apiarios/{apiario}/colmenas', [ColmenaApiController::class, 'indexByApiario'])->name('apiarios.colmenas');
        Route::get('apiarios/{apiario}/visitas',  [VisitaApiController::class,  'indexByApiario'])->name('apiarios.visitas');

        Route::get('apiarios/base', function () {
            return Apiario::where('tipo_apiario', 'trashumante')->where('activo', true)->paginate(50);
        })->name('apiarios.base');

        Route::get('apiarios/temporales', function () {
            return Apiario::where('tipo_apiario', 'temporal')->where('activo', true)->paginate(50);
        })->name('apiarios.temporales');

        Route::post('movimientos/traslado', [MovimientoController::class, 'traslado'])->name('movimientos.traslado');
        Route::post('movimientos/retorno',  [MovimientoController::class, 'retorno'])->name('movimientos.retorno');

        Route::post('agent/respond', [AssistantApiController::class, 'respond'])
            ->name('agent.respond')
            ->middleware('idempotency');

        Route::post('sync',       [SyncController::class, 'syncData'])->name('sync.data');
        Route::get('changes',     [SyncController::class, 'changes'])->name('sync.changes');
        Route::get('sync-status', [SyncController::class, 'syncStatus'])->name('sync.status');

        Route::get('stream', [SyncController::class, 'stream'])->name('stream');

        Route::post('voice/recognize', [VoiceController::class, 'recognize'])->name('voice.recognize');
        Route::post('voice/respond',   [VoiceController::class, 'respond'])->name('voice.respond');
        Route::post('ask-ai',          [AIController::class, 'ask'])->name('ask-ai');

        Route::get('stats/apiarios/count',  [StatsController::class, 'apiariosCount'])->name('stats.apiarios.count');
        Route::get('stats/colmenas/count',  [StatsController::class, 'colmenasCount'])->name('stats.colmenas.count');
        Route::get('stats/colmenas/health', [StatsController::class, 'colmenasHealth'])->name('stats.colmenas.health');
        Route::get('stats/visitas/semana',  [StatsController::class, 'visitasSemana'])->name('stats.visitas.semana');

        Route::get('indicadores/produccion',  [IndicadoresController::class, 'produccion'])->name('indicadores.produccion');
        Route::get('indicadores/comparativo', [IndicadoresController::class, 'comparativo'])->name('indicadores.comparativo');

        Route::post('devices/register',      [DeviceController::class, 'register'])->name('devices.register');
        Route::delete('devices/{device_id}', [DeviceController::class, 'destroy'])->name('devices.destroy');

        Route::post('files',        [FileController::class, 'store'])->name('files.store');
        Route::get('files',         [FileController::class, 'index'])->name('files.index');
        Route::delete('files/{id}', [FileController::class, 'destroy'])->name('files.destroy');

        Route::get('clima/actual',     [ClimaController::class, 'actual'])->name('clima.actual');
        Route::get('clima/pronostico', [ClimaController::class, 'pronostico'])->name('clima.pronostico');

        Route::post('notificaciones/email',    [NotificacionController::class, 'email'])->name('notificaciones.email');
        Route::post('notificaciones/whatsapp', [NotificacionController::class, 'whatsapp'])->name('notificaciones.whatsapp');
        Route::post('notificaciones/push',     [NotificacionController::class, 'push'])->name('notificaciones.push');
    });

});

// Fallback JSON
Route::fallback(function () {
    return response()->json([
        'error' => ['code' => 'NOT_FOUND', 'message' => 'Ruta no encontrada']
    ], 404);
});
