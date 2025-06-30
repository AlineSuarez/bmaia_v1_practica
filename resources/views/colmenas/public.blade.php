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
                        @php
                            $url = route('colmenas.public', ['colmena' => $colmena->id]);
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($url) }}&size=150x150"
                            alt="QR Colmena #{{ $colmena->numero }}" class="mb-3" />

                        <div class="mb-2">
                            <a href="{{ route('colmenas.qr-pdf', [$colmena->apiario_id, $colmena->id]) }}"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-print"></i> Imprimir QR
                            </a>
                        </div>

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
                        @php
                            $pccActual = $pccs->first();
                            $pcc3 = $lastAlimentacion ?? null;
                            $pcc4 = $lastVarroa ?? null;
                            $pcc5 = $lastNosemosis ?? null;
                            $pcc6 = optional($pccActual)->indiceCosecha;
                            $pcc7 = optional($pccActual)->preparacionInvernada;
                            $hasAny = $pccActual || $pcc3 || $pcc4 || $pcc5 || $pcc6 || $pcc7;
                        @endphp

                        {{-- Fecha --}}
                        @if($pccActual)
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="text-white mb-0">
                                    <i class="fas fa-calendar-alt"></i>
                                    Fecha de evaluación: {{ $pccActual->fecha->format('d/m/Y') }}
                                </h6>
                                <span class="badge bg-success"><i class="fas fa-star"></i> Última evaluación</span>
                            </div>
                        @endif

                        <div class="row">
                            {{-- PCC1 --}}
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3 h-100 shadow-sm">
                                    <h6 class="text-warning mb-3"><i class="fas fa-baby"></i> PCC1 – Desarrollo Cámara
                                        de Cría</h6>
                                    @if($pccActual && $pccActual->desarrolloCria)
                                        <ul class="list-unstyled small">
                                            <li><strong>Vigor colmena:</strong>
                                                {{ $pccActual->desarrolloCria->vigor_colmena }}</li>
                                            <li><strong>Actividad abejas:</strong>
                                                {{ $pccActual->desarrolloCria->actividad_abejas }}</li>
                                            <li><strong>Ingreso polen:</strong>
                                                {{ $pccActual->desarrolloCria->ingreso_polen }}</li>
                                            <li><strong>Celdas reales:</strong>
                                                {{ $pccActual->desarrolloCria->presencia_celdas_reales }}</li>
                                            <li><strong>Marcos con cría:</strong>
                                                {{ $pccActual->desarrolloCria->cantidad_marcos_con_cria }}</li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC2 --}}
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3 h-100 shadow-sm">
                                    <h6 class="text-warning mb-3"><i class="fas fa-crown"></i> PCC2 – Calidad de la
                                        Reina</h6>
                                    @if($pccActual && $pccActual->calidadReina)
                                        <ul class="list-unstyled small">
                                            <li><strong>Postura reina:</strong>
                                                {{ $pccActual->calidadReina->postura_reina }}</li>
                                            <li><strong>Estado cría:</strong> {{ $pccActual->calidadReina->estado_cria }}
                                            </li>
                                            <li><strong>Fecha introducción:</strong>
                                                {{ optional($pccActual->calidadReina->fecha_introduccion)->format('d/m/Y') }}
                                            </li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC3 --}}
                            @php
                                $sist3 = optional($pccActual)->estadoNutricional;
                                $vis3 = $lastAlimentacion ?? null;
                            @endphp
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3 h-100 shadow-sm">
                                    <h6 class="text-warning mb-3"><i class="fas fa-utensils"></i> PCC3 – Estado
                                        Nutricional</h6>
                                    @if($sist3)
                                        <ul class="list-unstyled small">
                                            <li><strong>Objetivo:</strong> {{ $sist3->objetivo }}</li>
                                            <li><strong>Tipo alimentación:</strong> {{ $sist3->tipo_alimentacion }}</li>
                                            <li><strong>Insumo utilizado:</strong> {{ $sist3->insumo_utilizado }}</li>
                                        </ul>
                                    @elseif($vis3)
                                        <ul class="list-unstyled small">
                                            <li><strong>Objetivo:</strong> {{ $vis3->objetivo }}</li>
                                            <li><strong>Tipo alimentación:</strong> {{ $vis3->tipo_alimentacion }}</li>
                                            <li><strong>Insumo utilizado:</strong> {{ $vis3->insumo_utilizado }}</li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC4 --}}
                            @php
                                $sist4 = optional($pccActual)->presenciaVarroa;
                                $vis4 = $lastVarroa ?? null;
                            @endphp
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3 h-100 shadow-sm">
                                    <h6 class="text-warning mb-3"><i class="fas fa-bug"></i> PCC4 – Varroa</h6>
                                    @if($sist4)
                                        <ul class="list-unstyled small">
                                            <li><strong>Diagnóstico visual:</strong> {{ $sist4->diagnostico_visual }}</li>
                                            <li><strong>Método diagnóstico:</strong> {{ $sist4->metodo_diagnostico }}</li>
                                            <li><strong>Tratamiento:</strong> {{ $sist4->tratamiento }}</li>
                                            <li><strong>Fecha aplicación:</strong>
                                                {{ optional($sist4->fecha_aplicacion)->format('d/m/Y') }}</li>
                                        </ul>
                                    @elseif($vis4)
                                        <ul class="list-unstyled small">
                                            <li><strong>Diagnóstico visual:</strong> {{ $vis4->diagnostico_visual }}</li>
                                            <li><strong>Método diagnóstico:</strong> {{ $vis4->metodo_diagnostico }}</li>
                                            <li><strong>Tratamiento:</strong> {{ $vis4->tratamiento }}</li>
                                            <li><strong>Fecha aplicación:</strong>
                                                {{ optional($vis4->fecha_aplicacion)->format('d/m/Y') }}</li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC5 --}}
                            @php
                                $sist5 = optional($pccActual)->presenciaNosemosis;
                                $vis5 = $lastNosemosis ?? null;
                            @endphp
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3 h-100 shadow-sm">
                                    <h6 class="text-warning mb-3"><i class="fas fa-microscope"></i> PCC5 – Nosemosis
                                    </h6>
                                    @if($sist5)
                                        <ul class="list-unstyled small">
                                            <li><strong>Signos clínicos:</strong> {{ $sist5->signos_clinicos }}</li>
                                            <li><strong>Método diagnóstico lab.:</strong>
                                                {{ $sist5->metodo_diagnostico_laboratorio }}</li>
                                            <li><strong>Fecha aplicación:</strong>
                                                {{ optional($sist5->fecha_aplicacion)->format('d/m/Y') }}</li>
                                        </ul>
                                    @elseif($vis5)
                                        <ul class="list-unstyled small">
                                            <li><strong>Signos clínicos:</strong> {{ $vis5->signos_clinicos }}</li>
                                            <li><strong>Método diagnóstico lab.:</strong>
                                                {{ $vis5->metodo_diagnostico_laboratorio }}</li>
                                            <li><strong>Fecha aplicación:</strong>
                                                {{ optional($vis5->fecha_aplicacion)->format('d/m/Y') }}</li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC6 --}}
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3 h-100 shadow-sm">
                                    <h6 class="text-warning mb-3"><i class="fas fa-honey-pot"></i> PCC6 – Índice de
                                        Cosecha</h6>
                                    @if($pcc6)
                                        <ul class="list-unstyled small">
                                            <li><strong>Madurez miel:</strong> {{ $pcc6->madurez_miel }}</li>
                                            <li><strong>Alzas promedio:</strong> {{ $pcc6->num_alzadas }}</li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC7 --}}
                            <div class="col-md-12 mb-4">
                                <div class="border rounded p-3 shadow-sm">
                                    <h6 class="text-warning mb-3"><i class="fas fa-snowflake"></i> PCC7 – Preparación
                                        Invernada</h6>
                                    @if($pcc7)
                                        <ul class="list-unstyled small">
                                            <li>
                                                <strong>Fecha cierre temporada:</strong>
                                                {{ optional($pcc7->fecha_cierre_temporada)->format('d/m/Y') ?? 'N/A' }}
                                            </li>
                                            <li>
                                                <strong>Última revisión previa receso:</strong>
                                                {{ optional($pcc7->fecha_ultima_revision_previa_receso)->format('d/m/Y') ?? 'N/A' }}
                                            </li>
                                            <li>
                                                <strong>Signos enfermedades visibles:</strong>
                                                {{ $pcc7->signos_enfermedades_visibles ?? 'N/A' }}
                                            </li>
                                            <li>
                                                <strong>Reina presente:</strong>
                                                <span
                                                    class="badge {{ $pcc7->presencia_reina ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $pcc7->presencia_reina ? 'Sí' : 'No' }}
                                                </span>
                                            </li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Mensaje global sólo si NO hay ningún dato --}}
                        @if(!$hasAny)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No hay registros para esta colmena.
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
    <style>
        body,
        .container-sm,
        .card,
        .card-body,
        .card-header,
        .card-footer {
            font-size: 0.90rem !important;
        }

        .card,
        .card-body,
        .card-header,
        .card-footer {
            padding: 0.5rem !important;
        }

        .list-unstyled li,
        .mb-3,
        .mb-2,
        .mb-1 {
            margin-bottom: 0.3rem !important;
        }

        .row.g-2 {
            --bs-gutter-x: 0.5rem;
        }

        .form-control,
        .btn,
        .badge {
            font-size: 0.85em !important;
            padding: 0.25em 0.5em !important;
        }

        h5,
        h6 {
            font-size: 1rem !important;
        }

        .breadcrumb {
            font-size: 0.85rem !important;
            margin-bottom: 0.5rem !important;
        }

        .p-3,
        .p-2 {
            padding: 0.5rem !important;
        }

        .rounded {
            border-radius: 0.3rem !important;
        }
    </style>
</body>

</html>