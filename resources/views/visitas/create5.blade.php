@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">
        @if(isset($indiceCosecha))
            Editar Registro de Cosecha de Miel - {{ $apiario->nombre }}
        @else
            Registro de Cosecha de Miel - {{ $apiario->nombre }}
        @endif
    </h4>

    <form method="POST" action="{{ route('visitas.store5', $apiario->id) }}">
        @csrf
        @if(isset($indiceCosecha))
            <!-- 🟡 Campo oculto para detectar edición -->
            <input type="hidden" name="indice_cosecha_id" value="{{ $indiceCosecha->id }}">
        @endif

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Madurez de la Miel (%)</label>
                <input type="text" name="madurez_miel" class="form-control" 
                       value="{{ old('madurez_miel', $indiceCosecha->madurez_miel ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label>N° de Alzadas</label>
                <input type="number" step="0.01" name="num_alzadas" class="form-control" 
                       value="{{ old('num_alzadas', $indiceCosecha->num_alzadas ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label>N° de Marcos con Miel</label>
                <input type="number" step="0.01" name="marcos_miel" class="form-control" 
                       value="{{ old('marcos_miel', $indiceCosecha->marcos_miel ?? '') }}" required>
            </div>
        </div>

        <hr>
        <h5>Detalles del Proceso de Cosecha</h5>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>ID Lote de Cosecha</label>
                <input type="text" name="id_lote_cosecha" class="form-control" 
                       value="{{ old('id_lote_cosecha', $indiceCosecha->id_lote_cosecha ?? '') }}">
            </div>
            <div class="col-md-3">
                <label>Fecha de Cosecha</label>
                <input type="date" name="fecha_cosecha" class="form-control" 
                       value="{{ old('fecha_cosecha', isset($indiceCosecha->fecha_cosecha) ? $indiceCosecha->fecha_cosecha->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-3">
                <label>Fecha de Extracción</label>
                <input type="date" name="fecha_extraccion" class="form-control" 
                       value="{{ old('fecha_extraccion', isset($indiceCosecha->fecha_extraccion) ? $indiceCosecha->fecha_extraccion->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-3">
                <label>Lugar de Extracción</label>
                <input type="text" name="lugar_extraccion" class="form-control" 
                       value="{{ old('lugar_extraccion', $indiceCosecha->lugar_extraccion ?? '') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Humedad de la Miel (%)</label>
                <input type="number" step="0.01" name="humedad_miel" class="form-control" 
                       value="{{ old('humedad_miel', $indiceCosecha->humedad_miel ?? '') }}">
            </div>
            <div class="col-md-3">
                <label>Temperatura Ambiente (°C)</label>
                <input type="number" step="0.1" name="temperatura_ambiente" class="form-control" 
                       value="{{ old('temperatura_ambiente', $indiceCosecha->temperatura_ambiente ?? '') }}">
            </div>
            <div class="col-md-3">
                <label>Responsable de Cosecha</label>
                <input type="text" name="responsable_cosecha" class="form-control" 
                       value="{{ old('responsable_cosecha', $indiceCosecha->responsable_cosecha ?? '') }}">
            </div>
            <div class="col-md-3">
                <label>Notas</label>
                <textarea name="notas" class="form-control" rows="1">{{ old('notas', $indiceCosecha->notas ?? '') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">
            @if(isset($indiceCosecha))
                Actualizar Registro
            @else
                Guardar Registro
            @endif
        </button>

        <a href="{{ route('visitas.historial', $apiario->id) }}" class="btn btn-secondary mt-3 ms-2">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </form>
</div>
@endsection
