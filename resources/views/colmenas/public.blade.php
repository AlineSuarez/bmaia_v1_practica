
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Colmena #{{ $colmena->numero }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container-sm py-2">
        <div class="row g-2">
            <!-- Columna izquierda: QR y datos básicos -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100" style="font-size: 0.95rem;">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-box"></i> Colmena #{{ $colmena->numero }}</h5>
                    </div>
                    <div class="card-body text-center">
                        <!-- Información básica de la colmena -->
                        <div class="text-start">
                            <h6 class="fw-bold mb-2 text-primary">
                                <i class="fas fa-info-circle"></i> Información Básica
                            </h6>
                            <ul class="list-unstyled mb-2">
                                <li class="mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-map-marker-alt text-success me-2"></i>
                                        <strong>Apiario:</strong>
                                    </div>
                                    <span class="text-muted ps-3">{{ $apiario->nombre }}</span>
                                </li>
                                <li class="mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-hashtag text-primary me-2"></i>
                                        <strong>Número:</strong>
                                    </div>
                                    <span class="text-muted ps-3">#{{ $colmena->numero }}</span>
                                </li>
                                @if($colmena->numero_marcos)
                                    <li class="mb-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-layer-group text-warning me-2"></i>
                                            <strong>Marcos:</strong>
                                        </div>
                                        <span class="text-muted ps-3">{{ $colmena->numero_marcos }}</span>
                                    </li>
                                @endif
                                @if($colmena->estado_inicial)
                                    <li class="mb-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-chart-line text-secondary me-2"></i>
                                            <strong>Estado inicial:</strong>
                                        </div>
                                        <span class="badge bg-light text-dark ps-3">{{ $colmena->estado_inicial }}</span>
                                    </li>
                                @endif
                                <li class="mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-calendar-plus text-success me-2"></i>
                                        <strong>Fecha de registro:</strong>
                                    </div>
                                    <span class="text-muted ps-3">{{ $colmena->created_at->format('d/m/Y') }}</span>
                                </li>
                                @if($colmena->updated_at != $colmena->created_at)
                                    <li class="mb-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-edit text-info me-2"></i>
                                            <strong>Última actualización:</strong>
                                        </div>
                                        <span class="text-muted ps-3">{{ $colmena->updated_at->format('d/m/Y H:i') }}</span>
                                    </li>
                                @endif
                            </ul>
                            @if($colmena->observaciones)
                                <div class="mt-4 p-3 bg-light rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-sticky-note text-warning me-2"></i>
                                        <strong>Observaciones:</strong>
                                    </div>
                                    <p class="text-muted small mb-0">{{ $colmena->observaciones }}</p>
                                </div>
                            @endif
                            <!-- Estadísticas rápidas -->
                            <div class="mt-3 p-2 bg-primary bg-opacity-10 rounded">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-chart-bar"></i> Estadísticas
                                </h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="text-primary mb-1">{{ $pccs->count() }}</h5>
                                            <small class="text-muted">Evaluaciones PCC</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-success mb-1">
                                            @if($pccs->isNotEmpty())
                                                {{ (int) \Carbon\Carbon::parse($pccs->first()->created_at)->diffInDays(\Carbon\Carbon::now()) }}
                                            @else
                                                0
                                            @endif
                                        </h5>
                                        <small class="text-muted">Días desde última evaluación</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Columna derecha: Detalles del PCC actual -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Evaluación PCC Actual</h5>
                    </div>
                    <div class="card-body">
                        @php $pccActual = $pccs->first(); @endphp
                        @if($pccActual)
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-calendar-alt"></i>
                                    Fecha de evaluación: {{ \Carbon\Carbon::parse($pccActual->fecha)->format('d/m/Y') }}
                                </h6>
                                <span class="badge bg-success">
                                    <i class="fas fa-star"></i> Última evaluación
                                </span>
                            </div>
                            <div class="row">
                                <!-- PCC1 - Desarrollo Cámara de Cría -->
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 h-100 shadow-sm">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-baby"></i> PCC1 – Desarrollo Cámara de Cría
                                        </h6>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">
                                                <strong>Vigor colmena:</strong>
                                                <span class="text-muted">{{ $pccActual->desarrolloCria->vigor_colmena ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Actividad abejas:</strong>
                                                <span class="text-muted">{{ $pccActual->desarrolloCria->actividad_abejas ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Ingreso polen:</strong>
                                                <span class="text-muted">{{ $pccActual->desarrolloCria->ingreso_polen ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Celdas reales:</strong>
                                                <span class="text-muted">{{ $pccActual->desarrolloCria->presencia_celdas_reales ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Marcos con cría:</strong>
                                                <span class="text-muted">{{ $pccActual->desarrolloCria->cantidad_marcos_con_cria ?? 'N/A' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- PCC2 - Calidad de la Reina -->
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 h-100 shadow-sm">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-crown"></i> PCC2 – Calidad de la Reina
                                        </h6>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">
                                                <strong>Postura reina:</strong>
                                                <span class="text-muted">{{ $pccActual->calidadReina->postura_reina ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Estado cría:</strong>
                                                <span class="text-muted">{{ $pccActual->calidadReina->estado_cria ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Fecha introducción:</strong>
                                                <span class="text-muted">{{ optional(optional($pccActual->calidadReina)->fecha_introduccion)->format('d/m/Y') ?? 'N/A' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- PCC3 - Estado Nutricional -->
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 h-100 shadow-sm">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-utensils"></i> PCC3 – Estado Nutricional
                                        </h6>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">
                                                <strong>Objetivo:</strong>
                                                <span class="text-muted">{{ $pccActual->estadoNutricional->objetivo ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Tipo alimentación:</strong>
                                                <span class="text-muted">{{ $pccActual->estadoNutricional->tipo_alimentacion ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Insumo utilizado:</strong>
                                                <span class="text-muted">{{ $pccActual->estadoNutricional->insumo_utilizado ?? 'N/A' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- PCC4 - Varroa -->
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 h-100 shadow-sm">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-bug"></i> PCC4 – Varroa
                                        </h6>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">
                                                <strong>Diagnóstico visual:</strong>
                                                <span class="text-muted">{{ $pccActual->presenciaVarroa->diagnostico_visual ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Método diagnóstico:</strong>
                                                <span class="text-muted">{{ $pccActual->presenciaVarroa->metodo_diagnostico ?? 'N/A' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- PCC5 - Nosemosis -->
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 h-100 shadow-sm">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-microscope"></i> PCC5 – Nosemosis
                                        </h6>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">
                                                <strong>Signos clínicos:</strong>
                                                <span class="text-muted">{{ $pccActual->presenciaNosemosis->signos_clinicos ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Tratamiento:</strong>
                                                <span class="text-muted">{{ $pccActual->presenciaNosemosis->tratamiento ?? 'N/A' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- PCC6 - Índice de Cosecha -->
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 h-100 shadow-sm">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-honey-pot"></i> PCC6 – Índice de Cosecha
                                        </h6>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">
                                                <strong>Madurez miel:</strong>
                                                <span class="text-muted">{{ $pccActual->indiceCosecha->madurez_miel ?? 'N/A' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Alzas promedio:</strong>
                                                <span class="text-muted">{{ $pccActual->indiceCosecha->num_alzadas ?? 'N/A' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- PCC7 - Preparación Invernada -->
                                <div class="col-md-12 mb-4">
                                    <div class="border rounded p-3 shadow-sm">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-snowflake"></i> PCC7 – Preparación Invernada
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled small">
                                                    <li class="mb-2">
                                                        <strong>Control sanitario:</strong>
                                                        <span class="text-muted">{{ $pccActual->preparacionInvernada->control_sanitario ?? 'N/A' }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled small">
                                                    <li class="mb-2">
                                                        <strong>Reina presente:</strong>
                                                        <span class="text-muted">
                                                            @if(isset($pccActual->preparacionInvernada) && $pccActual->preparacionInvernada->presencia_reina !== null)
                                                                <span class="badge {{ $pccActual->preparacionInvernada->presencia_reina ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ $pccActual->preparacionInvernada->presencia_reina ? 'Sí' : 'No' }}
                                                                </span>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No hay evaluaciones PCC registradas para esta colmena.
                            </div>
                        @endif
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <a href="{{ route('welcome') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver a inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center text-muted mt-3 small">Vista pública generada por QR</p>
    </div>
</body>
</html>