@extends('layouts.app')

@section('title', 'Editar Evaluación PCC – Colmena #' . $colmena->numero)

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-10">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Editar Evaluación PCC – Colmena #{{ $colmena->numero }}</h5>
        </div>
        <form method="POST" action="{{ route('sistemaexperto.update', $sistemaexperto) }}">
          @csrf
          @method('PUT')
          <div class="card-body row">
            {{-- Desarrollo Cría --}}
            <div class="col-md-6 mb-3">
              <h6>Desarrollo Cría</h6>
              <input type="text" name="desarrollo_cria[vigor_colmena]" class="form-control mb-2"
                     placeholder="Vigor colmena" value="{{ $sistemaexperto->desarrolloCria->vigor_colmena ?? '' }}">
              <input type="text" name="desarrollo_cria[actividad_abejas]" class="form-control mb-2"
                     placeholder="Actividad abejas" value="{{ $sistemaexperto->desarrolloCria->actividad_abejas ?? '' }}">
              <input type="text" name="desarrollo_cria[ingreso_polen]" class="form-control"
                     placeholder="Ingreso polen" value="{{ $sistemaexperto->desarrolloCria->ingreso_polen ?? '' }}">
            </div>

            {{-- Calidad Reina --}}
            <div class="col-md-6 mb-3">
              <h6>Calidad Reina</h6>
              <input type="text" name="calidad_reina[postura_reina]" class="form-control mb-2"
                     placeholder="Postura reina" value="{{ $sistemaexperto->calidadReina->postura_reina ?? '' }}">
              <input type="text" name="calidad_reina[estado_cria]" class="form-control"
                     placeholder="Estado cría" value="{{ $sistemaexperto->calidadReina->estado_cria ?? '' }}">
            </div>

            {{-- Estado Nutricional --}}
            <div class="col-md-6 mb-3">
              <h6>Estado Nutricional</h6>
              <input type="text" name="estado_nutricional[objetivo]" class="form-control"
                     placeholder="Objetivo nutricional" value="{{ $sistemaexperto->estadoNutricional->objetivo ?? '' }}">
            </div>

            {{-- Presencia Varroa --}}
            <div class="col-md-6 mb-3">
              <h6>Presencia Varroa</h6>
              <input type="text" name="presencia_varroa[diagnostico_visual]" class="form-control"
                     placeholder="Diagnóstico visual" value="{{ $sistemaexperto->presenciaVarroa->diagnostico_visual ?? '' }}">
            </div>

            {{-- Presencia Nosemosis --}}
            <div class="col-md-6 mb-3">
              <h6>Presencia Nosemosis</h6>
              <input type="text" name="presencia_nosemosis[signos_clinicos]" class="form-control"
                     placeholder="Signos clínicos" value="{{ $sistemaexperto->presenciaNosemosis->signos_clinicos ?? '' }}">
            </div>

            {{-- Índice Cosecha --}}
            <div class="col-md-6 mb-3">
              <h6>Índice de Cosecha</h6>
              <input type="text" name="indice_cosecha[produccion_esperada]" class="form-control"
                     placeholder="Producción esperada" value="{{ $sistemaexperto->indiceCosecha->produccion_esperada ?? '' }}">
            </div>

            {{-- Preparación Invernada --}}
            <div class="col-md-6 mb-3">
              <h6>Preparación Invernada</h6>
              <input type="text" name="preparacion_invernada[control_sanitario]" class="form-control"
                     placeholder="Control sanitario" value="{{ $sistemaexperto->preparacionInvernada->control_sanitario ?? '' }}">
            </div>
          </div>

          <!-- Historial de Visitas -->
          <div class="card-body">
            <h6>Historial de Visitas</h6>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Tipo de Visita</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                @foreach($colmena->visitas as $visita)
                  <tr>
                    <td>{{ $visita->fecha }}</td>
                    <td>{{ $visita->tipo_visita }}</td>
                    <td>
                      <a href="{{ route('generate.document.alimentacion', $visita->id) }}" class="btn btn-sm btn-outline-success">
                        Generar Documento
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="card-footer d-flex justify-content-between">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
