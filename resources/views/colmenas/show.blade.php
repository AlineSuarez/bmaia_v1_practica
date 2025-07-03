@extends('layouts.app')

@section('title', 'Detalle de Colmena')

@section('content')
        @php
            // PCC1–2: sistema experto (vienen como $pcc1, $pcc2)
            // PCC3–5: cuaderno de campo (vienen como $pcc3, $pcc4, $pcc5)
            // PCC6–7: sistema experto (vienen como $pcc6, $pcc7)
            $hasAny = $pcc1 || $pcc2 || $pcc3 || $pcc4 || $pcc5 || $pcc6 || $pcc7;
        @endphp

        <head>
            <link href="{{ asset('./css/components/home-user/show/show-colmenas.css') }}" rel="stylesheet">
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
                                Colmena #{{ $colmena->numero }}
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

                                <div class="mb-4 flex gap-3">
                                    <a href="{{ route('colmenas.qr-pdf', [$apiario->id, $colmena->id]) }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-print"></i> Imprimir QR
                                    </a>
                                    <a href="{{ route('colmenas.historial', [$apiario->id, $colmena->id]) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-history"></i> Ver historial
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

                                    @if($colmena->numero_marcos)
                                        <div class="info-item" style="--index: 2">
                                            <div class="info-icon"
                                                style="background: linear-gradient(135deg, var(--color-amber-500), var(--color-amber-600));">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Marcos:</span>
                                                <span class="info-value">{{ $colmena->numero_marcos }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($colmena->estado_inicial)
                                        <div class="info-item" style="--index: 3">
                                            <div class="info-icon secondary">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <div class="info-content">
                                                <span class="info-label">Estado inicial:</span>
                                                <span class="badge badge-light">{{ $colmena->estado_inicial }}</span>
                                            </div>
                                        </div>
                                    @endif

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

                                @if($colmena->observaciones)
                                    <div class="observations-section">
                                        <div class="observations-content">
                                            <div class="flex items-center gap-2 mb-3">
                                                <i class="fas fa-sticky-note" style="color: var(--color-amber-600);"></i>
                                                <strong style="color: var(--color-amber-800);">Observaciones:</strong>
                                            </div>
                                            <p style="color: var(--color-amber-700); font-size: var(--font-sm); margin: 0;">
                                                {{ $colmena->observaciones }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Estadísticas -->
                                <div class="stats-section">
                                    <h6
                                        style="margin: 0; font-weight: 700; font-size: var(--font-base); display: flex; align-items: center; gap: var(--spacing-2);">
                                        <i class="fas fa-chart-bar"></i> Estadísticas
                                    </h6>

                                    <div class="stats-grid">
                                        <div class="stat-item">
                                            <div class="stat-value">{{ $pccCount }}</div>
                                            <div class="stat-label">Evaluaciones PCC</div>
                                        </div>

                                        @php
                                            // diffInDays() YA devuelve un entero, pero por si acaso lo casteamos
                                            $dias = $lastFecha
                                                ? (int) \Carbon\Carbon::parse($lastFecha)->diffInDays()
                                                : 0;
                                        @endphp

                                        <div class="stat-item">
                                            <div class="stat-value">{{ $dias }}</div>
                                            <div class="stat-label">Días desde última evaluación</div>
                                        </div>
                                    </div>
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
                                            <i class="fas fa-baby"></i>
                                        </div>
                                        <h6 class="pcc-title">PCC1 – Desarrollo Cámara de Cría</h6>
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
                                                    <span class="pcc-list-value">{{ $pcc1->presencia_zanganos ? 'Sí' : 'No' }}</span>
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
                                        <h6 class="pcc-title">PCC2 – Calidad de la Reina</h6>
                                    </div>
                                    <div class="pcc-content">
                                        @if($pcc2)
                                            <ul class="pcc-list">
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Postura reina:</span>
                                                    <span class="pcc-list-value">{{ $pcc2->postura_reina }}</span>
                                                </li>
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Estado cría:</span>
                                                    <span class="pcc-list-value">{{ $pcc2->estado_cria }}</span>
                                                </li>
                                                @if($pcc2->fecha_introduccion)
                                                    <li class="pcc-list-item">
                                                        <span class="pcc-list-label">Fecha introducción:</span>
                                                        <span class="pcc-list-value">
                                                            {{ \Carbon\Carbon::parse($pcc2->fecha_introduccion)->format('d/m/Y') }}
                                                        </span>
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
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                        <h6 class="pcc-title">PCC3 – Estado Nutricional</h6>
                                    </div>
                                    <div class="pcc-content">
                                        @if($pcc3)
                                            <ul class="pcc-list">
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Objetivo:</span>
                                                    <span class="pcc-list-value">{{ $pcc3->objetivo }}</span>
                                                </li>
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Tipo alimentación:</span>
                                                    <span class="pcc-list-value">{{ $pcc3->tipo_alimentacion }}</span>
                                                </li>
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Insumo utilizado:</span>
                                                    <span class="pcc-list-value">{{ $pcc3->insumo_utilizado }}</span>
                                                </li>
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
                                        <h6 class="pcc-title">PCC4 – Varroa</h6>
                                    </div>
                                    <div class="pcc-content">
                                        @if($pcc4)
                                            <ul class="pcc-list">
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Diagnóstico visual:</span>
                                                    <span class="pcc-list-value">{{ $pcc4->diagnostico_visual }}</span>
                                                </li>
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Método diagnóstico:</span>
                                                    <span class="pcc-list-value">{{ $pcc4->metodo_diagnostico }}</span>
                                                </li>
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Tratamiento:</span>
                                                    <span class="pcc-list-value">{{ $pcc4->tratamiento }}</span>
                                                </li>
                                                @if($pcc4->fecha_aplicacion)
                                                    <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Fecha aplicación:</span>
                                                    <span class="pcc-list-value">
                                                        {{ \Carbon\Carbon::parse($pcc4->fecha_aplicacion)->format('d/m/Y') }}
                                                    </span>
                                                    </li>
                                                @endif
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
                                            <i class="fas fa-microscope"></i>
                                        </div>
                                        <h6 class="pcc-title">PCC5 – Nosemosis</h6>
                                    </div>
                                    <div class="pcc-content">
                                        @if($pcc5)
                                            <ul class="pcc-list">
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Signos clínicos:</span>
                                                    <span class="pcc-list-value">{{ $pcc5->signos_clinicos }}</span>
                                                </li>
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Método diagnóstico lab.:</span>
                                                    <span class="pcc-list-value">{{ $pcc5->metodo_diagnostico_laboratorio }}</span>
                                                </li>
                                                @if($pcc5->fecha_aplicacion)
                                                    <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Fecha aplicación:</span>
                                                    <span class="pcc-list-value">
                                                        {{ \Carbon\Carbon::parse($pcc5->fecha_aplicacion)->format('d/m/Y') }}
                                                    </span>
                                                    </li>
                                                @endif
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
                                            <i class="fas fa-tractor"></i>
                                        </div>
                                        <h6 class="pcc-title">PCC6 – Índice de Cosecha</h6>
                                    </div>
                                    <div class="pcc-content">
                                        @if($pcc6)
                                            <ul class="pcc-list">
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Madurez miel:</span>
                                                    <span class="pcc-list-value">{{ $pcc6->madurez_miel }}</span>
                                                </li>
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Alzas promedio:</span>
                                                    <span class="pcc-list-value">{{ $pcc6->num_alzadas }}</span>
                                                </li>
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
                                        <h6 class="pcc-title">PCC7 – Preparación Invernada</h6>
                                    </div>
                                    <div class="pcc-content">
                                        @if($pcc7)
                                            <ul class="pcc-list">
                                                @if($pcc7->fecha_cierre_temporada)
                                                    <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Fecha cierre temporada:</span>
                                                    <span class="pcc-list-value">
                                                        {{ \Carbon\Carbon::parse($pcc7->fecha_cierre_temporada)->format('d/m/Y') }}
                                                    </span>
                                                    </li>
                                                @endif
                                                @if($pcc7->fecha_ultima_revision_previa_receso)
                                                    <li class="pcc-list-item">
                                                        <span class="pcc-list-label">Última revisión previa receso:</span>
                                                        <span class="pcc-list-value">
                                                        {{ \Carbon\Carbon::parse($pcc7->fecha_ultima_revision_previa_receso)->format('d/m/Y') }}
                                                        </span>
                                                    </li>
                                                @endif
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Signos enfermedades:</span>
                                                    <span class="pcc-list-value">{{ $pcc7->signos_enfermedades_visibles ?? 'N/A' }}</span>
                                                </li>
                                                <li class="pcc-list-item">
                                                    <span class="pcc-list-label">Reina presente:</span>
                                                    <span class="badge {{ $pcc7->presencia_reina ? 'badge-success' : 'badge-danger' }}">
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
                                    // Sacamos la visita asociada (cualquiera de PCC3–5)
                                    $visitaId = optional($pcc3)->visita_id
                                            ?? optional($pcc4)->visita_id
                                            ?? optional($pcc5)->visita_id;
                                @endphp

                                @if($visitaId)
                                    <a href="{{ route('visitas.pcc.edit', ['visita' => $visitaId]) }}?colmena={{ $colmena->id }}"
                                    class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Editar PCC
                                    </a>
                                @else
                                    <span class="text-amber-600 font-bold flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Primero ingresa PCC 3, 4 o 5 en el Cuaderno de Campo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection