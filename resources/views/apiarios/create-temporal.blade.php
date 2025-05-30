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
                    <form action="{{ route('apiarios.storeTrashumante') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                        <input type="hidden" name="apiarios_ids" value="{{ $apiariosData->pluck('id')->implode(',') }}">

                        <div class="mb-3">
                            <label for="nombreTemporal">Nombre Apiario Temporal</label>
                            <input type="text" id="nombreTemporal" name="nombre" class="form-control" placeholder="Ej: Apiario trashumante Mayo 2025" required>
                        </div>

                        @foreach($apiariosData as $apiario)
                            <input type="hidden" name="apiarios_base[]" value="{{ $apiario->id }}">
                        @endforeach

                        
                        <input type="hidden" name="region_id" value="{{ $apiariosData->first()->region_id }}">
                        <input type="hidden" name="comuna_id" value="{{ $apiariosData->first()->comuna_id }}">


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
                                                <input type="checkbox" class="select-all-colmenas"
                                                    data-apiario="{{ $apiario->id }}">
                                                Seleccionar todas
                                            </label>
                                        </div>

                                        <div class="colmenas-compact">
                                            @for($i = 1; $i <= $apiario->num_colmenas; $i++)
                                                <label class="colmena-compact">
                                                    <input type="checkbox" name="colmenas[{{ $apiario->id }}][]" value="{{ $i }}"
                                                        class="colmena-check" data-apiario="{{ $apiario->id }}">
                                                    <span class="colmena-visual">
                                                        <i class="fas fa-cube"></i>
                                                        <small>{{ $i }}</small>
                                                    </span>
                                                </label>
                                            @endfor
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
                                            value="Juan Carlos Pérez Martínez">
                                    </div>
                                    <div class="form-col">
                                        <label>RUT del Apicultor</label>
                                        <input type="text" class="form-input" name="apicultor_rut" value="12.345.678-9">
                                    </div>
                                    <div class="form-col">
                                        <label>Nº Registro Nacional de Apicultores</label>
                                        <input type="text" class="form-input" name="registro_nacional"
                                            value="RNA-2024-001234">
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

                                <!-- Origen -->
                                <div class="section-card">
                                    <h5 class="section-title">Información del Apiario</h5>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label>Nombre del Apiario</label>
                                            <input type="text" class="form-input"
                                                value="{{ $apiariosData->first()->nombre }}" readonly>
                                        </div>
                                        <div class="form-col">
                                            <label>Nº Apiario</label>
                                            <input type="text" class="form-input"
                                                value="AP-{{ $apiariosData->first()->id }}" readonly>
                                        </div>
                                        <div class="form-col">
                                            <label>Colmenas seleccionadas</label>
                                            <input type="text" class="form-input" id="origen_colmenas"
                                                value="0 seleccionadas" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ubicación Origen -->
                                <div class="section-card">
                                    <h5 class="section-title">Ubicación Origen</h5>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label>Región</label>
                                            <input type="text" class="form-input"
                                                value="{{ $apiariosData->first()->region->nombre ?? 'Región Metropolitana' }}"
                                                readonly>
                                        </div>
                                        <div class="form-col">
                                            <label>Comuna</label>
                                            <input type="text" class="form-input"
                                                value="{{ $apiariosData->first()->comuna->nombre ?? 'Santiago' }}" readonly>
                                        </div>
                                        <div class="form-col">
                                            <label>Coordenadas</label>
                                            <input type="text" class="form-input" value="-33.4489, -70.6693" readonly>
                                        </div>
                                    </div>
                                </div>

                                @if($tipo == 'traslado')
                                    <!-- Ubicación Destino -->
                                    <div class="section-card">
                                        <h5 class="section-title">Ubicación Destino</h5>
                                        <div class="form-row">
                                            <div class="form-col">
                                                <label>Región Destino</label>
                                                <select class="form-input" name="destino_region">
                                                    <option value="">Seleccionar región...</option>
                                                    <option value="valparaiso" selected>Región de Valparaíso</option>
                                                    <option value="ohiggins">Región de O'Higgins</option>
                                                    <option value="maule">Región del Maule</option>
                                                </select>
                                            </div>
                                            <div class="form-col">
                                                <label>Comuna Destino</label>
                                                <select class="form-input" name="destino_comuna">
                                                    <option value="">Seleccionar comuna...</option>
                                                    <option value="quilpue" selected>Quilpué</option>
                                                    <option value="valparaiso">Valparaíso</option>
                                                    <option value="vina">Viña del Mar</option>
                                                </select>
                                            </div>
                                            <div class="form-col">
                                                <label>Coordenadas Destino</label>
                                                <input type="text" class="form-input" name="coordenadas_destino"
                                                    value="-33.0472, -71.4419" placeholder="Lat, Lng">
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
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Fecha Término</label>
                                            <input type="date" class="form-input" name="fecha_termino_mov"
                                                value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                                        </div>
                                        <div class="form-col">
                                            <label>Motivo</label>
                                            <select class="form-input" name="motivo_movimiento" id="motivo_select">
                                                <option value="produccion" selected>Producción</option>
                                                <option value="polinizacion">Polinización</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Polinización (condicional) -->
                                <div class="section-card polinizacion-section" style="display: none;">
                                    <h5 class="section-title">Información de Polinización</h5>
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label>Cultivo</label>
                                            <input type="text" class="form-input" name="cultivo" value="Almendros">
                                        </div>
                                        <div class="form-col">
                                            <label>Período Floración</label>
                                            <input type="text" class="form-input" name="periodo_floracion"
                                                value="Agosto - Septiembre">
                                        </div>
                                        <div class="form-col">
                                            <label>Hectáreas</label>
                                            <input type="number" class="form-input" name="hectareas" value="15">
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
                                                value="Transportes González Ltda.">
                                        </div>
                                        <div class="form-col">
                                            <label>RUT Transportista</label>
                                            <input type="text" class="form-input" name="transportista_rut"
                                                value="76.123.456-7">
                                        </div>
                                        <div class="form-col">
                                            <label>Patente Vehículo</label>
                                            <input type="text" class="form-input" name="vehiculo_patente" value="HLKJ-45">
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
@endsection

@section('optional-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('✅ Wizard con navegación rediseñada cargado');

            let currentStep = 1;
            const totalSteps = 4;

            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');
            const submitBtn = document.getElementById('submitBtn');
            const currentStepNumber = document.getElementById('currentStepNumber');
            const stepDescription = document.getElementById('stepDescription');
            const progressFill = document.getElementById('progressFill');
            const colmenasStatus = document.getElementById('colmenasStatus');
            const validationStatus = document.getElementById('validationStatus');
            const motivoSelect = document.getElementById('motivo_select');

            const stepDescriptions = [
                'Selecciona las colmenas para el {{ $tipo }}',
                'Información del apicultor responsable',
                'Ubicación origen y destino del movimiento',
                'Detalles finales del {{ $tipo }}'
            ];

            // Función para mostrar paso
            function showStep(step) {
                // Ocultar todos los pasos
                document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
                document.getElementById(`step-${step}`).classList.add('active');

                // Actualizar progress dots
                document.querySelectorAll('.nav-dot').forEach((dot, index) => {
                    dot.classList.remove('active', 'completed');
                    if (index + 1 === step) {
                        dot.classList.add('active');
                    } else if (index + 1 < step) {
                        dot.classList.add('completed');
                    }
                });

                // Actualizar progress bar
                const progressPercentage = ((step - 1) / (totalSteps - 1)) * 100;
                progressFill.style.width = `${progressPercentage}%`;

                // Actualizar botones
                prevBtn.style.display = step === 1 ? 'none' : 'flex';
                nextBtn.style.display = step === totalSteps ? 'none' : 'flex';
                submitBtn.style.display = step === totalSteps ? 'flex' : 'none';

                // Actualizar step info
                currentStepNumber.textContent = step;
                stepDescription.textContent = stepDescriptions[step - 1];

                // Actualizar estado de validación
                updateValidationStatus(step);
            }

            // Función para actualizar estado de validación
            function updateValidationStatus(step) {
                switch (step) {
                    case 1:
                        const selectedColmenas = document.querySelectorAll('.colmena-check:checked');
                        if (selectedColmenas.length > 0) {
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

            // Navegación
            nextBtn.addEventListener('click', function () {
                if (currentStep < totalSteps) {
                    // Agregar animación de salida
                    nextBtn.classList.add('loading');

                    setTimeout(() => {
                        currentStep++;
                        showStep(currentStep);
                        nextBtn.classList.remove('loading');
                    }, 300);
                }
            });

            prevBtn.addEventListener('click', function () {
                if (currentStep > 1) {
                    prevBtn.classList.add('loading');

                    setTimeout(() => {
                        currentStep--;
                        showStep(currentStep);
                        prevBtn.classList.remove('loading');
                    }, 300);
                }
            });

            // Navegación por dots
            document.querySelectorAll('.nav-dot').forEach((dot, index) => {
                dot.addEventListener('click', function () {
                    if (index + 1 <= currentStep + 1) { // Permitir ir solo a pasos completados o siguiente
                        currentStep = index + 1;
                        showStep(currentStep);
                    }
                });
            });

            // Manejo de colmenas
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('select-all-colmenas')) {
                    const apiarioId = e.target.dataset.apiario;
                    const colmenas = document.querySelectorAll(`input[data-apiario="${apiarioId}"].colmena-check`);
                    colmenas.forEach(colmena => colmena.checked = e.target.checked);
                    updateColmenasCount();
                }

                if (e.target.classList.contains('colmena-check')) {
                    updateColmenasCount();
                }
            });

            // Mostrar/ocultar sección polinización
            if (motivoSelect) {
                motivoSelect.addEventListener('change', function () {
                    const polinizacionSection = document.querySelector('.polinizacion-section');
                    if (this.value === 'polinizacion') {
                        polinizacionSection.style.display = 'block';
                    } else {
                        polinizacionSection.style.display = 'none';
                    }
                });
            }

            // Actualizar contador de colmenas
            function updateColmenasCount() {
                const selectedColmenas = document.querySelectorAll('.colmena-check:checked');
                const origenColmenasInput = document.getElementById('origen_colmenas');

                if (origenColmenasInput) {
                    origenColmenasInput.value = `${selectedColmenas.length} seleccionadas`;
                }

                // Actualizar status bar
                colmenasStatus.innerHTML = `<i class="fas fa-cube"></i><span>${selectedColmenas.length} colmenas seleccionadas</span>`;

                if (selectedColmenas.length > 0) {
                    colmenasStatus.classList.add('success');
                } else {
                    colmenasStatus.classList.remove('success');
                }

                // Actualizar validación si estamos en el paso 1
                if (currentStep === 1) {
                    updateValidationStatus(1);
                }
            }

            // Manejo del botón de confirmación
            submitBtn.addEventListener('click', function () {
                const selectedColmenas = document.querySelectorAll('.colmena-check:checked');
                if (selectedColmenas.length === 0) {
                    alert('Debes seleccionar al menos una colmena.');
                    return;
                }

                if (!confirm(`¿Confirmas el trasl​ado de ${selectedColmenas.length} colmenas?`)) {
                    return;
                }
            });

            // Inicializar
            showStep(1);
            updateColmenasCount();
        });
    </script>
@endsection