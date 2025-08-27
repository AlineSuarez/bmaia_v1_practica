@extends('layouts.app')

@section('title', 'Detalle de Colmena')

@section('content')
    @php
        $hasAny = $pcc1 || $pcc2 || $pcc3 || $pcc4 || $pcc5 || $pcc6 || $pcc7;
    @endphp

    <head>
        <link href="{{ asset('./css/components/home-user/show/show-colmenas.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    </head>

    <div class="main-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item">
                    <a href="{{ route('apiarios') }}" class="breadcrumb-link">Apiarios</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('colmenas.index', $apiario->id) }}" class="breadcrumb-link">
                        {{ $apiario->nombre }}
                    </a>
                </li>
                <li class="breadcrumb-item breadcrumb-current" aria-current="page">
                    Colmena #{{ $colmena->numero }}
                </li>
            </ol>

            <a href="{{ route('colmenas.index', $apiario->id) }}" class="btn btn-ghost btn-sm">
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
                            Nombre: {{ $colmena->nombre ?? ('#' . $colmena->numero) }}
                        </h5>
                    </div>

                    <div class="card-body">
                        <!-- QR Section -->
                        <div class="qr-section">
                            @php
                                $url = route('colmenas.show', [
                                    'apiario' => $apiario->id,
                                    'colmena' => $colmena->id
                                ]);
                            @endphp

                            <div class="qr-container">
                                <div class="qr-wrapper">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($url) }}&size=150x150"
                                        alt="QR Colmena #{{ $colmena->numero }}" class="qr-image" width="150"
                                        height="150" />
                                </div>
                            </div>

                            <div class="mb-4 flex-2-cols gap-3">
                                <a href="{{ route('colmenas.qr-pdf', [$apiario->id, $colmena->id]) }}"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-print"></i> Imprimir QR
                                </a>
                                <a href="{{ route('colmenas.historial', [$apiario->id, $colmena->id]) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-history"></i> Ver historial
                                </a>
                                <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalColorColmena">
                                    <i class="fas fa-palette"></i> Editar color
                                </a>
                                <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalEditarColmena" title="Editar Nombre y/o Numero de Colmena">
                                    <i class="fas fa-pencil-alt"></i>Editar Datos
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
                            Estado de Desarrollo de la Colmena
                        </h5>
                    </div>

                    <div class="card-body">
                        {{-- Fecha de evaluación --}}
                        @if($lastFecha)
                            <div class="evaluation-date">
                                <h6 class="evaluation-date-title">
                                    <i class="fas fa-calendar-alt"></i>
                                    @if($lastFecha)
                                        Fecha de evaluación: {{ \Carbon\Carbon::parse($lastFecha)->format('d/m/Y') }}
                                    @endif
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
                                            <li class="pcc-list-item"><span class="pcc-list-label">Postura reina:</span><span
                                                    class="pcc-list-value">{{ $pcc2->postura_reina }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Estado cría:</span><span
                                                    class="pcc-list-value">{{ $pcc2->estado_cria }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Postura zánganos:</span><span
                                                    class="pcc-list-value">{{ $pcc2->postura_zanganos }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Origen reina:</span><span
                                                    class="pcc-list-value">{{ $pcc2->origen_reina }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Raza:</span><span
                                                    class="pcc-list-value">{{ $pcc2->raza }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Línea genética:</span><span
                                                    class="pcc-list-value">{{ $pcc2->linea_genetica }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Estado actual:</span><span
                                                    class="pcc-list-value">{{ $pcc2->estado_actual }}</span></li>
                                            {{-- Reemplazos realizados --}}
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Reemplazos realizados:</span>
                                                <span class="pcc-list-value">
                                                    @php
                                                        $reemplazos = $pcc2->reemplazos_realizados;
                                                        if (is_string($reemplazos)) {
                                                            $json = json_decode($reemplazos, true);
                                                            if (is_array($json))
                                                                $reemplazos = $json;
                                                        }
                                                    @endphp
                                                    <span class="pcc-list-value">
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
                                            <li class="pcc-list-item"><span class="pcc-list-label">Fecha aplicación:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc3->fecha_aplicacion)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Insumo utilizado:</span><span
                                                    class="pcc-list-value">{{ $pcc3->insumo_utilizado }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Objetivo:</span><span
                                                    class="pcc-list-value">{{ $pcc3->objetivo }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Dosificación:</span><span
                                                    class="pcc-list-value">{{ $pcc3->dosifiacion }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Método utilizado:</span><span
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
                                            <li class="pcc-list-item"><span class="pcc-list-label">Fecha monitoreo:</span><span
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
                                            <li class="pcc-list-item"><span class="pcc-list-label">Fecha aplicación:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc5->fecha_aplicacion)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Dosificación:</span><span
                                                    class="pcc-list-value">{{ $pcc5->dosificacion }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Método
                                                    aplicación:</span><span
                                                    class="pcc-list-value">{{ $pcc5->metodo_aplicacion }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Fecha monitoreo:</span><span
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
                                            <li class="pcc-list-item"><span class="pcc-list-label">Número alzadas:</span><span
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
                                    <h6 class="pcc-title">Preparación para la Invernada</h6>
                                </div>
                                <div class="pcc-content">
                                    @if($pcc7)
                                        <ul class="pcc-list">
                                            <li class="pcc-list-item"><span class="pcc-list-label">Marcos con
                                                    abejas:</span><span
                                                    class="pcc-list-value">{{ $pcc7->cantidad_marcos_cubiertos_abejas }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Marcos con cría:</span><span
                                                    class="pcc-list-value">{{ $pcc7->cantidad_marcos_cubiertos_cria }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Marcos reservas
                                                    miel:</span><span
                                                    class="pcc-list-value">{{ $pcc7->marcos_reservas_miel }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Reservas polen:</span><span
                                                    class="pcc-list-value">{{ $pcc7->presencial_reservas_polen }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Presencia reina:</span><span
                                                    class="pcc-list-value">{{ $pcc7->presencia_reina ? 'Sí' : 'No' }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Infestación
                                                    varroa:</span><span
                                                    class="pcc-list-value">{{ $pcc7->nivel_infestacion_varroa }}</span></li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Últ. revisión
                                                    pre-receso:</span><span
                                                    class="pcc-list-value">{{ \Carbon\Carbon::parse($pcc7->fecha_ultima_revision_previa_receso)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="pcc-list-item"><span class="pcc-list-label">Cierre temporada:</span><span
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

                    {{-- Footer --}}
                    <div class="card-footer">
                        <div class="flex items-center gap-3">
                            @php
                                $visitaId = optional($pcc2)->visita_id
                                    ?? optional($pcc3)->visita_id
                                    ?? optional($pcc4)->visita_id
                                    ?? optional($pcc5)->visita_id;
                            @endphp

                            @if($visitaId)
                                <a href="{{ route('visitas.pcc.edit', ['visita' => $visitaId]) }}?colmena={{ $colmena->id }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar PCC
                                </a>
                                <a href="{{ route('colmenas.pcc.pdf', ['apiario' => $colmena->apiario_id, 'colmena' => $colmena->id]) }}"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-print"></i> Imprimir Detalle PCC
                                </a>
                            @else
                                <span class="text-amber-600 font-bold flex items-center">
                                    <i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i>
                                    Debes ingresar datos en el Cuaderno de Campo
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar datos de la colmena -->
    <div class="modal fade" id="modalEditarColmena" tabindex="-1" aria-labelledby="modalEditarColmenaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('colmenas.update', [$apiario->id, $colmena->id]) }}"
                    id="formEditarColmena">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditarColmenaLabel">
                            <i class="fas fa-edit me-2"></i>
                            Editar datos de la colmena
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group-modal">
                            <label for="edit_nombre" class="form-label">
                                <i class="fas fa-tag"></i> Nombre de la colmena *
                            </label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control-modal"
                                value="{{ old('nombre', $colmena->nombre) }}" placeholder="Ingrese un nombre" maxlength="50"
                                required>
                            <small class="form-help">Este campo es obligatorio.</small>
                        </div>

                        <div class="form-group-modal">
                            <label for="edit_numero" class="form-label">
                                <i class="fas fa-hashtag"></i> Número de colmena *
                            </label>
                            <input type="number" name="numero" id="edit_numero" class="form-control-modal"
                                value="{{ old('numero', $colmena->numero) }}" min="1" max="10000" required>
                            <small class="form-help">Este campo es obligatorio y debe ser único dentro del apiario.</small>
                        </div>

                        <!-- Vista previa dinámica de la colmena editada -->
                        <div class="colmena-preview-container-edit mb-3">
                            <div id="colmenaPreviewEdit" class="colmena-card-edit">
                                <div class="colmena-icon-edit">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <div class="colmena-name-edit" id="previewNombre">
                                    {{ $colmena->nombre ?: ('#' . $colmena->numero) }}
                                </div>
                                <div class="colmena-number-edit" id="previewNumero">#{{ $colmena->numero }}</div>
                            </div>
                        </div>

                        <div class="validation-errors" id="validationErrors" style="display: none;">
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span id="errorText"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="btnGuardarEdicion">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar color de la colmena -->
    <div class="modal fade" id="modalColorColmena" tabindex="-1" aria-labelledby="modalColorColmenaLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('colmenas.updateColor', [$apiario->id, $colmena->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalColorColmenaLabel">
                            <i class="fas fa-palette me-2"></i>
                            Editar color de la colmena
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Paleta de colores --}}
                        @php
                            $colores = [
                                '#ffc107' => 'Amarillo',
                                '#007bff' => 'Azul',
                                '#28a745' => 'Verde',
                                '#17a2b8' => 'Celeste',
                                '#dc3545' => 'Rojo',
                                '#6f42c1' => 'Morado',
                                '#343a40' => 'Negro',
                            ];

                            // Valor crudo desde la BD (puede ser nombre, hex sin #, o hex con #)
                            $rawColor = $colmena->color_etiqueta ?? '';

                            // Normalizar: si es nombre conocido, obtener el hex; si es hex sin '#', añadirla; si no es válido, dejar vacío.
                            $colorSeleccionado = '';
                            if ($rawColor) {
                                // buscar por nombre (case-insensitive)
                                $foundHex = null;
                                foreach ($colores as $hex => $nombre) {
                                    if (strcasecmp($rawColor, $nombre) === 0) {
                                        $foundHex = $hex;
                                        break;
                                    }
                                }

                                if ($foundHex) {
                                    $colorSeleccionado = $foundHex;
                                } else {
                                    // limpiar y validar hex (permitir con o sin '#')
                                    $candidate = ltrim($rawColor, '#');
                                    if (preg_match('/^[0-9a-fA-F]{6}$/', $candidate)) {
                                        $colorSeleccionado = '#' . strtolower($candidate);
                                    } else {
                                        // valor inválido para <input type="color"> -> ignorar y usar vacío (o fallback)
                                        $colorSeleccionado = '';
                                    }
                                }
                            }

                            $esPersonalizado = $colorSeleccionado !== '' && !array_key_exists($colorSeleccionado, $colores);
                        @endphp

                        <div class="color-palette">
                            @foreach ($colores as $hex => $nombre)
                                <label class="color-circle-label" title="{{ $nombre }}">
                                    <input type="radio" name="color_etiqueta" value="{{ $hex }}" {{ $colorSeleccionado == $hex ? 'checked' : '' }} style="display:none;">
                                    <span class="color-circle" style="background: {{ $hex }}">
                                        @if($colorSeleccionado == $hex)
                                            <span class="color-check">&#10003;</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach

                            {{-- Opción personalizado --}}
                            <label class="color-circle-label" title="Personalizado">
                                <input type="radio" name="color_etiqueta" value="personalizado" {{ $esPersonalizado ? 'checked' : '' }} style="display:none;">
                                <span class="color-circle"
                                    style="background: {{ $esPersonalizado ? $colorSeleccionado : '#ffc107' }}; position: relative;">
                                    <span class="color-custom-icon">&#9998;</span>
                                </span>
                            </label>
                        </div>
                        <div id="colorPersonalizadoDiv" class="mt-2"
                            style="display: {{ $esPersonalizado ? 'block' : 'none' }};">
                            <label for="color_personalizado" class="form-label">Selecciona tu color:</label>
                            <input type="color" name="color_personalizado" id="color_personalizado"
                                value="{{ $esPersonalizado ? $colorSeleccionado : '#ffc107' }}"
                                class="form-control form-control-color" style="width: 100%; height: 40px; border: none;">
                        </div>

                        <!-- Vista previa dinámica del color de la colmena -->
                        <div class="colmena-preview-container mb-3" style="display: flex; justify-content: center;">
                            <div id="colmenaPreview" class="colmena-card"
                                style="
                                                        background-color:
                                                            @if($colmena->color_etiqueta)
                                                                @php
                                                                    $hex = ltrim($colmena->color_etiqueta, '#');
                                                                    if (strlen($hex) === 6) {
                                                                        $r = hexdec(substr($hex, 0, 2));
                                                                        $g = hexdec(substr($hex, 2, 2));
                                                                        $b = hexdec(substr($hex, 4, 2));
                                                                        echo "rgba($r, $g, $b, 0.47)";
                                                                    } else {
                                                                        echo $colmena->color_etiqueta;
                                                                    }
                                                                @endphp
                                                            @else
                                                        transparent
                                                    @endif
                                                        ;
                                                        border-color: {{ $colmena->color_etiqueta ?? '#ccc' }};
                                                        width: 80px; height: 80px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: default;">
                                <div class="colmena-icon" style="font-size: 2rem;">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <div class="colmena-number" style="font-size: 1.1rem;">#{{ $colmena->numero }}</div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function hexToRgba(hex, alpha = 0.47) {
            hex = hex.replace('#', '');
            if (hex.length === 3) {
                hex = hex.split('').map(h => h + h).join('');
            }
            if (hex.length !== 6) return hex;
            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);
            return `rgba(${r},${g},${b},${alpha})`;
        }

        function updateColmenaPreview() {
            const radios = document.querySelectorAll('input[name="color_etiqueta"]');
            const colorInput = document.getElementById('color_personalizado');
            const preview = document.getElementById('colmenaPreview');
            let color = null;

            radios.forEach(function (radio) {
                if (radio.checked) {
                    color = radio.value;
                }
            });

            if (color === 'personalizado' && colorInput) {
                color = colorInput.value;
            }
            if (!color || color === '') {
                preview.style.backgroundColor = 'transparent';
                preview.style.borderColor = '#ccc';
            } else if (color.startsWith('#')) {
                preview.style.backgroundColor = hexToRgba(color, 0.47);
                preview.style.borderColor = color;
            } else {
                preview.style.backgroundColor = color;
                preview.style.borderColor = color;
            }
        }

        document.querySelectorAll('input[name="color_etiqueta"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (this.value === 'personalizado') {
                    document.getElementById('colorPersonalizadoDiv').style.display = 'block';
                } else {
                    document.getElementById('colorPersonalizadoDiv').style.display = 'none';
                }
                updateColmenaPreview();
            });
        });

        const colorInput = document.getElementById('color_personalizado');
        if (colorInput) {
            colorInput.addEventListener('input', function () {
                document.querySelector('input[name="color_etiqueta"][value="personalizado"]').checked = true;
                updateColmenaPreview();
            });
        }

        window.addEventListener('DOMContentLoaded', function () {
            updateColmenaPreview();
        });

        // Nuevo script para el modal de edición
        document.addEventListener('DOMContentLoaded', function () {
            const nombreInput = document.getElementById('edit_nombre');
            const numeroInput = document.getElementById('edit_numero');
            const previewNombre = document.getElementById('previewNombre');
            const previewNumero = document.getElementById('previewNumero');
            const validationErrors = document.getElementById('validationErrors');
            const errorText = document.getElementById('errorText');
            const btnGuardar = document.getElementById('btnGuardarEdicion');

            function updatePreview() {
                const nombre = nombreInput.value.trim();
                const numero = numeroInput.value;

                // Actualizar preview del nombre
                if (nombre) {
                    previewNombre.textContent = nombre;
                    previewNombre.style.display = 'block';
                } else {
                    previewNombre.style.display = 'none';
                }

                // Actualizar preview del número
                if (numero) {
                    previewNumero.textContent = '#' + numero;
                } else {
                    previewNumero.textContent = '#?';
                }
            }

            function validateForm() {
                const numero = numeroInput.value;
                let isValid = true;
                let errorMessage = '';

                // Validar número
                if (!numero || numero < 1) {
                    isValid = false;
                    errorMessage = 'El número de colmena debe ser mayor a 0.';
                }

                // Mostrar/ocultar errores
                if (!isValid) {
                    errorText.textContent = errorMessage;
                    validationErrors.style.display = 'block';
                    btnGuardar.disabled = true;
                    numeroInput.classList.add('error');
                } else {
                    validationErrors.style.display = 'none';
                    btnGuardar.disabled = false;
                    numeroInput.classList.remove('error');
                }

                return isValid;
            }

            // Event listeners
            nombreInput.addEventListener('input', updatePreview);
            numeroInput.addEventListener('input', function () {
                updatePreview();
                validateForm();
            });

            // Validación inicial
            updatePreview();
            validateForm();

            // Resetear formulario cuando se cierra el modal
            document.getElementById('modalEditarColmena').addEventListener('hidden.bs.modal', function () {
                validationErrors.style.display = 'none';
                numeroInput.classList.remove('error');
                btnGuardar.disabled = false;
            });
        });

        (function () {
            /**
             * Si el elemento activo está dentro de `modalEl` y el modal va a recibir aria-hidden="true",
             * mueve el foco al trigger almacenado o al body para evitar bloqueo de accesibilidad.
             */
            function ensureFocusMovedBeforeHide(modalEl) {
                // blur si el activo está dentro del modal
                const active = document.activeElement;
                if (active && modalEl.contains(active)) {
                    // buscar el trigger que abrió el modal (si existe)
                    const triggerSelector = '[data-bs-target="#' + modalEl.id + '"], [data-target="#' + modalEl.id + '"]';
                    const possibleTriggers = document.querySelectorAll(triggerSelector);
                    // priorizar el trigger que actualmente tiene datos de control aria-controls o el primero
                    let dest = null;
                    possibleTriggers.forEach(t => {
                        if (!dest) dest = t;
                    });
                    // mover foco al trigger o al body
                    if (dest && typeof dest.focus === 'function') {
                        dest.focus({ preventScroll: true });
                    } else {
                        // blur+focus body para asegurar que no queda foco en elemento oculto
                        active.blur && active.blur();
                        document.body.focus && document.body.focus({ preventScroll: true });
                    }
                }
            }

            // Observador para detectar cambios en aria-hidden en modales y actuar antes de que quede oculto.
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                // usar MutationObserver para detectar cuando aria-hidden cambia a "true"
                const mo = new MutationObserver(mutations => {
                    mutations.forEach(m => {
                        if (m.type === 'attributes' && m.attributeName === 'aria-hidden') {
                            const val = modal.getAttribute('aria-hidden');
                            if (val === 'true') {
                                // asegurar que ningún hijo con foco quede dentro del modal
                                ensureFocusMovedBeforeHide(modal);
                            }
                        }
                    });
                });
                mo.observe(modal, { attributes: true });
            });

            // También gestionar eventos de Bootstrap si están disponibles
            document.addEventListener('show.bs.modal', function (e) {
                const modal = e.target;
                // al mostrar, forzar foco dentro del modal (primer elemento focuseable)
                setTimeout(() => {
                    const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                    if (focusable && typeof focusable.focus === 'function') focusable.focus();
                }, 10);
            });

            document.addEventListener('hide.bs.modal', function (e) {
                // antes de que Bootstrap ponga aria-hidden, mover foco fuera si está dentro
                ensureFocusMovedBeforeHide(e.target);
            });
        })();
    </script>
@endsection