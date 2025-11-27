{{-- resources/views/hoja_ruta/explorador.blade.php --}}
@extends('layouts.app')

@section('title', 'Hoja de ruta • Explorador')

@section('content')
    @include('hoja_ruta.partials.subnav')

    {{-- Fuente + estilos compartidos del módulo (banner honeycomb, etc.) --}}
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/zonificacion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/hoja-ruta-explorador.css') }}">

    <div class="zonificacion-container">

        {{-- ========== BANNER HONEYCOMB (MANTENIDO) ========== --}}
        <div class="honeycomb-header">
            <div class="honeycomb-overlay"></div>
            <div class="header-content">
                <h1 class="zonificacion-title">Explorador de Zonas</h1>
                <p class="zonificacion-subtitle">
                    Mapa interactivo de regiones: pasa el mouse sobre una zona para ver su nombre.
                </p>
            </div>
        </div>

        {{-- ========== CONTENIDO PRINCIPAL: MAPA + ESPACIO DERECHO ========== --}}
        <div class="explorador-grid">
            {{-- Columna izquierda: mapa SVG --}}
            <section class="explorador-map-wrapper">
                <div class="explorador-map-title">Mapa de Chile (SVG propio)</div>
                <p class="explorador-map-subtitle">
                    Archivo <code>chile.svg</code> cargado desde tu carpeta
                    <code>public/img/maps</code>.
                </p>

                <div class="explorador-map-inner" id="mapContainer">

                    {{-- Región actual sobre el mapa --}}
                    <div class="map-region-pill" id="mapRegionPill">
                        <div class="map-region-label">Región actual</div>
                        <div class="map-region-name" id="regionName">
                            Pasa el cursor sobre una región del mapa
                        </div>
                        <div class="map-region-code" id="regionCode">
                            ID: —
                        </div>
                    </div>

                    {{-- Tooltip flotante --}}
                    <div id="mapTooltip" class="map-tooltip">Región</div>

                    {{-- IMPORTANTE: onload llama a initChileMap(this) --}}
                    <object
                        id="chileMap"
                        data="{{ asset('img/maps/chile.svg') }}"
                        type="image/svg+xml"
                        aria-label="Mapa de Chile"
                        onload="initChileMap(this)">
                    </object>

                    <div class="map-legend">
                        <span>Tip:</span> pasa el mouse sobre las zonas para ver el nombre de la región.
                    </div>
                </div>
            </section>

            {{-- Columna derecha: futuro panel de datos --}}
            <section class="explorador-side-card">
                <h2>Panel de datos de región</h2>
                <p>
                    Aquí podremos mostrar resúmenes de flora, apiarios, clima u otros datos
                    de la región seleccionada en el mapa. Lo configuramos en el siguiente paso.
                </p>
            </section>
        </div>
    </div> {{-- /zonificacion-container --}}
@endsection

@push('scripts')
    <script src="{{ asset('js/hoja_ruta/explorador.js') }}"></script>
@endpush
