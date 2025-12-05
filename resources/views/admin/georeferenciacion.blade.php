@extends('layouts.admin')

@section('title', 'Georeferenciación - Admin')
@section('page-title', 'Georeferenciación de Apiarios')

@section('content')
<style>
    #map {
        height: 600px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .stats-card h5 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .region-item {
        padding: 10px;
        border-bottom: 1px solid #ecf0f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .region-item:last-child {
        border-bottom: none;
    }

    .map-controls {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .filter-btn {
        padding: 8px 16px;
        border-radius: 20px;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #667eea;
        color: white;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="map-controls">
            <h5><i class="fas fa-filter"></i> Filtros</h5>
            <button class="filter-btn active" onclick="filtrarTodos()">
                <i class="fas fa-globe"></i> Todos ({{ $apiarios->count() }})
            </button>
            <button class="filter-btn" onclick="filtrarBase()">
                <i class="fas fa-home"></i> Base ({{ $apiarios->where('es_temporal', false)->count() }})
            </button>
            <button class="filter-btn" onclick="filtrarTemporales()">
                <i class="fas fa-truck"></i> Temporales ({{ $apiarios->where('es_temporal', true)->count() }})
            </button>
        </div>
    </div>
</div>

<div class="row">
    <!-- Mapa -->
    <div class="col-md-8">
        <div id="map"></div>
    </div>

    <!-- Estadísticas -->
    <div class="col-md-4">
        <div class="stats-card">
            <h5><i class="fas fa-chart-bar"></i> Apiarios por Región</h5>
            @foreach($apiariosPorRegion as $region)
            <div class="region-item">
                <span>{{ $region->region }}</span>
                <span class="badge bg-primary rounded-pill">{{ $region->total }}</span>
            </div>
            @endforeach
        </div>

        <div class="stats-card">
            <h5><i class="fas fa-info-circle"></i> Información General</h5>
            <div class="region-item">
                <span><i class="fas fa-warehouse text-primary"></i> Total de Apiarios</span>
                <strong>{{ $apiarios->count() }}</strong>
            </div>
            <div class="region-item">
                <span><i class="fas fa-cube text-info"></i> Total de Colmenas</span>
                <strong>{{ $apiarios->sum('num_colmenas') }}</strong>
            </div>
            <div class="region-item">
                <span><i class="fas fa-users text-success"></i> Total de Usuarios</span>
                <strong>{{ $totalUsuarios }}</strong>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let map;
    let markers = [];
    const apiarios = @json($apiarios);

    // Inicializar mapa
    function initMap() {
        // Centrar en Chile
        map = L.map('map').setView([-33.4489, -70.6693], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        // Agregar todos los marcadores
        mostrarApiarios(apiarios);
    }

    function mostrarApiarios(data) {
        // Limpiar marcadores existentes
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        data.forEach(apiario => {
            if (apiario.latitud && apiario.longitud) {
                const icon = L.icon({
                    iconUrl: '/img/apiario.webp',
                    iconSize: [38, 38],
                    iconAnchor: [19, 38],
                    popupAnchor: [0, -38]
                });

                const marker = L.marker([apiario.latitud, apiario.longitud], { icon })
                    .addTo(map)
                    .bindPopup(`
                        <div style="min-width: 200px;">
                            <h6 style="margin: 0 0 10px 0; color: #2c3e50;">
                                <i class="fas fa-warehouse"></i> ${apiario.nombre || 'Sin nombre'}
                            </h6>
                            <p style="margin: 5px 0; font-size: 0.9rem;">
                                <i class="fas fa-user"></i> <strong>Usuario:</strong> ${apiario.user?.name || 'N/A'}
                            </p>
                            <p style="margin: 5px 0; font-size: 0.9rem;">
                                <i class="fas fa-cube"></i> <strong>Colmenas:</strong> ${apiario.num_colmenas || 0}
                            </p>
                            <p style="margin: 5px 0; font-size: 0.9rem;">
                                <i class="fas fa-${apiario.es_temporal ? 'truck' : 'home'}"></i>
                                <strong>Tipo:</strong> ${apiario.es_temporal ? 'Temporal' : 'Base'}
                            </p>
                            ${apiario.direccion ? `
                                <p style="margin: 5px 0; font-size: 0.9rem;">
                                    <i class="fas fa-map-marker-alt"></i> ${apiario.direccion}
                                </p>
                            ` : ''}
                        </div>
                    `);

                markers.push(marker);
            }
        });

        // Ajustar vista para mostrar todos los marcadores
        if (markers.length > 0) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    function filtrarTodos() {
        mostrarApiarios(apiarios);
        actualizarBotones(0);
    }

    function filtrarBase() {
        const base = apiarios.filter(a => !a.es_temporal);
        mostrarApiarios(base);
        actualizarBotones(1);
    }

    function filtrarTemporales() {
        const temporales = apiarios.filter(a => a.es_temporal);
        mostrarApiarios(temporales);
        actualizarBotones(2);
    }

    function actualizarBotones(index) {
        document.querySelectorAll('.filter-btn').forEach((btn, i) => {
            btn.classList.toggle('active', i === index);
        });
    }

    // Inicializar cuando el documento esté listo
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
    });
</script>
@endpush
@endsection
