<?php
// Script temporal para listar usuarios
// se puede usar tinker o línea de comandos
// pero esto es más para tener un vistazo rápido y comodo de leer
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = \App\Models\User::select('id', 'email', 'name', 'google_calendar_token', 'google_calendar_synced')->get();

echo "=== USUARIOS EN LA BASE DE DATOS ===\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Nombre: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Token Google: " . ($user->google_calendar_token ? 'SÍ ✓' : 'NO ✗') . "\n";
    echo "Sincronizado: " . ($user->google_calendar_synced ? 'SÍ ✓' : 'NO ✗') . "\n";
    echo "---\n\n";
}

echo "Total usuarios: " . $users->count() . "\n";
