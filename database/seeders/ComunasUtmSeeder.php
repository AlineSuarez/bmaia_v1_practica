<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comuna;

class ComunasUtmSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('seeders/data/comunas-utm.json');

        if (!file_exists($path)) {
            $this->command->error("❌ No se encontró el archivo: {$path}");
            return;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            $this->command->error("❌ El JSON no es válido o no es un array.");
            return;
        }

        foreach ($data as $item) {
            if (!isset($item['id'], $item['utm_x'], $item['utm_y'], $item['utm_huso'])) {
                $this->command->warn("⚠️ Datos incompletos para comuna: " . json_encode($item));
                continue;
            }

            Comuna::where('id', $item['id'])
                ->update([
                    'utm_x'    => $item['utm_x'],
                    'utm_y'    => $item['utm_y'],
                    'utm_huso' => $item['utm_huso'],
                ]);

            $this->command->info("✅ Comuna ID {$item['id']} actualizada.");
        }

        $this->command->info("🎉 Seeder de UTM completado.");
    }
}
