@extends('layouts.app')
@section('title', 'Maia - Zonificación')
@section('content')
<div class="container">
    <h1 class="mb-4">Zonificación de Apiarios</h1>

    <div id="map-container" style="position: relative;">
        <div id="map" style="height: 500px; border-radius: 8px; overflow: hidden;" class="mb-4 shadow-lg"></div>

        <!-- Botón de simbología -->
        <button id="toggle-legend" onclick="toggleLegend()" style="position: absolute; top: 55px; left: 50px; z-index: 1000; background-color: #f0941b; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">
            Simbología
        </button>

        <!-- Leyenda -->
        <div id="legend" style="display: none; position: absolute; top: 50px; left: 10px; background-color: white; padding: 10px; border-radius: 8px; font-size: 0.9rem; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <strong style="color: #333; font-size: 1rem;">🗺️ Simbología</strong>
            <ul style="list-style: none; padding: 0; margin: 10px 0;">
                <li><span style="display: inline-block; width: 14px; height: 14px; background-color: orange; border-radius: 50%; margin-right: 8px;"></span> Mis apiarios</li>
                <li><span style="display: inline-block; width: 14px; height: 14px; background-color: red; border-radius: 50%; margin-right: 8px;"></span> Otros apiarios</li>
            </ul>
        </div>
    </div>

    <!-- Tabla de información -->
    <h2 class="mb-4">Información de Apiarios</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre del Apiario</th>
                <th>Ubicación</th>
                <th>Temperatura (°C)</th>
                <th>Humedad (%)</th>
                <th>Tiempo</th>
                <th>Fotografía</th>
            </tr>
        </thead>
        <tbody id="apiary-data">
            @foreach($apiarios as $apiario)
            <tr data-id="{{ $apiario->id }}">
                <td>{{ $apiario->nombre }}</td>
                <td>
                    {{-- Si tienes nombre_comuna disponible, muéstralo, si no muestra lat/lon --}}
                    @if(isset($apiario->nombre_comuna) && $apiario->nombre_comuna)
                        {{ $apiario->nombre_comuna }}
                    @else
                        <span title="Lat: {{ $apiario->latitud }}, Lon: {{ $apiario->longitud }}">
                            {{ number_format($apiario->latitud, 5) }}, {{ number_format($apiario->longitud, 5) }}
                        </span>
                    @endif
                </td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>
                    @if($apiario->foto)
                        <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Foto Apiario" style="max-width: 100px; border-radius: 8px;">
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('map').setView([-33.4489, -70.6693], 6);
    const beeIcon = L.icon({
        iconUrl: 'https://apicheck.cl/img/bee_icon.svg',
        iconSize: [52, 52],
        iconAnchor: [26, 26],
        popupAnchor: [0, -32]
    });

    const apiarios = @json($apiarios);
    const otrosApiarios = @json($otros_apiarios);
    const apiKey = 'e7898e26c93386e793bebfc5b7ead995';

    // Traducción clima
    const weatherEs = {
        'Clear': 'Despejado',
        'Clouds': 'Nublado',
        'Rain': 'Lluvioso',
        'Snow': 'Nevado',
        'Fog': 'Neblina',
        'Thunderstorm': 'Tormenta',
        'Drizzle': 'Llovizna',
        'Mist': 'Neblina',
        'Haze': 'Neblina',
        'Smoke': 'Humo',
        'Dust': 'Polvo',
        'Sand': 'Arena',
        'Ash': 'Ceniza',
        'Squall': 'Chubasco',
        'Tornado': 'Tornado'
    };
    // Diccionario de íconos FontAwesome por código openweather
    const weatherIcons = {
        '01d': '<i class="fa-solid fa-sun"></i>',
        '01n': '<i class="fa-solid fa-moon"></i>',
        '02d': '<i class="fa-solid fa-cloud-sun"></i>',
        '02n': '<i class="fa-solid fa-cloud-moon"></i>',
        '03d': '<i class="fa-solid fa-cloud"></i>',
        '03n': '<i class="fa-solid fa-cloud"></i>',
        '04d': '<i class="fa-solid fa-cloud"></i>',
        '04n': '<i class="fa-solid fa-cloud"></i>',
        '09d': '<i class="fa-solid fa-cloud-rain"></i>',
        '09n': '<i class="fa-solid fa-cloud-rain"></i>',
        '10d': '<i class="fa-solid fa-cloud-sun-rain"></i>',
        '10n': '<i class="fa-solid fa-cloud-moon-rain"></i>',
        '11d': '<i class="fa-solid fa-bolt"></i>',
        '11n': '<i class="fa-solid fa-bolt"></i>',
        '13d': '<i class="fa-solid fa-snowflake"></i>',
        '13n': '<i class="fa-solid fa-snowflake"></i>',
        '50d': '<i class="fa-solid fa-smog"></i>',
        '50n': '<i class="fa-solid fa-smog"></i>',
    };
    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 40 }).addTo(map);
    const esriSat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}');
    L.control.layers({ "Mapa Base (OSM)": osm, "Satélite (Esri)": esriSat }, null, { position: 'topright' }).addTo(map);

    const markers = L.featureGroup().addTo(map);
    const otrosGroup = L.layerGroup();

    // Switch de otros apiarios (por defecto apagado)
    const switchWrapper = document.createElement("div");
    switchWrapper.style.position = "absolute";
    switchWrapper.style.top = "10px";
    switchWrapper.style.left = "50px";
    switchWrapper.style.zIndex = "1000";
    switchWrapper.innerHTML = `
        <label class="toggle-switch">
            <input type="checkbox" id="toggle-others">
            <span class="slider"></span>
        </label>
        <span style="color: #f0941b; font-weight: 600; margin-left: 8px;">Otros apiarios</span>
    `;
    document.getElementById("map-container").appendChild(switchWrapper);

        // --- Actualiza filas de tabla según índice
    const tableRows = document.querySelectorAll("#apiary-data tr");
    

    
    // Mis apiarios
    apiarios.forEach((apiario, i) => {
        if (apiario.latitud && apiario.longitud) {
            const marker = L.marker([apiario.latitud, apiario.longitud], { icon: beeIcon })
                .bindPopup(`<img src="storage/${apiario.foto}" style="width: 100px;"><br><b>${apiario.nombre}</b><br>${apiario.nombre_comuna}<br>Colmenas: ${apiario.num_colmenas}`);
            marker.addTo(markers);

            const circle = L.circle([apiario.latitud, apiario.longitud], {
                color: 'orange', fillColor: 'orange', fillOpacity: 0.3, radius: 3500, interactive: false
            }).addTo(map);

            fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${apiario.latitud}&lon=${apiario.longitud}&appid=${apiKey}&units=metric`)
                .then(res => res.json())
                .then(data => {
                    if (data.main) {
                        const row = document.querySelector(`#apiary-data tr[data-id="${apiario.id}"]`);
                        row.cells[1].textContent = data.name;
                        row.cells[2].textContent = data.main.temp;
                        row.cells[3].textContent = data.main.humidity;
                        const icon = data.weather[0].icon;
                        const weatherDesc = weatherEs[data.weather[0].main] || data.weather[0].main;
                        const faIcon = weatherIcons[icon] || '';
                        row.cells[4].innerHTML = `${faIcon} ${weatherDesc}`;
                    }
                });
        }
    });

    // Otros apiarios (ocultos por defecto)
    otrosApiarios.forEach(apiario => {
        if (apiario.latitud && apiario.longitud) {
            const redCircle = L.circle([apiario.latitud, apiario.longitud], {
                color: 'red', fillColor: 'red', fillOpacity: 0.2, radius: 3500, interactive: true
            }).bindPopup(`<b>Otros apicultores</b><br>${apiario.nombre_comuna}<br>Colmenas: ${apiario.num_colmenas || 'N/A'}<br><img src="storage/${apiario.foto}" style="width: 100px;">`);

            const redDot = L.circleMarker([apiario.latitud, apiario.longitud], {
                color: 'red', radius: 2, fillOpacity: 1
            }).bindPopup(`<b>Otros apicultores</b><br>${apiario.nombre_comuna}<br>Colmenas: ${apiario.num_colmenas || 'N/A'}`);

            otrosGroup.addLayer(redCircle);
            otrosGroup.addLayer(redDot);
        }
    });

    document.getElementById("toggle-others").addEventListener("change", function () {
        if (this.checked) {
            otrosGroup.addTo(map);
        } else {
            otrosGroup.removeFrom(map);
        }
    });

    if (markers.getLayers().length > 0) {
        map.fitBounds(markers.getBounds(), { padding: [50, 50] });
    }

    window.toggleLegend = function () {
        const legend = document.getElementById("legend");
        legend.style.display = legend.style.display === "none" ? "block" : "none";
    };
});
</script>

<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
}
.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 26px;
}
.slider:before {
    content: "";
    position: absolute;
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: #f0941b;
}
input:checked + .slider:before {
    transform: translateX(22px);
}
</style>
@endsection

@section('optional-scripts')
<script src="{{ asset('vendor/chatbot/js/chatbot.js') }}"></script>
@endsection
