<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tarea = DB::table('subtareas')
    ->where('nombre', 'like', '%Tarea Estado Vencida%')
    ->orWhere('nombre', 'like', '%Estado Vencida%')
    ->orWhere('estado', 'Vencida')
    ->get(['id', 'nombre', 'prioridad', 'estado', 'fecha_inicio', 'fecha_limite']);

if ($tarea->isEmpty()) {
    echo "No se encontró ninguna tarea con ese nombre o en estado Vencida.\n";
    echo "\nBuscando todas las tareas para referencia:\n";
    $todas = DB::table('subtareas')->orderBy('id', 'desc')->limit(10)->get(['id', 'nombre', 'prioridad', 'estado']);
    foreach ($todas as $t) {
        echo "ID: {$t->id} | Nombre: {$t->nombre} | Prioridad: {$t->prioridad} | Estado: {$t->estado}\n";
    }
} else {
    echo "Tareas encontradas:\n\n";
    foreach ($tarea as $t) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID: {$t->id}\n";
        echo "Nombre: {$t->nombre}\n";
        echo "Prioridad: {$t->prioridad}\n";
        echo "Estado: {$t->estado}\n";
        echo "Fecha inicio: {$t->fecha_inicio}\n";
        echo "Fecha límite: {$t->fecha_limite}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
