{{-- resources/views/apiarios/edit-temporal.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Apiario Temporal')

@section('content')
    <head>
        <link href="{{ asset('css/components/home-user/create/create-temporal.css') }}" rel="stylesheet">
    </head>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- Header con Progress Bar --}}
                <div class="page-header mb-4">
                    <div class="header-content d-flex justify-content-between align-items-center">
                        <div class="header-info d-flex align-items-center gap-3">
                            <i class="fas fa-route fa-2x text-warning"></i>
                            <div>
                                <h1 class="page-title mb-0">Editar Apiario Temporal</h1>
                                <span class="badge bg-warning">Traslado</span>
                            </div>
                        </div>
                        <a href="{{ route('apiarios') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                    {{-- Progress Bar --}}
                    <div class="progress mt-3" style="height: 4px;">
                        <div id="progressFill" class="progress-bar bg-warning" role="progressbar" style="width: 0%;"></div>
                    </div>
                </div>

                {{-- Wizard Container --}}
                <div class="wizard-container">
                    <form id="form-temporal" action="{{ route('apiarios.updateTemporal', $apiario->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="tipo" value="traslado">

                        {{-- PASO 1: COLMENAS (solo lectura) --}}
                        <div class="wizard-step active" id="step-1">
                            <div class="step-header d-flex align-items-center mb-3">
                                <i class="fas fa-cubes fa-lg text-warning me-2"></i>
                                <h5 class="mb-0">Colmenas Trasladadas</h5>
                            </div>
                            <div class="row g-3">
                                @foreach($colmenasPorApiarioBase as $baseNombre => $colmenas)
                                    <div class="col-12">
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-warning text-white">
                                                Apiario Base: <strong>{{ $baseNombre }}</strong>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="colmena-container">
                                                    @foreach($colmenas as $c)
                                                    <div class="colmena-box">
                                                        <i class="fas fa-cube"></i>
                                                        <span>#{{ $c->numero }}</span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- PASO 2: UBICACIÓN DESTINO --}}
                        <div class="wizard-step" id="step-2">
                            <div class="step-content">
                                <h4 class="step-title"><i class="fas fa-map-marker-alt text-warning"></i> Ubicación Destino</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="regionSelect" class="form-label">Región Destino</label>
                                        <select id="regionSelect" name="destino_region_id"
                                            class="form-select @error('destino_region_id') is-invalid @enderror">
                                            <option value="">-- Seleccione región --</option>
                                            @foreach($regiones as $region)
                                                <option value="{{ $region->id }}"
                                                    {{ old('destino_region_id', $apiario->region_id)==$region->id?'selected':'' }}>
                                                    {{ $region->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('destino_region_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="comunaSelect" class="form-label">Comuna Destino</label>
                                        <select id="comunaSelect" name="destino_comuna_id"
                                            class="form-select @error('destino_comuna_id') is-invalid @enderror">
                                            <option value="">-- Seleccione comuna --</option>
                                        </select>
                                        @error('destino_comuna_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="coordenadas_destino" class="form-label">Coordenadas Destino</label>
                                    <input type="text" id="coordenadas_destino" name="coordenadas_destino"
                                        class="form-control @error('coordenadas_destino') is-invalid @enderror"
                                        value="{{ old('coordenadas_destino', "{$apiario->latitud}, {$apiario->longitud}") }}"
                                        placeholder="Lat, Lng">
                                    @error('coordenadas_destino')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- PASO 3: DETALLES DEL MOVIMIENTO --}}
                        <div class="wizard-step" id="step-3">
                            <div class="step-content">
                                <h4 class="step-title"><i class="fas fa-truck text-warning"></i> Detalles del Movimiento</h4>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label for="nombre" class="form-label">Nombre Apiario</label>
                                        <input type="text" id="nombre" name="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            value="{{ old('nombre', $apiario->nombre) }}">
                                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="fecha_inicio_mov" class="form-label">Fecha Inicio</label>
                                        <input type="date" id="fecha_inicio_mov" name="fecha_inicio_mov"
                                            class="form-control @error('fecha_inicio_mov') is-invalid @enderror"
                                            value="{{ old('fecha_inicio_mov', optional($mov)->fecha_inicio_mov->format('Y-m-d')) }}">
                                        @error('fecha_inicio_mov')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="fecha_termino_mov" class="form-label">Fecha Término</label>
                                        <input type="date" id="fecha_termino_mov" name="fecha_termino_mov"
                                            class="form-control @error('fecha_termino_mov') is-invalid @enderror"
                                            value="{{ old('fecha_termino_mov', optional($mov)->fecha_termino_mov->format('Y-m-d')) }}">
                                        @error('fecha_termino_mov')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="motivoSelect" class="form-label">Motivo</label>
                                    <select id="motivoSelect" name="motivo_movimiento"
                                        class="form-select @error('motivo_movimiento') is-invalid @enderror">
                                        <option value="Producción"
                                            {{ old('motivo_movimiento', $mov->motivo_movimiento)==='Producción'?'selected':'' }}>
                                            Producción
                                        </option>
                                        <option value="Polinización"
                                            {{ old('motivo_movimiento', $mov->motivo_movimiento)==='Polinización'?'selected':'' }}>
                                            Polinización
                                        </option>
                                    </select>
                                    @error('motivo_movimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Campos condicionales Polinización --}}
                                <div class="mb-4 polinizacion-section"
                                     style="{{ old('motivo_movimiento', $mov->motivo_movimiento)!=='Polinización'?'display:none':'display:block' }}">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="cultivo" class="form-label">Cultivo</label>
                                            <input type="text" id="cultivo" name="cultivo"
                                                class="form-control @error('cultivo') is-invalid @enderror"
                                                value="{{ old('cultivo', $mov->cultivo) }}">
                                            @error('cultivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="periodo_floracion" class="form-label">Período Floración</label>
                                            <input type="text" id="periodo_floracion" name="periodo_floracion"
                                                class="form-control @error('periodo_floracion') is-invalid @enderror"
                                                value="{{ old('periodo_floracion', $mov->periodo_floracion) }}">
                                            @error('periodo_floracion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="hectareas" class="form-label">Hectáreas</label>
                                            <input type="number" id="hectareas" name="hectareas"
                                                class="form-control @error('hectareas') is-invalid @enderror"
                                                value="{{ old('hectareas', $mov->hectareas) }}">
                                            @error('hectareas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Transporte --}}
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="transportista" class="form-label">Transportista</label>
                                        <input type="text" id="transportista" name="transportista"
                                            class="form-control @error('transportista') is-invalid @enderror"
                                            value="{{ old('transportista', $mov->transportista) }}">
                                        @error('transportista')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vehiculo" class="form-label">Vehículo</label>
                                        <input type="text" id="vehiculo" name="vehiculo"
                                            class="form-control @error('vehiculo') is-invalid @enderror"
                                            value="{{ old('vehiculo', $mov->vehiculo) }}">
                                        @error('vehiculo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Navegación Wizard --}}
                        <div class="wizard-navigation mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" id="prevBtn" class="btn btn-outline-secondary" style="display:none;">
                                    <i class="fas fa-arrow-left"></i> Anterior
                                </button>
                                <div class="step-info text-center">
                                    <span id="currentStepNumber">1</span> / 3
                                </div>
                                <button type="button" id="nextBtn" class="btn btn-warning">
                                    Siguiente <i class="fas fa-arrow-right"></i>
                                </button>
                                <button type="button" id="submitBtn" class="btn btn-success" style="display:none;">
                                    <i class="fas fa-check"></i> Guardar
                                </button>
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
    const cultivo = document.getElementById('cultivo');
    const periodoFloracion = document.getElementById('periodo_floracion');
    const hectareas = document.getElementById('hectareas');

    const regiones = @json($regiones);
    const oldComuna = "{{ old('destino_comuna_id', $apiario->comuna_id) }}";

    function showStep(step) {
        steps.forEach((s,i) => s.classList.toggle('active', i+1===step));
        prevBtn.style.display   = step>1         ? 'inline-block':'none';
        nextBtn.style.display   = step<totalSteps? 'inline-block':'none';
        submitBtn.style.display = step===totalSteps?'inline-block':'none';
        currentStepNumber.textContent = step;
        progressFill.style.width = ((step-1)/(totalSteps-1))*100+'%';
    }

    function populateComunas() {
        comunaSelect.innerHTML = `<option value="">-- Seleccione comuna --</option>`;
        const reg = regiones.find(r=>r.id==regionSelect.value);
        if (!reg) return;
        reg.comunas.forEach(c=>{
            const o = new Option(c.nombre, c.id);
            if (c.id==oldComuna) o.selected=true;
            comunaSelect.add(o);
        });
    }

    regionSelect?.addEventListener('change', populateComunas);
    motivoSelect.addEventListener('change', e=>{
        polinizacionSection.style.display = e.target.value==='Polinización'?'block':'none';
    });

    nextBtn.addEventListener('click', ()=>{
        if (currentStep<totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    });
    prevBtn.addEventListener('click', ()=>{
        if (currentStep>1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    submitBtn.addEventListener('click', ()=>{
        document.getElementById('form-temporal').submit();
    });

    // init
    populateComunas();
    showStep(1);
});
</script>

<style>
    /* CONTENEDOR QUE ENVUELVE TODAS LAS CAJITAS */
    .colmena-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background: #fff;
    }

    /* CADA CAJITA DE COLMENA */
    .colmena-box {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        min-width: 48px;
        height: 48px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background: #fafafa;
        color: #666;
        font-size: 0.875rem;
        transition: border-color 0.2s, background-color 0.2s;
    }

    /* ICONO DENTRO */
    .colmena-box i {
        font-size: 1rem;
        color: #999;
    }

    /* ESTADO HOVER / FOCUS (opcional) */
    .colmena-box:hover {
        border-color: #bbb;
        background: #f5f5f5;
    }

    /* SI QUIERES DESTACAR UNA CAJITA COMO “SELECCIONADA” */
    .colmena-box.selected {
        border-color: #4caf50;
        background: #e8f5e9;
        color: #2e7d32;
    }
</style>
@endpush
