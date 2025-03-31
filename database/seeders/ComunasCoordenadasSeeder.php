<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comuna;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComunasCoordenadasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ruta al archivo JSON con los datos de las comunas
        $jsonFilePath = database_path('seeders/data/tabla_comunas.json');

        // Verificar si el archivo JSON existe
        if (!file_exists($jsonFilePath)) {
            Log::error("El archivo JSON no se encontró en: " . $jsonFilePath);
            echo "Error: El archivo JSON no se encontró.\n";
            return; // Detener la ejecución del seeder si el archivo no existe
        }

        // Leer el contenido del archivo JSON
        $jsonContent = file_get_contents($jsonFilePath);

        // Decodificar el contenido JSON a un array asociativo
        $comunasData = json_decode($jsonContent, true);

        // Verificar si la decodificación fue exitosa
        if (!is_array($comunasData)) {
            Log::error("Error al decodificar el archivo JSON.");
            echo "Error: No se pudo decodificar el archivo JSON.\n";
            return; // Detener la ejecución del seeder si la decodificación falla
        }

        // Iniciar una transacción de base de datos para mejorar el rendimiento
        DB::beginTransaction();

        try {
            // Iterar sobre cada comuna en el array
            foreach ($comunasData as $comunaData) {
                // Actualizar o crear la comuna en la base de datos
                Comuna::updateOrCreate(
                    ['id' => $comunaData['id']],
                    [
                        'nombre' => $comunaData['nombre'],
                        'region_id' => $comunaData['region_id'],
                        'lat' => $comunaData['lat'],
                        'lon' => $comunaData['lon'],
                        'created_at' => $comunaData['created_at'],
                        'updated_at' => $comunaData['updated_at'],
                    ]
                );
            }

            // Confirmar la transacción si todo se ejecuta correctamente
            DB::commit();

            echo "Coordenadas de comunas importadas correctamente.\n";
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            Log::error("Error al importar coordenadas de comunas: " . $e->getMessage());
            echo "Error: Ocurrió un error durante la importación.\n";
        }
    }
}