<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\User;
use App\Models\Task;
use App\Models\SubTarea;
use App\Models\Visita;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    // Metodo para obtener los datos del apicultor
    private function getBeekeeperData()
    {
        $user = auth()->user(); 

        return [
            'legal_representative' => $user->name,
            'last_name' => $user->last_name ?? '',
            'registration_number' => $user->numero_registro ?? '',
            'email' => $user->email,
            'rut' => $user->rut ?? '',
            'phone' => $user->telefono ?? '',
            'address' => $user->direccion ?? '',
            'region' => $user->region->nombre ?? '',
            'commune' => $user->comuna->nombre ?? '',
            'firma' => $user->firma ?? '',
        ];
    }

    // Metodo para obtener los datos del apiario
    private function getApiaryData(Apiario $apiario)
    {
        return [
            'apiary_name' => $apiario->nombre,
            'apiary_number' => '#00' . $apiario->id,
            'activity' => $apiario->objetivo_produccion ?? $apiario->actividad ?? '',
            'installation_date' => $apiario->fecha_instalacion ? $apiario->fecha_instalacion->format('Y-m-d') : '',
            'utm_x' => $apiario->utm_x ?? '',
            'utm_y' => $apiario->utm_y ?? '',
            'utm_huso' => $apiario->utm_huso ?? '19',
            'latitude' => $apiario->latitud ?? '',
            'longitude' => $apiario->longitud ?? '',
            'nomadic' => $apiario->trashumante ? 'Sí' : 'No',
            'hive_count' => $apiario->num_colmenas ?? '',
        ];
    }


    // Generar el documento PDF incluyendo todas las visitas
    public function generateDocument($id)
    {
        $user = Auth::user();
        $apiario = Apiario::findOrFail($id);
        // Obtener las visitas asociadas al apiario con el ID dado
        $visitas = Visita::whereHas('apiario', function ($query) use ($id) {
            $query->where('id', $id);
        })->get();
          // Coordenadas de ejemplo
                $latitude = $apiario->latitud;
                $longitude = $apiario->longitud;

                /*
                // Inicializar Proj4php
                $proj4 = new Proj4php();

                // Definir sistemas de coordenadas
                $projWGS84 = new Proj('EPSG:4326', $proj4); // Sistema WGS84
                $projUTM = new Proj('EPSG:32719', $proj4); // Sistema UTM Zona 19 Sur (para Chile)

                // Convertir coordenadas
                $pointSource = new Point($longitude, $latitude, $projWGS84); // Longitud, Latitud
                $pointDest = $proj4->transform($projUTM, $pointSource);

                // Extraer coordenadas UTM
                $utm_x = $pointDest->x;
                $utm_y = $pointDest->y;
                $utm_huso = 19; // Huso UTM según longitud (Chile está en 19S o 18S)
                */
        $data = [
            'last_name' => $user->last_name,
            'nombre_usuario' => $user->name,
            'address' => $user->dirección,
            'rut' => $user->rut,
            'phone' => $user->telefono,
            'region' => $user->region ? $user->region->nombre : 'No especificada',
            'commune' => $user->comuna ? $user->comuna->nombre : 'No especificada',
            'email' => $user->email,
            'legal_representative' => $user->representante_legal,
            'registration_number' => $user->numero_registro,
            'apiary_name' => $apiario->nombre,
            'apiary_number' => '#00'.$apiario->id,
            'activity' => $apiario->objetivo_produccion,
            'installation_date' => $apiario->temporada_produccion,
            'utm_x' => '-', // o null
            'utm_y' => '-',
            'utm_huso' => '-',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'nomadic' => 'No',
            'hive_count' => $apiario->num_colmenas,
            'visits' => $visitas,
            'foto' => $apiario->foto,
        ];
        // Renderizar la vista para el PDF
        $pdf = Pdf::loadView('documents.apiary-detail', compact('data'));
        // Descargar el PDF
        return $pdf->download('Detalles_Apiario_'.$apiario->nombre.'.pdf');
    }

    public function generateVisitasDocument($id)
    {
        $apiario = Apiario::findOrFail($id);
        $visitas = Visita::with('usuario')
            ->where('apiario_id', $id)
            ->where('tipo_visita', 'Visita General')
            ->get();

        $user = auth()->user();

        $beekeeperData = $this->getBeekeeperData();
        $apiaryData = $this->getApiaryData($apiario);

        $data = array_merge($beekeeperData, $apiaryData, ['visits' => $visitas]);

        $pdf = Pdf::loadView('documents.visit-record', compact('data'));
        return $pdf->download('Visitas_Apiario_'.$apiario->nombre.'.pdf');
    }

    public function generateInspeccionDocument($apiarioId)
    {
        $user = auth()->user();
        $apiario = Apiario::findOrFail($apiarioId);
        $inspecciones = Visita::where('apiario_id', $apiarioId)
        ->where('tipo_visita', 'Inspección de Visita')
        ->get();

        $beekeeperData = $this->getBeekeeperData();
        $apiaryData = $this->getApiaryData($apiario);

        $data = array_merge($beekeeperData, $apiaryData, ['visits' => $inspecciones]);

        $pdf = Pdf::loadView('documents.inspection-record', compact('data'));
    return $pdf->download('Inspecciones_Apiario_' . $apiario->numero . '.pdf');
    }

    public function generateMedicamentsDocument($apiarioId)
    {
        $apiario = Apiario::findOrFail($apiarioId);
        $medicamentos = Visita::where('apiario_id', $apiarioId)
            ->where('tipo_visita', 'Uso de Medicamentos')
            ->get();

        $beekeeperData = $this->getBeekeeperData();
        $apiaryData = $this->getApiaryData($apiario);

        $data = array_merge($beekeeperData, $apiaryData, ['visits' => $medicamentos]);

        $pdf = Pdf::loadView('documents.medicaments-record', compact('data'));
        return $pdf->download('Medicamentos_Apiario_' . $apiario->nombre . '.pdf');
    }


    private function getApiarioData($apiario)
    {
        $user = Auth::user();
        $visitas = Visita::where('apiario_id', $apiario->id)->get();

        $latitude = $apiario->latitud;
        $longitude = $apiario->longitud;

        /*
                $proj4 = new Proj4php();
        $projWGS84 = new Proj('EPSG:4326', $proj4);
        $projUTM = new Proj('EPSG:32719', $proj4);

        $pointSource = new Point($longitude, $latitude, $projWGS84);
        $pointDest = $proj4->transform($projUTM, $pointSource);

        $utm_x = $pointDest->x;
        $utm_y = $pointDest->y;
        $utm_huso = 19;

        */
        return [
            'last_name' => $user->last_name,
            'nombre_usuario' => $user->name,
            'address' => $user->dirección,
            'rut' => $user->rut,
            'phone' => $user->telefono,
            'region' => $user->region ? $user->region->nombre : 'No especificada',
            'commune' => $user->comuna ? $user->comuna->nombre : 'No especificada',
            'email' => $user->email,
            'legal_representative' => $user->representante_legal,
            'registration_number' => $user->numero_registro,
            'apiary_name' => $apiario->nombre,
            'apiary_number' => '#00'.$apiario->id,
            'activity' => $apiario->objetivo_produccion,
            'installation_date' => $apiario->temporada_produccion,
            'utm_x' => '-', // o null
            'utm_y' => '-',
            'utm_huso' => '-',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'nomadic' => 'No',
            'hive_count' => $apiario->num_colmenas,
            'visits' => $visitas,
        ];
    }
}