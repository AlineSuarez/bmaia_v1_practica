@extends('layouts.app')

@section('title', 'Editar Apiario Temporal')

@section('content')
    <head>
        <link href="{{ asset('css/components/home-user/create/create-temporal.css') }}" rel="stylesheet">
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
              <a href="{{ route('apiarios') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
              </a>
            </div>
            <div class="progress">
              <div id="progressFill" class="progress-bar bg-warning"></div>
            </div>
          </div>

          {{-- Wizard --}}
          <div class="wizard-container shadow-sm bg-white rounded p-4">

            <form id="form-temporal" action="{{ route('apiarios.updateTemporal', $apiario->id) }}" method="POST">
              @csrf @method('PATCH')
              <input type="hidden" name="tipo" value="traslado">

              {{-- — PASO 1: Colmenas — --}}
              <section class="wizard-step active" id="step-1">
                <div class="step-content">
                  <h4 class="step-title">
                    <i class="fas fa-cube"></i> Colmenas Trasladadas
                  </h4>
                </div>

                @foreach($colmenasPorApiarioBase as $baseNombre => $colmenas)
                  <div class="card mb-3 border-warning">
                    <div class="card-header bg-warning text-white">
                      Apiario Base: <strong>{{ $baseNombre }}</strong>
                    </div>
                    <div class="card-body">
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
                @endforeach
              </section>

              {{-- — PASO 2: Ubicación Destino — --}}
              <section class="wizard-step" id="step-2">
                <div class="step-header mb-3">
                  <i class="fas fa-map-marker-alt text-warning me-2"></i>
                  <h5 class="d-inline">Ubicación Destino</h5>
                </div>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="regionSelect" class="form-label">Región</label>
                    <select id="regionSelect" name="destino_region_id"
                            class="form-select @error('destino_region_id') is-invalid @enderror">
                      <option value="">-- Seleccione región --</option>
                      @foreach($regiones as $region)
                        <option value="{{ $region->id }}"
                                @selected(old('destino_region_id', $apiario->region_id)==$region->id)>
                          {{ $region->nombre }}
                        </option>
                      @endforeach
                    </select>
                    @error('destino_region_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-6">
                    <label for="comunaSelect" class="form-label">Comuna</label>
                    <select id="comunaSelect" name="destino_comuna_id"
                            class="form-select @error('destino_comuna_id') is-invalid @enderror">
                      <option value="">-- Seleccione comuna --</option>
                    </select>
                    @error('destino_comuna_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="mt-3">
                  <label for="coordenadas_destino" class="form-label">Coordenadas</label>
                  <input type="text" id="coordenadas_destino" name="coordenadas_destino"
                         class="form-control @error('coordenadas_destino') is-invalid @enderror"
                         placeholder="Lat, Lng"
                         value="{{ old('coordenadas_destino', "{$apiario->latitud}, {$apiario->longitud}") }}">
                  @error('coordenadas_destino')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </section>

              <!-- PASO 3: Detalles del Movimiento -->
              <div class="wizard-step" id="step-3">
                <div class="step-content">
                  <h4 class="step-title">
                    <i class="fas fa-truck"></i> Detalles del Movimiento
                  </h4>
                  <div class="row">
                    <div class="col-md-4">
                      <label>Nombre Apiario</label>
                      <input type="text" name="nombre" class="form-input"
                             value="{{ old('nombre',$apiario->nombre) }}">
                    </div>
                    <div class="col-md-4">
                      <label>Fecha Inicio</label>
                      <input type="date" name="fecha_inicio_mov" class="form-input"
                             value="{{ old('fecha_inicio_mov',optional($mov)->fecha_inicio_mov?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                      <label>Fecha Término</label>
                      <input type="date" name="fecha_termino_mov" class="form-input"
                             value="{{ old('fecha_termino_mov',optional($mov)->fecha_termino_mov?->format('Y-m-d')) }}">
                    </div>
                  </div>
                  <div class="mt-3">
                    <label>Motivo</label>
                    <select id="motivoSelect" name="motivo_movimiento" class="form-input">
                      <option value="Producción"
                        @selected(old('motivo_movimiento',$mov->motivo_movimiento)==='Producción')>Producción</option>
                      <option value="Polinización"
                        @selected(old('motivo_movimiento',$mov->motivo_movimiento)==='Polinización')>Polinización</option>
                    </select>
                  </div>

                  <div class="polinizacion-section mt-3"
                       style="{{ old('motivo_movimiento',$mov->motivo_movimiento)==='Polinización'?'':'display:none' }}">
                    <div class="row">
                      <div class="col-md-4">
                        <label>Cultivo</label>
                        <input type="text" name="cultivo" class="form-input"
                               value="{{ old('cultivo',$mov->cultivo) }}">
                      </div>
                      <div class="col-md-4">
                        <label>Período Floración</label>
                        <input type="text" name="periodo_floracion" class="form-input"
                               value="{{ old('periodo_floracion',$mov->periodo_floracion) }}">
                      </div>
                      <div class="col-md-4">
                        <label>Hectáreas</label>
                        <input type="number" name="hectareas" class="form-input"
                               value="{{ old('hectareas',$mov->hectareas) }}">
                      </div>
                    </div>
                  </div>

                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label>Transportista</label>
                      <input type="text" name="transportista" class="form-input"
                             value="{{ old('transportista',$mov->transportista) }}">
                    </div>
                    <div class="col-md-6">
                      <label>Vehículo</label>
                      <input type="text" name="vehiculo" class="form-input"
                             value="{{ old('vehiculo',$mov->vehiculo) }}">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Navegación --}}
              <div class="wizard-navigation mt-4">
                <div class="d-flex justify-content-between align-items-center">
                  <button type="button" id="prevBtn" class="btn btn-outline-secondary" style="display:none;">
                    <i class="fas fa-arrow-left me-1"></i> Anterior
                  </button>
                  <div class="step-info text-center">
                    <span id="stepNumbers" style="white-space: nowrap;">
                        <span id="currentStepNumber">1</span>&nbsp;/&nbsp;<span id="totalSteps">3</span>
                    </span>
                    </div>
                  <button type="button" id="nextBtn" class="btn btn-warning">
                    Siguiente <i class="fas fa-arrow-right ms-1"></i>
                  </button>
                  <button type="submit" id="submitBtn" class="btn btn-success" style="display:none;">
                    <i class="fas fa-check me-1"></i> Guardar
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
