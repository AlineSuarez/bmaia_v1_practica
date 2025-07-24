@extends('layouts.app')

@section('title', 'Detalle de Colmena')

@section('content')
    @php
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
                                                <span
                                                    class="pcc-list-value">{{ $pcc7->signos_enfermedades_visibles ?? 'N/A' }}</span>
                                            </li>
                                            <li class="pcc-list-item">
                                                <span class="pcc-list-label">Reina presente:</span>
                                                <span
                                                    class="badge {{ $pcc7->presencia_reina ? 'badge-success' : 'badge-danger' }}">
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
                            @else
                                <span class="text-amber-600 font-bold flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Primero ingresa PCC 2 3, 4 o 5 en el Cuaderno de Campo
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
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
                        <div class="form-group mb-3">
                            <label for="color_etiqueta" class="form-label">Color etiqueta</label>
                            <select name="color_etiqueta" id="color_etiqueta" class="form-control mb-2">
                                @php
                                    $colores = [
                                        '' => 'Sin color',
                                        '#ffc107' => 'Amarillo',
                                        '#007bff' => 'Azul',
                                        '#28a745' => 'Verde',
                                        '#17a2b8' => 'Celeste',
                                        '#dc3545' => 'Rojo',
                                        '#6f42c1' => 'Morado',
                                        '#343a40' => 'Negro',
                                    ];
                                @endphp
                                <option value="" {{ ($colmena->color_etiqueta === null || $colmena->color_etiqueta === '') ? 'selected' : '' }}>
                                    Sin color (color por defecto)
                                </option>
                                @foreach ($colores as $hex => $nombre)
                                    @if($hex !== '')
                                        <option value="{{ $hex }}" {{ ($colmena->color_etiqueta ?? '') == $hex ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endif
                                @endforeach
                                <option value="personalizado" {{ (!empty($colmena->color_etiqueta) && !in_array($colmena->color_etiqueta, array_keys($colores))) ? 'selected' : '' }}>
                                    Personalizado
                                </option>
                            </select>
                            <div id="colorPersonalizadoDiv" class="mt-2" style="display: none;">
                                <label for="color_personalizado" class="form-label">Selecciona tu color:</label>
                                <input type="color" name="color_personalizado" id="color_personalizado"
                                    value="{{ (!in_array($colmena->color_etiqueta ?? '', array_keys($colores)) && !empty($colmena->color_etiqueta)) ? $colmena->color_etiqueta : '#ffc107' }}"
                                    class="form-control form-control-color"
                                    style="width: 100%; height: 40px; border: none;">
                            </div>
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
        function toggleColorPersonalizado() {
            const select = document.getElementById('color_etiqueta');
            const div = document.getElementById('colorPersonalizadoDiv');
            if (select.value === 'personalizado') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }
        document.getElementById('color_etiqueta').addEventListener('change', toggleColorPersonalizado);
        window.addEventListener('DOMContentLoaded', toggleColorPersonalizado);

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
            const select = document.getElementById('color_etiqueta');
            const colorInput = document.getElementById('color_personalizado');
            const preview = document.getElementById('colmenaPreview');
            let color = select.value;

            if (color === 'personalizado' && colorInput) {
                color = colorInput.value;
            }
            // Si no hay color seleccionado, muestra el color original y borde gris
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

        document.getElementById('color_etiqueta').addEventListener('change', function () {
            toggleColorPersonalizado();
            updateColmenaPreview();
        });

        document.getElementById('color_personalizado').addEventListener('input', updateColmenaPreview);

        window.addEventListener('DOMContentLoaded', function () {
            toggleColorPersonalizado();
            updateColmenaPreview();
        });
    </script>
@endsection