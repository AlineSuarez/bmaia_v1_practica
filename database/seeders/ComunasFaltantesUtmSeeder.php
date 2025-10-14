<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comuna;
use App\Models\Region;

class ComunasFaltantesUtmSeeder extends Seeder
{
    public function run()
    {
        // 🧹 Primero eliminamos las comunas duplicadas o incorrectas
        $aEliminar = [
            ['nombre' => 'Marchigüe', 'region' => "Región de O’Higgins"],
            ['nombre' => 'Saavedra', 'region' => "Región de La Araucanía"],
        ];

        foreach ($aEliminar as $item) {
            $region = Region::where('nombre', $item['region'])->first();

            if (!$region) {
                $this->command->warn("⚠️ Región no encontrada al eliminar: {$item['region']}");
                continue;
            }

            $comuna = Comuna::where('nombre', $item['nombre'])
                ->where('region_id', $region->id)
                ->first();

            if ($comuna) {
                $comuna->delete();
                $this->command->info("🗑️ Comuna eliminada: {$item['nombre']} ({$item['region']})");
            } else {
                $this->command->line("✔️ No se encontró para eliminar: {$item['nombre']} ({$item['region']})");
            }
        }

        // 🧭 Ahora insertamos o actualizamos las comunas faltantes
        $faltantes = [
            // Región de Valparaíso
            ['nombre' => 'Juan Fernández', 'region' => 'Región de Valparaíso', 'lat' => -33.6250, 'lon' => -78.8333, 'utm_x' => 210000.0, 'utm_y' => 6278000.0, 'utm_huso' => 12],
            ['nombre' => 'Puchuncaví', 'region' => 'Región de Valparaíso', 'lat' => -32.7276, 'lon' => -71.4148, 'utm_x' => 273200.0, 'utm_y' => 6375500.0, 'utm_huso' => 19],
            ['nombre' => 'Quintero', 'region' => 'Región de Valparaíso', 'lat' => -32.7772, 'lon' => -71.5275, 'utm_x' => 265100.0, 'utm_y' => 6370500.0, 'utm_huso' => 19],
            ['nombre' => 'Limache', 'region' => 'Región de Valparaíso', 'lat' => -33.0167, 'lon' => -71.2667, 'utm_x' => 284000.0, 'utm_y' => 6342500.0, 'utm_huso' => 19],
            ['nombre' => 'Olmué', 'region' => 'Región de Valparaíso', 'lat' => -32.9950, 'lon' => -71.1780, 'utm_x' => 290700.0, 'utm_y' => 6344500.0, 'utm_huso' => 19],
            ['nombre' => 'Isla de Pascua', 'region' => 'Región de Valparaíso', 'lat' => -27.1127, 'lon' => -109.3497, 'utm_x' => 781500.0, 'utm_y' => 6997000.0, 'utm_huso' => 14],

            // Región Metropolitana
            ['nombre' => 'Pirque', 'region' => 'Región Metropolitana de Santiago', 'lat' => -33.6333, 'lon' => -70.5500, 'utm_x' => 346800.0, 'utm_y' => 6275500.0, 'utm_huso' => 19],
            ['nombre' => 'San José de Maipo', 'region' => 'Región Metropolitana de Santiago', 'lat' => -33.6500, 'lon' => -70.3500, 'utm_x' => 362700.0, 'utm_y' => 6274000.0, 'utm_huso' => 19],
            ['nombre' => 'Quinta Normal', 'region' => 'Región Metropolitana de Santiago', 'lat' => -33.4333, 'lon' => -70.6833, 'utm_x' => 333200.0, 'utm_y' => 6297000.0, 'utm_huso' => 19],

            // Región de O’Higgins
            ['nombre' => 'Olivar', 'region' => "Región de O’Higgins", 'lat' => -34.2167, 'lon' => -70.7833, 'utm_x' => 325500.0, 'utm_y' => 6214000.0, 'utm_huso' => 19],
            ['nombre' => 'Paredones', 'region' => "Región de O’Higgins", 'lat' => -34.6333, 'lon' => -71.9667, 'utm_x' => 238600.0, 'utm_y' => 6167000.0, 'utm_huso' => 19],
            ['nombre' => 'Quinta de Tilcoco', 'region' => "Región de O’Higgins", 'lat' => -34.3667, 'lon' => -70.9667, 'utm_x' => 312400.0, 'utm_y' => 6193500.0, 'utm_huso' => 19],
            ['nombre' => 'Chépica', 'region' => "Región de O’Higgins", 'lat' => -34.7167, 'lon' => -71.2833, 'utm_x' => 289400.0, 'utm_y' => 6162000.0, 'utm_huso' => 19],

            // Región del Maule
            ['nombre' => 'Empedrado', 'region' => 'Región del Maule', 'lat' => -35.6000, 'lon' => -72.2833, 'utm_x' => 213400.0, 'utm_y' => 6061000.0, 'utm_huso' => 18],
            ['nombre' => 'Rauco', 'region' => 'Región del Maule', 'lat' => -34.9667, 'lon' => -71.2667, 'utm_x' => 290300.0, 'utm_y' => 6140000.0, 'utm_huso' => 19],
            ['nombre' => 'San Javier', 'region' => 'Región del Maule', 'lat' => -35.5931, 'lon' => -71.7456, 'utm_x' => 254500.0, 'utm_y' => 6062000.0, 'utm_huso' => 19],

            // Región de Ñuble
            ['nombre' => 'Coelemu', 'region' => 'Región de Ñuble', 'lat' => -36.4833, 'lon' => -72.7000, 'utm_x' => 186000.0, 'utm_y' => 5959000.0, 'utm_huso' => 18],

            // Región del Biobío
            ['nombre' => 'Alto Biobío', 'region' => 'Región del Biobío', 'lat' => -38.1333, 'lon' => -71.3333, 'utm_x' => 294500.0, 'utm_y' => 5776000.0, 'utm_huso' => 19],

            // Región de La Araucanía
            ['nombre' => 'Curacautín', 'region' => 'Región de La Araucanía', 'lat' => -38.4333, 'lon' => -71.8833, 'utm_x' => 252900.0, 'utm_y' => 5740000.0, 'utm_huso' => 19],
            ['nombre' => 'Lonquimay', 'region' => 'Región de La Araucanía', 'lat' => -38.4500, 'lon' => -71.2333, 'utm_x' => 301000.0, 'utm_y' => 5739000.0, 'utm_huso' => 19],

            // Región de Los Lagos
            ['nombre' => 'Cochamó', 'region' => 'Región de Los Lagos', 'lat' => -41.5000, 'lon' => -72.3167, 'utm_x' => 227800.0, 'utm_y' => 5408000.0, 'utm_huso' => 18],
            ['nombre' => 'Puerto Octay', 'region' => 'Región de Los Lagos', 'lat' => -40.9667, 'lon' => -72.8833, 'utm_x' => 184000.0, 'utm_y' => 5469000.0, 'utm_huso' => 18],
            ['nombre' => 'Puyehue', 'region' => 'Región de Los Lagos', 'lat' => -40.6833, 'lon' => -72.6167, 'utm_x' => 204500.0, 'utm_y' => 5502000.0, 'utm_huso' => 18],
            ['nombre' => 'Chaitén', 'region' => 'Región de Los Lagos', 'lat' => -42.9178, 'lon' => -72.7133, 'utm_x' => 190200.0, 'utm_y' => 5245000.0, 'utm_huso' => 18],
            ['nombre' => 'Hualaihué', 'region' => 'Región de Los Lagos', 'lat' => -42.0167, 'lon' => -72.6833, 'utm_x' => 192000.0, 'utm_y' => 5342000.0, 'utm_huso' => 18],
            ['nombre' => 'Futaleufú', 'region' => 'Región de Los Lagos', 'lat' => -43.1833, 'lon' => -71.8500, 'utm_x' => 255300.0, 'utm_y' => 5210000.0, 'utm_huso' => 19],
            ['nombre' => 'Palena', 'region' => 'Región de Los Lagos', 'lat' => -43.6167, 'lon' => -71.8000, 'utm_x' => 258500.0, 'utm_y' => 5162000.0, 'utm_huso' => 19],
        ];

        foreach ($faltantes as $data) {
            $region = Region::where('nombre', $data['region'])->first();

            if (!$region) {
                $this->command->warn("⚠️ Región no encontrada: {$data['region']}");
                continue;
            }

            // Validamos si la comuna ya existe para no crearla de nuevo
            $existingComuna = Comuna::where('nombre', $data['nombre'])
                ->where('region_id', $region->id)
                ->first();

            if ($existingComuna) {
                $this->command->line("✔️ Comuna ya existe: {$data['nombre']} ({$data['region']})");
                continue; // Salta la inserción si ya existe
            }

            // Si no existe, creamos la comuna
            $comuna = new Comuna();
            $comuna->nombre = $data['nombre'];
            $comuna->region_id = $region->id;
            $comuna->lat = $data['lat'];
            $comuna->lon = $data['lon'];
            $comuna->utm_x = $data['utm_x'];
            $comuna->utm_y = $data['utm_y'];
            $comuna->utm_huso = $data['utm_huso'];
            $comuna->save();

            $this->command->info("✅ Comuna creada: {$data['nombre']} ({$data['region']}) con coordenadas.");
        }

        $this->command->info("🎉 Comunas actualizadas/insertadas correctamente.");
    }
}
