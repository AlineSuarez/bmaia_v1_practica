@extends('layouts.app')

@section('title', 'Editar Apiario Temporal')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/edit/edit-temporal.css') }}" rel="stylesheet">
    </head>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <!-- Header con Progress Bar -->
                <div class="page-header">
                    <div class="header-content">
                        <div class="header-info">
                            <i class="fas fa-route"></i>
                            <h1 class="page-title">
                                Editar Apiario Temporal
                                <span class="badge badge-warning">Traslado</span>
                            </h1>
                        </div>
                        <a href="{{ route('apiarios') }}" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                    <div class="progress">
                        <div id="progressFill" class="progress-fill"></div>
                    </div>
                </div>

                {{-- Wizard --}}
                <div class="wizard-container">
                    <form id="form-temporal" action="{{ route('apiarios.updateTemporal', $apiario->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="tipo" value="traslado">

                        {{-- PASO 1: Colmenas --}}
                        <div class="wizard-step active" id="step-1">
                            <div class="step-content">
                                <h4 class="step-title">
                                    <i class="fas fa-cube"></i> Colmenas Trasladadas
                                </h4>
                            </div>

                            @foreach($colmenasPorApiarioBase as $baseNombre => $colmenas)
                                <div class="apiario-card">
                                    <div class="apiario-info">
                                        <h5>Apiario Base: {{ $baseNombre }}</h5>
                                        <div class="apiario-meta">{{ count($colmenas) }} colmenas</div>
                                    </div>
                                    <div class="colmenas-compact">
                                        @foreach($colmenas as $c)
                                            <div class="colmena-compact">
                                                <div class="colmena-visual">
                                                    <i class="fas fa-cube"></i>
                                                    <span>#{{ $c->numero }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- PASO 2: Ubicación Destino --}}
                        <div class="wizard-step" id="step-2">
                            <div class="step-content">
                                <h4 class="step-title">
                                    <i class="fas fa-map-marker-alt"></i> Ubicación Destino
                                </h4>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label class="form-label">Región</label>
                                        <select id="regionSelect" name="destino_region_id"
                                            class="form-input @error('destino_region_id') is-invalid @enderror">
                                            <option value="">-- Seleccione región --</option>
                                            @foreach($regiones as $region)
                                                <option value="{{ $region->id }}" @selected(old('destino_region_id', $apiario->region_id) == $region->id)>
                                                    {{ $region->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('destino_region_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-col">
                                        <label class="form-label">Comuna</label>
                                        <select id="comunaSelect" name="destino_comuna_id"
                                            class="form-input @error('destino_comuna_id') is-invalid @enderror">
                                            <option value="">-- Seleccione comuna --</option>
                                        </select>
                                        @error('destino_comuna_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-col">
                                    <label class="form-label">Coordenadas</label>
                                    <input type="text" id="coordenadas_destino" name="coordenadas_destino"
                                        class="form-input @error('coordenadas_destino') is-invalid @enderror"
                                        placeholder="Lat, Lng"
                                        value="{{ old('coordenadas_destino', "{$apiario->latitud}, {$apiario->longitud}") }}">
                                    @error('coordenadas_destino')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- PASO 3: Detalles del Movimiento --}}
                        <div class="wizard-step" id="step-3">
                            <div class="step-content">
                                <h4 class="step-title">
                                    <i class="fas fa-truck"></i> Detalles del Movimiento
                                </h4>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label class="form-label">Nombre Apiario</label>
                                        <input type="text" name="nombre" class="form-input"
                                            value="{{ old('nombre', $apiario->nombre) }}">
                                    </div>
                                    <div class="form-col">
                                        <label class="form-label">Fecha Inicio</label>
                                        <input type="date" name="fecha_inicio_mov" class="form-input"
                                            value="{{ old('fecha_inicio_mov', optional($mov)->fecha_inicio_mov?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="form-col">
                                        <label class="form-label">Fecha Término</label>
                                        <input type="date" name="fecha_termino_mov" class="form-input"
                                            value="{{ old('fecha_termino_mov', optional($mov)->fecha_termino_mov?->format('Y-m-d')) }}">
                                    </div>
                                </div>

                                <div class="form-col">
                                    <label class="form-label">Motivo</label>
                                    <select id="motivoSelect" name="motivo_movimiento" class="form-input">
                                        <option value="Producción" @selected(old('motivo_movimiento', $mov->motivo_movimiento) === 'Producción')>
                                            Producción</option>
                                        <option value="Polinización" @selected(old('motivo_movimiento', $mov->motivo_movimiento) === 'Polinización')>
                                            Polinización</option>
                                    </select>
                                </div>

                                <div class="polinizacion-section"
                                    style="{{ old('motivo_movimiento', $mov->motivo_movimiento) === 'Polinización' ? '' : 'display:none' }}">
                                    <div class="form-row">
                                        <div class="form-col">
                                            <label class="form-label">Cultivo</label>
                                            <input type="text" name="cultivo" class="form-input"
                                                value="{{ old('cultivo', $mov->cultivo) }}">
                                        </div>
                                        <div class="form-col">
                                            <label class="form-label">Período Floración</label>
                                            <input type="text" name="periodo_floracion" class="form-input"
                                                value="{{ old('periodo_floracion', $mov->periodo_floracion) }}">
                                        </div>
                                        <div class="form-col">
                                            <label class="form-label">Hectáreas</label>
                                            <input type="number" name="hectareas" class="form-input"
                                                value="{{ old('hectareas', $mov->hectareas) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-col">
                                        <label class="form-label">Transportista</label>
                                        <input type="text" name="transportista" class="form-input"
                                            value="{{ old('transportista', $mov->transportista) }}">
                                    </div>
                                    <div class="form-col">
                                        <label class="form-label">Vehículo</label>
                                        <input type="text" name="vehiculo" class="form-input"
                                            value="{{ old('vehiculo', $mov->vehiculo) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Navegación --}}
                        <div class="wizard-navigation">
                            <div class="nav-controls">
                                <div class="nav-left">
                                    <button type="button" id="prevBtn" class="nav-btn nav-btn-back" style="display:none;">
                                        <i class="fas fa-arrow-left btn-icon"></i>
                                        <span class="btn-text">Anterior</span>
                                    </button>
                                </div>
                                <div class="nav-center">
                                    <div class="step-info">
                                        <div class="step-current">
                                            <span class="step-number" id="currentStepNumber">1</span>
                                            <span class="step-divider">/</span>
                                            <span class="step-total" id="totalSteps">3</span>
                                        </div>
                                        <div class="step-description">Paso actual del proceso</div>
                                    </div>
                                </div>
                                <div class="nav-right">
                                    <button type="button" id="nextBtn" class="nav-btn nav-btn-next">
                                        <span class="btn-text">Siguiente</span>
                                        <i class="fas fa-arrow-right btn-icon"></i>
                                    </button>
                                    <button type="submit" id="submitBtn" class="nav-btn nav-btn-submit"
                                        style="display:none;">
                                        <i class="fas fa-check btn-icon"></i>
                                        <span class="btn-text">Guardar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentStep = 1;
            const totalSteps = 3;
            const steps = document.querySelectorAll('.wizard-step');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const progressFill = document.getElementById('progressFill');
            const currentStepNumber = document.getElementById('currentStepNumber');

            const regionSelect = document.getElementById('regionSelect');
            const comunaSelect = document.getElementById('comunaSelect');
            const motivoSelect = document.getElementById('motivoSelect');
            const polinizacionSection = document.querySelector('.polinizacion-section');

            const regiones = @json($regiones);
            const oldComuna = "{{ old('destino_comuna_id', $apiario->comuna_id) }}";

            function showStep(step) {
                steps.forEach((s, i) => s.classList.toggle('active', i + 1 === step));
                prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
                nextBtn.style.display = step < totalSteps ? 'inline-flex' : 'none';
                submitBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';
                currentStepNumber.textContent = step;
                progressFill.style.width = ((step - 1) / (totalSteps - 1)) * 100 + '%';
            }

            function populateComunas() {
                comunaSelect.innerHTML = `<option value="">-- Seleccione comuna --</option>`;
                const reg = regiones.find(r => r.id == regionSelect.value);
                if (!reg) return;
                reg.comunas.forEach(c => {
                    const o = new Option(c.nombre, c.id);
                    if (c.id == oldComuna) o.selected = true;
                    comunaSelect.add(o);
                });
            }

            regionSelect?.addEventListener('change', populateComunas);
            motivoSelect.addEventListener('change', e => {
                polinizacionSection.style.display = e.target.value === 'Polinización' ? 'block' : 'none';
            });

            nextBtn.addEventListener('click', () => {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            });
            prevBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
            submitBtn.addEventListener('click', () => {
                document.getElementById('form-temporal').submit();
            });

            // init
            populateComunas();
            showStep(1);
        });
    </script>
@endpush