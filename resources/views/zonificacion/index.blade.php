@extends('layouts.app')
@section('title', 'Maia - Zonificación')
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
                        <p class="stat-value">{{ count($apiarios) }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-temperature-half"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Temp. Promedio</h3>
                        <p class="stat-value" id="avg-temp">Calculando...</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Humedad Promedio</h3>
                        <p class="stat-value" id="avg-humidity">Calculando...</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Última Actualización</h3>
                        <p class="stat-value">{{ date('d/m/Y H:i') }}</p>
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
                            <span class="toggle-label">Otros apiarios</span>
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
                            <span>Mis apiarios</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background:#e74c3c;"></span>
                            <span>Otros apiarios</span>
                        </div>
                        <div class="legend-item">
                            <i class="fa-solid fa-sun legend-icon"></i>
                            <span>Despejado</span>
                        </div>
                        <div class="legend-item">
                            <i class="fa-solid fa-cloud legend-icon"></i>
                            <span>Nublado</span>
                        </div>
                        <div class="legend-item">
                            <i class="fa-solid fa-cloud-rain legend-icon"></i>
                            <span>Lluvia</span>
                        </div>
                        <div class="legend-item">
                            <span>Humedad óptima: 40-60%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de información con pestañas -->
            <div class="apiary-info-section">
                <div class="section-header">
                    <h2 class="section-title"><i class="fa-solid fa-table-cells"></i> Información de Apiarios</h2>
                    <div class="view-controls">
                        <button class="view-btn active" data-view="table">
                            <i class="fa-solid fa-table-list" style="margin-right: 0.5rem;"></i> Tabla
                        </button>
                        <button class="view-btn" data-view="cards">
                            <i class="fa-solid fa-grip" style="margin-right: 0.5rem;"></i> Tarjetas
                        </button>
                    </div>
                </div>

                <!-- Vista de tabla -->
                <div class="view-container table-view active">
                    <div class="table-responsive">
                        <table class="apiary-table">
                            <thead>
                                <tr>
                                    <th><i class="fa-solid fa-signature"></i> Nombre</th>
                                    <th><i class="fa-solid fa-location-dot"></i> Ubicación</th>
                                    <th><i class="fa-solid fa-temperature-half"></i> Temperatura</th>
                                    <th><i class="fa-solid fa-droplet"></i> Humedad</th>
                                    <th><i class="fa-solid fa-cloud"></i> Tiempo</th>
                                    <th><i class="fa-solid fa-image"></i> Fotografía</th>
                                    <th><i class="fa-solid fa-gear"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="apiary-data">
                                @foreach($apiarios as $apiario)
                                    <tr data-id="{{ $apiario->id }}" class="apiary-row">
                                        <td>{{ $apiario->nombre }}</td>
                                        <td>
                                            @if(isset($apiario->nombre_comuna) && $apiario->nombre_comuna)
                                                {{ $apiario->nombre_comuna }}
                                            @else
                                                <span title="Lat: {{ $apiario->latitud }}, Lon: {{ $apiario->longitud }}"
                                                    class="coordinates-tooltip">
                                                    {{ number_format($apiario->latitud, 5) }}, {{ number_format($apiario->longitud, 5) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="weather-data temp-data">N/A</td>
                                        <td class="weather-data humidity-data">N/A</td>
                                        <td class="weather-data weather-desc">N/A</td>
                                        <td>
                                            @if($apiario->foto)
                                                <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Foto Apiario" class="apiary-image">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('apiarios.editar', $apiario->id) }}"
                                                    class="action-btn edit-btn"
                                                    title="Editar apiario">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <button class="action-btn locate-btn" title="Localizar en mapa" 
                                                    data-lat="{{ $apiario->latitud }}" data-lon="{{ $apiario->longitud }}">
                                                    <i class="fa-solid fa-location-crosshairs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Vista de tarjetas -->
                <div class="view-container cards-view">
                    <div class="apiary-cards">
                        @foreach($apiarios as $apiario)
                            <div class="apiary-card" data-id="{{ $apiario->id }}">
                                <div class="card-header">
                                    <h3>{{ $apiario->nombre }}</h3>
                                    <div class="card-actions">
                                        <button class="card-action locate-btn" title="Localizar en mapa" 
                                            data-lat="{{ $apiario->latitud }}" data-lon="{{ $apiario->longitud }}">
                                            <i class="fa-solid fa-location-crosshairs"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-image">
                                    @if($apiario->foto)
                                        <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Foto Apiario">
                                    @else
                                        <div class="no-image">
                                            <i class="fa-solid fa-image-slash"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="card-info">
                                        <div class="info-item">
                                            <i class="fa-solid fa-location-dot"></i>
                                            <span>
                                                @if(isset($apiario->nombre_comuna) && $apiario->nombre_comuna)
                                                    {{ $apiario->nombre_comuna }}
                                                @else
                                                    <span title="Lat: {{ $apiario->latitud }}, Lon: {{ $apiario->longitud }}"
                                                        class="coordinates-tooltip">
                                                        {{ number_format($apiario->latitud, 5) }}, {{ number_format($apiario->longitud, 5) }}
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa-solid fa-temperature-half"></i>
                                            <span class="temp-data">N/A</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa-solid fa-droplet"></i>
                                            <span class="humidity-data">N/A</span>
                                        </div>
                                    </div>
                                    <div class="weather-badge">
                                        <span class="weather-desc">N/A</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button class="btn-secondary">Editar</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
        <link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('optional-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Inicialización del mapa
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

            // Capas del mapa
            const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 40 }).addTo(map);
            const esriSat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}');
            L.control.layers({ "Mapa Base (OSM)": osm, "Satélite (Esri)": esriSat }, null, { position: 'topright' }).addTo(map);

            const markers = L.featureGroup().addTo(map);
            const otrosGroup = L.layerGroup();
            const markerMap = new Map(); // Para almacenar referencias a los marcadores

            // Variables para estadísticas
            let totalTemp = 0;
            let totalHumidity = 0;
            let countWithData = 0;
            let tempData = [];
            let humidityData = [];

            // Mis apiarios
            apiarios.forEach((apiario, i) => {
                if (apiario.latitud && apiario.longitud) {
                    // 1. POPUP DEL MAPA (quitar botón "Ver detalles")
                    const marker = L.marker([apiario.latitud, apiario.longitud], { icon: beeIcon })
                        .bindPopup(`
                            <div class="custom-popup">
                                <img src="storage/${apiario.foto}" class="popup-image">
                                <h3>${apiario.nombre}</h3>
                                <div class="popup-info">
                                    <p><i class="fa-solid fa-location-dot"></i> ${apiario.nombre_comuna || 'Sin ubicación'}</p>
                                    <p><i class="fa-solid fa-cubes-stacked"></i> Colmenas: ${apiario.num_colmenas}</p>
                                    <p class="popup-weather" data-id="${apiario.id}">
                                        <i class="fa-solid fa-cloud"></i> Cargando datos del clima...
                                    </p>
                                </div>
                                <!-- <button class="popup-btn">Ver detalles</button> -->
                            </div>
                        `);
                    marker.addTo(markers);
                    markerMap.set(apiario.id, marker);

                    const circle = L.circle([apiario.latitud, apiario.longitud], {
                        color: '#f0941b', 
                        fillColor: '#f0941b', 
                        fillOpacity: 0.3, 
                        radius: 3500, 
                        interactive: false
                    }).addTo(map);

                    // Obtener datos del clima
                    fetchWeatherData(apiario);
                }
            });

            // Función para obtener datos del clima
            function fetchWeatherData(apiario) {
                fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${apiario.latitud}&lon=${apiario.longitud}&appid=${apiKey}&units=metric`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.main) {
                            // Actualizar tabla
                            const row = document.querySelector(`#apiary-data tr[data-id="${apiario.id}"]`);
                            if (row) {
                                const tempCell = row.querySelector('.temp-data');
                                const humidityCell = row.querySelector('.humidity-data');
                                const weatherCell = row.querySelector('.weather-desc');

                                if (tempCell) tempCell.textContent = `${data.main.temp.toFixed(1)}°C`;
                                if (humidityCell) humidityCell.textContent = `${data.main.humidity}%`;

                                const icon = data.weather[0].icon;
                                const weatherDesc = weatherEs[data.weather[0].main] || data.weather[0].main;
                                const faIcon = weatherIcons[icon] || '';

                                if (weatherCell) weatherCell.innerHTML = `${faIcon} ${weatherDesc}`;

                                // Aplicar clases según rangos óptimos
                                if (data.main.temp < 18) {
                                    tempCell.classList.add('temp-low');
                                } else if (data.main.temp > 32) {
                                    tempCell.classList.add('temp-high');
                                } else {
                                    tempCell.classList.add('temp-optimal');
                                }

                                if (data.main.humidity < 40) {
                                    humidityCell.classList.add('humidity-low');
                                } else if (data.main.humidity > 60) {
                                    humidityCell.classList.add('humidity-high');
                                } else {
                                    humidityCell.classList.add('humidity-optimal');
                                }
                            }

                            // Actualizar tarjetas
                            const card = document.querySelector(`.apiary-card[data-id="${apiario.id}"]`);
                            if (card) {
                                const tempElement = card.querySelector('.temp-data');
                                const humidityElement = card.querySelector('.humidity-data');
                                const weatherElement = card.querySelector('.weather-desc');

                                if (tempElement) tempElement.textContent = `${data.main.temp.toFixed(1)}°C`;
                                if (humidityElement) humidityElement.textContent = `${data.main.humidity}%`;

                                const icon = data.weather[0].icon;
                                const weatherDesc = weatherEs[data.weather[0].main] || data.weather[0].main;
                                const faIcon = weatherIcons[icon] || '';

                                if (weatherElement) weatherElement.innerHTML = `${faIcon} ${weatherDesc}`;
                            }

                            // Actualizar popup
                            const popupWeather = document.querySelector(`.popup-weather[data-id="${apiario.id}"]`);
                            if (popupWeather) {
                                popupWeather.innerHTML = `
                                    <i class="fa-solid fa-temperature-half"></i> ${data.main.temp.toFixed(1)}°C, 
                                    <i class="fa-solid fa-droplet"></i> ${data.main.humidity}%, 
                                    ${weatherIcons[data.weather[0].icon] || ''} ${weatherEs[data.weather[0].main] || data.weather[0].main}
                                `;
                            }
                            // Actualizar estadísticas
                            totalTemp += data.main.temp;
                            totalHumidity += data.main.humidity;
                            countWithData++;

                            tempData.push(data.main.temp);
                            humidityData.push(data.main.humidity);

                            // Actualizar promedios
                            if (countWithData > 0) {
                                document.getElementById('avg-temp').textContent = `${(totalTemp / countWithData).toFixed(1)}°C`;
                                document.getElementById('avg-humidity').textContent = `${Math.round(totalHumidity / countWithData)}%`;
                            }
                        }
                    })
                    .catch(err => {
                        console.error("Error al obtener datos del clima:", err);
                    });
            }

            // Otros apiarios (ocultos por defecto)
            otrosApiarios.forEach(apiario => {
                if (apiario.latitud && apiario.longitud) {
                    const redCircle = L.circle([apiario.latitud, apiario.longitud], {
                        color: '#e74c3c', 
                        fillColor: '#e74c3c', 
                        fillOpacity: 0.2, 
                        radius: 3500, 
                        interactive: true
                    }).bindPopup(`
                        <div class="custom-popup other-popup">
                            <h3>Otros Apicultores</h3>
                            <div class="popup-info">
                                <p><i class="fa-solid fa-location-dot"></i> ${apiario.nombre_comuna || 'Sin ubicación'}</p>
                                <p><i class="fa-solid fa-cubes-stacked"></i> Colmenas: ${apiario.num_colmenas || 'N/A'}</p>
                            </div>
                            ${apiario.foto ? `<img src="storage/${apiario.foto}" class="popup-image">` : ''}
                        </div>
                    `);

                    const redDot = L.circleMarker([apiario.latitud, apiario.longitud], {
                        color: '#e74c3c', 
                        radius: 4, 
                        fillOpacity: 1
                    });

                    otrosGroup.addLayer(redCircle);
                    otrosGroup.addLayer(redDot);
                }
            });

            // Toggle de otros apiarios
            document.getElementById("toggle-others").addEventListener("change", function () {
                if (this.checked) {
                    otrosGroup.addTo(map);
                } else {
                    otrosGroup.removeFrom(map);
                }
            });

            // Ajustar vista del mapa
            if (markers.getLayers().length > 0) {
                map.fitBounds(markers.getBounds(), { padding: [50, 50] });
            }

            // Cambio entre vistas de tabla y tarjetas
            const viewBtns = document.querySelectorAll('.view-btn');
            viewBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const view = this.getAttribute('data-view');

                    // Si ya está activo, no hacer nada
                    if (this.classList.contains('active')) return;

                    // Desactivar todos los botones y vistas
                    viewBtns.forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.view-container').forEach(v => v.classList.remove('active'));

                    // Activar el botón y vista seleccionados
                    this.classList.add('active');
                    document.querySelector(`.${view}-view`).classList.add('active');
                });
            });

            // Botones de localizar en mapa
            document.querySelectorAll('.locate-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const lat = parseFloat(this.getAttribute('data-lat'));
                    const lon = parseFloat(this.getAttribute('data-lon'));

                    if (!isNaN(lat) && !isNaN(lon)) {
                        map.setView([lat, lon], 12);

                        // Buscar el ID del apiario
                        const row = this.closest('tr');
                        const card = this.closest('.apiary-card');
                        let apiarioId;

                        if (row) {
                            apiarioId = row.getAttribute('data-id');
                        } else if (card) {
                            apiarioId = card.getAttribute('data-id');
                        }

                        // Abrir popup si encontramos el marcador
                        if (apiarioId && markerMap.has(parseInt(apiarioId))) {
                            const marker = markerMap.get(parseInt(apiarioId));
                            marker.openPopup();
                        }
                    }
                });
            });

            // Mostrar/ocultar leyenda del mapa
            const legendBtn = document.getElementById('toggle-legend');
            const legend = document.querySelector('.map-legend');
            legendBtn.addEventListener('click', function () {
                legend.classList.toggle('visible');
            });
        });
    </script>
@endsection