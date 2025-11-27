@extends('layouts.app')
@section('title', 'B-Maia - Detalle Apiario')

@section('content')
<div class="zonificacion-container">
    <!-- Header con efecto panal -->
    <div class="honeycomb-header">
        <div class="honeycomb-overlay"></div>
        <div class="header-content">
            <h1 class="zonificacion-title">{{ $apiario->nombre ?? 'Apiario' }}</h1>
            <p class="zonificacion-subtitle">Detalle del apiario seleccionado</p>
        </div>
    </div>

    <!-- Chips resumen básicos -->
    <div class="stats-container" style="margin-top:14px;">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-house-chimney"></i></div>
            <div class="stat-content">
                <h3>Apiario</h3>
                <p class="stat-value">{{ $apiario->nombre ?? '—' }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="stat-content">
                <h3>Colmenas</h3>
                <p class="stat-value">{{ $apiario->num_colmenas ?? '—' }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="stat-content">
                <h3>Coordenadas</h3>
                <p class="stat-value">
                    @if(!is_null($apiario->latitud) && !is_null($apiario->longitud))
                        {{ number_format((float)$apiario->latitud, 6) }},
                        {{ number_format((float)$apiario->longitud, 6) }}
                    @else
                        Sin ubicación registrada
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Mapa sencillo -->
    <div class="map-section" style="margin-top:20px;">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fa-solid fa-map"></i> Ubicación del Apiario
            </h2>
        </div>

        <div id="map" style="height:480px; border-radius:12px; overflow:hidden;"></div>
    </div>
</div>

{{-- Estilos / librerías necesarias --}}
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet"
      href="{{ asset('css/components/home-user/zonificacion.css') }}">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ============ MAPA MUY SIMPLE ============
document.addEventListener('DOMContentLoaded', function () {
    const latRaw = @json($apiario->latitud);
    const lonRaw = @json($apiario->longitud);

    const lat = Number(latRaw);
    const lon = Number(lonRaw);
    const hasCoords = Number.isFinite(lat) && Number.isFinite(lon);

    const map = L.map('map', { zoomControl: true });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    if (hasCoords) {
        const beeIcon = L.icon({
            iconUrl: '/img/apiario.webp',
            iconSize: [38, 38],
            iconAnchor: [19, 19],
            popupAnchor: [0, -16]
        });

        L.marker([lat, lon], { icon: beeIcon })
            .addTo(map)
            .bindPopup(`<b>{{ $apiario->nombre }}</b><br>{{ $apiario->num_colmenas ?? '—' }} colmenas`)
            .openPopup();

        map.setView([lat, lon], 15);
    } else {
        // Centro genérico en Chile
        map.setView([-33.45, -70.6667], 6);
    }
});
</script>
@endsection
