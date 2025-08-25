@extends('layouts.app')

@section('title', 'Crear Apiario Temporal')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/create/create-temporal.css') }}" rel="stylesheet">
    </head>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Sistema de Notificaciones Profesional -->
                <div class="notification-container" id="notificationContainer"></div>

                <!-- Header con Progress Bar -->
                <div class="page-header">
                    <div class="header-content">
                        <div class="header-info">
                            <h1 class="page-title">
                                <i class="fas fa-route"></i>
                                Registro de {{ $tipo == 'traslado' ? 'Traslado' : 'Retorno' }}
                            </h1>
                            <span class="badge badge-{{ $tipo == 'traslado' ? 'warning' : 'success' }}">
                                {{ ucfirst($tipo) }}
                            </span>
                        </div>
                        <a href="{{ route('apiarios') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <!-- Formulario Wizard Compacto -->
                <div class="wizard-container">
                    <form id="form-temporal" action="{{ route('trashumancia.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                        @foreach($apiariosData as $apiario)
                            <input type="hidden" name="apiarios_base[]" value="{{ $apiario->id }}">
                        @endforeach
                        <div class="nombre-temporal-container">
                            <label for="nombreTemporal">Nombre Apiario Temporal</label>
                            <input type="text" id="nombreTemporal" name="nombre" class="form-control"
                                placeholder="Ingrese el nombre del apiario temporal" value="{{ old('nombre') }}" required>
                        </div>

                        <!-- PASO 1: COLMENAS -->
                        <div class="wizard-step active" id="step-1">
                            <div class="step-content">
                                <h4 class="step-title">
                                    <i class="fas fa-cube"></i>
                                    Selección de Colmenas
                                </h4>

                                @foreach($apiariosData as $apiario)
                                    <div class="apiario-card">
                                        <div class="apiario-info">
                                            <h5>{{ $apiario->nombre }}</h5>
                                            <span class="apiario-meta">{{ $apiario->colmenas->count() }} colmenas
                                                disponibles</span>
                                            <label class="select-all">
                                                <input type="checkbox" class="select-all-colmenas"
                                                    data-apiario="{{ $apiario->id }}">
                                                Seleccionar todas
                                            </label>
                                        </div>

                                        <div class="colmenas-compact">
                                            @if($apiario->colmenas->isEmpty())
                                                <p class="text-muted">No hay colmenas disponibles en este apiario.</p>
                                            @else
                                                @foreach($apiario->colmenas as $colmena)
                                                    <label class="colmena-compact">
                                                        <input type="checkbox" name="colmenas[{{ $apiario->id }}][]"
                                                            value="{{ $colmena->id }}" class="colmena-check"
                                                            data-apiario="{{ $apiario->id }}">
                                                        <span class="colmena-visual">
                                                            <i class="fas fa-cube"></i>
                                                            <small>{{ $colmena->numero }}</small>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- PASO 2: APICULTOR -->
                        <div class="wizard-step" id="step-2">
                            <div class="step-content">
                                <h4 class="step-title">
                                    <i class="fas fa-user"></i>
                                    Información del Apicultor
                                </h4>

                                <div class="form-row">
                                    <div class="form-col">
                                        <label>Nombre del Apicultor</label>
                                        <input type="text" class="form-input" name="apicultor_nombre"
                                            placeholder="Ingrese el nombre completo del apicultor"
                                            value="{{ old('apicultor_nombre') }}">
                                    </div>
                                    <div class="form-col">
                                        <label>RUT del Apicultor</label>
                                        <input type="text" class="form-input" name="apicultor_rut"
                                            placeholder="Formato: 12.345.678-9" value="{{ old('apicultor_rut') }}">
                                    </div>
                                    <div class="form-col">
                                        <label>Nº Registro Nacional de Apicultores</label>
                                        <input type="text" class="form-input" name="registro_nacional"
                                            placeholder="Formato: RNA-YYYY-XXXXXX" value="{{ old('registro_nacional') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PASO 3: APIARIO Y UBICACIÓN -->
                        <div class="wizard-step" id="step-3">
                            <div class="step-content">
                                <h4 class="step-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Apiario y Ubicación
                                </h4>

                                {{-- Recorremos cada apiario base para mostrar su info de origen --}}
                                @foreach($apiariosData as $apiario)
                                    <div class="section-card mb-4">
                                        <h5 class="section-title">Apiario: {{ $apiario->nombre }} (AP-{{ $apiario->id }})</h5>
                                        <div class="form-row">
                                            <div class="form-col">
                                                <label>Región Origen</label>
                                                <input type="text" class="form-input"
                                                    value="{{ optional(optional($apiario->comuna)->region)->nombre ?? 'Sin región' }}"
                                                    readonly>
                                            </div>
                                            <div class="form-col">
                                                <label>Comuna Origen</label>
                                                <input type="text" class="form-input"
                                                    value="{{ optional($apiario->comuna)->nombre ?? 'Sin comuna' }}" readonly>
                                            </div>
                                            <div class="form-col">
                                                <label>Coordenadas Origen</label>
                                                <input type="text" class="form-input"
                                                    value="{{ $apiario->latitud }}, {{ $apiario->longitud }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if($tipo == 'traslado')
                                    <!-- Una sola ubicación destino para TODO el temporal -->
                                    <div class="section-card">
                                        <h5 class="section-title">Ubicación Destino</h5>
                                        <div class="form-row">
                                            <div class="form-col">
                                                <label>Región Destino</label>
                                                <select class="form-input" name="destino_region_id" id="destinoRegionSelect"
                                                    required>
                                                    <option value="">Seleccionar región</option>
                                                    @foreach($regiones as $reg)
                                                        <option value="{{ $reg->id }}" {{ old('destino_region_id') == $reg->id ? 'selected' : '' }}>
                                                            {{ $reg->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-col">
                                                <label>Comuna Destino</label>
                                                <select class="form-input" name="destino_comuna_id" id="destinoComunaSelect"
                                                    required>
                                                    <option value="">Seleccionar comuna</option>
                                                    {{-- Se rellenará con JS --}}
                                                </select>
                                            </div>
                                            <div class="form-col">
                                                <label>Coordenadas Destino</label>
                                                <input type="text" class="form-input" name="coordenadas_destino"
                                                    value="{{ old('coordenadas_destino') }}"
                                                    placeholder="Formato: -33.0472, -71.4419" readonly required>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- PASO 4: MOVIMIENTO -->
                        <div class="wizard-step" id="step-4">
                            <div class="step-content">
                                <h4 class="step-title">
                                    <i class="fas fa-truck"></i>
                                    Información del Movimiento
                                </h4>

                                <!-- Fechas y Motivo -->
                                <div class="section-card">
                                    <h5 class="section-title">Detalles del Movimiento</h5>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label>Fecha Inicio</label>
                                            <input type="date" class="form-input" name="fecha_inicio_mov"
                                                id="fecha_inicio_mov" value="{{ old('fecha_inicio_mov', date('Y-m-d')) }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Fecha Término</label>
                                            <input type="date" class="form-input" name="fecha_termino_mov"
                                                id="fecha_termino_mov"
                                                value="{{ old('fecha_termino_mov', date('Y-m-d', strtotime('+7 days'))) }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Motivo</label>
                                            <select class="form-input" name="motivo_movimiento" id="motivo_select">
                                                <option value="">Seleccionar motivo</option>
                                                <option value="Producción" {{ old('motivo_movimiento') == 'Producción' ? 'selected' : '' }}>Producción</option>
                                                <option value="Polinización" {{ old('motivo_movimiento') == 'Polinización' ? 'selected' : '' }}>Polinización</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Polinización (condicional) -->
                                <div class="section-card polinizacion-section"
                                    style="{{ old('motivo_movimiento') == 'Polinización' ? 'display:block' : 'display:none' }};">
                                    <h5 class="section-title">Información de Polinización</h5>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label>Cultivo</label>
                                            <input type="text" class="form-input" name="cultivo" id="cultivo"
                                                placeholder="Tipo de cultivo (ej: Almendros, Paltos, Cerezos)"
                                                value="{{ old('cultivo') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Período Floración</label>
                                            <input type="text" class="form-input" name="periodo_floracion"
                                                id="periodo_floracion"
                                                placeholder="Período de floración (ej: Agosto - Septiembre)"
                                                value="{{ old('periodo_floracion') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Hectáreas</label>
                                            <input type="number" class="form-input" name="hectareas" id="hectareas"
                                                placeholder="Cantidad de hectáreas" value="{{ old('hectareas') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Transportista -->
                                <div class="section-card">
                                    <h5 class="section-title">Información del Transporte</h5>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label>Transportista</label>
                                            <input type="text" class="form-input" name="transportista_nombre"
                                                placeholder="Nombre o empresa de transporte"
                                                value="{{ old('transportista_nombre') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>RUT Transportista</label>
                                            <input type="text" class="form-input" name="transportista_rut"
                                                placeholder="RUT del transportista (76.123.456-7)"
                                                value="{{ old('transportista_rut') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Patente Vehículo</label>
                                            <input type="text" class="form-input" name="vehiculo_patente"
                                                placeholder="Patente del vehículo (ABCD-12)"
                                                value="{{ old('vehiculo_patente') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- NAVEGACIÓN COMPLETAMENTE REDISEÑADA -->
                        <div class="wizard-navigation">
                            <!-- Progress Dots -->
                            <div class="nav-progress">
                                <div class="progress-track">
                                    <div class="progress-fill" id="progressFill"></div>
                                </div>
                                <div class="nav-dots">
                                    <div class="nav-dot active" data-step="1">
                                        <span class="dot-number">1</span>
                                        <span class="dot-label">Colmenas</span>
                                    </div>
                                    <div class="nav-dot" data-step="2">
                                        <span class="dot-number">2</span>
                                        <span class="dot-label">Apicultor</span>
                                    </div>
                                    <div class="nav-dot" data-step="3">
                                        <span class="dot-number">3</span>
                                        <span class="dot-label">Ubicación</span>
                                    </div>
                                    <div class="nav-dot" data-step="4">
                                        <span class="dot-number">4</span>
                                        <span class="dot-label">Movimiento</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Controls Section -->
                            <div class="nav-controls">
                                <!-- Left Side: Back Button -->
                                <div class="nav-left">
                                    <button type="button" class="nav-btn nav-btn-back" id="prevBtn" style="display: none;">
                                        <div class="btn-icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </div>
                                        <span class="btn-text">Anterior</span>
                                    </button>
                                </div>

                                <!-- Center: Step Info -->
                                <div class="nav-center">
                                    <div class="step-info">
                                        <div class="step-current">
                                            <span class="step-number" id="currentStepNumber">1</span>
                                            <span class="step-divider">/</span>
                                            <span class="step-total">4</span>
                                        </div>
                                        <div class="step-description" id="stepDescription">
                                            Selecciona las colmenas para el {{ $tipo }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Side: Next/Submit Button -->
                                <div class="nav-right">
                                    <button type="button" class="nav-btn nav-btn-next" id="nextBtn">
                                        <span class="btn-text">Siguiente</span>
                                        <div class="btn-icon">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                    </button>

                                    <button type="submit" class="nav-btn nav-btn-submit" id="submitBtn"
                                        style="display: none;">
                                        <div class="btn-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <span class="btn-text">Confirmar {{ ucfirst($tipo) }}</span>
                                        <div class="btn-sparkle"></div>
                                    </button>
                                </div>
                            </div>

                            <!-- Status Bar -->
                            <div class="nav-status">
                                <div class="status-item" id="colmenasStatus">
                                    <i class="fas fa-cube"></i>
                                    <span>0 colmenas seleccionadas</span>
                                </div>
                                <div class="status-item" id="validationStatus">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Completa todos los campos requeridos</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-container">
            <!-- Header con efecto brillante -->
            <div class="modal-header">
                <div class="modal-sparkles">
                    <span class="sparkle" style="top: 20%; left: 15%;"></span>
                    <span class="sparkle" style="top: 60%; left: 80%;"></span>
                    <span class="sparkle" style="top: 40%; left: 10%;"></span>
                    <span class="sparkle" style="top: 70%; left: 70%;"></span>
                </div>
                <div class="modal-icon">
                    <i class="fas fa-route"></i>
                </div>
                <h3 class="modal-title">Confirmar {{ ucfirst($tipo) }}</h3>
            </div>

            <!-- Body del Modal -->
            <div class="modal-body">
                <p class="modal-subtitle">
                    ¿Está seguro de que desea crear este apiario temporal para el {{ $tipo }}?
                </p>

                <!-- Detalles del apiario -->
                <div class="modal-details">
                    <div class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-tag"></i>
                            Nombre del Apiario
                        </span>
                        <span class="detail-value" id="modalApiarioNombre">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-cube"></i>
                            Colmenas Seleccionadas
                        </span>
                        <span class="detail-value" id="modalColmenasCount">0</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-calendar"></i>
                            Fecha de Inicio
                        </span>
                        <span class="detail-value" id="modalFechaInicio">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-bullseye"></i>
                            Motivo
                        </span>
                        <span class="detail-value" id="modalMotivo">-</span>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" id="modalCancel">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="button" class="modal-btn modal-btn-confirm" id="modalConfirm">
                        <i class="fas fa-check"></i>
                        <span>Confirmar {{ ucfirst($tipo) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('optional-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Sistema de wizard profesional cargado');

            let currentStep = 1;
            const totalSteps = 4;

            // ===== SISTEMA DE NOTIFICACIONES PROFESIONAL =====
            const notificationContainer = document.getElementById('notificationContainer');
            let notificationId = 0;

            function showNotification(message, type = 'error', duration = 6000) {
                const notificationId = Date.now();
                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;
                notification.setAttribute('data-notification-id', notificationId);

                // Configurar iconos profesionales según el tipo
                const icons = {
                    error: 'fas fa-exclamation-triangle',
                    success: 'fas fa-check-circle',
                    warning: 'fas fa-exclamation-circle',
                    info: 'fas fa-info-circle'
                };

                // Configurar títulos profesionales
                const titles = {
                    error: 'Error de Validación',
                    success: 'Operación Exitosa',
                    warning: 'Advertencia',
                    info: 'Información'
                };

                notification.innerHTML = `
                            <div class="notification-content">
                                <div class="notification-icon">
                                    <i class="${icons[type]}"></i>
                                </div>
                                <div class="notification-body">
                                    <div class="notification-title">${titles[type]}</div>
                                    <div class="notification-message">${message}</div>
                                </div>
                                <button class="notification-close" onclick="closeNotification(${notificationId})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="notification-progress"></div>
                        `;

                // Agregar al contenedor
                notificationContainer.appendChild(notification);

                // Mostrar con animación
                setTimeout(() => {
                    notification.classList.add('show');
                }, 100);

                // Iniciar barra de progreso
                const progressBar = notification.querySelector('.notification-progress');
                progressBar.style.animation = `notificationProgress ${duration}ms linear forwards`;

                // Auto-cerrar
                setTimeout(() => {
                    closeNotification(notificationId);
                }, duration);

                return notification;
            }

            // Función global para cerrar notificaciones
            window.closeNotification = function (notificationId) {
                const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notification) {
                    notification.classList.add('hide');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }
            };

            // 1) Selección de nodos en DOM
            const steps = document.querySelectorAll('.wizard-step');
            const navDots = document.querySelectorAll('.nav-dot');
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');
            const submitBtn = document.getElementById('submitBtn');
            const currentStepNumber = document.getElementById('currentStepNumber');
            const stepDescription = document.getElementById('stepDescription');
            const progressFill = document.getElementById('progressFill');
            const colmenasStatus = document.getElementById('colmenasStatus');
            const validationStatus = document.getElementById('validationStatus');
            const motivoMovimiento = document.getElementById('motivo_select');

            // 2) Controles específicos por paso
            const nombreTemporal = document.getElementById('nombreTemporal');
            const destinoRegion = document.getElementById('destinoRegionSelect');
            const destinoComuna = document.getElementById('destinoComunaSelect');
            const fechaInicio = document.getElementById('fecha_inicio_mov');
            const fechaTermino = document.getElementById('fecha_termino_mov');
            const polinizacionSection = document.querySelector('.polinizacion-section');
            const cultivo = document.getElementById('cultivo');
            const periodoFloracion = document.getElementById('periodo_floracion');
            const hectareas = document.getElementById('hectareas');

            // Campos del Paso 2 (Apicultor)
            const apicultorNombre = document.querySelector('input[name="apicultor_nombre"]');
            const apicultorRut = document.querySelector('input[name="apicultor_rut"]');
            const registroNacional = document.querySelector('input[name="registro_nacional"]');

            // Campos del Paso 4 (Transporte)
            const transportistaNombre = document.querySelector('input[name="transportista_nombre"]');
            const transportistaRut = document.querySelector('input[name="transportista_rut"]');
            const vehiculoPatente = document.querySelector('input[name="vehiculo_patente"]');
            const coordenadasDestino = document.querySelector('input[name="coordenadas_destino"]');

            // ===== FUNCIONES DE VALIDACIÓN POR PASO =====

            function validateNombreApiario() {
                if (!nombreTemporal.value.trim()) {
                    showNotification('Debe ingresar un nombre para el apiario temporal', 'warning');
                    nombreTemporal.focus();
                    return false;
                }
                if (nombreTemporal.value.trim().length < 3) {
                    showNotification('El nombre del apiario debe contener al menos 3 caracteres', 'warning');
                    nombreTemporal.focus();
                    return false;
                }
                return true;
            }

            function validateStep1() {
                if (!validateNombreApiario()) return false;

                const selectedColmenas = document.querySelectorAll('.colmena-check:checked');
                if (selectedColmenas.length === 0) {
                    showNotification('Debe seleccionar al menos una colmena para proceder con el {{ $tipo }}', 'error');
                    return false;
                }

                // Validar que se seleccione al menos una colmena de cada apiario
                const apiarios = document.querySelectorAll('.apiario-card');
                const apiariosConColmenas = [];
                const apiariosVacios = [];

                apiarios.forEach(apiarioCard => {
                    const apiarioInfo = apiarioCard.querySelector('.apiario-info h5');
                    const apiarioNombre = apiarioInfo.textContent.trim();

                    // Obtener el ID del apiario del primer checkbox
                    const firstCheckbox = apiarioCard.querySelector('.colmena-check');
                    if (!firstCheckbox) return; // Si no hay colmenas, saltar

                    const apiarioId = firstCheckbox.dataset.apiario;
                    const colmenasDelApiario = apiarioCard.querySelectorAll('.colmena-check:checked');

                    if (colmenasDelApiario.length > 0) {
                        apiariosConColmenas.push({
                            nombre: apiarioNombre,
                            id: apiarioId,
                            cantidad: colmenasDelApiario.length
                        });
                    } else {
                        // Solo considerar como vacío si el apiario tiene colmenas disponibles
                        const totalColmenas = apiarioCard.querySelectorAll('.colmena-check');
                        if (totalColmenas.length > 0) {
                            apiariosVacios.push({
                                nombre: apiarioNombre,
                                id: apiarioId
                            });
                        }
                    }
                });

                // Si hay más de un apiario con colmenas disponibles, validar que todos tengan selección
                const totalApiariosDisponibles = apiariosConColmenas.length + apiariosVacios.length;

                if (totalApiariosDisponibles > 1 && apiariosVacios.length > 0) {
                    const nombresVacios = apiariosVacios.map(a => a.nombre).join(', ');
                    showNotification(
                        `Debe seleccionar al menos una colmena de cada apiario. Faltan colmenas de: ${nombresVacios}`,
                        'warning'
                    );

                    // Hacer scroll al primer apiario vacío para mejor UX
                    const primerApiarioVacio = document.querySelector(`[data-apiario="${apiariosVacios[0].id}"]`).closest('.apiario-card');
                    primerApiarioVacio.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    return false;
                }

                // Mostrar mensaje de éxito detallado
                if (apiariosConColmenas.length === 1) {
                    showNotification(
                        `Configuración completada: ${selectedColmenas.length} colmenas seleccionadas del apiario ${apiariosConColmenas[0].nombre}`,
                        'success',
                        3000
                    );
                } else {
                    const detalleApiarios = apiariosConColmenas.map(a => `${a.cantidad} de ${a.nombre}`).join(', ');
                    showNotification(
                        `Configuración completada: ${selectedColmenas.length} colmenas seleccionadas (${detalleApiarios})`,
                        'success',
                        3000
                    );
                }

                return true;
            }

            function validateStep2() {
                const requiredFields = [
                    { field: apicultorNombre, name: 'Nombre del Apicultor' },
                    { field: apicultorRut, name: 'RUT del Apicultor' },
                    { field: registroNacional, name: 'Registro Nacional de Apicultores' }
                ];

                for (let item of requiredFields) {
                    if (!item.field.value.trim()) {
                        showNotification(`El campo "${item.name}" es obligatorio y debe ser completado`, 'error');
                        item.field.focus();
                        return false;
                    }
                }

                // Validar formato RUT básico
                if (!validateRutFormat(apicultorRut.value)) {
                    showNotification('El formato del RUT del apicultor no es válido. Use el formato: 12.345.678-9', 'error');
                    apicultorRut.focus();
                    return false;
                }

                showNotification('Información del apicultor validada correctamente', 'success', 3000);
                return true;
            }

            function validateStep3() {
                @if($tipo == 'traslado')
                    if (!destinoRegion.value) {
                        showNotification('Debe seleccionar la región de destino para el traslado', 'error');
                        destinoRegion.focus();
                        return false;
                    }

                    if (!destinoComuna.value) {
                        showNotification('Debe seleccionar la comuna de destino para el traslado', 'error');
                        destinoComuna.focus();
                        return false;
                    }

                    if (coordenadasDestino && !coordenadasDestino.value.trim()) {
                        showNotification('Las coordenadas de destino son obligatorias para el traslado', 'error');
                        coordenadasDestino.focus();
                        return false;
                    }

                    if (coordenadasDestino && !validateCoordinatesFormat(coordenadasDestino.value)) {
                        showNotification('El formato de coordenadas no es válido. Use el formato: -33.0472, -71.4419', 'error');
                        coordenadasDestino.focus();
                        return false;
                    }

                    showNotification('Ubicaciones de origen y destino configuradas correctamente', 'success', 3000);
                @else
                    showNotification('Información de ubicación verificada correctamente', 'success', 3000);
                @endif

                        return true;
            }

            function validateStep4() {
                const requiredFields = [
                    { field: fechaInicio, name: 'Fecha de Inicio' },
                    { field: fechaTermino, name: 'Fecha de Término' },
                    { field: motivoMovimiento, name: 'Motivo del Movimiento' },
                    { field: transportistaNombre, name: 'Nombre del Transportista' },
                    { field: transportistaRut, name: 'RUT del Transportista' },
                    { field: vehiculoPatente, name: 'Patente del Vehículo' }
                ];

                for (let item of requiredFields) {
                    if (!item.field.value.trim()) {
                        showNotification(`El campo "${item.name}" es obligatorio para completar el registro`, 'error');
                        item.field.focus();
                        return false;
                    }
                }

                // Validar fechas
                if (fechaInicio.value > fechaTermino.value) {
                    showNotification('La fecha de inicio no puede ser posterior a la fecha de término', 'error');
                    fechaInicio.focus();
                    return false;
                }

                // Validar fechas no sean del pasado
                const today = new Date().toISOString().split('T')[0];
                if (fechaInicio.value < today) {
                    showNotification('La fecha de inicio no puede ser anterior a la fecha actual', 'warning');
                    fechaInicio.focus();
                    return false;
                }

                // Validar RUT transportista
                if (!validateRutFormat(transportistaRut.value)) {
                    showNotification('El formato del RUT del transportista no es válido', 'error');
                    transportistaRut.focus();
                    return false;
                }

                // Validar patente
                if (!validatePatenteFormat(vehiculoPatente.value)) {
                    showNotification('El formato de la patente del vehículo no es válido. Use formatos como: ABCD-12 o AB-1234', 'error');
                    vehiculoPatente.focus();
                    return false;
                }

                // Si es polinización, validar campos adicionales
                if (motivoMovimiento.value === 'Polinización') {
                    const polinizacionFields = [
                        { field: cultivo, name: 'Tipo de Cultivo' },
                        { field: periodoFloracion, name: 'Período de Floración' },
                        { field: hectareas, name: 'Cantidad de Hectáreas' }
                    ];

                    for (let item of polinizacionFields) {
                        if (!item.field.value.trim()) {
                            showNotification(`El campo "${item.name}" es obligatorio para actividades de polinización`, 'error');
                            item.field.focus();
                            return false;
                        }
                    }

                    if (parseFloat(hectareas.value) <= 0) {
                        showNotification('La cantidad de hectáreas debe ser mayor a 0', 'error');
                        hectareas.focus();
                        return false;
                    }
                }

                showNotification('Información del movimiento validada correctamente', 'success', 3000);
                return true;
            }

            // ===== FUNCIONES DE VALIDACIÓN AUXILIARES =====

            function validateRutFormat(rut) {
                const rutRegex = /^\d{1,2}\.?\d{3}\.?\d{3}-[\dkK]$/;
                return rutRegex.test(rut.trim());
            }

            function validatePatenteFormat(patente) {
                const patenteRegex = /^[A-Z]{2,4}-?\d{2,4}$/i;
                return patenteRegex.test(patente.trim());
            }

            function validateCoordinatesFormat(coords) {
                const coordsRegex = /^-?\d+\.?\d*,\s*-?\d+\.?\d*$/;
                return coordsRegex.test(coords.trim());
            }

            function showValidationSuccess(message) {
                validationStatus.innerHTML = `<i class="fas fa-check-circle"></i><span>${message}</span>`;
                validationStatus.classList.add('success');
                validationStatus.classList.remove('error', 'warning');
            }

            // ===== FUNCIONES PRINCIPALES =====

            function showStep(step) {
                steps.forEach(div => div.classList.remove('active'));
                document.getElementById(`step-${step}`).classList.add('active');

                prevBtn.style.display = (step === 1) ? 'none' : 'inline-block';
                nextBtn.style.display = (step === totalSteps) ? 'none' : 'inline-block';
                submitBtn.style.display = (step === totalSteps) ? 'inline-block' : 'none';

                navDots.forEach((dot, idx) => {
                    dot.classList.remove('active', 'completed');
                    if (idx + 1 < step) dot.classList.add('completed');
                    if (idx + 1 === step) dot.classList.add('active');
                });

                currentStepNumber.textContent = step;
                updateStepDescription(step);
                updateValidationStatus(step);
                updateProgressBar(step);
            }

            function updateStepDescription(step) {
                const descriptions = {
                    1: 'Seleccione las colmenas para el {{ $tipo }}',
                    2: 'Complete la información del apicultor responsable',
                    3: 'Configure las ubicaciones {{ $tipo == "traslado" ? "de origen y destino" : "del retorno" }}',
                    4: 'Ingrese los detalles del movimiento y transporte'
                };
                stepDescription.textContent = descriptions[step];
            }

            function updateProgressBar(step) {
                const progress = ((step - 1) / (totalSteps - 1)) * 100;
                progressFill.style.width = `${progress}%`;
            }

            function updateValidationStatus(step) {
                switch (step) {
                    case 1:
                        const selCount = document.querySelectorAll('.colmena-check:checked').length;
                        const nombreOk = nombreTemporal.value.trim().length >= 3;

                        if (nombreOk && selCount > 0) {
                            // Verificar distribución por apiario
                            const apiarios = document.querySelectorAll('.apiario-card');
                            const apiariosConColmenas = [];
                            const apiariosVacios = [];

                            apiarios.forEach(apiarioCard => {
                                const firstCheckbox = apiarioCard.querySelector('.colmena-check');
                                if (!firstCheckbox) return;

                                const apiarioId = firstCheckbox.dataset.apiario;
                                const colmenasDelApiario = apiarioCard.querySelectorAll('.colmena-check:checked');
                                const totalColmenas = apiarioCard.querySelectorAll('.colmena-check');

                                if (colmenasDelApiario.length > 0) {
                                    apiariosConColmenas.push(apiarioId);
                                } else if (totalColmenas.length > 0) {
                                    apiariosVacios.push(apiarioId);
                                }
                            });

                            const totalApiariosDisponibles = apiariosConColmenas.length + apiariosVacios.length;

                            if (totalApiariosDisponibles > 1 && apiariosVacios.length > 0) {
                                validationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Seleccione colmenas de todos los apiarios disponibles</span>';
                                validationStatus.classList.add('warning');
                                validationStatus.classList.remove('success', 'error');
                            } else {
                                showValidationSuccess('Configuración de colmenas completada');
                            }
                        } else if (!nombreOk) {
                            validationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Ingrese un nombre para el apiario temporal</span>';
                            validationStatus.classList.add('warning');
                            validationStatus.classList.remove('success', 'error');
                        } else {
                            validationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Seleccione al menos una colmena</span>';
                            validationStatus.classList.add('warning');
                            validationStatus.classList.remove('success', 'error');
                        }
                        break;
                    case 2:
                        validationStatus.innerHTML = '<i class="fas fa-user-check"></i><span>Complete la información del apicultor</span>';
                        validationStatus.classList.add('warning');
                        validationStatus.classList.remove('success', 'error');
                        break;
                    case 3:
                        validationStatus.innerHTML = '<i class="fas fa-map-marker-alt"></i><span>Configure las ubicaciones correctamente</span>';
                        validationStatus.classList.add('warning');
                        validationStatus.classList.remove('success', 'error');
                        break;
                    case 4:
                        validationStatus.innerHTML = '<i class="fas fa-clipboard-check"></i><span>Complete la información del movimiento</span>';
                        validationStatus.classList.add('warning');
                        validationStatus.classList.remove('success', 'error');
                        break;
                }
            }

            // ===== EVENT LISTENERS =====

            nextBtn.addEventListener('click', function () {
                let isValid = false;

                switch (currentStep) {
                    case 1: isValid = validateStep1(); break;
                    case 2: isValid = validateStep2(); break;
                    case 3: isValid = validateStep3(); break;
                    case 4: isValid = validateStep4(); break;
                }

                if (isValid && currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            prevBtn.addEventListener('click', function () {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            nombreTemporal.addEventListener('input', function () {
                if (currentStep === 1) {
                    updateValidationStatus(1);
                }
            });

            motivoMovimiento.addEventListener('change', function () {
                if (this.value === 'Polinización') {
                    polinizacionSection.style.display = 'block';
                    showNotification('Se han habilitado los campos específicos para polinización', 'info', 3000);
                } else {
                    polinizacionSection.style.display = 'none';
                }
            });

            submitBtn.addEventListener('click', function (evt) {
                evt.preventDefault();

                // Validación final completa
                if (!validateStep1()) return;
                if (!validateStep2()) return;
                if (!validateStep3()) return;
                if (!validateStep4()) return;

                // Si todo está correcto, mostrar modal
                const selectedColmenas = document.querySelectorAll('.colmena-check:checked');
                showConfirmationModal(selectedColmenas.length);
            });

            // ===== MODAL DE CONFIRMACIÓN =====

            function showConfirmationModal(colmenasCount) {
                const modal = document.getElementById('confirmModal');
                const modalApiarioNombre = document.getElementById('modalApiarioNombre');
                const modalColmenasCount = document.getElementById('modalColmenasCount');
                const modalFechaInicio = document.getElementById('modalFechaInicio');
                const modalMotivo = document.getElementById('modalMotivo');

                modalApiarioNombre.textContent = nombreTemporal.value || 'Sin nombre';
                modalColmenasCount.textContent = `${colmenasCount} colmenas`;
                modalFechaInicio.textContent = fechaInicio.value ? new Date(fechaInicio.value).toLocaleDateString('es-ES') : 'Sin fecha';
                modalMotivo.textContent = motivoMovimiento.value || 'Sin motivo';

                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            document.getElementById('modalCancel').addEventListener('click', function () {
                closeModal();
            });

            document.getElementById('modalConfirm').addEventListener('click', function () {
                const confirmBtn = this;
                confirmBtn.classList.add('loading');
                confirmBtn.querySelector('span').textContent = 'Procesando...';

                setTimeout(() => {
                    document.getElementById('form-temporal').submit();
                }, 1500);
            });

            document.getElementById('confirmModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            function closeModal() {
                const modal = document.getElementById('confirmModal');
                modal.classList.remove('active');
                document.body.style.overflow = '';

                const confirmBtn = document.getElementById('modalConfirm');
                confirmBtn.classList.remove('loading');
                confirmBtn.querySelector('span').textContent = 'Confirmar {{ $tipo }}';
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            // ===== SELECCIÓN DE COLMENAS =====

            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('select-all-colmenas')) {
                    const apiarioId = e.target.dataset.apiario;
                    const colmenas = document.querySelectorAll(`input[data-apiario="${apiarioId}"].colmena-check`);
                    colmenas.forEach(c => c.checked = e.target.checked);
                    updateColmenasCount();
                }
                if (e.target.classList.contains('colmena-check')) {
                    updateColmenasCount();
                }
            });

            function updateColmenasCount() {
                const selCount = document.querySelectorAll('.colmena-check:checked').length;

                // Contar por apiario
                const apiarios = document.querySelectorAll('.apiario-card');
                const distribucion = [];

                apiarios.forEach(apiarioCard => {
                    const apiarioInfo = apiarioCard.querySelector('.apiario-info h5');
                    const apiarioNombre = apiarioInfo.textContent.trim();
                    const colmenasSeleccionadas = apiarioCard.querySelectorAll('.colmena-check:checked').length;
                    const totalColmenas = apiarioCard.querySelectorAll('.colmena-check').length;

                    if (totalColmenas > 0) {
                        distribucion.push({
                            nombre: apiarioNombre,
                            seleccionadas: colmenasSeleccionadas,
                            total: totalColmenas
                        });
                    }
                });

                // Mostrar información detallada
                if (distribucion.length > 1) {
                    const detalles = distribucion.map(d =>
                        `${d.seleccionadas}/${d.total} de ${d.nombre.substring(0, 15)}${d.nombre.length > 15 ? '...' : ''}`
                    ).join(' | ');

                    colmenasStatus.innerHTML = `<i class="fas fa-cube"></i><span>${selCount} colmenas seleccionadas (${detalles})</span>`;
                } else {
                    colmenasStatus.innerHTML = `<i class="fas fa-cube"></i><span>${selCount} colmenas seleccionadas</span>`;
                }

                if (selCount > 0) {
                    colmenasStatus.classList.add('success');
                } else {
                    colmenasStatus.classList.remove('success');
                }

                if (currentStep === 1) {
                    updateValidationStatus(1);
                }
            }

            // ===== CARGA DE COMUNAS =====

            if (destinoRegion) {
                destinoRegion.addEventListener('change', function () {
                    const regionId = this.value;
                    destinoComuna.innerHTML = '<option value="">Cargando comunas...</option>';
                    if (!regionId) {
                        destinoComuna.innerHTML = '<option value="">Seleccionar comuna</option>';
                        return;
                    }
                    fetch(`/comunas/${regionId}`)
                        .then(res => res.json())
                        .then(json => {
                            let html = '<option value="">Seleccionar comuna</option>';
                            json.forEach(c => {
                                html += `<option value="${c.id}">${c.nombre}</option>`;
                            });
                            destinoComuna.innerHTML = html;
                            showNotification('Comunas cargadas correctamente', 'success', 3000);
                        })
                        .catch(() => {
                            destinoComuna.innerHTML = '<option value="">Error al cargar comunas</option>';
                            showNotification('Error al cargar las comunas de la región seleccionada', 'error');
                        });
                });
            }

            if (destinoComuna) {
                destinoComuna.addEventListener('change', function () {
                    const comunaId = this.value;
                    const coordenadasInput = document.querySelector('input[name="coordenadas_destino"]');

                    if (!comunaId) {
                        coordenadasInput.value = '';
                        return;
                    }

                    fetch(`/comuna-coordenadas/${comunaId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.lat && data.lon) {
                                coordenadasInput.value = `${data.lat}, ${data.lon}`;
                                showNotification('Coordenadas obtenidas automáticamente', 'success', 3000);
                            } else {
                                coordenadasInput.value = '';
                                showNotification('No se pudieron obtener las coordenadas automáticamente', 'warning');
                            }
                        })
                        .catch(() => {
                            showNotification('Error al obtener las coordenadas de la comuna seleccionada', 'error');
                            coordenadasInput.value = '';
                        });
                });
            }

            // ===== INICIALIZACIÓN =====

            if (motivoMovimiento?.value === 'Polinización') {
                polinizacionSection.style.display = 'block';
            } else {
                polinizacionSection.style.display = 'none';
            }

            showStep(1);
            updateColmenasCount();

            setTimeout(() => {
                showNotification('Sistema de registro iniciado. Complete todos los pasos para proceder', 'info', 4000);
            }, 500);
        });
    </script>
@endsection