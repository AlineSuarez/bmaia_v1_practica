{{-- resources/views/hoja_ruta/capacidad.blade.php --}}
@extends('layouts.app')

@section('title', 'B-Maia - Capacidad de Carga')

@section('content')
    {{-- Submenú Hoja de Ruta --}}
    @include('hoja_ruta.partials.subnav')

    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/zonificacion.css') }}">

    {{-- ✅ Estilos específicos de Capacidad de Carga --}}
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/hoja-ruta-capacidad.css') }}">

    {{-- ESTILOS Y LIBRERÍAS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="{{ asset('./js/components/home-user/zonificacion.js') }}"></script>

    <div class="zonificacion-container"
         id="capacity-root"
         data-apiarios-fijos='@json($apiariosFijos ?? [])'
         data-apiarios-base='@json($apiariosBase ?? [])'
         data-apiarios-temporales='@json($apiariosTemporales ?? [])'
         data-apiarios-archivados='@json($apiariosArchivados ?? [])'
         data-api-key="e7898e26c93386e793bebfc5b7ead995">

        {{-- BANNER HONEYCOMB --}}
        <div class="honeycomb-header">
            <div class="honeycomb-overlay"></div>
            <div class="header-content">
                <h1 class="zonificacion-title">Capacidad de Carga</h1>
                <p class="zonificacion-subtitle">
                    Calcula y gestiona la capacidad máxima de tus vehículos para optimizar
                    la planificación de la Hoja de Ruta y tus recorridos apícolas.
                </p>
            </div>
        </div>

        {{-- STATS ARRIBA (opcionales) --}}
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Apiarios</h3>
                    <p class="stat-value">
                        {{ count($apiariosBase ?? []) + count($apiariosTemporales ?? []) + count($apiariosFijos ?? []) }}
                    </p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <div class="stat-content">
                    <h3>Apiarios Base</h3>
                    <p class="stat-value">{{ count($apiariosBase ?? []) }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Apiarios Temporales</h3>
                    <p class="stat-value">{{ count($apiariosTemporales ?? []) }}</p>
                </div>
            </div>
        </div>

        {{-- ====== LAYOUT PRINCIPAL (FORM + PANEL DERECHA) ====== --}}
        <div class="capacity-main-grid">
            {{-- Columna izquierda: Región / Zona / Camino --}}
            <div class="capacity-form-card">
                <h2 class="capacity-title">Planifica tu Hoja de Ruta</h2>

                <form class="capacity-form" action="#" method="GET">
                    {{-- Región --}}
                    <div class="form-row">
                        <label for="region" class="form-label">Región</label>
                        <select id="region" name="region" class="form-input">
                            <option value="">Selecciona una región</option>
                            <option value="maule">Región del Maule</option>
                            <option value="ñuble">Región de Ñuble</option>
                            <option value="biobio">Región del Biobío</option>
                            <option value="araucania">Región de La Araucanía</option>
                            {{-- Más regiones después si quieres --}}
                        </select>
                    </div>

                    {{-- Zona --}}
                    <div class="form-row">
                        <label for="zona" class="form-label">Zona</label>
                        <select id="zona" name="zona" class="form-input">
                            <option value="">Selecciona zona</option>
                            <option value="costa">Costa</option>
                            <option value="precordillera" selected>Precordillera</option>
                            <option value="interior">Valle / Interior</option>
                            <option value="cordillera">Cordillera</option>
                        </select>
                    </div>

                    {{-- Camino Sí/No --}}
                    <div class="form-row">
                        <span class="form-label">Camino</span>
                        <div class="form-inline-options">
                            <label class="radio-pill">
                                <input type="radio" name="camino" value="si" checked>
                                <span>Si</span>
                            </label>
                            <label class="radio-pill">
                                <input type="radio" name="camino" value="no">
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    {{-- Botón de acción (por ahora solo visual) --}}
                    <div class="form-actions">
                        <button type="button" class="btn-primary-capacity">
                            Calcular Capacidad de Carga
                        </button>
                    </div>
                </form>
            </div>

            {{-- Columna derecha: Área de Pecoreo --}}
            <div class="capacity-area-card">
                <div class="area-header">
                    <span class="area-label">Área de pecoreo: 3 km</span>
                    <i class="fa-solid fa-exclamation-circle area-alert-icon"></i>
                </div>

                <div class="area-circle-wrapper">
                    <div class="area-circle">
                        <div class="area-radius-text">3 km</div>
                        <div class="area-value">2.827 Ha</div>
                    </div>
                </div>

                <div class="area-details">
                    <div class="area-row">
                        <span class="area-detail-label">Capacidad de Carga</span>
                        <span class="area-detail-value">13,65 colmenas/km²</span>
                    </div>
                    <div class="area-row">
                        <span class="area-detail-label">Área cubierta</span>
                        <span class="area-detail-value">28,27 km² = 2.827 hectáreas</span>
                    </div>
                </div>

                <button type="button" class="btn-colmenas-total">
                    385 Colmenas
                </button>
            </div>
        </div>

        {{-- ====== MAPA CON APIARIOS (debajo) ====== --}}
        <div class="map-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fa-solid fa-map"></i>
                    Apiarios para Capacidad de Carga
                </h2>
                <div class="map-controls">
                    <div class="map-filter">
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-others">
                            <span class="slider"></span>
                        </label>
                        <span class="toggle-label">Activar Radio</span>
                    </div>
                </div>
            </div>

            <div id="map-container" style="position:relative;">
                <div id="map" class="apiary-map"></div>

                <button id="toggle-legend" class="legend-toggle-btn" title="Mostrar/Ocultar leyenda">
                    <i class="fa-solid fa-book-open"></i>
                </button>

                <div class="map-legend">
                    <h4>Leyenda</h4>
                    <div class="legend-item">
                        <span class="legend-color" style="background:#3498db;"></span>
                        <span>Apiarios Base</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background:#27ae60;"></span>
                        <span>Apiarios Temporales</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background:#ff0000;"></span>
                        <span>Apiarios Archivados</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/hoja_ruta/capacidad.js') }}"></script>
@endpush
