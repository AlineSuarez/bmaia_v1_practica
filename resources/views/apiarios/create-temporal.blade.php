@extends('layouts.app')

@section('title', 'Crear Apiario Temporal')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/create/create-temporal.css') }}" rel="stylesheet">
    </head>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
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
                                placeholder="Ej: Apiario Temporal" value="{{ old('nombre') }}" required>
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
                                            <span class="apiario-meta">{{ $apiario->num_colmenas }} colmenas disponibles</span>
                                            <label class="select-all">
                                                <input type="checkbox"
                                                    class="select-all-colmenas"
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
                                                        <input
                                                            type="checkbox"
                                                            name="colmenas[{{ $apiario->id }}][]"
                                                            value="{{ $colmena->id }}"
                                                            class="colmena-check"
                                                            data-apiario="{{ $apiario->id }}"
                                                        >
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
                                            value="{{ old('apicultor_nombre', 'Juan Carlos Pérez Martínez') }}">
                                    </div>
                                    <div class="form-col">
                                        <label>RUT del Apicultor</label>
                                        <input type="text" class="form-input" name="apicultor_rut" value="{{ old('apicultor_rut', '12.345.678-9') }}">
                                    </div>
                                    <div class="form-col">
                                        <label>Nº Registro Nacional de Apicultores</label>
                                        <input type="text" class="form-input" name="registro_nacional"
                                            value="{{ old('registro_nacional', 'RNA-2024-001234') }}">
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
                                                    value="{{ optional($apiario->comuna)->nombre ?? 'Sin comuna' }}"
                                                    readonly>
                                            </div>
                                            <div class="form-col">
                                                <label>Coordenadas Origen</label>
                                                <input type="text" class="form-input"
                                                    value="{{ $apiario->latitud }}, {{ $apiario->longitud }}"
                                                    readonly>
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
                                                <select class="form-input" name="destino_region_id" id="destinoRegionSelect" required>
                                                    <option value="">Seleccionar región…</option>
                                                    @foreach($regiones as $reg)
                                                        <option value="{{ $reg->id }}"
                                                            {{ old('destino_region_id') == $reg->id ? 'selected' : '' }}>
                                                            {{ $reg->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-col">
                                                <label>Comuna Destino</label>
                                                <select class="form-input" name="destino_comuna_id" id="destinoComunaSelect" required>
                                                    <option value="">Seleccionar comuna…</option>
                                                    {{-- Se rellenará con JS --}}
                                                </select>
                                            </div>
                                            <div class="form-col">
                                                <label>Coordenadas Destino</label>
                                                <input type="text" class="form-input" name="coordenadas_destino"
                                                    value="{{ old('coordenadas_destino', '-33.0472, -71.4419') }}"
                                                    placeholder="Lat, Lng" required>
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
                                           <input type="date" class="form-input" name="fecha_inicio_mov" id="fecha_inicio_mov"
                                                value="{{ old('fecha_inicio_mov', date('Y-m-d')) }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Fecha Término</label>
                                            <input type="date" class="form-input" name="fecha_termino_mov" id="fecha_termino_mov"
                                                value="{{ old('fecha_termino_mov', date('Y-m-d', strtotime('+7 days'))) }}" >
                                        </div>
                                        <div class="form-col">
                                            <label>Motivo</label>
                                            <select class="form-input" name="motivo_movimiento" id="motivo_select">
                                                <option value="Producción" {{ old('motivo_movimiento') == 'Producción' ? 'selected' : '' }} >Producción</option>
                                                <option value="Polinización" {{ old('motivo_movimiento') == 'Polinización' ? 'selected' : '' }} >Polinización</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Polinización (condicional) -->
                                <div class="section-card polinizacion-section" style="{{ old('motivo_movimiento') == 'Polinización' ? 'display:block' : 'display:none' }};">
                                    <h5 class="section-title">Información de Polinización</h5>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label>Cultivo</label>
                                            <input type="text" class="form-input" name="cultivo" id="cultivo" value="{{ old('cultivo', 'Almendros') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Período Floración</label>
                                            <input type="text" class="form-input" name="periodo_floracion" id="periodo_floracion"
                                                value="{{ old('periodo_floracion', 'Agosto - Septiembre') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Hectáreas</label>
                                            <input type="number" class="form-input" name="hectareas" id="hectareas"  value="{{ old('hectareas', 15) }}">
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
                                                value="{{ old('transportista_nombre', 'Transportes González Ltda.') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>RUT Transportista</label>
                                            <input type="text" class="form-input" name="transportista_rut"
                                                value="{{ old('transportista_rut', '76.123.456-7') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Patente Vehículo</label>
                                            <input type="text" class="form-input" name="vehiculo_patente" value="{{ old('vehiculo_patente', 'HLKJ-45') }}">
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
                    <span class="sparkle" style="top: 20%; left: 15%;">✨</span>
                    <span class="sparkle" style="top: 60%; left: 80%;">⭐</span>
                    <span class="sparkle" style="top: 40%; left: 10%;">💫</span>
                    <span class="sparkle" style="top: 70%; left: 70%;">✨</span>
                </div>
                <div class="modal-icon">
                    <i class="fas fa-route"></i>
                </div>
                <h3 class="modal-title">Confirmar {{ ucfirst($tipo) }}</h3>
            </div>

            <!-- Body del Modal -->
            <div class="modal-body">
                <p class="modal-subtitle">
                    ¿Estás seguro de que deseas crear este apiario temporal para el {{ $tipo }}?
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
        console.log('✅ Wizard con navegación rediseñada cargado');

        let currentStep = 1;
        const totalSteps = 4;

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

        // 2) Controles del Paso 3 (ubicación destino, solo si tipo == 'traslado')
        const destinoRegion  = document.getElementById('destinoRegionSelect');
        const destinoComuna  = document.getElementById('destinoComunaSelect');

        // 3) Controles del Paso 4 (movimiento)
        const fechaInicio      = document.getElementById('fecha_inicio_mov');
        const fechaTermino     = document.getElementById('fecha_termino_mov');

        // 4) Campos de “Polinización” dentro de Paso 4. Inicialmente pueden estar ocultos:
        const polinizacionSection = document.querySelector('.polinizacion-section');
        const cultivo            = document.getElementById('cultivo');
        const periodoFloracion   = document.getElementById('periodo_floracion');
        const hectareas          = document.getElementById('hectareas');

        // Función para mostrar/ocultar el paso actual y ajustar required
        function showStep(step) {
            // (a) Ocultar todos los .wizard-step y solo mostrar el actual
            steps.forEach(div => div.classList.remove('active'));
            document.getElementById(`step-${step}`).classList.add('active');

            // (b) Ajustar visibilidad de botones “Anterior” / “Siguiente” / “Confirmar”
            prevBtn.style.display   = (step === 1)          ? 'none'         : 'inline-block';
            nextBtn.style.display   = (step === totalSteps) ? 'none'         : 'inline-block';
            submitBtn.style.display = (step === totalSteps) ? 'inline-block' : 'none';

            // (c) Actualizar los puntos de progreso
            navDots.forEach((dot, idx) => {
                dot.classList.remove('active','completed');
                if (idx + 1 < step)    dot.classList.add('completed');
                if (idx + 1 === step)  dot.classList.add('active');
            });

            // (d) Required dinámicos:

            // → Paso 3: Ubicación Destino (solo si estamos en paso 3 y destinoRegion existe)
            if (step === 3 && destinoRegion && destinoComuna) {
                destinoRegion.setAttribute('required','required');
                destinoComuna.setAttribute('required','required');
            } else {
                if (destinoRegion) destinoRegion.removeAttribute('required');
                if (destinoComuna) destinoComuna.removeAttribute('required');
            }

            // → Paso 4: Movimiento → siempre required en fechaInicio, fechaTermino y motivoSelect
            if (step === 4) {
                if (fechaInicio)      fechaInicio.setAttribute('required','required');
                if (fechaTermino)     fechaTermino.setAttribute('required','required');
                if (motivoMovimiento) motivoMovimiento.setAttribute('required','required');
            } else {
                if (fechaInicio)      fechaInicio.removeAttribute('required');
                if (fechaTermino)     fechaTermino.removeAttribute('required');
                if (motivoMovimiento) motivoMovimiento.removeAttribute('required');
            }

            // → Paso 4 + Motivo == “Polinización” → cultivar / periodo_floracion / hectareas required
            if (step === 4 && motivoMovimiento?.value === 'Polinización') {
                if (cultivo)           cultivo.setAttribute('required','required');
                if (periodoFloracion)  periodoFloracion.setAttribute('required','required');
                if (hectareas)         hectareas.setAttribute('required','required');
            } else {
                if (cultivo)           cultivo.removeAttribute('required');
                if (periodoFloracion)  periodoFloracion.removeAttribute('required');
                if (hectareas)         hectareas.removeAttribute('required');
            }
        }

        // 5) Botón “Siguiente” → avanza paso (con validación interna en paso 1)
        nextBtn.addEventListener('click', function () {
            if (currentStep === 1) {
                const selCount = document.querySelectorAll('.colmena-check:checked').length;
                if (selCount === 0) {
                    alert('Debes seleccionar al menos una colmena.');
                    return;
                }
            }
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });

        // 6) Botón “Anterior” → retrocede paso
        prevBtn.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // 7) Cuando cambia el select “motivo_movimiento” → muestro/oculto la sección Polinización
        motivoMovimiento.addEventListener('change', function () {
            if (this.value === 'Polinización') {
                polinizacionSection.style.display = 'block';
            } else {
                polinizacionSection.style.display = 'none';
            }
            // Después de mostrar/ocultar, vuelvo a invocar showStep para reajustar required
            showStep(currentStep);
        });

        // 8) Botón "Confirmar Traslado/Retorno" (Submit) - ACTUALIZADO CON MODAL
        submitBtn.addEventListener('click', function (evt) {
            evt.preventDefault(); // evitamos envío automático

            // → Validar que Fecha Inicio ≤ Fecha Término
            if (fechaInicio.value && fechaTermino.value) {
                if (fechaInicio.value > fechaTermino.value) {
                    alert('La fecha de inicio no puede ser mayor que la fecha término.');
                    return;
                }
            }

            // → Si motivo == "Polinización", validar campos obligatorios
            if ((motivoMovimiento?.value || '') === 'Polinización') {
                if (!cultivo.value.trim() ||
                    !periodoFloracion.value.trim() ||
                    !hectareas.value) {
                    alert('Debes completar todos los campos de polinización.');
                    return;
                }
            }

            // → Validar nuevamente al menos una colmena seleccionada
            const selectedColmenas = document.querySelectorAll('.colmena-check:checked');
            if (selectedColmenas.length === 0) {
                alert('Debes seleccionar al menos una colmena.');
                return;
            }

            // → Mostrar modal con datos del formulario
            showConfirmationModal(selectedColmenas.length);
        });

        // Nueva función para mostrar el modal de confirmación
        function showConfirmationModal(colmenasCount) {
            const modal = document.getElementById('confirmModal');
            const modalApiarioNombre = document.getElementById('modalApiarioNombre');
            const modalColmenasCount = document.getElementById('modalColmenasCount');
            const modalFechaInicio = document.getElementById('modalFechaInicio');
            const modalMotivo = document.getElementById('modalMotivo');

            // Rellenar datos del modal
            modalApiarioNombre.textContent = document.getElementById('nombreTemporal').value || 'Sin nombre';
            modalColmenasCount.textContent = `${colmenasCount} colmenas`;
            modalFechaInicio.textContent = fechaInicio.value ? new Date(fechaInicio.value).toLocaleDateString('es-ES') : 'Sin fecha';
            modalMotivo.textContent = motivoMovimiento.value || 'Sin motivo';

            // Mostrar modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Manejo de los botones del modal
        document.getElementById('modalCancel').addEventListener('click', function() {
            closeModal();
        });

        document.getElementById('modalConfirm').addEventListener('click', function() {
            const confirmBtn = this;

            // Mostrar loading
            confirmBtn.classList.add('loading');
            confirmBtn.querySelector('span').textContent = 'Creando...';

            // Simular delay para mejor UX
            setTimeout(() => {
                document.getElementById('form-temporal').submit();
                console.log('✅ Formulario enviado correctamente');
            }, 1500);
        });

        // Cerrar modal al hacer click en el overlay
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Función para cerrar el modal
        function closeModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';

            // Reset loading state
            const confirmBtn = document.getElementById('modalConfirm');
            confirmBtn.classList.remove('loading');
            confirmBtn.querySelector('span').textContent = 'Confirmar {{ $tipo }}';
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // 9) Actualización de mensaje de validación en la barra inferior
        function updateValidationStatus(step) {
            switch (step) {
                case 1:
                    const selCount = document.querySelectorAll('.colmena-check:checked').length;
                    if (selCount > 0) {
                        validationStatus.innerHTML = '<i class="fas fa-check-circle"></i><span>Colmenas seleccionadas correctamente</span>';
                        validationStatus.classList.add('success');
                        validationStatus.classList.remove('warning');
                    } else {
                        validationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Selecciona al menos una colmena</span>';
                        validationStatus.classList.add('warning');
                        validationStatus.classList.remove('success');
                    }
                    break;
                case 2:
                    validationStatus.innerHTML = '<i class="fas fa-user-check"></i><span>Información del apicultor completa</span>';
                    validationStatus.classList.add('success');
                    validationStatus.classList.remove('warning');
                    break;
                case 3:
                    validationStatus.innerHTML = '<i class="fas fa-map-check"></i><span>Ubicaciones configuradas</span>';
                    validationStatus.classList.add('success');
                    validationStatus.classList.remove('warning');
                    break;
                case 4:
                    validationStatus.innerHTML = '<i class="fas fa-clipboard-check"></i><span>Listo para confirmar</span>';
                    validationStatus.classList.add('success');
                    validationStatus.classList.remove('warning');
                    break;
            }
        }

        // 10) Click en las “nav-dots” para saltar pasos (solo a pasos completados o siguiente)
        document.querySelectorAll('.nav-dot').forEach((dot, idx) => {
            dot.addEventListener('click', function () {
                if (idx + 1 <= currentStep + 1) {
                    currentStep = idx + 1;
                    showStep(currentStep);
                }
            });
        });

        // 11) Manejo de selección de colmenas (Paso 1)
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

        // 12) Mostrar/ocultar la sección de Polinización en caso de que el select ya traiga “Polinización” en old()
        if (motivoMovimiento?.value === 'Polinización') {
            polinizacionSection.style.display = 'block';
        } else {
            polinizacionSection.style.display = 'none';
        }

        // 13) Función que actualiza el contador de colmenas en la pantalla
        function updateColmenasCount() {
            const selCount = document.querySelectorAll('.colmena-check:checked').length;
            const origenColmenasInput = document.getElementById('origen_colmenas');

            if (origenColmenasInput) {
                origenColmenasInput.value = `${selCount} seleccionadas`;
            }

            // Actualizar la barra inferior de estatus
            colmenasStatus.innerHTML = `<i class="fas fa-cube"></i><span>${selCount} colmenas seleccionadas</span>`;
            if (selCount > 0) {
                colmenasStatus.classList.add('success');
            } else {
                colmenasStatus.classList.remove('success');
            }

            if (currentStep === 1) {
                updateValidationStatus(1);
            }
        }

        // 14) Inicializar wizard en paso 1
        showStep(1);
        updateColmenasCount();

        // 15) Cargar comunas cuando cambia región (Paso 3)
        if (destinoRegion) {
            destinoRegion.addEventListener('change', function () {
                const regionId = this.value;
                destinoComuna.innerHTML = '<option value="">Cargando comunas…</option>';
                if (!regionId) {
                    destinoComuna.innerHTML = '<option value="">Seleccionar comuna…</option>';
                    return;
                }
                fetch(`/comunas/${regionId}`)
                    .then(res => res.json())
                    .then(json => {
                        let html = '<option value="">Seleccionar comuna…</option>';
                        json.forEach(c => {
                            html += `<option value="${c.id}">${c.nombre}</option>`;
                        });
                        destinoComuna.innerHTML = html;
                    })
                    .catch(() => {
                        destinoComuna.innerHTML = '<option value="">Error al cargar comunas</option>';
                    });
            });
        }
    });
    </script>
    <!-- SCRIPT DE ALERTAS PARA CONFIRMAR CADA INPUT Y OPCIONES QUE EXISTAN DENTRO DEL FORMULARIO -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('✅ Wizard con validaciones completas cargado');

            let currentStep = 1;
            const totalSteps = 4;

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

            // Validación Paso 0: Nombre del Apiario Temporal
            function validateNombreApiario() {
                if (!nombreTemporal.value.trim()) {
                    showValidationError('Debes ingresar un nombre para el apiario temporal');
                    nombreTemporal.focus();
                    return false;
                }
                if (nombreTemporal.value.trim().length < 3) {
                    showValidationError('El nombre del apiario debe tener al menos 3 caracteres');
                    nombreTemporal.focus();
                    return false;
                }
                return true;
            }

            // Validación Paso 1: Selección de Colmenas
            function validateStep1() {
                if (!validateNombreApiario()) return false;

                const selectedColmenas = document.querySelectorAll('.colmena-check:checked');
                if (selectedColmenas.length === 0) {
                    showValidationError('Debes seleccionar al menos una colmena para el ' + '{{ $tipo }}');
                    return false;
                }
                return true;
            }

            // Validación Paso 2: Información del Apicultor
            function validateStep2() {
                const requiredFields = [
                    { field: apicultorNombre, name: 'Nombre del Apicultor' },
                    { field: apicultorRut, name: 'RUT del Apicultor' },
                    { field: registroNacional, name: 'Registro Nacional de Apicultores' }
                ];

                for (let item of requiredFields) {
                    if (!item.field.value.trim()) {
                        showValidationError(`El campo "${item.name}" es obligatorio`);
                        item.field.focus();
                        return false;
                    }
                }

                // Validar formato RUT básico
                if (!validateRutFormat(apicultorRut.value)) {
                    showValidationError('El formato del RUT del apicultor no es válido (ej: 12.345.678-9)');
                    apicultorRut.focus();
                    return false;
                }

                return true;
            }

            // Validación Paso 3: Ubicación
            function validateStep3() {
                @if($tipo == 'traslado')
                    // Solo validar destino si es traslado
                    if (!destinoRegion.value) {
                        showValidationError('Debes seleccionar la región destino');
                        destinoRegion.focus();
                        return false;
                    }

                    if (!destinoComuna.value) {
                        showValidationError('Debes seleccionar la comuna destino');
                        destinoComuna.focus();
                        return false;
                    }

                    if (coordenadasDestino && !coordenadasDestino.value.trim()) {
                        showValidationError('Debes ingresar las coordenadas del destino');
                        coordenadasDestino.focus();
                        return false;
                    }

                    // Validar formato de coordenadas básico
                    if (coordenadasDestino && !validateCoordinatesFormat(coordenadasDestino.value)) {
                        showValidationError('El formato de coordenadas no es válido (ej: -33.0472, -71.4419)');
                        coordenadasDestino.focus();
                        return false;
                    }
                @endif

            return true;
            }

            // Validación Paso 4: Movimiento
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
                        showValidationError(`El campo "${item.name}" es obligatorio`);
                        item.field.focus();
                        return false;
                    }
                }

                // Validar fechas
                if (fechaInicio.value > fechaTermino.value) {
                    showValidationError('La fecha de inicio no puede ser mayor que la fecha término');
                    fechaInicio.focus();
                    return false;
                }

                // Validar fechas no sean del pasado
                const today = new Date().toISOString().split('T')[0];
                if (fechaInicio.value < today) {
                    showValidationError('La fecha de inicio no puede ser anterior a hoy');
                    fechaInicio.focus();
                    return false;
                }

                // Validar RUT transportista
                if (!validateRutFormat(transportistaRut.value)) {
                    showValidationError('El formato del RUT del transportista no es válido');
                    transportistaRut.focus();
                    return false;
                }

                // Validar patente
                if (!validatePatenteFormat(vehiculoPatente.value)) {
                    showValidationError('El formato de la patente no es válido (ej: ABCD-12 o AB-1234)');
                    vehiculoPatente.focus();
                    return false;
                }

                // Si es polinización, validar campos adicionales
                if (motivoMovimiento.value === 'Polinización') {
                    const polinizacionFields = [
                        { field: cultivo, name: 'Cultivo' },
                        { field: periodoFloracion, name: 'Período de Floración' },
                        { field: hectareas, name: 'Hectáreas' }
                    ];

                    for (let item of polinizacionFields) {
                        if (!item.field.value.trim()) {
                            showValidationError(`El campo "${item.name}" es obligatorio para polinización`);
                            item.field.focus();
                            return false;
                        }
                    }

                    if (hectareas.value <= 0) {
                        showValidationError('Las hectáreas deben ser mayor a 0');
                        hectareas.focus();
                        return false;
                    }
                }

                return true;
            }

            // ===== FUNCIONES DE VALIDACIÓN AUXILIARES =====

            function validateRutFormat(rut) {
                // Formato básico: 12.345.678-9 o 12345678-9
                const rutRegex = /^\d{1,2}\.?\d{3}\.?\d{3}-[\dkK]$/;
                return rutRegex.test(rut.trim());
            }

            function validatePatenteFormat(patente) {
                // Formatos: ABCD-12, AB-1234, ABC-123
                const patenteRegex = /^[A-Z]{2,4}-?\d{2,4}$/i;
                return patenteRegex.test(patente.trim());
            }

            function validateCoordinatesFormat(coords) {
                // Formato: -33.0472, -71.4419
                const coordsRegex = /^-?\d+\.?\d*,\s*-?\d+\.?\d*$/;
                return coordsRegex.test(coords.trim());
            }

            function showValidationError(message) {
                // Mostrar error en la barra de estado
                validationStatus.innerHTML = `<i class="fas fa-exclamation-triangle"></i><span>${message}</span>`;
                validationStatus.classList.add('error');
                validationStatus.classList.remove('success', 'warning');

                // También mostrar alert para mayor visibilidad
                alert(message);
            }

            function showValidationSuccess(message) {
                validationStatus.innerHTML = `<i class="fas fa-check-circle"></i><span>${message}</span>`;
                validationStatus.classList.add('success');
                validationStatus.classList.remove('error', 'warning');
            }

            // ===== FUNCIONES PRINCIPALES =====

            function showStep(step) {
                // Ocultar todos los steps y mostrar el actual
                steps.forEach(div => div.classList.remove('active'));
                document.getElementById(`step-${step}`).classList.add('active');

                // Ajustar botones
                prevBtn.style.display = (step === 1) ? 'none' : 'inline-block';
                nextBtn.style.display = (step === totalSteps) ? 'none' : 'inline-block';
                submitBtn.style.display = (step === totalSteps) ? 'inline-block' : 'none';

                // Actualizar dots de progreso
                navDots.forEach((dot, idx) => {
                    dot.classList.remove('active', 'completed');
                    if (idx + 1 < step) dot.classList.add('completed');
                    if (idx + 1 === step) dot.classList.add('active');
                });

                // Actualizar números y descripción
                currentStepNumber.textContent = step;
                updateStepDescription(step);
                updateValidationStatus(step);
                updateProgressBar(step);
            }

            function updateStepDescription(step) {
                const descriptions = {
                    1: 'Selecciona las colmenas para el {{ $tipo }}',
                    2: 'Completa la información del apicultor responsable',
                    3: 'Configura las ubicaciones {{ $tipo == "traslado" ? "origen y destino" : "del retorno" }}',
                    4: 'Ingresa los detalles del movimiento y transporte'
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
                            showValidationSuccess('Nombre y colmenas configurados correctamente');
                        } else if (!nombreOk) {
                            validationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Ingresa un nombre para el apiario temporal</span>';
                            validationStatus.classList.add('warning');
                            validationStatus.classList.remove('success', 'error');
                        } else {
                            validationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Selecciona al menos una colmena</span>';
                            validationStatus.classList.add('warning');
                            validationStatus.classList.remove('success', 'error');
                        }
                        break;
                    case 2:
                        validationStatus.innerHTML = '<i class="fas fa-info-circle"></i><span>Completa todos los campos del apicultor</span>';
                        validationStatus.classList.add('warning');
                        validationStatus.classList.remove('success', 'error');
                        break;
                    case 3:
                        validationStatus.innerHTML = '<i class="fas fa-info-circle"></i><span>Configura las ubicaciones correctamente</span>';
                        validationStatus.classList.add('warning');
                        validationStatus.classList.remove('success', 'error');
                        break;
                    case 4:
                        validationStatus.innerHTML = '<i class="fas fa-clipboard-check"></i><span>Completa la información del movimiento</span>';
                        validationStatus.classList.add('warning');
                        validationStatus.classList.remove('success', 'error');
                        break;
                }
            }

            // ===== EVENT LISTENERS =====

            // Botón Siguiente con validación
            nextBtn.addEventListener('click', function () {
                let isValid = false;

                switch (currentStep) {
                    case 1:
                        isValid = validateStep1();
                        break;
                    case 2:
                        isValid = validateStep2();
                        break;
                    case 3:
                        isValid = validateStep3();
                        break;
                    case 4:
                        isValid = validateStep4();
                        break;
                }

                if (isValid && currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            // Botón Anterior
            prevBtn.addEventListener('click', function () {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            // Validación en tiempo real del nombre del apiario
            nombreTemporal.addEventListener('input', function () {
                if (currentStep === 1) {
                    updateValidationStatus(1);
                }
            });

            // Mostrar/ocultar sección de polinización
            motivoMovimiento.addEventListener('change', function () {
                if (this.value === 'Polinización') {
                    polinizacionSection.style.display = 'block';
                } else {
                    polinizacionSection.style.display = 'none';
                }
            });

            // Botón Submit con validación final
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

                // Rellenar datos del modal
                modalApiarioNombre.textContent = nombreTemporal.value || 'Sin nombre';
                modalColmenasCount.textContent = `${colmenasCount} colmenas`;
                modalFechaInicio.textContent = fechaInicio.value ? new Date(fechaInicio.value).toLocaleDateString('es-ES') : 'Sin fecha';
                modalMotivo.textContent = motivoMovimiento.value || 'Sin motivo';

                // Mostrar modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            // Botones del modal
            document.getElementById('modalCancel').addEventListener('click', function () {
                closeModal();
            });

            document.getElementById('modalConfirm').addEventListener('click', function () {
                const confirmBtn = this;

                // Mostrar loading
                confirmBtn.classList.add('loading');
                confirmBtn.querySelector('span').textContent = 'Creando...';

                // Simular delay para mejor UX
                setTimeout(() => {
                    document.getElementById('form-temporal').submit();
                    console.log('✅ Formulario enviado correctamente');
                }, 1500);
            });

            // Cerrar modal
            document.getElementById('confirmModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            function closeModal() {
                const modal = document.getElementById('confirmModal');
                modal.classList.remove('active');
                document.body.style.overflow = '';

                // Reset loading state
                const confirmBtn = document.getElementById('modalConfirm');
                confirmBtn.classList.remove('loading');
                confirmBtn.querySelector('span').textContent = 'Confirmar {{ $tipo }}';
            }

            // Cerrar modal con ESC
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
                const origenColmenasInput = document.getElementById('origen_colmenas');

                if (origenColmenasInput) {
                    origenColmenasInput.value = `${selCount} seleccionadas`;
                }

                // Actualizar la barra inferior de estatus
                colmenasStatus.innerHTML = `<i class="fas fa-cube"></i><span>${selCount} colmenas seleccionadas</span>`;
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
                    destinoComuna.innerHTML = '<option value="">Cargando comunas…</option>';
                    if (!regionId) {
                        destinoComuna.innerHTML = '<option value="">Seleccionar comuna…</option>';
                        return;
                    }
                    fetch(`/comunas/${regionId}`)
                        .then(res => res.json())
                        .then(json => {
                            let html = '<option value="">Seleccionar comuna…</option>';
                            json.forEach(c => {
                                html += `<option value="${c.id}">${c.nombre}</option>`;
                            });
                            destinoComuna.innerHTML = html;
                        })
                        .catch(() => {
                            destinoComuna.innerHTML = '<option value="">Error al cargar comunas</option>';
                        });
                });
            }

            // ===== INICIALIZACIÓN =====

            // Configurar sección de polinización si ya está seleccionada
            if (motivoMovimiento?.value === 'Polinización') {
                polinizacionSection.style.display = 'block';
            } else {
                polinizacionSection.style.display = 'none';
            }

            // Inicializar wizard
            showStep(1);
            updateColmenasCount();

            console.log('✅ Validaciones completas configuradas');
        });
    </script>
@endsection
