<?php

namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Visita;
use App\Models\Colmena;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SistemaExperto;
use App\Models\CalidadReina;
use App\Models\Comuna;
use App\Models\Region;

class DocumentController extends Controller
{
    // Verificar si GD está disponible
    private function isGdAvailable()
    {
        return extension_loaded('gd') && function_exists('imagecreatefrompng');
    }

    // Método principal para convertir imagen a base64 usando GD
    private function convertImageToBase64($imagePath)
    {
        if (!$imagePath || !file_exists($imagePath)) {
            return null;
        }

        try {
            if (!$this->isGdAvailable()) {
                \Log::info('GD no disponible, usando método básico para imágenes.');
                return $this->convertImageBasic($imagePath);
            }

            $imageInfo = getimagesize($imagePath);
            if ($imageInfo === false) {
                return null;
            }

            $mimeType = $imageInfo['mime'];

            // Procesar según el tipo de imagen con GD
            switch ($mimeType) {
                case 'image/png':
                    return $this->processPngWithGd($imagePath);

                case 'image/jpeg':
                case 'image/jpg':
                    return $this->processJpegWithGd($imagePath);

                case 'image/gif':
                    return $this->processGifWithGd($imagePath);

                case 'image/webp':
                    return $this->processWebpWithGd($imagePath);

                default:
                    \Log::warning("Formato de imagen no soportado: {$mimeType} en {$imagePath}");
                    return null;
            }

        } catch (\Exception $e) {
            \Log::error('Error converting image to base64: ' . $e->getMessage());
            // Fallback al método básico si GD falla
            return $this->convertImageBasic($imagePath);
        }
    }

    // Método básico sin GD (FALLBACK)
    private function convertImageBasic($imagePath)
    {
        try {
            $imageInfo = getimagesize($imagePath);
            if ($imageInfo === false) {
                return null;
            }

            $mimeType = $imageInfo['mime'];

            // Solo procesar formatos compatibles con dompdf sin GD
            $compatibleFormats = [
                'image/jpeg' => 'image/jpeg',
                'image/jpg' => 'image/jpeg',
                'image/gif' => 'image/gif'
            ];

            if (array_key_exists($mimeType, $compatibleFormats)) {
                $imageData = file_get_contents($imagePath);
                return 'data:' . $compatibleFormats[$mimeType] . ';base64,' . base64_encode($imageData);
            }

            if ($mimeType === 'image/png') {
                \Log::info("PNG detectado pero GD no disponible. Imagen omitida: {$imagePath}");
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('Error en método básico de conversión: ' . $e->getMessage());
            return null;
        }
    }

    // Procesar PNG con GD (convertir a JPEG)
    private function processPngWithGd($imagePath)
    {
        try {
            // Crear imagen desde PNG
            $sourceImage = imagecreatefrompng($imagePath);
            if (!$sourceImage) {
                \Log::error("No se pudo crear imagen desde PNG: {$imagePath}");
                return null;
            }

            // Obtener dimensiones
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);

            // Crear nueva imagen con fondo blanco
            $jpegImage = imagecreatetruecolor($width, $height);

            // Crear color blanco para el fondo
            $white = imagecolorallocate($jpegImage, 255, 255, 255);
            imagefill($jpegImage, 0, 0, $white);

            // Copiar la imagen PNG sobre el fondo blanco
            imagecopy($jpegImage, $sourceImage, 0, 0, 0, 0, $width, $height);

            // Capturar la imagen JPEG en memoria
            ob_start();
            imagejpeg($jpegImage, null, 85); // 85% de calidad
            $jpegData = ob_get_contents();
            ob_end_clean();

            // Liberar memoria
            imagedestroy($sourceImage);
            imagedestroy($jpegImage);

            return 'data:image/jpeg;base64,' . base64_encode($jpegData);

        } catch (\Exception $e) {
            \Log::error('Error procesando PNG con GD: ' . $e->getMessage());
            return null;
        }
    }

    // Procesar JPEG con GD (optimizar)
    private function processJpegWithGd($imagePath)
    {
        try {
            // Para JPEG, podemos optimizar o simplemente usar el original
            $sourceImage = imagecreatefromjpeg($imagePath);
            if (!$sourceImage) {
                // Si falla con GD, usar el archivo original
                $imageData = file_get_contents($imagePath);
                return 'data:image/jpeg;base64,' . base64_encode($imageData);
            }

            // Optimizar la imagen
            ob_start();
            imagejpeg($sourceImage, null, 85); // 85% de calidad
            $jpegData = ob_get_contents();
            ob_end_clean();

            imagedestroy($sourceImage);

            return 'data:image/jpeg;base64,' . base64_encode($jpegData);

        } catch (\Exception $e) {
            \Log::error('Error procesando JPEG con GD: ' . $e->getMessage());

            // Fallback: usar archivo original
            try {
                $imageData = file_get_contents($imagePath);
                return 'data:image/jpeg;base64,' . base64_encode($imageData);
            } catch (\Exception $fallbackError) {
                return null;
            }
        }
    }

    // Procesar GIF con GD
    private function processGifWithGd($imagePath)
    {
        try {
            // Para GIF, usar el archivo original ya que GD puede perder animación
            $imageData = file_get_contents($imagePath);
            return 'data:image/gif;base64,' . base64_encode($imageData);

        } catch (\Exception $e) {
            \Log::error('Error procesando GIF: ' . $e->getMessage());
            return null;
        }
    }

    // Procesar WebP con GD (convertir a JPEG)
    private function processWebpWithGd($imagePath)
    {
        try {
            // Verificar si GD soporta WebP
            if (!function_exists('imagecreatefromwebp')) {
                \Log::warning('GD no soporta WebP en este servidor');
                return null;
            }

            $sourceImage = imagecreatefromwebp($imagePath);
            if (!$sourceImage) {
                \Log::error("No se pudo crear imagen desde WebP: {$imagePath}");
                return null;
            }

            // Convertir WebP a JPEG
            ob_start();
            imagejpeg($sourceImage, null, 85);
            $jpegData = ob_get_contents();
            ob_end_clean();

            imagedestroy($sourceImage);

            return 'data:image/jpeg;base64,' . base64_encode($jpegData);

        } catch (\Exception $e) {
            \Log::error('Error procesando WebP con GD: ' . $e->getMessage());
            return null;
        }
    }

    // Método principal para preparar imágenes para PDF
    private function prepareImageForPdf($imagePath)
    {
        if (!$imagePath || !file_exists($imagePath)) {
            return null;
        }

        try {
            return $this->convertImageToBase64($imagePath);

        } catch (\Exception $e) {
            \Log::error('Error preparando imagen para PDF: ' . $e->getMessage());
            return null;
        }
    }

    // Metodo para obtener los datos del apicultor
    private function getBeekeeperData()
    {
        $user = auth()->user();

        // Convertir firma a base64 (con o sin GD)
        $firmaBase64 = null;
        if ($user->firma) {
            $firmaPath = storage_path('app/public/firmas/' . $user->firma);
            $firmaBase64 = $this->prepareImageForPdf($firmaPath);
        }

        return [
            'legal_representative' => $user->name,
            'last_name' => $user->last_name ?? '',
            'registration_number' => $user->numero_registro ?? '',
            'email' => $user->email,
            'rut' => $user->rut ?? '',
            'phone' => $user->telefono ?? '',
            'address' => $user->direccion ?? '',
            'region' => optional($user->region)->nombre ?? '',
            'commune' => optional($user->comuna)->nombre ?? '',
            'firma' => $user->firma ?? '',
            'firma_base64' => $firmaBase64,
        ];
    }

    // Metodo para obtener los datos del apiario
    private function getApiaryData(Apiario $apiario)
    {
        $comuna = $apiario->comuna;

        // Convertir foto del apiario a base64 (con o sin GD)
        $fotoBase64 = null;
        if ($apiario->foto) {
            $fotoPath = storage_path('app/public/' . $apiario->foto);
            $fotoBase64 = $this->prepareImageForPdf($fotoPath);
        }

        return [
            'apiary_name' => $apiario->nombre,
            'apiary_number' => '#00' . $apiario->id,
            'activity' => $apiario->objetivo_produccion ?? $apiario->actividad ?? '',
            'installation_date' => $apiario->temporada_produccion ?? '',
            'utm_x' => optional($comuna)->utm_x ?? '',
            'utm_y' => optional($comuna)->utm_y ?? '',
            'utm_huso' => optional($comuna)->utm_huso ?? '',
            'latitude' => $apiario->latitud ?? '',
            'longitude' => $apiario->longitud ?? '',
            'nomadic' => $apiario->trashumante ? 'Sí' : 'No',
            'hive_count' => $apiario->num_colmenas ?? '',
            'foto' => $apiario->foto ?? '',
            'foto_base64' => $fotoBase64,
        ];
    }

    // Generar el documento PDF (SIN verificación obligatoria de GD)
    public function generateDocument($id)
    {
        try {
            $user = Auth::user();
            $apiario = Apiario::with(['comuna.region'])->findOrFail($id);

            $visitas = Visita::whereHas('apiario', function ($query) use ($id) {
                $query->where('id', $id);
            })->get();

            $beekeeperData = $this->getBeekeeperData();
            $apiaryData = $this->getApiaryData($apiario);

            $data = array_merge(
                $beekeeperData,
                $apiaryData,
                ['visits' => $visitas]
            );

            // Log para debugging
            if (!$this->isGdAvailable()) {
                \Log::info('PDF generado sin GD. Algunas imágenes pueden no mostrarse correctamente.');
            }

            // Configurar opciones de dompdf
            $pdf = Pdf::loadView('documents.apiary-detail', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'enable_remote' => false,
                'enable_css_float' => false,
                'enable_javascript' => false,
                'debugKeepTemp' => false,
            ]);

            return $pdf->download('Detalles_Apiario_' . $apiario->nombre . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generando documento: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el documento: ' . $e->getMessage());
        }
    }

    // Resto de métodos SIN verificación obligatoria de GD
    public function generateVisitasDocument($id)
    {
        try {
            $apiario = Apiario::with(['comuna.region'])->findOrFail($id);
            $visitas = Visita::with('usuario')
                ->where('apiario_id', $id)
                ->where('tipo_visita', 'Visita General')
                ->get();

            $beekeeperData = $this->getBeekeeperData();
            $apiaryData = $this->getApiaryData($apiario);

            $data = array_merge($beekeeperData, $apiaryData, ['visits' => $visitas]);

            $pdf = Pdf::loadView('documents.visit-record', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'enable_remote' => false
            ]);

            return $pdf->download('Visitas_Apiario_' . $apiario->nombre . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generating visits document: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el documento de visitas: ' . $e->getMessage());
        }
    }

    public function generateInspeccionDocument($apiarioId)
    {
        try {
            $apiario = Apiario::with(['comuna.region'])->findOrFail($apiarioId);
            $inspecciones = Visita::where('apiario_id', $apiarioId)
                ->where('tipo_visita', 'Inspección de Visita')
                ->get();

            $beekeeperData = $this->getBeekeeperData();
            $apiaryData = $this->getApiaryData($apiario);

            $data = array_merge($beekeeperData, $apiaryData, ['visits' => $inspecciones]);

            $pdf = Pdf::loadView('documents.inspection-record', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'enable_remote' => false
            ]);

            return $pdf->download('Inspecciones_Apiario_' . $apiario->numero . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generating inspection document: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el documento de inspecciones: ' . $e->getMessage());
        }
    }

    public function generateMedicamentsDocument($apiarioId)
    {
        try {
            $apiario = Apiario::with(['comuna.region'])->findOrFail($apiarioId);
            $medicamentos = Visita::where('apiario_id', $apiarioId)
                ->where('tipo_visita', 'Uso de Medicamentos')
                ->get();

            $beekeeperData = $this->getBeekeeperData();
            $apiaryData = $this->getApiaryData($apiario);

            $data = array_merge($beekeeperData, $apiaryData, ['visits' => $medicamentos]);

            $pdf = Pdf::loadView('documents.medicaments-record', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'enable_remote' => false
            ]);

            return $pdf->download('Medicamentos_Apiario_' . $apiario->nombre . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generating medicaments document: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el documento de medicamentos: ' . $e->getMessage());
        }
    }

    public function generateAlimentacionDocument($apiarioId)
    {
        $apiario = Apiario::with(['comuna.region'])->findOrFail($apiarioId);

        // Trae todas las visitas de tipo "Alimentación" con su EstadoNutricional y la Colmena
        $visitas = Visita::with(['estadoNutricional','colmena'])
            ->where('apiario_id', $apiarioId)
            ->where('tipo_visita', 'Alimentación')
            ->get();

        if ($visitas->isEmpty()) {
            return back()->with('error', 'No hay registros de alimentación válidos para este apiario.');
        }

        $beekeeper  = $this->getBeekeeperData();
        $apiaryData = $this->getApiaryData($apiario);

        // Usamos la Opción A: un solo $data para tu vista Blade existente
        $data = array_merge(
            $beekeeper,
            $apiaryData,
            ['visits' => $visitas]
        );

        $pdf = Pdf::loadView('documents.alimentacion-record', compact('data'))
            ->setPaper('A4','portrait')
            ->setOptions([
                'isHtml5ParserEnabled'=>true,
                'isPhpEnabled'=>false,
                'defaultFont'=>'DejaVu Sans',
                'enable_remote'=>false,
            ]);

        return $pdf->download("Alimentacion_{$apiario->nombre}.pdf");
    }

    public function generateReinaDocument($calidadReinaId)
    {
        // 1. Carga el registro de reina con sus relaciones:
        $calidadReina = CalidadReina::with([
            'visita.apiario.user',
            'visita.apiario.comuna.region'
        ])->findOrFail($calidadReinaId);

        // 2. Extrae apiario y apicultor:
        $visita     = $calidadReina->visita;
        $apiario    = $visita->apiario;
        $apicultor  = $apiario->user;

        // 3. Valida que existan:
        if (!$visita) {
            return back()->with('error', 'No se encontró la visita asociada.');
        }
        if (!$apiario) {
            return back()->with('error', 'No se encontró el apiario asociado.');
        }
        if (!$apicultor) {
            return back()->with('error', 'No se encontró el apicultor asociado.');
        }

        // 4. Genera el PDF usando la misma vista Blade que ya tienes:
        $pdf = Pdf::loadView(
            'documents.reina-record',
            compact('calidadReina', 'apiario', 'apicultor')
        )
        ->setPaper('A4','portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled'         => true,
            'defaultFont'          => 'DejaVu Sans',
            'enable_remote'        => true,
            'chroot'               => public_path(),
        ]);

        // 5. Descarga con nombre claro:
        $filename = "Reina_{$apiario->nombre}.pdf";
        return $pdf->download($filename);
    }

    public function qrPdf(Apiario $apiario, Colmena $colmena)
    {
        // URL a codigicar en el QR
        $url = route('colmenas.show', [
            'apiario' => $apiario->id,
            'colmena' => $colmena->id,
        ]);
        // Generar PDF 
        $pdf = Pdf::setOptions([
            'isRemoteEnabled' => true,
        ])->loadView('documents.colmena-qr-pdf', compact('url','apiario','colmena'));
        // Nombre dinámico
        $filename = "qr_colmena_{$colmena->numero}_{$apiario->nombre}.pdf";
        // Descargar el archivo PDF
        return $pdf->download($filename);
    }

}