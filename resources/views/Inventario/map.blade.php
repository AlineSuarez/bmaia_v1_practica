<div class="filter-container">
    <select id="producto_id_modal_scraping" name="producto_scraping" class="product-select">
        <option value="">Seleccionar producto con bajo stock</option>
        @foreach($productos as $producto)
            <option value="{{ $producto->nombreProducto }}">
                {{ $producto->nombreProducto }} ({{ $producto->cantidad }} {{ $producto->unidad }})
            </option>
        @endforeach
    </select>
    <button onclick="filtrarPorProducto()" class="btn-filter">
        🔍 Filtrar por Producto
    </button>
</div>

<div class="map-container">
    <div class="map-controls">
        <input 
            type="text" 
            id="searchProduct" 
            placeholder="Ej: Azúcar Flor, Miel, Supermercado"
            class="search-input"
        />
        <button onclick="buscarProveedores()" class="btn-search">
            🔍 Buscar Proveedores
        </button>
        <button onclick="centrarEnMiUbicacion()" class="btn-location">
            📍 Mi Ubicación
        </button>
    </div>
    
    <div id="map" class="apiary-map"></div>
    
    <!-- Loader -->
    <div id="mapLoader" class="map-loader" style="display: none;">
        <div class="loader-content">
            <div class="spinner"></div>
            <p>Buscando proveedores...</p>
        </div>
    </div>
    
    <div class="map-legend">
        <div class="legend-item">
            <span class="legend-marker" style="background: #10b981;"></span>
            <span>Dentro del área (250m)</span>
        </div>
        <div class="legend-item">
            <span class="legend-marker" style="background: #f59e0b;"></span>
            <span>Fuera del área</span>
        </div>
        <div class="legend-item">
            <span class="legend-marker" style="background: #3b82f6;"></span>
            <span>Tu ubicación</span>
        </div>
    </div>
</div>

<!-- Cargar Google Maps API con Places (New) -->
<script>
(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.googleapis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})
({key: "", v: "weekly"});
</script>

<script>
let map;
let userMarker;
let userCircle;
let userLocation;
const RADIO_BUSQUEDA = 250;
let markers = [];
let infoWindow;

document.addEventListener('DOMContentLoaded', function() {
    inicializarMapa();
});

async function inicializarMapa() {
    try {
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        
        const tempLocation = { lat: 0, lng: 0 };
        
        map = new Map(document.getElementById('map'), {
            center: tempLocation,
            zoom: 2,
            mapId: 'DEMO_MAP_ID',
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true,
            zoomControl: true
        });

        infoWindow = new google.maps.InfoWindow();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    console.log('✅ Ubicación obtenida:', userLocation);
                    
                    centrarEnUbicacion(userLocation);
                    await crearMarcadorUsuario(userLocation);
                    
                    mostrarAlerta('Ubicación obtenida correctamente', 'success');
                },
                (error) => {
                    console.error('❌ Error de geolocalización:', error);
                    
                    let mensaje = 'No se pudo obtener tu ubicación. ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            mensaje += 'Debes permitir el acceso a tu ubicación en tu navegador.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            mensaje += 'Tu ubicación no está disponible en este momento.';
                            break;
                        case error.TIMEOUT:
                            mensaje += 'Tiempo de espera agotado. Intenta nuevamente.';
                            break;
                    }
                    mostrarAlerta(mensaje, 'error');
                    
                    const fallbackLocation = { lat: -18.4746, lng: -70.2979 };
                    map.setCenter(fallbackLocation);
                    map.setZoom(13);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        } else {
            mostrarAlerta('Tu navegador no soporta geolocalización. Actualiza tu navegador.', 'error');
        }
    } catch (error) {
        console.error('Error al inicializar el mapa:', error);
        mostrarAlerta('Error al cargar el mapa. Por favor, recarga la página.', 'error');
    }
}

function centrarEnMiUbicacion() {
    if (userLocation) {
        map.setCenter(userLocation);
        map.setZoom(17);
        mostrarAlerta('Vista centrada en tu ubicación', 'success');
    } else {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    centrarEnUbicacion(userLocation);
                    await crearMarcadorUsuario(userLocation);
                    mostrarAlerta('Ubicación actualizada correctamente', 'success');
                },
                (error) => {
                    console.error('Error al actualizar ubicación:', error);
                    mostrarAlerta('No se pudo obtener tu ubicación. Verifica los permisos de ubicación.', 'error');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        } else {
            mostrarAlerta('Tu navegador no soporta geolocalización.', 'error');
        }
    }
}

function centrarEnUbicacion(location) {
    map.setCenter(location);
    map.setZoom(17);
}

async function crearMarcadorUsuario(location) {
    try {
        const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
        
        if (userMarker) {
            userMarker.map = null;
        }
        if (userCircle) {
            userCircle.setMap(null);
        }

        const userPin = new PinElement({
            background: '#3b82f6',
            borderColor: '#ffffff',
            glyphColor: '#ffffff',
            scale: 1.3
        });

        userMarker = new AdvancedMarkerElement({
            map: map,
            position: location,
            title: 'Tu ubicación',
            content: userPin.element
        });

        userCircle = new google.maps.Circle({
            strokeColor: '#3b82f6',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#3b82f6',
            fillOpacity: 0.15,
            map: map,
            center: location,
            radius: RADIO_BUSQUEDA
        });

        userMarker.addListener('click', () => {
            infoWindow.setContent(`
                <div style="padding:10px;">
                    <strong>📍 Tu ubicación</strong><br/>
                    Radio de búsqueda: ${RADIO_BUSQUEDA}m<br/>
                    <small>Lat: ${location.lat.toFixed(6)}, Lng: ${location.lng.toFixed(6)}</small>
                </div>
            `);
            infoWindow.open(map, userMarker);
        });
    } catch (error) {
        console.error('Error al crear marcador:', error);
    }
}

async function buscarProveedores() {
    const producto = document.getElementById('searchProduct').value.trim();
    
    if (!userLocation) {
        mostrarAlerta('Primero necesitamos tu ubicación. Haz clic en "Mi Ubicación" o permite el acceso a tu ubicación.', 'error');
        return;
    }

    if (!producto) {
        mostrarAlerta('Por favor, ingresa un producto para buscar (ej: Azúcar Flor, Miel, Supermercado)', 'error');
        return;
    }

    mostrarLoader(true);
    limpiarMarcadores();

    try {
        const { Place } = await google.maps.importLibrary("places");
        
        const busquedas = [
            `${producto} tienda chile`,
            `supermercado ${producto} chile`,
            `${producto} venta chile`,
            `tienda ${producto} chile`
        ];

        let todosLosResultados = [];

        for (const query of busquedas) {
            const request = {
                textQuery: query,
                fields: ['displayName', 'location', 'rating', 'formattedAddress', 'businessStatus', 'types'],
                locationBias: {
                    center: userLocation,
                    radius: 5000
                },
                language: 'es',
                maxResultCount: 20
            };

            try {
                const { places } = await Place.searchByText(request);
                if (places && places.length > 0) {
                    todosLosResultados = todosLosResultados.concat(places);
                }
            } catch (err) {
                console.warn(`Error en búsqueda "${query}":`, err);
            }
        }

        const resultadosUnicos = todosLosResultados.filter((place, index, self) =>
            index === self.findIndex((p) => 
                p.location.lat() === place.location.lat() && 
                p.location.lng() === place.location.lng()
            )
        );

        // Filtrar solo resultados en Chile
        const resultadosChile = resultadosUnicos.filter(place => {
            if (!place.formattedAddress) return false;
            const address = place.formattedAddress.toLowerCase();
            return address.includes('chile') || 
                   address.includes('arica') || 
                   address.includes('región') ||
                   address.includes('region');
        });

        mostrarLoader(false);

        if (resultadosChile.length > 0) {
            await procesarResultados(resultadosChile, producto);
            mostrarAlerta(`✓ Se encontraron ${resultadosChile.length} proveedores cercanos en Chile`, 'success');
        } else {
            mostrarAlerta('No se encontraron proveedores en Chile. Intenta con "supermercado" o términos más generales.', 'error');
        }
    } catch (error) {
        console.error('Error en búsqueda:', error);
        mostrarLoader(false);
        mostrarAlerta('Error al buscar proveedores. Por favor, intenta nuevamente.', 'error');
    }
}

async function filtrarPorProducto() {
    const select = document.getElementById('producto_id_modal_scraping');
    const productoNombre = select.value;
    
    if (!productoNombre) {
        mostrarAlerta('Por favor, selecciona un producto de la lista', 'error');
        return;
    }

    if (!userLocation) {
        mostrarAlerta('Primero necesitamos tu ubicación. Haz clic en "Mi Ubicación"', 'error');
        return;
    }

    document.getElementById('searchProduct').value = productoNombre;
    buscarProveedores();
}

async function procesarResultados(places, producto) {
    try {
        const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
        
        for (const place of places) {
            if (!place.location) continue;

            const distancia = calcularDistancia(
                userLocation.lat,
                userLocation.lng,
                place.location.lat(),
                place.location.lng()
            );

            const dentroDelArea = distancia <= RADIO_BUSQUEDA;
            const color = dentroDelArea ? '#10b981' : '#f59e0b';

            const pin = new PinElement({
                background: color,
                borderColor: '#ffffff',
                glyphColor: '#ffffff',
                scale: 1.0
            });

            const marker = new AdvancedMarkerElement({
                map: map,
                position: place.location,
                title: place.displayName,
                content: pin.element
            });

            let content = `
                <div style="padding:15px; max-width:300px;">
                    <h3 style="margin:0 0 10px 0; color:#374151; font-size:16px;">
                        ${place.displayName}
                    </h3>
            `;

            if (dentroDelArea) {
                content += `<div style="background:#d1fae5; color:#065f46; padding:5px 10px; border-radius:5px; margin-bottom:10px; font-size:12px; font-weight:600;">
                    ✓ Dentro del área (${Math.round(distancia)}m)
                </div>`;
            } else {
                content += `<div style="background:#fef3c7; color:#92400e; padding:5px 10px; border-radius:5px; margin-bottom:10px; font-size:12px;">
                    A ${Math.round(distancia)}m de tu ubicación
                </div>`;
            }

            if (place.formattedAddress) {
                content += `<p style="margin:5px 0; color:#6b7280; font-size:13px;">
                    📍 ${place.formattedAddress}
                </p>`;
            }

            if (place.rating) {
                content += `<p style="margin:5px 0; color:#6b7280; font-size:13px;">
                    ⭐ ${place.rating} / 5
                </p>`;
            }

            if (place.businessStatus) {
                const abierto = place.businessStatus === 'OPERATIONAL';
                content += `<p style="margin:5px 0; color:${abierto ? '#10b981' : '#6b7280'}; font-size:13px;">
                    ${abierto ? '🟢 Operativo' : '⚪ Estado desconocido'}
                </p>`;
            }

            const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${place.location.lat()},${place.location.lng()}`;
            content += `<a href="${mapsUrl}" target="_blank" style="display:inline-block; margin-top:10px; padding:8px 15px; background:#3b82f6; color:white; text-decoration:none; border-radius:5px; font-size:13px;">
                Ver en Google Maps
            </a>`;

            content += `</div>`;

            marker.addListener('click', () => {
                infoWindow.setContent(content);
                infoWindow.open({
                    anchor: marker,
                    map: map
                });
            });

            markers.push(marker);
        }

        if (markers.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(userLocation);
            markers.forEach(marker => bounds.extend(marker.position));
            map.fitBounds(bounds);
        }
        
        return true;
    } catch (error) {
        console.error('Error al procesar resultados:', error);
        mostrarAlerta('Error al procesar los resultados', 'error');
        return false;
    }
}

function limpiarMarcadores() {
    markers.forEach(marker => {
        marker.map = null;
    });
    markers = [];
}

function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371e3;
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c;
}

function mostrarAlerta(mensaje, tipo) {
    const alert = document.createElement('div');
    alert.className = `custom-alert ${tipo}`;
    alert.innerHTML = `
        <span class="icon">${tipo === 'success' ? '✓' : '⚠'}</span>
        <span>${mensaje}</span>
        <button class="close-alert" onclick="this.parentElement.remove()">×</button>
    `;
    document.body.appendChild(alert);

    setTimeout(() => {
        if (alert.parentElement) {
            alert.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
}

function mostrarLoader(mostrar) {
    const loader = document.getElementById('mapLoader');
    if (loader) {
        loader.style.display = mostrar ? 'flex' : 'none';
    }
}

const searchInput = document.getElementById('searchProduct');
if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarProveedores();
        }
    });
}
</script>

<style>
.filter-container {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    flex-wrap: wrap;
    align-items: center;
}

.product-select {
    flex: 1;
    min-width: 250px;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.product-select:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.btn-filter {
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s;
    background: #10b981;
    color: white;
}

.btn-filter:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-filter:active {
    transform: translateY(0);
}

.map-container {
    width: 100%;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
}

.map-controls {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.search-input {
    flex: 1;
    min-width: 250px;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-search, .btn-location {
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-search {
    background: #10b981;
    color: white;
}

.btn-search:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-location {
    background: #3b82f6;
    color: white;
}

.btn-location:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.apiary-map {
    height: 600px;
    width: 100%;
    border-radius: 6px;
    border: 2px solid #e5e7eb;
    position: relative;
}

.map-loader {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 6px;
}

.loader-content {
    text-align: center;
    padding: 30px;
}

.spinner {
    width: 50px;
    height: 50px;
    margin: 0 auto 20px;
    border: 4px solid #e5e7eb;
    border-top: 4px solid #10b981;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loader-content p {
    color: #374151;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
}

.map-legend {
    display: flex;
    gap: 20px;
    margin-top: 15px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 6px;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #374151;
}

.legend-marker {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.custom-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 10000;
    animation: slideIn 0.3s ease-out;
    max-width: 400px;
    font-size: 14px;
}

.custom-alert.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.custom-alert.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.custom-alert .icon {
    font-size: 18px;
    font-weight: bold;
}

.custom-alert .close-alert {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: inherit;
    opacity: 0.7;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.custom-alert .close-alert:hover {
    opacity: 1;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

@media (max-width: 768px) {
    .filter-container {
        flex-direction: column;
    }
    
    .product-select {
        width: 100%;
    }
    
    .map-controls {
        flex-direction: column;
    }
    
    .search-input {
        width: 100%;
    }
    
    .apiary-map {
        height: 400px;
    }
    
    .map-legend {
        flex-direction: column;
        gap: 10px;
    }
}
</style>