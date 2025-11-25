<?php

namespace Database\Seeders;

use App\Models\Flor;
use App\Models\Fenofase;
use App\Models\FlorImagen;
use Illuminate\Database\Seeder;

class FlorImagenesSeeder extends Seeder
{
    public function run(): void
    {
        $fases = Fenofase::pluck('id','clave'); // ['boton'=>1, 'inicio'=>2, ...]

        // Helper para crear una imagen si el archivo existe
        $add = function(string $slug, string $faseClave, string $file, string $credito = null) use ($fases) {
            $flor = Flor::where('slug',$slug)->first();
            if (!$flor || !isset($fases[$faseClave])) return;

            $path = "flowers/{$slug}/{$faseClave}/{$file}"; // relative to storage/app/public
            // Ojo: no validamos existencia física aquí, pero puedes hacerlo si quieres.
            FlorImagen::firstOrCreate([
                'flor_id' => $flor->id,
                'fenofase_id' => $fases[$faseClave],
                'path' => $path,
            ],[
                'credito' => $credito,
                'es_principal' => true,
            ]);
        };

        // === Tevo ===
        $slug = 'tevo';
        $add($slug,'boton','tevo_boton.jpg','B-Maia');
        $add($slug,'inicio','tevo_inicio.jpg','B-Maia');
        $add($slug,'plena','tevo_plena.jpg','B-Maia');
        $add($slug,'terminal','tevo_terminal.jpg','B-Maia');

        // === Quillay (ejemplo) ===
        $slug = 'quillay';
        $add($slug,'boton','quillay_boton.jpg','B-Maia');
        $add($slug,'inicio','quillay_inicio.jpg','B-Maia');
        $add($slug,'plena','quillay_plena.jpg','B-Maia');
        $add($slug,'terminal','quillay_terminal.jpg','B-Maia');

        // Repite para las demás especies que quieras publicar.
    }
}
