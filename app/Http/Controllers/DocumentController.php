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
        $comuna = $apiario->comuna;
        return [
            'apiary_name' => $apiario->nombre,
            'apiary_number' => '#00' . $apiario->id,
            'activity' => $apiario->objetivo_produccion ?? $apiario->actividad ?? '',
            'installation_date' => $apiario->temporada_produccion ?? '',
            'utm_x'             => optional($comuna)->utm_x    ?? '',
            'utm_y'             => optional($comuna)->utm_y    ?? '',
            'utm_huso'          => optional($comuna)->utm_huso ?? '',
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
          
        $beekeeperData = $this->getBeekeeperData();
        $apiaryData    = $this->getApiaryData($apiario);

        $data = array_merge(
            $beekeeperData,
            $apiaryData,
            ['visits' => $visitas]
        );
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
}