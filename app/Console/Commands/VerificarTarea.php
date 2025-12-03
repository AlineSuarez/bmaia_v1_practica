<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubTarea;

class VerificarTarea extends Command
{
    protected $signature = 'tareas:verificar {fecha?}';
    protected $description = 'Verifica prioridad y prioridad_base de tareas';

    public function handle()
    {
        $fecha = $this->argument('fecha') ?? '2025-01-27';
        
        $tareas = SubTarea::where('fecha_inicio', 'like', $fecha . '%')->get();
        
        if ($tareas->isEmpty()) {
            $this->error("No se encontraron tareas con fecha de inicio: $fecha");
            return 1;
        }
        
        $this->info("Tareas encontradas con fecha de inicio: $fecha\n");
        
        foreach ($tareas as $tarea) {
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("ID: {$tarea->id}");
            $this->line("Nombre: {$tarea->nombre}");
            $this->line("Prioridad: {$tarea->prioridad}");
            $this->line("Prioridad Base: " . ($tarea->prioridad_base ?? 'NULL'));
            $this->line("Estado: {$tarea->estado}");
            $this->line("Fecha Inicio: {$tarea->fecha_inicio}");
            $this->line("Fecha Límite: {$tarea->fecha_limite}");
            $this->newLine();
        }
        
        return 0;
    }
}
