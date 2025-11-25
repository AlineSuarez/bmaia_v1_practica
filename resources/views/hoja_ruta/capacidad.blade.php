{{-- resources/views/hoja_ruta/capacidad.blade.php --}}
@extends('layouts.app')

@section('title', 'B-Maia - Capacidad de Carga')

@section('content')
    {{-- Submenú Hoja de Ruta --}}
    @include('hoja_ruta.partials.subnav')

    <div class="zonificacion-container">

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

    {{-- ESTILOS Y LIBRERÍAS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">

    <style>
        /* Layout dos columnas */
        .capacity-main-grid {
            margin-top: 22px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1.4fr);
            gap: 18px;
        }

        .capacity-form-card,
        .capacity-area-card {
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            padding: 18px 20px 20px;
        }

        .capacity-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 14px;
            color: #111827;
        }

        .capacity-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
        }

        .form-input {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            padding: 7px 10px;
            font-size: 0.9rem;
            color: #111827;
            background: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 1px rgba(249, 115, 22, 0.35);
            background: #ffffff;
        }

        .form-inline-options {
            display: flex;
            gap: 8px;
        }

        .radio-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            font-size: 0.85rem;
            color: #374151;
            background: #f9fafb;
        }

        .radio-pill input {
            display: none;
        }

        .radio-pill input:checked + span,
        .radio-pill span:hover {
            color: #ffffff;
            background: #f97316;
            border-radius: 999px;
            padding: 4px 10px;
        }

        .form-actions {
            margin-top: 6px;
        }

        .btn-primary-capacity {
            border: none;
            border-radius: 999px;
            padding: 8px 18px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ffffff;
            background: #f97316;
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease, background 0.1s ease;
        }

        .btn-primary-capacity:hover {
            background: #ea580c;
            box-shadow: 0 10px 25px rgba(248, 113, 22, 0.4);
            transform: translateY(-1px);
        }

        /* Panel Área de Pecoreo */
        .capacity-area-card {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .area-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
        }

        .area-label {
            border-radius: 999px;
            padding: 4px 10px;
            background: #fef3c7;
            color: #b45309;
        }

        .area-alert-icon {
            color: #f97316;
            font-size: 1rem;
        }

        .area-circle-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .area-circle {
            width: 170px;
            height: 170px;
            border-radius: 999px;
            border: 3px solid #ef4444;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            color: #ef4444;
        }

        .area-radius-text {
            position: absolute;
            top: 16px;
            right: 30px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .area-value {
            font-size: 1.4rem;
            font-weight: 800;
        }

        .area-details {
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 0.82rem;
            color: #4b5563;
        }

        .area-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .area-detail-label {
            font-weight: 500;
        }

        .area-detail-value {
            font-weight: 600;
            color: #111827;
            text-align: right;
        }

        .btn-colmenas-total {
            margin-top: 10px;
            width: 100%;
            border: none;
            border-radius: 0.5rem;
            padding: 10px 12px;
            background: #f97316;
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            text-align: center;
            transition: background 0.1s ease, transform 0.1s ease, box-shadow 0.1s ease;
        }

        .btn-colmenas-total:hover {
            background: #ea580c;
            transform: translateY(-1px);
            box-shadow: 0 12px 25px rgba(248, 113, 22, 0.4);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .capacity-main-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="{{ asset('./js/components/home-user/zonificacion.js') }}"></script>
@endsection

@section('optional-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const apiariosFijos      = @json($apiariosFijos ?? []);
            const apiariosBase       = @json($apiariosBase ?? []);
            const apiariosTemporales = @json($apiariosTemporales ?? []);
            const apiariosArchivados = @json($apiariosArchivados ?? []);

            const apiKey = 'e7898e26c93386e793bebfc5b7ead995';

            if (window.initZonificacion) {
                window.initZonificacion(
                    apiariosFijos,
                    apiariosBase,
                    apiariosTemporales,
                    apiariosArchivados,
                    apiKey
                );
            }
        });
    </script>
@endsection
