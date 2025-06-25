@extends('layouts.app')

@section('title', 'Historial de la Colmena')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/movimiento-colmena.css') }}" rel="stylesheet">
    </head>

    <div class="container">
        <!-- Header con información de la colmena -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="header-text">
                    <h1>Historial de Movimientos</h1>
                    <p class="header-subtitle">
                        Colmena #{{ $colmena->numero }} • Apiario "{{ $apiario->nombre }}"
                    </p>
                    <div class="colmena-info">
                        <span class="info-badge" style="background-color: {{ $colmena->color_etiqueta }};">
                            {{ $colmena->color_etiqueta }}
                        </span>
                        @if($colmena->estado_inicial)
                            <span class="info-badge estado">{{ $colmena->estado_inicial }}</span>
                        @endif
                        @if($colmena->numero_marcos)
                            <span class="info-badge marcos">{{ $colmena->numero_marcos }} marcos</span>
                        @endif
                        @php
                            $apiarioBase = null;
                            
                            if ($movimientos->isNotEmpty()) {
                                $primerMovimiento = $movimientos->last();
                                
                                $apiarioBase = $primerMovimiento->apiarioOrigen;
                                
                                if (!$apiarioBase) {
                                    $apiarioBase = $primerMovimiento->apiarioDestino;
                                }
                            } else {
                                $apiarioBase = $colmena->apiario;
                            }
                        @endphp
                        @if($apiarioBase && $apiarioBase->id !== $apiario->id)
                            <span class="info-badge origen">Extraída de: {{ $apiarioBase->nombre }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estadísticas útiles -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number">{{ $movimientos->count() }}</div>
                    <div class="stat-label">Total Movimientos</div>
                </div>
                @if($movimientos->isNotEmpty())
                    <div class="stat-card">
                        <div class="stat-number">{{ $movimientos->first()->fecha_movimiento->format('d/m/Y') }}</div>
                        <div class="stat-label">Último Movimiento</div>
                    </div>
                    @php
                        $apiariosVisitados = $movimientos->pluck('apiarioDestino.nombre')->unique()->filter()->count();
                    @endphp
                    <div class="stat-card">
                        <div class="stat-number">{{ $apiariosVisitados }}</div>
                        <div class="stat-label">Apiarios Visitados</div>
                    </div>
                    @php
                        $tiempoEnActual = $movimientos->first()->fecha_movimiento->diffForHumans(null, false, false, 2);
                        // Convertir a español
                        $tiempoEnActual = str_replace([
                            'seconds', 'second', 'minutes', 'minute', 'hours', 'hour', 
                            'days', 'day', 'weeks', 'week', 'months', 'month', 'years', 'year',
                            'ago', 'from now', 'before', 'after'
                        ], [
                            'segundos', 'segundo', 'minutos', 'minuto', 'horas', 'hora',
                            'días', 'día', 'semanas', 'semana', 'meses', 'mes', 'años', 'año',
                            '', 'en', 'antes', 'después'
                        ], $tiempoEnActual);
                    @endphp
                    <div class="stat-card">
                        <div class="stat-number">{{ $tiempoEnActual }}</div>
                        <div class="stat-label">En Ubicación Actual</div>
                    </div>
                @endif
            </div>
        </div>

        @if($movimientos->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>No hay movimientos registrados</h3>
                <p>Esta colmena aún no tiene historial de movimientos.</p>
                <div class="empty-actions">
                    <a href="{{ route('colmenas.show', [$apiario->id, $colmena->id]) }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver a la colmena
                    </a>
                </div>
            </div>
        @else
            <!-- Controles útiles -->
            <div class="controls-section">
                <div class="date-filters">
                    <label for="dateFrom">Desde:</label>
                    <input type="date" id="dateFrom" class="date-input">
                    <label for="dateTo">Hasta:</label>
                    <input type="date" id="dateTo" class="date-input">
                    <button id="clearDates" class="btn-clear">Limpiar</button>
                </div>

                <div class="action-buttons">
                    <button id="exportHistory" class="btn-export">
                        <i class="fas fa-download"></i> Exportar Historial
                    </button>
                    <button id="showRoute" class="btn-route">
                        <i class="fas fa-route"></i> Ver Ruta
                    </button>
                    <a href="{{ route('colmenas.show', [$apiario->id, $colmena->id]) }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Resumen de ubicaciones -->
            <div class="locations-summary">
                <h3>Resumen de Ubicaciones</h3>
                <div class="locations-grid">
                    @php
                        $ubicacionesConTiempo = $movimientos->groupBy('apiarioDestino.nombre')->map(function ($group, $nombre) {
                            $movs = $group->sortBy('fecha_movimiento');
                            $primer = $movs->first();
                            $ultimo = $movs->last();

                            return [
                                'nombre' => $nombre ?: 'Sin especificar',
                                'visitas' => $group->count(),
                                'primera_visita' => $primer->fecha_movimiento,
                                'ultima_visita' => $ultimo->fecha_movimiento,
                                'tiempo_total' => abs((int) $primer->fecha_movimiento->diffInDays($ultimo->fecha_movimiento))
                            ];
                        });
                    @endphp

                    @foreach($ubicacionesConTiempo as $ubicacion)
                        <div class="location-card">
                            <div class="location-name">{{ $ubicacion['nombre'] }}</div>
                            <div class="location-stats">
                                <span class="stat">{{ $ubicacion['visitas'] }} {{ $ubicacion['visitas'] == 1 ? 'visita' : 'visitas' }}</span>
                                <span class="stat">{{ $ubicacion['tiempo_total'] }} {{ $ubicacion['tiempo_total'] == 1 ? 'día' : 'días' }}</span>
                            </div>
                            <div class="location-dates">
                                <small>
                                    Primera: {{ $ubicacion['primera_visita']->format('d/m/Y') }}
                                    @if($ubicacion['visitas'] > 1)
                                        | Última: {{ $ubicacion['ultima_visita']->format('d/m/Y') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Timeline con información más útil -->
            <div class="timeline">
                @foreach($movimientos as $index => $mov)
                    @php
                        $siguienteMovimiento = $movimientos->get($index + 1);
                        $diasEnUbicacion = $siguienteMovimiento ?
                            abs((int) $mov->fecha_movimiento->diffInDays($siguienteMovimiento->fecha_movimiento)) :
                            abs((int) $mov->fecha_movimiento->diffInDays(now()));
                    @endphp

                    <div class="timeline-item" data-fecha="{{ $mov->fecha_movimiento->format('Y-m-d') }}">
                        <div class="timeline-marker">
                            <div class="marker-icon">
                                @if($index === 0)
                                    <i class="fas fa-map-marker-alt"></i>
                                @else
                                    <i class="fas fa-truck"></i>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-content">
                            <div class="movement-card">
                                <div class="card-header">
                                    <div class="movement-type">
                                        <span class="type-badge">
                                            @if($index === 0)
                                                Ubicación Actual
                                            @else
                                                Movimiento #{{ $movimientos->count() - $index }}
                                            @endif
                                        </span>
                                        <span class="movement-date">
                                            {{ $mov->fecha_movimiento->format('d/m/Y') }}
                                        </span>
                                        <span class="movement-time">
                                            {{ $mov->fecha_movimiento->format('H:i') }}
                                        </span>
                                        <span class="dias-ubicacion">
                                            {{ $diasEnUbicacion }} {{ $diasEnUbicacion == 1 ? 'día' : 'días' }} en esta ubicación
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="movement-info">
                                        <!-- Flujo de ubicaciones mejorado -->
                                        @if($mov->apiarioOrigen && $index > 0)
                                            <div class="location-flow">
                                                <div class="location-item">
                                                    <div class="location-label">Origen</div>
                                                    <div class="location-name">{{ $mov->apiarioOrigen->nombre }}</div>
                                                </div>
                                                <div class="flow-arrow">
                                                    <i class="fas fa-arrow-right"></i>
                                                </div>
                                                <div class="location-item">
                                                    <div class="location-label">Destino</div>
                                                    <div class="location-name">
                                                        {{ optional($mov->apiarioDestino)->nombre ?? 'No especificado' }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Ubicación principal -->
                                            <div class="location-main">
                                                <div class="location-current">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <div class="location-details">
                                                        <div class="location-name">
                                                            {{ optional($mov->apiarioDestino)->nombre ?? 'Ubicación no especificada' }}
                                                        </div>
                                                        @if($index === 0)
                                                            <div class="location-from">
                                                                Ubicación actual desde el {{ $mov->fecha_movimiento->format('d \d\e F \d\e Y') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Detalles del movimiento -->
                                        <div class="movement-details">
                                            @if($mov->motivo_movimiento)
                                                <div class="detail-item">
                                                    <i class="fas fa-clipboard-list"></i>
                                                    <span class="detail-label">Motivo:</span>
                                                    <span class="detail-value">{{ $mov->motivo_movimiento }}</span>
                                                </div>
                                            @endif

                                            @if($mov->observaciones)
                                                <div class="detail-item">
                                                    <i class="fas fa-sticky-note"></i>
                                                    <span class="detail-label">Observaciones:</span>
                                                    <span class="detail-value">{{ $mov->observaciones }}</span>
                                                </div>
                                            @endif

                                            @if($mov->transportista)
                                                <div class="detail-item">
                                                    <i class="fas fa-user"></i>
                                                    <span class="detail-label">Transportista:</span>
                                                    <span class="detail-value">{{ $mov->transportista }}</span>
                                                </div>
                                            @endif

                                            @if($mov->vehiculo)
                                                <div class="detail-item">
                                                    <i class="fas fa-car"></i>
                                                    <span class="detail-label">Vehículo:</span>
                                                    <span class="detail-value">{{ $mov->vehiculo }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- JavaScript mejorado -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filtro por fechas
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const clearDates = document.getElementById('clearDates');
            const timelineItems = document.querySelectorAll('.timeline-item');

            function filterByDate() {
                const from = dateFrom.value;
                const to = dateTo.value;

                timelineItems.forEach(item => {
                    const itemDate = item.getAttribute('data-fecha');
                    let show = true;

                    if (from && itemDate < from) show = false;
                    if (to && itemDate > to) show = false;

                    item.style.display = show ? 'block' : 'none';
                });
            }

            dateFrom?.addEventListener('change', filterByDate);
            dateTo?.addEventListener('change', filterByDate);

            clearDates?.addEventListener('click', function () {
                dateFrom.value = '';
                dateTo.value = '';
                timelineItems.forEach(item => {
                    item.style.display = 'block';
                });
            });

            // Exportar historial
            document.getElementById('exportHistory')?.addEventListener('click', function () {
                const apiarioId = {{ $apiario->id }};
                const colmenaId = {{ $colmena->id }};
                window.open(`/apiarios/${apiarioId}/colmenas/${colmenaId}/historial/export`, '_blank');
            });

            // Ver ruta en mapa (funcionalidad futura)
            document.getElementById('showRoute')?.addEventListener('click', function () {
                alert('Funcionalidad de mapa en desarrollo');
            });
        });
    </script>
@endsection