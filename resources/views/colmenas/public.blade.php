<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colmena #{{ $colmena->numero }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link href="{{ asset('./css/components/home-user/show/show-colmenas.css') }}" rel="stylesheet">
</head>

<body>
    <style>
        * {
            font-family: 'Arial', sans-serif;
        }
    </style>
    @php
$hasAny = $pcc1 || $pcc2 || $pcc3 || $pcc4 || $pcc5 || $pcc6 || $pcc7;
    @endphp

    <div class="main-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item">
                    <a href="{{ route('welcome') }}" class="breadcrumb-link">Inicio</a>
                </li>
                <li class="breadcrumb-item breadcrumb-current" aria-current="page">
                    Colmena #{{ $colmena->numero }}
                </li>
            </ol>

            <a href="{{ route('welcome') }}" class="btn btn-ghost btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </nav>

        <!-- Grid principal -->
        <div class="grid grid-cols-12">
            <!-- Columna izquierda: QR y datos básicos -->
            <div class="col-span-12 md:col-span-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-box card-title-icon"></i>
                            Nombre: {{ $colmena->nombre ?? ('#' . $colmena->numero) }} - Número #{{ $colmena->numero }}
                        </h5>
                    </div>

                    <div class="card-body">
                        <!-- QR Section -->
                        <div class="qr-section">
                            @php
// QR apunta a la vista pública de la colmena
$url = route('colmenas.public', ['colmena' => $colmena->id]);
                            @endphp

                            <div class="qr-container">
                                <div class="qr-wrapper">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($url) }}&size=150x150"
                                        alt="QR Colmena #{{ $colmena->numero }}" class="qr-image" width="150"
                                        height="150" />
                                </div>
                            </div>

                            <div class="mb-4 flex gap-3">
                                <a href="{{ route('colmenas.qr-pdf.public', [$colmena->apiario_id, $colmena->id]) }}"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-print"></i> Imprimir QR
                                </a>
                            </div>
                        </div>

                        <!-- Información básica -->
                        <div class="info-section">
                            <h6 class="info-section-title flex items-center gap-3 mb-4">
                                <i class="fas fa-info-circle info-icon primary"></i>
                                Información Básica
                            </h6>

                            <div class="info-list">
                                <div class="info-item" style="--index: 0">
                                    <div class="info-icon success">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Apiario:</span>
                                        <span class="info-value">{{ $apiario->nombre }}</span>
                                    </div>
                                </div>

                                <div class="info-item" style="--index: 1">
                                    <div class="info-icon primary">
                                        <i class="fas fa-hashtag"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Número:</span>
                                        <span class="info-value">#{{ $colmena->numero }}</span>
                                    </div>
                                </div>

                                <div class="info-item" style="--index: 4">
                                    <div class="info-icon success">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Fecha de registro:</span>
                                        <span class="info-value">{{ $colmena->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                @if($colmena->updated_at != $colmena->created_at)
                                    <div class="info-item" style="--index: 5">
                                        <div class="info-icon info">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="info-content">
                                            <span class="info-label">Últ. actualización:</span>
                                            <span class="info-value">{{ $colmena->updated_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Detalles del PCC actual -->
            <div class="col-span-12 md:col-span-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-clipboard-list card-title-icon"></i>
                            Evaluación PCC Actual
                        </h5>
                    </div>

                    <div class="card-body">
                        {{-- Fecha de evaluación --}}
                        @if($lastFecha)
                            <div class="evaluation-date">
                                <h6 class="evaluation-date-title">
                                    <i class="fas fa-calendar-alt"></i>
                                    Fecha de evaluación: {{ \Carbon\Carbon::parse($lastFecha)->format('d/m/Y') }}
                                </h6>
                                <span class="badge badge-success">
                                    <i class="fas fa-star"></i> Última evaluación
                                </span>
                            </div>
                        @endif

                        <!-- Grid de PCC -->
                        <div class="pcc-grid">
                            {{-- PCC1 --}}
                            <div class="pcc-card">
                                <div class="pcc-header">
                                    <div class="pcc-icon">
                                        <i class="fi fi-rr-bee"></i>
                                    </div>
                                    <h6 class="pcc-title">Cámara de Cría</h6>
                                </div>
                                <div class="pcc-content">
                                    @if($pcc1)
                                        <ul class="pcc-list">
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Vigor colmena:</span>
                                                <span class="pcc-list-value">{{ $pcc1->vigor_colmena }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Actividad abejas:</span>
                                                <span class="pcc-list-value">{{ $pcc1->actividad_abejas }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Ingreso polen:</span>
                                                <span class="pcc-list-value">{{ $pcc1->ingreso_polen }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Bloqueo cámara:</span>
                                                <span class="pcc-list-value">{{ $pcc1->bloqueo_camara_cria }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Presencia celdas reales:</span>
                                                <span class="pcc-list-value">{{ $pcc1->presencia_celdas_reales }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Marcos con cría:</span>
                                                <span class="pcc-list-value">{{ $pcc1->cantidad_marcos_con_cria }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Marcos con abejas:</span>
                                                <span class="pcc-list-value">{{ $pcc1->cantidad_marcos_con_abejas }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Reservas (miel/polén):</span>
                                                <span class="pcc-list-value">{{ $pcc1->cantidad_reservas }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Presencia zánganos:</span>
                                                <span
                                                    class="pcc-list-value">{{ $pcc1->presencia_zanganos ? 'Sí' : 'No' }}</span>
                                            </li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC2 --}}
                            <div class="pcc-card">
                                <div class="pcc-header">
                                    <div class="pcc-icon">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <h6 class="pcc-title">Estado de la Reina</h6>
                                </div>
                                <div class="pcc-content">
                                    @if($pcc2)
                                        <ul class="pcc-list">
                                            <li class="pcc-list-item"><span class="pcc-list-label">Postura
                                                    reina:</span><span
                                                    class="pcc-list-value">{{ $pcc2->postura_reina }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Estado cría:</span><span
                                                    class="pcc-list-value">{{ $pcc2->estado_cria }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Postura
                                                    zánganos:</span><span
                                                    class="pcc-list-value">{{ $pcc2->postura_zanganos }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Origen reina:</span><span
                                                    class="pcc-list-value">{{ $pcc2->origen_reina }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Raza:</span><span
                                                    class="pcc-list-value">{{ $pcc2->raza }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Línea
                                                    genética:</span><span
                                                    class="pcc-list-value">{{ $pcc2->linea_genetica }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Estado
                                                    actual:</span><span
                                                    class="pcc-list-value">{{ $pcc2->estado_actual }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Reemplazos
                                                    realizados:</span>
                                                <span class="pcc-list-value">
                                                    @php
    $reemplazos = $pcc2->reemplazos_realizados;
    if (is_string($reemplazos)) {
        $json = json_decode($reemplazos, true);
        if (is_array($json))
            $reemplazos = $json;
    }
                                                    @endphp
                                                    @if(is_array($reemplazos))
                                                        @foreach($reemplazos as $rep)
                                                            @if(is_array($rep) && isset($rep['fecha']))
                                                                <div>
                                                                    <strong>Fecha:</strong>
                                                                    {{ \Carbon\Carbon::parse($rep['fecha'])->format('d/m/Y') }}
                                                                    @if(isset($rep['motivo']))
                                                                        <span><strong> Motivo:</strong> {{ $rep['motivo'] }}</span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                {{ $rep }}
                                                            @endif
                                                        @endforeach
                                                    @elseif($reemplazos)
                                                        {{ $reemplazos }}
                                                    @else
                                                        <span style="color: #888;">Sin datos</span>
                                                    @endif
                                                </span>
                                            </li>
                                            @if($pcc2->fecha_introduccion)
                                                <li class="pcc-list-item"><span class="pcc-list-label">Fecha
                                                        introducción:</span><span
                                                        class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc2->fecha_introduccion)->format('d/m/Y') }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC3 --}}
                            <div class="pcc-card">
                                <div class="pcc-header">
                                    <div class="pcc-icon">
                                        <i class="fas fa-apple-whole"></i>
                                    </div>
                                    <h6 class="pcc-title">Estado Nutricional</h6>
                                </div>
                                <div class="pcc-content">
                                    @if($pcc3)
                                        <ul class="pcc-list">
                                            <li class="pcc-list-item"><span class="pcc-list-label">Tipo
                                                    alimentación:</span><span
                                                    class="pcc-list-value">{{ $pcc3->tipo_alimentacion }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Fecha
                                                    aplicación:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc3->fecha_aplicacion)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Insumo
                                                    utilizado:</span><span
                                                    class="pcc-list-value">{{ $pcc3->insumo_utilizado }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Objetivo:</span><span
                                                    class="pcc-list-value">{{ $pcc3->objetivo }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Dosificación:</span><span
                                                    class="pcc-list-value">{{ $pcc3->dosifiacion }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Método
                                                    utilizado:</span><span
                                                    class="pcc-list-value">{{ $pcc3->metodo_utilizado }}</span></li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC4 --}}
                            <div class="pcc-card">
                                <div class="pcc-header">
                                    <div class="pcc-icon">
                                        <i class="fas fa-bug"></i>
                                    </div>
                                    <h6 class="pcc-title">Control de Varroa</h6>
                                </div>
                                <div class="pcc-content">
                                    @if($pcc4)
                                        <ul class="pcc-list">
                                            <li class="pcc-list-item"><span class="pcc-list-label">Método
                                                    diagnóstico:</span><span
                                                    class="pcc-list-value">{{ $pcc4->metodo_diagnostico }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Tratamiento:</span><span
                                                    class="pcc-list-value">{{ $pcc4->tratamiento }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Fecha
                                                    monitoreo:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc4->fecha_monitoreo_varroa)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Producto
                                                    comercial:</span><span
                                                    class="pcc-list-value">{{ $pcc4->producto_comercial }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Ingrediente
                                                    activo:</span><span
                                                    class="pcc-list-value">{{ $pcc4->ingrediente_activo }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Período de
                                                    carencia:</span><span
                                                    class="pcc-list-value">{{ $pcc4->periodo_carencia }}</span></li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC5 --}}
                            <div class="pcc-card">
                                <div class="pcc-header">
                                    <div class="pcc-icon">
                                        <i class="fi fi-rr-microscope"></i>
                                    </div>
                                    <h6 class="pcc-title">Control de Nosema</h6>
                                </div>
                                <div class="pcc-content">
                                    @if($pcc5)
                                        <ul class="pcc-list">
                                            <li class="pcc-list-item"><span class="pcc-list-label">Método diagnóstico
                                                    laboratorio:</span><span
                                                    class="pcc-list-value">{{ $pcc5->metodo_diagnostico_laboratorio }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Fecha
                                                    aplicación:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc5->fecha_aplicacion)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Dosificación:</span><span
                                                    class="pcc-list-value">{{ $pcc5->dosificacion }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Método
                                                    aplicación:</span><span
                                                    class="pcc-list-value">{{ $pcc5->metodo_aplicacion }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Fecha
                                                    monitoreo:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc5->fecha_monitoreo_nosema)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Producto
                                                    comercial:</span><span
                                                    class="pcc-list-value">{{ $pcc5->producto_comercial }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Ingrediente
                                                    activo:</span><span
                                                    class="pcc-list-value">{{ $pcc5->ingrediente_activo }}</span></li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC6 --}}
                            <div class="pcc-card">
                                <div class="pcc-header">
                                    <div class="pcc-icon">
                                        <i class="fi fi-rr-box"></i>
                                    </div>
                                    <h6 class="pcc-title">Índice de Cosecha</h6>
                                </div>
                                <div class="pcc-content">
                                    @if($pcc6)
                                        <ul class="pcc-list">
                                            <li class="pcc-list-item"><span class="pcc-list-label">Madurez miel:</span><span
                                                    class="pcc-list-value">{{ $pcc6->madurez_miel }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Número
                                                    alzadas:</span><span
                                                    class="pcc-list-value">{{ $pcc6->num_alzadas }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Marcos miel:</span><span
                                                    class="pcc-list-value">{{ $pcc6->marcos_miel }}</span></li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- PCC7 --}}
                            <div class="pcc-card" style="grid-column: span 2;">
                                <div class="pcc-header">
                                    <div class="pcc-icon">
                                        <i class="fas fa-snowflake"></i>
                                    </div>
                                    <h6 class="pcc-title">Preparación Invernada</h6>
                                </div>
                                <div class="pcc-content">
                                    @if($pcc7)
                                        <ul class="pcc-list">
                                            <li class="pcc-list-item"><span class="pcc-list-label">Marcos con
                                                    abejas:</span><span
                                                    class="pcc-list-value">{{ $pcc7->cantidad_marcos_cubiertos_abejas }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Marcos con
                                                    cría:</span><span
                                                    class="pcc-list-value">{{ $pcc7->cantidad_marcos_cubiertos_cria }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Marcos reservas
                                                    miel:</span><span
                                                    class="pcc-list-value">{{ $pcc7->marcos_reservas_miel }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Reservas
                                                    polen:</span><span
                                                    class="pcc-list-value">{{ $pcc7->presencial_reservas_polen }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Presencia
                                                    reina:</span><span
                                                    class="pcc-list-value">{{ $pcc7->presencia_reina ? 'Sí' : 'No' }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Infestación
                                                    varroa:</span><span
                                                    class="pcc-list-value">{{ $pcc7->nivel_infestacion_varroa }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Últ. revisión
                                                    pre-receso:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc7->fecha_ultima_revision_previa_receso)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Cierre
                                                    temporada:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc7->fecha_cierre_temporada)->format('d/m/Y') }}</span>
                                            </li>
                                        </ul>
                                    @else
                                        <p class="small mb-0">No hay datos registrados.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Mensaje global si no hay datos --}}
                        @if(!$hasAny)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle alert-icon"></i>
                                <span>No hay registros para esta colmena.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center" style="margin-top: 2rem; color: var(--color-amber-600); font-size: var(--font-sm);">
            Vista pública generada por QR
        </p>
    </div>
</body>

</html>