<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ApiarioController;
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
//use App\Http\Controllers\MovimientoColmenaController;

//Rutas de las policies 
Route::view('/politicas-de-privacidad', 'legal.privacidad')->name('privacidad');
Route::view('/terminos-de-uso', 'legal.terminos')->name('terminos');
Route::view('/politica-de-cookies', 'legal.cookies')->name('cookies');

Route::get('/tareas/search', [TaskController::class, 'search'])->name('tareas.search');
//Route::resource('tareas', TaskController::class);
Route::get('/tareas/view/{view}', [TaskController::class, 'loadView'])->name('tareas.view');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
// Rutas de autenticación
Auth::routes();
Route::get('login/google', [LoginController::class, 'redirectToGoogle']);
Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Rutas para apicultores
Route::middleware(['auth'])->group(function () {
    //Route::get('/home', [ApiarioController::class, 'home'])->name('home');
    Route::get('/home', function () {
        $default = optional(Auth::user()->preference)->default_view ?? 'dashboard';
        $routes = [
            'dashboard' => 'dashboard',
            'apiaries' => 'apiarios',
            'calendar' => 'tareas.calendario',
            'reports' => 'dashboard',
            'home' => 'home',
            'cuaderno' => 'visitas.index',
            'tareas' => 'tareas',
            'zonificacion' => 'zonificacion',
            'sistemaexperto' => 'sistemaexperto',
        ];
        return redirect()->route($routes[$default] ?? 'dashboard');
    })->middleware(['auth']);




    Route::get('/apiarios/create-temporal', [TrashumanciaController::class, 'create'])->name('apiarios.createTemporal');
    Route::resource('apiarios', ApiarioController::class);
    Route::post('/apiarios/store-fijo', [ApiarioController::class, 'storeFijo'])->name('apiarios.storeFijo');
    Route::post('/apiarios/store-trashumante', [TrashumanciaController::class, 'store'])->name('apiarios.storeTrashumante');
    //Route::post('/apiarios-trashumantes/{id}/archivar', [TrashumanciaController::class, 'archivar'])->name('apiarios-trashumantes.archivar');
    //Route::post('/apiarios/store-temporal')


    Route::resource('visita', VisitaController::class);
    Route::get('/visitas', [VisitaController::class, 'index'])->name('visitas.index');
    Route::get('visitas/create/{id}', [VisitaController::class, 'create'])->name('visitas.create');

    // Rutas para Registro de Inspección de Apiario
    Route::get('visitas/create/{id}', [VisitaController::class, 'create'])->name('visitas.create');
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

    Route::post('/apiarios/massDelete', [ApiarioController::class, 'massDelete'])->name('apiarios.massDelete');
});

// Redirigir a Google para autenticación
Route::get('login/google', function () {
    return Socialite::driver('google')->redirect();
});

//chatbot
//Route::resource('chatbot', ChatbotController::class);
Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
Route::get('/chatbot/messages', [ChatbotController::class, 'getMessages'])->name('chatbot.messages');
Route::get('/chatbot/consejos', [ChatbotController::class, 'generarConsejos'])->name('consejos');

// Callback después de la autenticación
Route::get('login/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();
    // Aquí puedes encontrar o crear un usuario en tu base de datos
    $user = User::where('email', $googleUser->getEmail())->first();
    if (!$user) {
        // Crea un nuevo usuario si no existe
        $user = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'password' => bcrypt(str_random(16)),
        ]);
    }
    // Autentica al usuario
    Auth::login($user);
    return redirect()->intended('home'); // Cambia 'dashboard' por la ruta a la que quieras redirigir al usuario
});

// Ruta de registro
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/home', [DashboardController::class, 'home'])->name('home');

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


// Historial de visitas de un apiario
Route::get('apiarios/{apiario}/visitas', [VisitaController::class, 'showHistorial'])->name('visitas.historial');

//Task
Route::delete('/tareas/{id}', [TaskController::class, 'destroy'])->name('tareas.destroy');
Route::get('/tareas', [TaskController::class, 'index'])->name('tareas');
Route::post('/tareas', [TaskController::class, 'store'])->name('tareas.store');
Route::post('/tareas/default', [TaskController::class, 'default'])->name('tareas.default');
Route::patch('/tareas/{id}', [TaskController::class, 'update'])->name('tareas.update');
Route::patch('/tareas/{id}/update-status', [TaskController::class, 'updateStatus']);
Route::get('/tareas/{id}', [TaskController::class, 'show'])->name('tareas.show');
Route::post('/tareas/update/{id}', [TaskController::class, 'guardarCambios']);
Route::patch('/tareas/{id}/update', [TaskController::class, 'updateTarea']);
Route::get('/todas-las-tareas/imprimir', [TaskController::class, 'imprimirTodas'])->name('tareas.imprimirTodas');

// 1) Pantalla del calendario
Route::get('/tareas/calendario', [TaskController::class, 'calendario'])->name('tareas.calendario');
// 2) JSON de eventos para FullCalendar
Route::get('/tareas/json', [TaskController::class, 'obtenerEventosJson'])->name('tareas.json');



// Visitas
Route::get('/visitas', [VisitaController::class, 'index'])->name('visitas');
Route::post('apiarios/{apiario}/visitas', [VisitaController::class, 'store'])->name('visitas.store');
Route::post('apiarios/{apiario}/visitas1', [VisitaController::class, 'store1'])->name('visitas.store1');

// Grupo de rutas protegidas por el middleware de autenticación y verificación de pago
//Route::middleware(['auth', 'check.payment'])->group(function () {});
Route::get('/zonificacion', [ZonificacionController::class, 'index'])->name('zonificacion');
//Usuarios
Route::middleware(['auth'])->group(function () {
    Route::get('/user/settings', [UserController::class, 'settings'])->name('user.settings');
    Route::patch('/user/update-name', [UserController::class, 'updateName'])->name('user.update.name');
    //Route::patch('/user/update-avatar', [UserController::class, 'updateAvatar'])->name('user.update.avatar');
    Route::post('/user/avatar', [UserController::class, 'updateAvatar'])
        ->name('user.updateAvatar')
        ->middleware('auth');
    Route::patch('/user/update-password', [UserController::class, 'updatePassword'])->name('user.update.password');
    Route::post('/user/update-settings', [UserController::class, 'updateSettings'])->name('user.updateSettings');
    Route::post('/user/update-plan', [UserController::class, 'updatePlan'])->name('user.updatePlan');
    // FACTURACION
    Route::patch('/user/settings/invoice-settings', [UserController::class, 'updateInvoiceSettings'])->name('user.update.invoiceSettings');
    // permisos
    Route::get('/user/settings/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
    Route::post('/user/settings/permissions', [PermissionsController::class, 'update'])->name('permissions.update');
    Route::post('/user/settings/permissions/reset', [PermissionsController::class, 'reset'])->name('permissions.reset');
    // preferencias
    Route::get('/user/settings/preferences', [PreferencesController::class, 'index'])->name('preferences.index');
    Route::post('/user/settings/preferences', [PreferencesController::class, 'update'])->name('preferences.update');
    Route::post('/user/settings/preferences/reset', [PreferencesController::class, 'reset'])->name('preferences.reset');

    // formato flobal de fechas test
    Route::get('/user/settings/preferences/demo', [PreferencesController::class, 'dateFormatDemo'])
        ->name('preferences.demo')
        ->middleware('auth');

    Route::post('/user/settings/preferences/date-format', [PreferencesController::class, 'updateDateFormat'])
        ->name('preferences.updateDateFormat')
        ->middleware('auth');

    Route::get('/debug-date', function () {
        return [
            'auth_user' => Auth::check() ? Auth::user()->email : 'no user',
            'date_format' => config('app.date_format'),
            'from_model' => optional(Auth::user()->preference)->date_format,
        ];
    });


    // test cambio idioma
    Route::get('/test-lang', function () {
        return view('apiarios.test-lang');
    })->middleware(['auth']);



    // UTILIDADES
    // utilidades - alertas
    Route::get('/user/settings/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/user/settings/alerts', [AlertController::class, 'store'])->name('alerts.store');
    Route::put('/user/settings/alerts/{alert}', [AlertController::class, 'update'])->name('alerts.update');
    Route::delete('/user/settings/alerts/{alert}', [AlertController::class, 'destroy'])->name('alerts.destroy');
    // utilidades - recordatorios
    Route::get('/user/settings/reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::post('/user/settings/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::put('/user/settings/reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
    Route::delete('/user/settings/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');
    // utilidades - fechas importantes
    Route::get('/user/settings/dates', [ImportantDateController::class, 'index'])->name('dates.index');
    Route::post('/user/settings/dates', [ImportantDateController::class, 'store'])->name('dates.store');
    Route::put('/user/settings/dates/{date}', [ImportantDateController::class, 'update'])->name('dates.update');
    Route::delete('/user/settings/dates/{date}', [ImportantDateController::class, 'destroy'])->name('dates.destroy');
    // utilidades - contactos de emergencia
    Route::get('/user/settings/contacts', [EmergencyContactController::class, 'index'])->name('contacts.index');
    Route::post('/user/settings/contacts', [EmergencyContactController::class, 'store'])->name('contacts.store');
    Route::put('/user/settings/contacts/{contact}', [EmergencyContactController::class, 'update'])->name('contacts.update');
    Route::delete('/user/settings/contacts/{contact}', [EmergencyContactController::class, 'destroy'])->name('contacts.destroy');
});
//(estas rutas estaban tambien dentro de un midleware de pago)
// La ruta de sistema experto que lista los apiarios
Route::get('/sistemaexperto', [App\Http\Controllers\ApiarioController::class, 'indexSistemaExperto'])->name('sistemaexperto');
// Formulario para registrar PCC (puede aceptar el id del apiario)
Route::get('/sistemaexperto/{apiario}/create', [ChatbotController::class, 'sistemaExpertoCreate'])->name('sistemaexperto.create');
// Guardar PCC
Route::post('/sistemaexperto/guardar', [App\Http\Controllers\VisitaController::class, 'storeSistemaExperto'])->name('sistemaexperto.store');
// AJAX individual: obtener consejo por apiario (simulado)
Route::get('/apiarios/{apiario}/obtener-consejo', [App\Http\Controllers\ApiarioController::class, 'obtenerConsejo']);

// Documentos y Emails
Route::get('/generate-document/{id}', [DocumentController::class, 'generateDocument'])->name('generate.document');
Route::get('/send-email', [MailController::class, 'sendEmail']);

// Pagos
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/response', [PaymentController::class, 'paymentResponse'])->name('payment.response');
});

// Ruta para la página de pago exitoso
Route::get('/payment/success', function () {
    return view('payment.success');
})->name('payment.success');

// Ruta para la página de pago fallido
Route::get('/payment/failed', function () {
    return view('payment.failed');
})->name('payment.failed');

Route::get('/payment/required', function () {
    return view('payment.required');
})->name('payment.required');
