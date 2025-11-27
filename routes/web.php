<?php
use App\Http\Controllers\RouteTimeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
//use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ApiarioController;
use App\Http\Controllers\ColmenaController;
use App\Http\Controllers\TrashumanciaController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\DashboardController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ZonificacionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PreferencesController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ImportantDateController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\SistemaExpertoController;
use App\Http\Controllers\DatoFacturacionController;
use App\Http\Controllers\FacturaController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\HojaRutaController;
use App\Http\Controllers\FloraCatalogController;
// 🔹 NUEVO: controlador de monitoreo histórico
use App\Http\Controllers\MonitoreoController;

// === AGREGADO: controlador para el catálogo de flora (ver más / editar perfil)
use App\Http\Controllers\FloraPerfilController;

// === AGREGADO: controlador del PROXY WMS/WFS (evita problemas de CORS)
use App\Http\Controllers\GeoProxyController;

// === AGREGADO: controlador para el Explorador del mapa (inyecta $regiones)
use App\Http\Controllers\MapaExploradorController;

/** ====== AGREGADO PASO 4: API perfiles de Región y Comuna ====== */
use App\Http\Controllers\RegionPerfilController as HRRegionPerfilController;
use App\Http\Controllers\ComunaPerfilController as HRComunaPerfilController;

Auth::routes();

// Proxy para reverse geocoding (Nominatim)
Route::get('/reverse-geocode', function (Illuminate\Http\Request $request) {
    $lat = $request->query('lat');
    $lon = $request->query('lon');
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&zoom=10&addressdetails=1";
    $response = Http::get($url);
    return $response->json();
});

// === PROXY WMS/WFS principal (controlador robusto)
Route::get('/wms-proxy', [GeoProxyController::class, 'proxy'])->name('geo.wms-proxy');

// === NUEVO: Ping de capacidades (para probar conectividad del WMS)
Route::get('/wms-proxy/ping', [GeoProxyController::class, 'ping'])->name('geo.wms-ping');

// === PROXY WMS sencillo (closure) para casos puntuales (opcional, no reemplaza lo anterior)
Route::get('/wms-proxy-lite', function (\Illuminate\Http\Request $req) {
    $baseUrl = $req->query('url'); // URL base del servicio WMS/WFS (ej: https://ide.mma.gob.cl/geoserver/ows)
    if (!$baseUrl) {
        return response()->json(['ok' => false, 'error' => 'Falta parámetro url'], 400);
    }

    // (Opcional) Lista blanca rápida — ajusta dominios permitidos si quieres endurecer
    $allowed = [
        'ide.mma.gob.cl',
        'ide.chile.gob.cl',
        'mapas.conaf.cl',
        'mapas5.arcgisonline.com',
        'mapas.snic.conaf.cl',
        'geoportal.conaf.cl',
        'sig.sernageomin.cl',
        'geoserver',
        'arcgis'
    ];
    $host = parse_url($baseUrl, PHP_URL_HOST);
    $allowedHost = $host && collect($allowed)->first(fn($d) => str_contains($host, $d));
    if (!$allowedHost) {
        return response()->json(['ok' => false, 'error' => 'Host no permitido'], 403);
    }

    // Reenviamos todos los parámetros (excepto url)
    $params = $req->except('url');
    $qs = http_build_query($params);
    $final = $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . $qs;

    try {
        $r = Http::withHeaders([
            'User-Agent' => 'B-MaiA/1.0 (educational)'
        ])->get($final);

        return response($r->body(), $r->status())
            ->header('Content-Type', $r->header('Content-Type', 'application/json'));
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
})->name('geo.wms-proxy-lite');

//Rutas de las policies 
Route::view('/politicas-de-privacidad', 'legal.privacidad')->name('privacidad');
Route::view('/terminos-de-uso', 'legal.terminos')->name('terminos');
Route::view('/politica-de-cookies', 'legal.cookies')->name('cookies');

// Login con Google usando controlador
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

/*
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/home');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function () {
        request()->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Correo de verificación enviado.');
    })->middleware(['throttle:6,1'])->name('verification.send');
});
*/

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Página de contacto - Formulario
Route::get('/contacto', function () {
    return view('contacto.contacto-form');
})->name('contacto.form');

Route::post('/contacto', [App\Http\Controllers\ContactoController::class, 'enviar'])->name('contacto.enviar');

// Ruta AJAX para obtener dinamicamente coordenadas de comuna al crear temporal
Route::get('/comuna-coordenadas/{id}', function ($id) {
    $comuna = \App\Models\Comuna::find($id);
    return response()->json([
        'lat' => $comuna->lat,
        'lon' => $comuna->lon,
    ]);
});
Route::get('/colmena-publica/{colmena}', [ColmenaController::class, 'publicView'])->name('colmenas.public');

// AJAX individual: obtener consejo por apiario (simulado)
Route::get('/apiarios/{apiario}/obtener-consejo', [App\Http\Controllers\ApiarioController::class, 'obtenerConsejo']);

// Documentos y Emails
Route::get('/generate-document/{id}', [DocumentController::class, 'generateDocument'])->name('generate.document');
Route::get('/send-email', [MailController::class, 'sendEmail']);

// Pagos (NO PROTEGIDAS POR check.payment)
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/response', [PaymentController::class, 'paymentResponse'])->name('payment.response');
    Route::post('/trial/start', [PaymentController::class, 'startTrial'])->name('trial.start');
});

Route::get('/payment/success', [PaymentController::class, 'showSuccess'])->name('payment.success');
//Route::match(['get', 'post'], '/payment/failed', [PaymentController::class, 'showFailed'])->name('payment.failed');

Route::get('/payment/required', [PaymentController::class, 'showRequired'])->name('payment.required');
Route::get('/payment/failed', [PaymentController::class, 'showFailed'])->name('payment.failed');
Route::post('/payment/failed', [PaymentController::class, 'showFailed']);
/*
Route::middleware(['auth'])->group(function () {
        Route::post('/payment/refund', [PaymentController::class, 'refund'])->name('payment.refund');
        Route::post('/payment/refund-partial', [PaymentController::class, 'refundPartial'])->name('payment.refund.partial');
    });
*/
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/response', [PaymentController::class, 'paymentResponse'])->name('payment.response');
    Route::post('/trial/start', [PaymentController::class, 'startTrial'])->name('trial.start');
    Route::get('/home', [DashboardController::class, 'home'])->name('home');
});

Route::get('/facturas/{factura}/descargar', [DatoFacturacionController::class, 'download'])->name('facturas.descargar');
Route::get('/facturas/{factura}/ver', [DatoFacturacionController::class, 'view'])->name('facturas.ver');

Route::middleware(['auth'])->group(function () {
    Route::get('/user/settings', [UserController::class, 'settings'])->name('user.settings');
    Route::patch('/user/update-name', [UserController::class, 'updateName'])->name('user.update.name');
    Route::post('/user/avatar', [UserController::class, 'updateAvatar'])->name('user.updateAvatar');
    Route::patch('/user/update-password', [UserController::class, 'updatePassword'])->name('user.update.password');
    Route::post('/user/update-settings', [UserController::class, 'updateSettings'])->name('user.updateSettings');
    Route::post('/user/update-plan', [UserController::class, 'updatePlan'])->name('user.updatePlan');

    // Permisos
    Route::get('/user/settings/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
    Route::post('/user/settings/permissions', [PermissionsController::class, 'update'])->name('permissions.update');
    Route::post('/user/settings/permissions/reset', [PermissionsController::class, 'reset'])->name('permissions.reset');

    // Preferencias
    Route::get('/user/settings/preferences', [PreferencesController::class, 'index'])->name('preferences.index');
    Route::post('/user/settings/preferences', [PreferencesController::class, 'update'])->name('preferences.update');
    Route::post('/user/settings/preferences/reset', [PreferencesController::class, 'reset'])->name('preferences.reset');
    Route::get('/user/settings/preferences/demo', [PreferencesController::class, 'dateFormatDemo'])->name('preferences.demo');
    Route::post('/user/settings/preferences/date-format', [PreferencesController::class, 'updateDateFormat'])->name('preferences.updateDateFormat');

    // Alertas
    Route::get('/user/settings/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/user/settings/alerts', [AlertController::class, 'store'])->name('alerts.store');
    Route::put('/user/settings/alerts/{alert}', [AlertController::class, 'update'])->name('alerts.update');
    Route::delete('/user/settings/alerts/{alert}', [AlertController::class, 'destroy'])->name('alerts.destroy');

    // Recordatorios
    Route::get('/user/settings/reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::post('/user/settings/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::put('/user/settings/reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
    Route::delete('/user/settings/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');

    // Fechas importantes
    Route::get('/user/settings/dates', [ImportantDateController::class, 'index'])->name('dates.index');
    Route::post('/user/settings/dates', [ImportantDateController::class, 'store'])->name('dates.store');
    Route::put('/user/settings/dates/{date}', [ImportantDateController::class, 'update'])->name('dates.update');
    Route::delete('/user/settings/dates/{date}', [ImportantDateController::class, 'destroy'])->name('dates.destroy');

    // Contactos de emergencia
    Route::get('/user/settings/contacts', [EmergencyContactController::class, 'index'])->name('contacts.index');
    Route::post('/user/settings/contacts', [EmergencyContactController::class, 'store'])->name('contacts.store');
    Route::put('/user/settings/contacts/{contact}', [EmergencyContactController::class, 'update'])->name('contacts.update');
    Route::delete('/user/settings/contacts/{contact}', [EmergencyContactController::class, 'destroy'])->name('contacts.destroy');

    // FACTURACION
    Route::post('user/datos-facturacion', [DatoFacturacionController::class, 'store'])->name('datos-facturacion.store');
    Route::post('/facturas/{factura}/enviar-correo', [DatoFacturacionController::class, 'enviarCorreo'])->name('facturas.enviarCorreo');

    // RUTA: ver detalle de factura
    Route::get('/facturas/{factura}', [App\Http\Controllers\FacturaController::class, 'show'])
        ->name('facturas.show');
});

// ================================
// RUTAS PROTEGIDAS POR SISTEMA DE PAGO
// ================================
Route::middleware(['auth', 'check.payment'])->group(function () {

    // Dashboard y Home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/apiarios/create-temporal', [ApiarioController::class, 'createTemporal'])->name('apiarios.createTemporal');

    // Apiarios
    Route::resource('apiarios', ApiarioController::class)->except(['show']);
    Route::get('/apiarios', [ApiarioController::class, 'index'])->name('apiarios');
    Route::get('apiarios/create', [ApiarioController::class, 'create'])->name('apiarios.create');
    Route::post('apiarios/store', [ApiarioController::class, 'store'])->name('apiarios.store');
    Route::get('apiarios/editar/{id}', [ApiarioController::class, 'edit'])->name('apiarios.edit');
    Route::post('apiarios/editar/{id}', [ApiarioController::class, 'update'])->name('apiarios.editar');
    Route::get('comunas/{region}', [ApiarioController::class, 'getComunas']);
    Route::delete('/apiarios/delete/{apiario}', [ApiarioController::class, 'deleterApiario'])->name('apiarios.destroy');
    Route::get('/apiarios/{id}', [ApiarioController::class, 'show'])->name('apiarios.show');
    Route::get('/apiarios-temporales/{apiario}/detalle-movimiento', [ApiarioController::class, 'detalleMovimiento']);
    Route::get('/apiarios-temporales/{apiario}/exportar-historial', [ApiarioController::class, 'exportarHistorial'])->name('apiarios.exportarHistorial');
    Route::post('/apiarios/massDelete', [ApiarioController::class, 'massDelete'])->name('apiarios.massDelete');
    Route::post('/apiarios/{id}/convertir-trashumante-base', [ApiarioController::class, 'convertirEnTrashumanteBase'])->name('apiarios.convertirBase');
    Route::post('/apiarios/{apiario}/convertir-fijo', [ApiarioController::class, 'convertirAFijo'])->name('apiarios.convertirFijo');

    // Trashumancia - temporal
    Route::post('/trashumancia/store', [TrashumanciaController::class, 'store'])->name('trashumancia.store');
    Route::post('/apiarios/{id}/archivar', [TrashumanciaController::class, 'archivar'])->name('apiarios.archivar');
    Route::post('/apiarios/archivar-multiples', [TrashumanciaController::class, 'archivarMultiples'])->name('apiarios.archivarMultiples');
    Route::get('apiarios/{apiario}/edit-temporal', [TrashumanciaController::class, 'editTemporal'])->name('apiarios.editTemporal');
    Route::patch('apiarios/{apiario}/update-temporal', [TrashumanciaController::class, 'updateTemporal'])->name('apiarios.updateTemporal');

    // Colmenas
    Route::prefix('apiarios/{apiario}/colmenas')->name('colmenas.')->group(function () {
        Route::get('/', [ColmenaController::class, 'index'])->name('index');
        Route::get('/create', [ColmenaController::class, 'create'])->name('create');
        Route::post('/', [ColmenaController::class, 'store'])->name('store');
        Route::get('/historicas', [ColmenaController::class, 'historicas'])->name('historicas');
        Route::get('/historicas/export', [ColmenaController::class, 'exportHistoricas'])->name('historicas.export');
        //Route::get('/{colmena}', [ColmenaController::class, 'show'])->name('show');
        Route::get('/{colmena}/edit', [ColmenaController::class, 'edit'])->name('edit');
        Route::put('/{colmena}', [ColmenaController::class, 'update'])->name('update');
        Route::delete('/{colmena}', [ColmenaController::class, 'destroy'])->name('destroy');
        Route::get('/{colmena}/historial', [ColmenaController::class, 'historial'])->name('historial');
        Route::get('/{colmena}/historial/export', [ColmenaController::class, 'exportHistorial'])->name('historial.export');
        Route::get('/{colmena}/qr-pdf', [DocumentController::class, 'qrPdf'])->name('qr-pdf');
        Route::post('/qr-multiple', [ColmenaController::class, 'qrMultiplePdf'])->name('qr.multiple');
        Route::post('/delete-multiple', [ColmenaController::class, 'deleteMultiple'])->name('delete.multiple');
        Route::get('/{colmena}/pcc/pdf', [ColmenaController::class, 'generarPccPdf'])->name('pcc.pdf');
        Route::put('/{colmena}/color', [ColmenaController::class, 'updateColor'])->name('updateColor');
    });

    // Visitas
    Route::resource('visita', VisitaController::class);
    Route::get('/visitas', [VisitaController::class, 'index'])->name('visitas');
    Route::get('visitas/create/{id}', [VisitaController::class, 'create'])->name('visitas.create');
    Route::post('apiarios/{apiario}/visitas', [VisitaController::class, 'store'])->name('visitas.store');
    Route::post('apiarios/{apiario}/visitas1', [VisitaController::class, 'store1'])->name('visitas.store1');

    // Rutas para Registro de Inspección de Apiario
    Route::post('apiarios/{apiario}/inspeccion-apiario', [VisitaController::class, 'store'])->name('apiarios.inspeccion-apiario.store');
    Route::get('/generate-document/inspeccion/{id}', [DocumentController::class, 'generateInspeccionDocument'])->name('generate.document.inspeccion');

    // Rutas para Registro General de Visitas
    Route::get('visitas/create1/{id}', [VisitaController::class, 'createGeneral'])->name('visitas.visitas-general');
    Route::post('apiarios/{apiario}/visitas-general', [VisitaController::class, 'storeGeneral'])->name('apiarios.visitas-general.store');
    Route::get('/generate-document/visitas/{id}', [DocumentController::class, 'generateVisitasDocument'])->name('generate.document.visitas');

    //Rutas para registro de uso de medicamentos.
    Route::get('visitas/create2/{id}', [VisitaController::class, 'createMedicamentos'])->name('visitas.medicamentos-registro');
    Route::post('apiarios/{apiario}/medicamentos-registro', [VisitaController::class, 'storeMedicamentos'])->name('apiarios.medicamentos-registro.store');
    Route::get('/generate-document/medicamentos/{apiarioId}', [DocumentController::class, 'generateMedicamentsDocument'])->name('generate.document.medicamentos');
    Route::get('apiarios/{apiario}/medicamentos-registro/{visita}/edit', [VisitaController::class, 'editMedicamentos'])->name('apiarios.medicamentos-registro.edit');

    // Rutas para registro de Alimentación
    Route::get('visitas/create3/{apiario}', [VisitaController::class, 'createAlimentacion'])->name('visitas.create3');
    Route::post('visitas/create3/{apiario}', [VisitaController::class, 'storeAlimentacion'])->name('visitas.store3');
    Route::get('/generate-document/alimentacion-record/{apiarioId}', [DocumentController::class, 'generateAlimentacionDocument'])->name('generate.document.alimentacion');
    Route::get('visitas/create3/{apiario}/{visita}/edit', [VisitaController::class, 'editAlimentacion'])->name('visitas.alimentacion.edit');

    // Rutas para registro de Reina
    Route::get('visitas/create4/{apiario}', [VisitaController::class, 'createReina'])->name('visitas.create4');
    Route::post('visitas/create4/{apiario}', [VisitaController::class, 'storeReina'])->name('visitas.store4');
    Route::get('/generate-document/reina-record/{apiarioId}', [DocumentController::class, 'generateReinaDocument'])->name('generate.document.reina');
    Route::get('visitas/create4/{apiario}/{visita}/edit', [VisitaController::class, 'editReina'])->name('visitas.reina.edit');

    // PCCs para colmenas
    Route::prefix('visitas')->name('visitas.')->group(function () {
        Route::get('{visita}/pcc/create', [VisitaController::class, 'createPcc'])->name('pcc.create');
        Route::post('{visita}/pcc', [VisitaController::class, 'storePcc'])->name('pcc.store');
        Route::get('{visita}/pcc/edit', [VisitaController::class, 'editPcc'])->name('pcc.edit');
        Route::put('{visita}/pcc', [VisitaController::class, 'updatePcc'])->name('pcc.update');
    });

    // Historial de visitas de un apiario
    Route::get('apiarios/{apiario}/visitas', [VisitaController::class, 'showHistorial'])->name('visitas.historial');

    // Chatbot
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
    Route::get('/chatbot/messages', [ChatbotController::class, 'getMessages'])->name('chatbot.messages');
    Route::get('/chatbot/consejos', [ChatbotController::class, 'generarConsejos'])->name('consejos');

    // Tareas
    Route::get('/tareas/search', [TaskController::class, 'search'])->name('tareas.search');
    Route::get('/tareas/view/{view}', [TaskController::class, 'loadView'])->name('tareas.view');
    Route::get('/tareas/json', [TaskController::class, 'obtenerEventosJson'])->name('tareas.json');
    Route::post('/tareas/{id}/archivar', [TaskController::class, 'archivar'])->name('tareas.archivar');
    Route::post('/tareas/restaurar/{id}', [TaskController::class, 'restaurar'])->name('tareas.restaurar');
    Route::get('/tareas/archivadas', [TaskController::class, 'verArchivadas'])->name('tareas.archivadas');
    Route::delete('/tareas/{id}', [TaskController::class, 'destroy'])->name('tareas.destroy');
    Route::get('/tareas', [TaskController::class, 'index'])->name('tareas');
    Route::post('/tareas', [TaskController::class, 'store'])->name('tareas.store');
    Route::post('/tareas/default', [TaskController::class, 'default'])->name('tareas.default');
    Route::patch('/tareas/{id}', [TaskController::class, 'update'])->name('tareas.update');
    Route::patch('/tareas/{id}/update-status', [TaskController::class, 'updateStatus']);
    Route::get('/tareas/{id}', [TaskController::class, 'show'])->name('tareas.show');
    Route::post('/tareas/update/{id}', [TaskController::class, 'guardarCambios']);
    Route::patch('/tareas/{id}/update', [TaskController::class, 'updateTarea']);
    Route::get('/todas-las-tareas/imprimir', [DocumentController::class, 'imprimirTodasSubtareas'])->name('tareas.imprimirTodas');
    Route::get('/datos-subtareas', [TaskController::class, 'obtenerSubtareasJson'])->name('tareas.datos');
    Route::get('/tareas/calendario', [TaskController::class, 'calendario'])->name('tareas.calendario');
    Route::post('/tareas-generales', [TaskController::class, 'storeAjax']);

    // Zonificación
    Route::get('/zonificacion', [ZonificacionController::class, 'index'])->name('zonificacion');

    Route::get('/zonificacion/{apiario}', [ZonificacionController::class, 'show'])
        ->name('zonificacion.show');

    Route::get('/debug-date', function () {
        return [
            'auth_user' => Auth::check() ? Auth::user()->email : 'no user',
            'date_format' => config('app.date_format'),
            'from_model' => optional(Auth::user()->preference)->date_format ?? 'default_format',
        ];
    });

    Route::get('/test-lang', function () {
        return view('apiarios.test-lang');
    });
 
    // Sistema Experto
    Route::prefix('sistemaexperto')->name('sistemaexperto.')->group(function () {
        Route::get('/', [SistemaExpertoController::class, 'index'])->name('index');
        Route::get('{apiario}/create', [SistemaExpertoController::class, 'create'])->name('create');
        Route::post('{apiario}/store', [SistemaExpertoController::class, 'store'])->name('store');
        Route::get('colmenas/{colmena}/edit', [SistemaExpertoController::class, 'editPcc'])->name('editpcc');
        Route::put('sistemaexperto/{sistemaexperto}', [SistemaExpertoController::class, 'update'])->name('update');
    });
    
});

// Ruta pública para detalle de colmena
Route::get('apiarios/{apiario}/colmenas/{colmena}', [App\Http\Controllers\ColmenaController::class, 'show'])->name('colmenas.show');

// Ruta pública para imprimir QR (PDF) sin autenticación
Route::get('/apiarios/{apiario}/colmenas/{colmena}/qr-pdf-public', [App\Http\Controllers\DocumentController::class, 'qrPdfPublic'])
    ->name('colmenas.qr-pdf.public');

/* =======================================================================
 *  HOJA DE RUTA + CATÁLOGO DE FLORA
 * ======================================================================= */

//  Todo el módulo Hoja de Ruta queda protegido por auth + check.payment
Route::middleware(['auth', 'check.payment'])->group(function () {

    // Vista principal del módulo Hoja de Ruta
    Route::view('/hoja-de-ruta', 'hoja_ruta.index')->name('hoja.ruta');

    // Explorador (mapa SVG) – usando HojaRutaController
    Route::get('/hoja-de-ruta/explorador', [HojaRutaController::class, 'explorador'])
        ->name('hoja.explorador');

    // Cálculo de ruta
    Route::view('/hoja-de-ruta/calculo', 'hoja_ruta.calculo')->name('hoja.calculo');

    // Monitoreo histórico
    Route::get('/hoja-de-ruta/monitoreo', [MonitoreoController::class, 'index'])->name('hoja.monitoreo');

    // Capacidad de carga y SVG extra
    Route::view('/hoja-de-ruta/capacidad', 'hoja_ruta.capacidad')->name('hoja.capacidad');
    Route::view('/hoja-de-ruta/explorador-svg', 'hoja_ruta.explorador_svg')->name('hoja.explorador.svg');

    /*
     * Catálogo de Flora
      */
    // Ruta antigua: redirige al catálogo nuevo
    Route::get('/hoja-de-ruta/catalogo', function () {
        return redirect()->route('flora.catalogo.index');
    })->name('hoja.catalogo');

    // Listado principal del catálogo (INDEX)
    Route::get('/hoja-de-ruta/catalogo-flora', [FloraCatalogController::class, 'index'])
        ->name('flora.catalogo.index');

    // Detalle de una especie
    Route::get('/hoja-de-ruta/catalogo-flora/{species}', [FloraCatalogController::class, 'show'])
        ->name('flora.catalogo.show');

    // API de cálculo de tiempo de ruta
    Route::get('/api/route-time', [RouteTimeController::class, 'calc'])->name('route-time');
});
