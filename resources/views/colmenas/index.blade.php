@extends('layouts.app')

@section('title', 'Colmenas del Apiario')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/colmenas.css') }}" rel="stylesheet">
    </head>

    <div class="main-layout">
        <div class="container">
            <div class="page-header">
                <div class="header-row">
                    <h1 class="page-title">Colmenas del Apiario</h1>
                    <div class="back-button-container">
                        <a href="{{ route('apiarios') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver a Apiarios</span>
                        </a>
                    </div>
                </div>
                <div class="apiario-info">{{ $apiario->nombre }}</div>
                <div class="apiario-stats">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-cube"></i></div>
                        <span>Total: {{ $colmenasPorApiarioBase->flatten()->count() }} colmenas</span>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <span>Tipo: {{ ucfirst($apiario->tipo_apiario) }}</span>
                    </div>
                    @if($apiario->es_temporal)
                        <div class="stat-item">
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                            <span>Temporal</span>
                        </div>
                    @endif
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                        <span>Apiarios Origen: {{ $colmenasPorApiarioBase->count() }}</span>
                    </div>
                </div>
            </div>

            @foreach($colmenasPorApiarioBase as $nombreBase => $colmenas)
                <div class="section-group">
                    @if($mostrarTitulos)
                        <h2 class="section-title">
                            <i class="fas fa-home mr-2"></i>{{ $nombreBase }}
                        </h2>
                    @endif

                    <div class="colmenas-container">
                        <div class="colmenas-info">
                            <div class="colmenas-count">
                                <strong>{{ $colmenas->count() }}</strong> colmenas en este grupo
                            </div>
                        </div>

                        <div class="colmenas-grid">
                            @forelse($colmenas as $colmena)

                                @php
                                    $url = route('colmenas.show', [
                                        'apiario' => $apiario->id,
                                        'colmena' => $colmena->id,
                                    ]);
                                    $color = $colmena->color_etiqueta ?? 'transparent'; // Color por defecto
                                    // Si el color es muy fuerte, puedes agregar transparencia
                                    if ($colmena->color_etiqueta) {
                                        // Ejemplo: convertir hex a rgba con opacidad 0.47
                                        $hex = ltrim($colmena->color_etiqueta, '#');
                                        if (strlen($hex) === 6) {
                                            $r = hexdec(substr($hex, 0, 2));
                                            $g = hexdec(substr($hex, 2, 2));
                                            $b = hexdec(substr($hex, 4, 2));
                                            $color = "rgba($r, $g, $b, 0.47)";
                                        } else {
                                            $color = $colmena->color_etiqueta;
                                        }
                                    } else {
                                        $color = 'transparent';
                                    }
                                @endphp

                                <div class="colmena-card"
                                    style="background-color: {{ $color }}; border-color: {{ $colmena->color_etiqueta ?? '#70707045' }}; --colmena-color: {{ $colmena->color_etiqueta ?? '#f5f5f5' }};"
                                    onclick="window.location='{{ $url }}'"
                                    data-tooltip="<img src='https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($url) }}&size=100x100'>">
                                    <div class="colmena-icon">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div class="colmena-number">#{{ $colmena->numero }}</div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div>No hay colmenas registradas para este grupo</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="{{ asset('js/components/home-user/colmenas.js') }}"></script>
@endsection