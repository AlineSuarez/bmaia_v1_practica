@extends('layouts.app')
@section('title', 'B-Maia - Zonificación')
@section('content')
    <div class="zonificacion-container">
        <!-- Header con efecto panal -->
        <div class="honeycomb-header">
            <div class="honeycomb-overlay"></div>
            <div class="header-content">
                <h1 class="zonificacion-title">Zonificación de Apiarios</h1>
                <p class="zonificacion-subtitle">Visualiza y gestiona la distribución geográfica de tus colmenas</p>
            </div>
        </div>

        <!-- Dashboard Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Apiarios</h3>
                    <p class="stat-value">{{ count($apiariosFijos) + count($apiariosBase) + count($apiariosTemporales) }}
                    </p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-home"></i>
                </div>
                <div class="stat-content">
                    <h3>Apiarios Fijos</h3>
                    <p class="stat-value">{{ count($apiariosFijos) }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <div class="stat-content">
                    <h3>Apiarios Base</h3>
                    <p class="stat-value">{{ count($apiariosBase) }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Apiarios Temporales</h3>
                    <p class="stat-value">{{ count($apiariosTemporales) }}</p>
                </div>
            </div>
        </div>

        <!-- Mapa con controles mejorados -->
        <div class="map-section">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-map"></i> Mapa de Apiarios</h2>
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
                        <span class="legend-color" style="background:#f0941b;"></span>
                        <span>Apiarios Fijos</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background:#3498db;"></span>
                        <span>Apiarios Base</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background:#27ae60;"></span>
                        <span>Apiarios Temporales</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Apiarios por Categorías -->
        <div class="apiary-info-section">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-table-cells"></i> Información de Apiarios por Categoría</h2>
                <div class="view-controls">
                    <button class="view-btn active" data-view="table">
                        <i class="fa-solid fa-table-list" style="margin-right: 0.5rem;"></i> Tabla
                    </button>
                    <button class="view-btn" data-view="cards">
                        <i class="fa-solid fa-grip" style="margin-right: 0.5rem;"></i> Tarjetas
                    </button>
                </div>
            </div>

            <!-- Sistema de pestañas -->
            <div class="tabs-container">
                <div class="tabs-nav">
                    <button class="tab-btn active" data-tab="fijos" data-tooltip="Api. Fijos">
                        <i class="fa-solid fa-home"></i>
                        <span class="tab-text">Fijos</span>
                        <span class="tab-count">({{ count($apiariosFijos) }})</span>
                    </button>
                    <button class="tab-btn" data-tab="base" data-tooltip="Api. Base">
                        <i class="fa-solid fa-truck"></i>
                        <span class="tab-text">Base</span>
                        <span class="tab-count">({{ count($apiariosBase) }})</span>
                    </button>
                    <button class="tab-btn" data-tab="temporales" data-tooltip="Api. Temporales">
                        <i class="fa-solid fa-clock"></i>
                        <span class="tab-text">Temporales</span>
                        <span class="tab-count">({{ count($apiariosTemporales) }})</span>
                    </button>
                </div>

                <!-- Vista de tabla -->
                <div class="view-container table-view active">
                    <!-- TAB FIJOS -->
                    <div class="tab-content active" data-tab="fijos">
                        <div class="table-responsive">
                            <table class="apiary-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Ubicación</th>
                                        <th>Colmenas</th>
                                        <th>Fotografía</th>
                                        <th>Temperatura</th>
                                        <th>Humedad</th>
                                        <th>Clima</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- El contenido será generado por JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB BASE -->
                    <div class="tab-content" data-tab="base">
                        <div class="table-responsive">
                            <table class="apiary-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Ubicación</th>
                                        <th>Colmenas</th>
                                        <th>Fotografía</th>
                                        <th>Temperatura</th>
                                        <th>Humedad</th>
                                        <th>Clima</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- El contenido será generado por JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB TEMPORALES -->
                    <div class="tab-content" data-tab="temporales">
                        <div class="table-responsive">
                            <table class="apiary-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Ubicación</th>
                                        <th>Colmenas</th>
                                        <th>Temperatura</th>
                                        <th>Humedad</th>
                                        <th>Clima</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- El contenido será generado por JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Vista de tarjetas -->
                <div class="view-container cards-view">
                    <!-- TAB FIJOS -->
                    <div class="tab-content active" data-tab="fijos">
                        <div class="apiary-cards">
                            <!-- El contenido será generado por JavaScript -->
                        </div>
                    </div>

                    <!-- TAB BASE -->
                    <div class="tab-content" data-tab="base">
                        <div class="apiary-cards">
                            <!-- El contenido será generado por JavaScript -->
                        </div>
                    </div>

                    <!-- TAB TEMPORALES -->
                    <div class="tab-content" data-tab="temporales">
                        <div class="apiary-cards">
                            <!-- El contenido será generado por JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css"> -->
    <link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="{{ asset('./js/components/home-user/zonificacion.js') }}"></script>
@endsection

@section('optional-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const apiariosFijos = @json($apiariosFijos);
            const apiariosBase = @json($apiariosBase);
            const apiariosTemporales = @json($apiariosTemporales);
            const apiariosArchivados = @json($apiariosArchivados);
            const apiKey = 'e7898e26c93386e793bebfc5b7ead995';
            window.initZonificacion(apiariosFijos, apiariosBase, apiariosTemporales, apiariosArchivados, apiKey);
        });
    </script>
@endsection