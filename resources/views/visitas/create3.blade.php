@extends('layouts.app')

@section('content')
<div class="expert-system-container">
  <div class="container-fluid">

    <div class="row g-4">
      @php
          $visita = $visita ?? null;
          $estado = $estado ?? null;
      @endphp

      {{-- Contenido de PCC3 --}}
      <div class="col-lg-9 col-md-8">
        <form action="{{ route('visitas.store3', $apiario) }}" method="POST">
          @csrf
          @if(isset($visita))
                <input type="hidden" name="visita_id" value="{{ $visita->id }}">
          @endif

          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="step-card p-4 bg-white rounded shadow-sm">
            <div class="step-header mb-4 d-flex align-items-center">
              <div class="step-icon bg-green-100 p-2 rounded-circle me-3">
                <i class="fas fa-leaf text-green-600"></i>
              </div>
              <div>
                <h4>PCC3 – Estado Nutricional</h4>
                <small>Evaluación de la alimentación y reservas de la colmena</small>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Objetivo</label>
                <select name="objetivo" class="form-select" required>
                  <option value="">Seleccionar…</option>
                  <option value="estimulacion" {{ old('objetivo', $estado->objetivo ?? '') == 'estimulacion' ? 'selected' : '' }}>
                    Estimulación
                  </option>
                  <option value="mantencion" {{ old('objetivo', $estado->objetivo ?? '') == 'mantencion' ? 'selected' : '' }}>
                    Mantención
                  </option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tipo de alimentación</label>
                <input
                  type="text"
                  name="tipo_alimentacion"
                  value="{{ old('tipo_alimentacion', $estado->tipo_alimentacion ?? '') }}"
                  class="form-control"
                  placeholder="Ej: Jarabe, Polen…"
                  required
                />
              </div>
              <div class="col-md-6">
                <label class="form-label">Fecha de aplicación</label>
                <input
                  type="date"
                  name="fecha_aplicacion_insumo_utilizado"
                  value="{{ old('fecha_aplicacion_insumo_utilizado', optional($estado)->fecha_aplicacion?->format('Y-m-d')) }}""
                  class="form-control"
                  required
                />
              </div>
              <div class="col-md-6">
                <label class="form-label">Insumo utilizado</label>
                <input
                  type="text"
                  name="insumo_utilizado"
                  value="{{ old('insumo_utilizado', $estado->insumo_utilizado ?? '') }}"
                  class="form-control"
                  placeholder="Nombre del insumo…"
                />
              </div>
              <div class="col-md-6">
                <label class="form-label">Dosificación</label>
                <input
                  type="text"
                  name="dosificacion"
                  value="{{ old('dosificacion', $estado->dosifiacion ?? '') }}"
                  class="form-control"
                  placeholder="Cantidad y frecuencia…"
                />
              </div>
              <div class="col-md-6">
                <label class="form-label">Método utilizado</label>
                <input
                  type="text"
                  name="metodo_utilizado"
                  value="{{ old('metodo_utilizado', $estado->metodo_utilizado ?? '') }}"
                  class="form-control"
                  placeholder="Método de aplicación…"
                />
              </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
              <a href="{{ url()->previous() }}" class="btn btn-secondary">
                ← Volver
              </a>
              <button type="submit" class="btn btn-success">
                Guardar PCC3
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection
