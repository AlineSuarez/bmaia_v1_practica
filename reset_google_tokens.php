<?php
// Script para limpiar tokens de Google Calendar de un usuario
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$userId = $argv[1] ?? null;

if (!$userId) {
    echo "Uso: php reset_google_tokens.php USER_ID\n";
    exit(1);
}

$user = \App\Models\User::find($userId);

if (!$user) {
    echo "Error: Usuario {$userId} no encontrado\n";
    exit(1);
}

echo "Usuario: {$user->name} ({$user->email})\n";
echo "¿Desea limpiar los tokens de Google Calendar? (si/no): ";
$handle = fopen ("php://stdin","r");
$line = trim(fgets($handle));

if ($line !== 'si' && $line !== 'yes') {
    echo "Operación cancelada\n";
    exit(0);
}

$user->update([
    'google_calendar_token' => null,
    'google_calendar_refresh_token' => null,
    'google_calendar_token_expires_at' => null,
    'google_calendar_synced' => false,
]);

echo "\n✓ Tokens limpiados exitosamente\n";
echo "\nAhora sigue estos pasos:\n";
echo "1. Ve a la agenda en tu navegador\n";
echo "2. Haz clic en 'Conectar con Google Calendar'\n";
echo "3. Confirma la autorización\n";
echo "4. Google te pedirá permisos nuevamente\n";
echo "5. Autoriza y las tareas se sincronizarán automáticamente\n";
