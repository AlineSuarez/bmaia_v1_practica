<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Este script eliminará TODOS los eventos de Google Calendar del usuario.\n";
echo "Usuario ID: ";
$userId = trim(fgets(STDIN));

$user = DB::table('users')->find($userId);

if (!$user) {
    echo "❌ Usuario no encontrado.\n";
    exit(1);
}

echo "Usuario: {$user->name} ({$user->email})\n";

if (empty($user->google_calendar_token)) {
    echo "❌ No tiene token de Google Calendar.\n";
    exit(1);
}

echo "¿Desea eliminar TODOS los eventos del calendario? (si/no): ";
$confirmacion = trim(fgets(STDIN));

if (strtolower($confirmacion) !== 'si') {
    echo "❌ Operación cancelada.\n";
    exit(0);
}

try {
    $client = new Google_Client();
    $client->setClientId(config('services.google.client_id'));
    $client->setClientSecret(config('services.google.client_secret'));
    $client->setAccessToken($user->google_calendar_token);

    // Verificar token
    if ($client->isAccessTokenExpired()) {
        if ($user->google_calendar_refresh_token) {
            $client->fetchAccessTokenWithRefreshToken($user->google_calendar_refresh_token);
            $newToken = $client->getAccessToken();
            DB::table('users')->where('id', $userId)->update([
                'google_calendar_token' => $newToken['access_token'],
            ]);
        } else {
            echo "❌ Token expirado y no hay refresh token.\n";
            exit(1);
        }
    }

    $service = new Google_Service_Calendar($client);
    
    // Obtener todos los eventos
    $events = $service->events->listEvents('primary', [
        'maxResults' => 250,
        'orderBy' => 'startTime',
        'singleEvents' => true,
    ]);

    $total = count($events->getItems());
    echo "\nEncontrados {$total} eventos.\n";
    
    if ($total === 0) {
        echo "✓ No hay eventos para eliminar.\n";
        exit(0);
    }

    $eliminados = 0;
    foreach ($events->getItems() as $event) {
        try {
            $service->events->delete('primary', $event->getId());
            $eliminados++;
            echo "✓ Eliminado: {$event->getSummary()}\n";
        } catch (Exception $e) {
            echo "✗ Error eliminando {$event->getSummary()}: {$e->getMessage()}\n";
        }
    }

    echo "\n✓ Eliminados {$eliminados} de {$total} eventos.\n";

} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    exit(1);
}
