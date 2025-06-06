@extends('layouts.app')

<head>
    <link href="{{ asset('./css/components/home-user/create/create-apiario.css') }}" rel="stylesheet">
</head>
@section('title', 'Maia - Editar Apiario')
@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&family=Roboto+Mono&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/apiario-luxury.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@section('content')
    <div class="container">
        <!-- Efectos de fondo -->
        <div class="blur-effect" style="top: 20%; left: 10%;"></div>
        <div class="blur-effect" style="top: 60%; right: 15%;"></div>
        <div class="blur-effect" style="bottom: 10%; left: 30%;"></div>

        <!-- Botón para volver al menú anterior -->
        <a href="javascript:history.back()" class="btn-back">
            <i class="fas fa-arrow-left"></i> Volver al listado de apiarios
        </a>

        <h1 class="fade-in-up">
            <span>Sistema de Gestión Apícola</span>
            Editar Apiario
        </h1>

        <!-- Contenedor global para tooltips -->
        <div id="tooltip-container"></div>

        <form action="{{ route('apiarios.update', $apiario->id) }}" method="POST" enctype="multipart/form-data"
            class="fade-in-up">
            @csrf
            @method('PUT')
            <div class="honeycomb-bg"></div>

            <!-- Campo para Nombre del Apiario -->
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-signature fa-fw"></i> Nombre del Apiario
                </label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $apiario->nombre }}" required
                    placeholder="Ingrese el nombre del apiario">
                <div class="custom-tooltip" data-tooltip-for="nombre">Asigna un nombre único y descriptivo a tu apiario para
                    identificarlo fácilmente.</div>
            </div>

            <!-- Primera fila: Temporada de Producción y Registro SAG -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="temporada_produccion">
                        <i class="fas fa-calendar-alt fa-fw"></i> Temporada de Producción
                    </label>
                    <select class="form-control" id="temporada_produccion" name="temporada_produccion" required>
                        <!-- Opciones generadas dinámicamente por JavaScript -->
                    </select>
                    <div class="custom-tooltip" data-tooltip-for="temporada_produccion">Selecciona la temporada actual de
                        producción para tu apiario.</div>
                </div>
                <div class="form-group col-md-6">
                    <label for="registro_sag">
                        <i class="fas fa-id-card fa-fw"></i> N° Registro SAG (FRADA)
                    </label>
                    <input type="text" class="form-control" id="registro_sag" name="registro_sag"
                        value="{{ $apiario->registro_sag }}" required placeholder="Ingrese el número de registro">
                    <div class="custom-tooltip" data-tooltip-for="registro_sag">Ingresa el número de registro oficial
                        asignado por el SAG (FRADA).</div>
                </div>
            </div>

            <!-- Segunda fila: N° de Colmenas y Tipo de Apiario -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="num_colmenas">
                        <i class="fas fa-archive fa-fw"></i> N° de Colmenas
                    </label>
                    <input type="number" class="form-control" id="num_colmenas" name="num_colmenas"
                        value="{{ $apiario->num_colmenas }}" required placeholder="Ingrese el número de colmenas">
                    <div class="custom-tooltip" data-tooltip-for="num_colmenas">Indica la cantidad total de colmenas que
                        tiene este apiario.</div>
                </div>
                <div class="form-group col-md-6">
                    <label for="tipo_apiario">
                        <i class="fas fa-tags fa-fw"></i> Tipo de Apiario
                    </label>
                    <select name="tipo_apiario" id="tipo_apiario" class="form-control" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="fijo" {{ old('tipo_apiario') == 'fijo' ? 'selected' : '' }}>Fijo</option>
                        <option value="trashumante" {{ old('tipo_apiario') == 'trashumante' ? 'selected' : '' }}>Trashumante
                        </option>
                    </select>
                </div>
            </div>

            <!-- Tercera fila: Tipo de Manejo y Objetivo de Producción -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="tipo_manejo">
                        <i class="fas fa-cogs fa-fw"></i> Tipo de Manejo
                    </label>
                    <input type="text" class="form-control" id="tipo_manejo" name="tipo_manejo"
                        value="{{ $apiario->tipo_manejo }}" required placeholder="Ej: Orgánico, Convencional">
                    <div class="custom-tooltip" data-tooltip-for="tipo_manejo">Define el método de manejo que utilizas en
                        este apiario.</div>
                </div>
                <div class="form-group col-md-6">
                    <label for="objetivo_produccion">
                        <i class="fas fa-bullseye fa-fw"></i> Objetivo de Producción
                    </label>
                    <input type="text" class="form-control" id="objetivo_produccion" name="objetivo_produccion"
                        value="{{ $apiario->objetivo_produccion }}" required placeholder="Ej: Miel, Polen, Propóleo">
                    <div class="custom-tooltip" data-tooltip-for="objetivo_produccion">Indica los productos principales que
                        esperas obtener de este apiario.</div>
                </div>
            </div>

            <!-- Cuarta fila: Región y Comuna -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="region">
                        <i class="fas fa-map fa-fw"></i> Región
                    </label>
                    <select class="form-control" id="region" name="region" required>
                        <option value="">Selecciona una Región</option>
                        @foreach($regiones as $region)
                            <option value="{{ $region->id }}" {{ $apiario->region_id == $region->id ? 'selected' : '' }}>
                                {{ $region->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <div class="custom-tooltip" data-tooltip-for="region">Selecciona la región donde se encuentra ubicado el
                        apiario.</div>
                </div>
                <div class="form-group col-md-6">
                    <label for="comuna">
                        <i class="fas fa-map-marker-alt fa-fw"></i> Comuna
                    </label>
                    <select class="form-control" id="comuna" name="comuna" required>
                        <option value="">Selecciona una Comuna</option>
                        @foreach($comunas as $comuna)
                            <option value="{{ $comuna->id }}" data-nombre="{{ $comuna->nombre }}" {{ $apiario->comuna_id == $comuna->id ? 'selected' : '' }}>
                                {{ $comuna->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <div class="custom-tooltip" data-tooltip-for="comuna">Selecciona la comuna específica donde se encuentra
                        el apiario.</div>
                </div>
            </div>

            <!-- Mapa y coordenadas -->
            <div class="form-group">
                <label for="map">
                    <i class="fas fa-map-marked-alt fa-fw"></i> Ubicación en el Mapa
                </label>
                <div id="map" class="gold-shine"></div>
                <div class="map-instructions glass-effect">
                    <i class="fas fa-info-circle"></i> Haz clic en el mapa o arrastra el marcador para ajustar la ubicación
                    exacta de tu apiario.
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="latitud">
                            <i class="fas fa-compass fa-fw"></i> Latitud
                        </label>
                        <input type="text" class="form-control" id="latitud" name="latitud" value="{{ $apiario->latitud }}"
                            readonly required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="longitud">
                            <i class="fas fa-compass fa-fw"></i> Longitud
                        </label>
                        <input type="text" class="form-control" id="longitud" name="longitud"
                            value="{{ $apiario->longitud }}" readonly required>
                    </div>
                </div>
            </div>

            <!-- Fotografía del Apiario -->
            <div class="form-group">
                <label for="foto">
                    <i class="fas fa-camera fa-fw"></i> Foto del Apiario
                </label>
                <div class="file-upload-container">
                    <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*">
                    <label for="foto" class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i> Seleccionar imagen
                    </label>
                    <div class="file-name" id="file-name">
                        @if($apiario->foto)
                            Imagen actual: {{ basename($apiario->foto) }}
                        @else
                            Ningún archivo seleccionado
                        @endif
                    </div>
                </div>
                <small class="form-text text-muted">Formatos aceptados: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB</small>
                <div id="preview-container">
                    @if($apiario->foto)
                        <img id="preview-image" src="{{ asset('storage/' . $apiario->foto) }}" alt="Vista previa de la imagen"
                            style="display: block; max-height: 200px; margin: 15px auto;">
                    @else
                        <img id="preview-image" alt="Vista previa de la imagen" style="display: none;">
                    @endif
                    <p id="error-message" style="display: none;">El archivo seleccionado no es una imagen válida.</p>
                </div>
            </div>

            <!-- Botón de envío -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-gold gold-shine">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>

            <!-- Firma -->
            <div class="signature-section">
                <p class="signature-text">"La apicultura es un arte, y cada apiario una obra maestra"</p>
                <div class="signature-logo">MAIA</div>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        let map;
        let marker;
        let comunasCoordenadas = @json($comunasCoordenadas);

        $(document).ready(function () {
            // Añadir efectos de brillo
            addSparkleEffects();

            // Inicializar el sistema de tooltips mejorado
            initTooltips();

            // Generar años para la temporada de producción
            const currentYear = new Date().getFullYear();
            let options = '';

            // Crear las opciones para el rango actual y pasado
            for (let i = 1; i >= -1; i--) {
                const startYear = currentYear - i;
                const endYear = startYear + 1;
                options += `<option value="${startYear}-${endYear}" ${('${startYear}-${endYear}' === '{{ $apiario->temporada_produccion }}') ? 'selected' : ''}>${startYear}-${endYear}</option>`;
            }
            $('#temporada_produccion').html(options);

            // Inicializar el mapa con la ubicación del apiario
            function initMap() {
                showLoading('map');
                const lat = {{ $apiario->latitud }};
                const lng = {{ $apiario->longitud }};

                map = L.map('map').setView([lat, lng], 13);

                // Estilo personalizado para el mapa
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Personalizar el icono del marcador
                const apiaryIcon = L.icon({
                    iconUrl: 'data:image/svg+xml;base64,' + btoa(`
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 36" width="24" height="36">
                                    <defs>
                                        <linearGradient id="grad1" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" style="stop-color:#FFD700;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#FFA500;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                    <path d="M12,2 C8.1,2 5,5.1 5,9 C5,14.2 12,22 12,22 S19,14.2 19,9 C19,5.1 15.9,2 12,2 Z" 
                                          fill="url(#grad1)" stroke="#B8860B" stroke-width="1"/>
                                    <circle cx="12" cy="9" r="4" fill="#FFFFFF" stroke="#B8860B" stroke-width="1"/>
                                    <path d="M12,6 L13.5,8.5 L16,8.5 L14.2,10.2 L15,12.5 L12,11 L9,12.5 L9.8,10.2 L8,8.5 L10.5,8.5 Z" 
                                          fill="#FFD700"/>
                                </svg>
                            `),
                    iconSize: [32, 48],
                    iconAnchor: [16, 48],
                    popupAnchor: [0, -48]
                });

                marker = L.marker([lat, lng], {
                    draggable: true,
                    icon: apiaryIcon
                }).addTo(map);

                // Personalizar el popup
                const popupContent = `
                                <div class="custom-popup">
                                    <h4><i class="fas fa-map-pin"></i> Tu Apiario</h4>
                                    <p>Arrastra el marcador para ajustar la ubicación exacta.</p>
                                    <div class="popup-coordinates">
                                        <span><strong>Lat:</strong> ${lat.toFixed(6)}</span><br>
                                        <span><strong>Lng:</strong> ${lng.toFixed(6)}</span>
                                    </div>
                                </div>
                            `;

                marker.bindPopup(popupContent).openPopup();

                marker.on('dragend', function (event) {
                    const position = marker.getLatLng();
                    $('#latitud').val(position.lat.toFixed(6));
                    $('#longitud').val(position.lng.toFixed(6));

                    // Actualizar el popup con las nuevas coordenadas
                    const updatedPopupContent = `
                                    <div class="custom-popup">
                                        <h4><i class="fas fa-map-pin"></i> Tu Apiario</h4>
                                        <p>Ubicación actualizada correctamente.</p>
                                        <div class="popup-coordinates">
                                            <span><strong>Lat:</strong> ${position.lat.toFixed(6)}</span><br>
                                            <span><strong>Lng:</strong> ${position.lng.toFixed(6)}</span>
                                        </div>
                                    </div>
                                `;
                    marker.getPopup().setContent(updatedPopupContent);
                    marker.openPopup();

                    highlightField('#latitud');
                    highlightField('#longitud');
                });

                map.on('click', function (e) {
                    marker.setLatLng(e.latlng);
                    $('#latitud').val(e.latlng.lat.toFixed(6));
                    $('#longitud').val(e.latlng.lng.toFixed(6));

                    // Actualizar el popup con las nuevas coordenadas
                    const updatedPopupContent = `
                                    <div class="custom-popup">
                                        <h4><i class="fas fa-map-pin"></i> Tu Apiario</h4>
                                        <p>Ubicación actualizada correctamente.</p>
                                        <div class="popup-coordinates">
                                            <span><strong>Lat:</strong> ${e.latlng.lat.toFixed(6)}</span><br>
                                            <span><strong>Lng:</strong> ${e.latlng.lng.toFixed(6)}</span>
                                        </div>
                                    </div>
                                `;
                    marker.getPopup().setContent(updatedPopupContent);
                    marker.openPopup();

                    highlightField('#latitud');
                    highlightField('#longitud');
                });

                // Actualizar el mapa cuando cambia el tamaño de la ventana
                setTimeout(function () {
                    map.invalidateSize();
                }, 100);

                // Añadir estilos personalizados para el popup
                const style = document.createElement('style');
                style.textContent = `
                                .custom-popup {
                                    font-family: var(--font-body);
                                    padding: 8px;
                                }
                                .custom-popup h4 {
                                    color: var(--color-brown);
                                    margin: 0 0 8px 0;
                                    font-size: 16px;
                                    font-weight: 600;
                                }
                                .custom-popup p {
                                    margin: 0 0 8px 0;
                                    font-size: 14px;
                                }
                                .popup-coordinates {
                                    font-family: var(--font-mono);
                                    font-size: 12px;
                                    color: var(--color-text-light);
                                    background-color: rgba(212, 175, 55, 0.05);
                                    padding: 8px;
                                    border-radius: 4px;
                                    margin-top: 5px;
                                    border-left: 2px solid var(--color-gold);
                                }
                                .map-instructions {
                                    text-align: center;
                                    padding: 10px 15px;
                                    border-radius: 8px;
                                    margin: 10px 0 15px 0;
                                    font-size: 14px;
                                    color: var(--color-brown);
                                }
                            `;
                document.head.appendChild(style);

                hideLoading('map');
            }

            initMap();

            // Cambiar las coordenadas al seleccionar una comuna
            $('#comuna').change(function () {
                const comunaNombre = $(this).find('option:selected').data('nombre');
                if (comunasCoordenadas[comunaNombre]) {
                    const { lat, lon } = comunasCoordenadas[comunaNombre];
                    map.setView([lat, lon], 13);
                    marker.setLatLng([lat, lon]);

                    // Obtener la posición real del marcador después de moverlo
                    const position = marker.getLatLng();
                    $('#latitud').val(position.lat.toFixed(6));
                    $('#longitud').val(position.lng.toFixed(6));

                    // Actualizar el popup con las nuevas coordenadas
                    const updatedPopupContent = `
                                    <div class="custom-popup">
                                        <h4><i class="fas fa-map-pin"></i> Comuna: ${comunaNombre}</h4>
                                        <p>Ubicación central de la comuna seleccionada.</p>
                                        <div class="popup-coordinates">
                                            <span><strong>Lat:</strong> ${position.lat.toFixed(6)}</span><br>
                                            <span><strong>Lng:</strong> ${position.lng.toFixed(6)}</span>
                                        </div>
                                    </div>
                                `;
                    marker.getPopup().setContent(updatedPopupContent);
                    marker.openPopup();

                    // Efecto visual para los campos de coordenadas
                    highlightField('#latitud');
                    highlightField('#longitud');

                    showNotification(`Ubicación actualizada a la comuna de ${comunaNombre}`, 'success');
                } else {
                    showNotification('No se encontraron las coordenadas de la comuna seleccionada.', 'error');
                }
            });

            // Mejorar la experiencia del input de archivo
            document.getElementById('foto').addEventListener('change', function (event) {
                const file = event.target.files[0];
                const previewImage = document.getElementById('preview-image');
                const errorMessage = document.getElementById('error-message');
                const fileName = document.getElementById('file-name');

                if (file) {
                    const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    const maxSize = 5 * 1024 * 1024; // 5MB

                    // Validar el tipo de archivo
                    if (!validImageTypes.includes(file.type)) {
                        previewImage.style.display = 'none';
                        errorMessage.textContent = 'El archivo seleccionado no es una imagen válida.';
                        errorMessage.style.display = 'block';
                        fileName.textContent = 'Archivo no válido';
                        event.target.value = ''; // Limpiar el input
                        showNotification('El archivo seleccionado no es una imagen válida.', 'error');
                        return;
                    }

                    // Validar el tamaño del archivo
                    if (file.size > maxSize) {
                        previewImage.style.display = 'none';
                        errorMessage.textContent = 'La imagen excede el tamaño máximo de 5MB.';
                        errorMessage.style.display = 'block';
                        fileName.textContent = 'Archivo demasiado grande';
                        event.target.value = ''; // Limpiar el input
                        showNotification('La imagen excede el tamaño máximo de 5MB.', 'error');
                        return;
                    }

                    errorMessage.style.display = 'none';
                    fileName.textContent = file.name;

                    // Crear una URL para mostrar la imagen
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';

                        // Animación de entrada para la imagen
                        previewImage.style.opacity = '0';
                        previewImage.style.transform = 'scale(0.9)';

                        setTimeout(() => {
                            previewImage.style.opacity = '1';
                            previewImage.style.transform = 'scale(1)';
                        }, 10);

                        showNotification('Imagen cargada correctamente', 'success');
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Si no hay archivo seleccionado
                    previewImage.style.display = 'none';
                    errorMessage.style.display = 'none';
                    fileName.textContent = 'Ningún archivo seleccionado';
                }
            });

            // Cargar comunas al cambiar región
            $('#region').change(function () {
                let regionId = $(this).val();
                let comunaSelect = $('#comuna');
                let regionNombre = $(this).find('option:selected').text();

                comunaSelect.html('<option value="">Selecciona una Comuna</option>');

                if (regionId) {
                    // Mostrar indicador de carga
                    comunaSelect.html('<option value="">Cargando comunas...</option>');
                    showLoading('comuna');

                    // Simular tiempo de carga para mejor experiencia de usuario
                    setTimeout(function () {
                        // Obtener comunas de la región seleccionada
                        let comunasPorRegion = @json($regiones->mapWithKeys(fn($region) => [$region->id => $region->comunas]));

                        if (comunasPorRegion[regionId]) {
                            comunaSelect.html('<option value="">Selecciona una Comuna</option>');
                            comunasPorRegion[regionId].forEach(comuna => {
                                comunaSelect.append(`<option value="${comuna.id}" data-nombre="${comuna.nombre}" ${comuna.id == {{ $apiario->comuna_id }} ? 'selected' : ''}>${comuna.nombre}</option>`);
                            });
                            hideLoading('comuna');
                            showNotification(`Comunas de ${regionNombre} cargadas correctamente`, 'success');

                            // Seleccionar automáticamente la comuna del apiario si existe
                            if ({{ $apiario->comuna_id }}) {
                                comunaSelect.val({{ $apiario->comuna_id }}).trigger('change');
                            } else {
                                // Seleccionar automáticamente la primera comuna válida y disparar el evento change
                                let firstComuna = comunaSelect.find('option[value!=""]').first();
                                if (firstComuna.length) {
                                    firstComuna.prop('selected', true);
                                    comunaSelect.trigger('change');
                                }
                            }
                        } else {
                            hideLoading('comuna');
                            showNotification('No se encontraron comunas para esta región', 'warning');
                        }
                    }, 800);
                }
            });

            // Función para añadir efectos de brillo
            function addSparkleEffects() {
                // Añadir destellos al título
                const h1 = document.querySelector('h1');
                for (let i = 0; i < 5; i++) {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'sparkle';
                    sparkle.style.top = `${Math.random() * 100}%`;
                    sparkle.style.left = `${Math.random() * 100}%`;
                    sparkle.style.animationDelay = `${Math.random() * 2}s`;
                    h1.appendChild(sparkle);
                }

                // Añadir destellos al botón de envío
                const submitBtn = document.querySelector('.btn-gold');
                for (let i = 0; i < 3; i++) {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'sparkle';
                    sparkle.style.top = `${Math.random() * 100}%`;
                    sparkle.style.left = `${Math.random() * 100}%`;
                    sparkle.style.animationDelay = `${Math.random() * 2}s`;
                    submitBtn.appendChild(sparkle);
                }
            }

            // Función para inicializar el sistema de tooltips mejorado
            function initTooltips() {
                // Asegurarse de que el contenedor de tooltips existe
                if (!document.getElementById('tooltip-container')) {
                    const tooltipContainer = document.createElement('div');
                    tooltipContainer.id = 'tooltip-container';
                    document.body.appendChild(tooltipContainer);
                }

                // Mover todos los tooltips al contenedor global
                const tooltips = document.querySelectorAll('.custom-tooltip');
                const tooltipContainer = document.getElementById('tooltip-container');

                tooltips.forEach(tooltip => {
                    const forElement = tooltip.getAttribute('data-tooltip-for');
                    const targetElement = document.getElementById(forElement);

                    if (targetElement) {
                        // Clonar el tooltip y añadirlo al contenedor global
                        const clonedTooltip = tooltip.cloneNode(true);
                        tooltipContainer.appendChild(clonedTooltip);

                        // Ocultar el tooltip original
                        tooltip.style.display = 'none';

                        // Posicionar el tooltip clonado cuando se hace hover
                        targetElement.addEventListener('mouseenter', () => {
                            const rect = targetElement.getBoundingClientRect();
                            clonedTooltip.style.position = 'fixed';
                            clonedTooltip.style.left = `${rect.left}px`;
                            clonedTooltip.style.top = `${rect.bottom + 10}px`;
                            clonedTooltip.style.opacity = '1';
                            clonedTooltip.style.visibility = 'visible';
                            clonedTooltip.style.transform = 'translateY(0)';

                            // Asegurarse de que el tooltip no se salga de la pantalla
                            const tooltipRect = clonedTooltip.getBoundingClientRect();
                            if (tooltipRect.right > window.innerWidth) {
                                clonedTooltip.style.left = `${window.innerWidth - tooltipRect.width - 20}px`;
                            }
                        });

                        // Ocultar el tooltip cuando se quita el hover
                        targetElement.addEventListener('mouseleave', () => {
                            clonedTooltip.style.opacity = '0';
                            clonedTooltip.style.visibility = 'hidden';
                            clonedTooltip.style.transform = 'translateY(10px)';
                        });
                    }
                });
            }

            // Función para mostrar notificaciones
            function showNotification(message, type = 'info') {
                // Crear elemento de notificación
                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;

                // Determinar el icono según el tipo
                let icon = 'info-circle';
                if (type === 'success') icon = 'check-circle';
                if (type === 'warning') icon = 'exclamation-triangle';
                if (type === 'error') icon = 'times-circle';

                notification.innerHTML = `
                                <div class="notification-icon">
                                    <i class="fas fa-${icon}"></i>
                                </div>
                                <div class="notification-content">${message}</div>
                                <button class="notification-close"><i class="fas fa-times"></i></button>
                            `;

                document.body.appendChild(notification);

                // Mostrar con animación
                setTimeout(() => {
                    notification.classList.add('show');
                }, 10);

                // Ocultar después de 5 segundos
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 5000);

                // Cerrar al hacer clic en el botón
                notification.querySelector('.notification-close').addEventListener('click', () => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                });
            }

            // Función para mostrar indicador de carga
            function showLoading(elementId) {
                const element = document.getElementById(elementId);
                if (!element) return;

                const loadingContainer = document.createElement('div');
                loadingContainer.className = 'loading-container';
                loadingContainer.innerHTML = `
                                <div class="loading-indicator">
                                    <div></div><div></div><div></div><div></div>
                                </div>
                            `;

                // Si es el mapa, añadir directamente al contenedor
                if (elementId === 'map') {
                    element.style.position = 'relative';
                    loadingContainer.style.position = 'absolute';
                    loadingContainer.style.top = '0';
                    loadingContainer.style.left = '0';
                    loadingContainer.style.width = '100%';
                    loadingContainer.style.height = '100%';
                    loadingContainer.style.display = 'flex';
                    loadingContainer.style.alignItems = 'center';
                    loadingContainer.style.justifyContent = 'center';
                    loadingContainer.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                    loadingContainer.style.zIndex = '1000';
                    loadingContainer.style.borderRadius = 'var(--border-radius)';
                    element.appendChild(loadingContainer);
                } else {
                    // Para otros elementos, añadir después
                    element.parentNode.style.position = 'relative';
                    loadingContainer.style.position = 'absolute';
                    loadingContainer.style.top = '100%';
                    loadingContainer.style.left = '0';
                    loadingContainer.style.width = '100%';
                    loadingContainer.style.padding = '10px';
                    loadingContainer.style.display = 'flex';
                    loadingContainer.style.alignItems = 'center';
                    loadingContainer.style.justifyContent = 'center';
                    loadingContainer.style.zIndex = '10';
                    element.parentNode.appendChild(loadingContainer);
                }
            }

            // Función para ocultar indicador de carga
            function hideLoading(elementId) {
                const element = document.getElementById(elementId);
                if (!element) return;

                const container = elementId === 'map' ? element : element.parentNode;
                const loadingContainer = container.querySelector('.loading-container');

                if (loadingContainer) {
                    loadingContainer.remove();
                }
            }

            // Función para resaltar campos con efecto visual
            function highlightField(selector) {
                const field = $(selector);
                field.addClass('highlight-animation');

                setTimeout(() => {
                    field.removeClass('highlight-animation');
                }, 1000);
            }

            // Añadir estilos para efectos visuales
            const styleElement = document.createElement('style');
            styleElement.textContent = `
                            .highlight-animation {
                                animation: highlightPulse 1s ease;
                            }

                            @keyframes highlightPulse {
                                0% { background-color: var(--color-background); }
                                50% { background-color: rgba(212, 175, 55, 0.2); }
                                100% { background-color: var(--color-background); }
                            }

                            .loading-container {
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                padding: 10px;
                            }

                            .loading-indicator {
                                display: inline-block;
                                position: relative;
                                width: 80px;
                                height: 13px;
                            }

                            .loading-indicator div {
                                position: absolute;
                                top: 0;
                                width: 13px;
                                height: 13px;
                                border-radius: 50%;
                                background: var(--color-gold);
                                animation-timing-function: cubic-bezier(0, 1, 1, 0);
                            }

                            .loading-indicator div:nth-child(1) {
                                left: 8px;
                                animation: loading1 0.6s infinite;
                            }

                            .loading-indicator div:nth-child(2) {
                                left: 8px;
                                animation: loading2 0.6s infinite;
                            }

                            .loading-indicator div:nth-child(3) {
                                left: 32px;
                                animation: loading2 0.6s infinite;
                            }

                            .loading-indicator div:nth-child(4) {
                                left: 56px;
                                animation: loading3 0.6s infinite;
                            }
                        `;
            document.head.appendChild(styleElement);

            // Añadir efectos de animación al enviar el formulario
            $('form').on('submit', function (e) {
                // Forzar actualización de los campos antes de enviar
                if (marker) {
                    const position = marker.getLatLng();
                    $('#latitud').val(position.lat.toFixed(6));
                    $('#longitud').val(position.lng.toFixed(6));
                    // Para depuración
                    console.log('Latitud enviada:', $('#latitud').val());
                    console.log('Longitud enviada:', $('#longitud').val());
                }
                // Validar campos antes de enviar
                let isValid = true;

                // Validar campos requeridos
                $(this).find('input[required], select[required]').each(function () {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');

                        // Añadir mensaje de error
                        if (!$(this).next('.error-message').length) {
                            $('<div class="error-message">Este campo es obligatorio</div>').insertAfter($(this));
                        }

                        // Animar el campo con error
                        $(this).parent().addClass('shake-animation');
                        setTimeout(() => {
                            $(this).parent().removeClass('shake-animation');
                        }, 500);
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.error-message').remove();
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    showNotification('Por favor, completa todos los campos requeridos', 'error');

                    // Desplazarse al primer campo con error
                    $('html, body').animate({
                        scrollTop: $('.is-invalid:first').offset().top - 100
                    }, 500);

                    return false;
                }

                // Añadir animación de envío
                $(this).addClass('submitting');
                showNotification('Enviando formulario...', 'info');

                // Añadir estilos para la animación de envío
                const submitStyle = document.createElement('style');
                submitStyle.textContent = `
                                .submitting {
                                    position: relative;
                                }

                                .submitting::after {
                                    content: "";
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    background-color: rgba(255, 255, 255, 0.7);
                                    backdrop-filter: blur(3px);
                                    z-index: 1000;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 1.5rem;
                                    color: var(--color-gold);
                                    border-radius: var(--border-radius);
                                }

                                .shake-animation {
                                    animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
                                }

                                @keyframes shake {
                                    10%, 90% { transform: translate3d(-1px, 0, 0); }
                                    20%, 80% { transform: translate3d(2px, 0, 0); }
                                    30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
                                    40%, 60% { transform: translate3d(4px, 0, 0); }
                                }

                                .error-message {
                                    color: var(--color-error);
                                    font-size: 0.8rem;
                                    margin-top: 0.25rem;
                                    animation: fadeIn 0.3s ease;
                                }

                                .is-invalid {
                                    border-color: var(--color-error) !important;
                                    background-color: rgba(244, 67, 54, 0.05) !important;
                                }

                                @keyframes fadeIn {
                                    from { opacity: 0; transform: translateY(-10px); }
                                    to { opacity: 1; transform: translateY(0); }
                                }
                            `;
                document.head.appendChild(submitStyle);
            });

            // Eliminar mensajes de error al cambiar el valor del campo
            $(document).on('input change', 'input, select', function () {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                }
            });

            // Añadir efectos de hover a los campos
            $('input, select').hover(
                function () {
                    $(this).addClass('hover-effect');
                },
                function () {
                    $(this).removeClass('hover-effect');
                }
            );

            // Añadir estilos para efectos de hover
            const hoverStyle = document.createElement('style');
            hoverStyle.textContent = `
                            .hover-effect {
                                transform: translateY(-2px);
                                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                                transition: all 0.3s ease;
                            }
                        `;
            document.head.appendChild(hoverStyle);
        });
    </script>
@endsection