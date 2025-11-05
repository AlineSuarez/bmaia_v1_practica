@extends('layouts.app')

@section('title', isset($tarea) ? 'Editar Tarea del Apiario' : 'Registrar Tarea del Apiario')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-tasks me-2"></i>
        {{ isset($tarea) ? 'Editar Tarea del Apiario' : 'Registrar Tarea del Apiario' }}
    </h2>

    <form method="POST"
        action="{{ isset($tarea)
            ? route('tareas-apiario.update', ['apiarioId' => $apiario->id, 'id' => $tarea->id])
            : route('tareas-apiario.store', $apiario->id) }}">
        @csrf
        @if(isset($tarea))
            @method('PUT')
        @endif

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Categoría de Tarea</label>
                <select name="categoria_tarea" class="form-select" required>
                    <option value="">Seleccione...</option>
                    @foreach (['Inspección','Sanidad','Alimentación','Manejo','Otro'] as $opcion)
                        <option value="{{ $opcion }}" {{ (isset($tarea) && $tarea->categoria_tarea == $opcion) ? 'selected' : '' }}>
                            {{ $opcion }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tarea Específica</label>
                <input type="text" name="tarea_especifica" class="form-control"
                       value="{{ old('tarea_especifica', $tarea->tarea_especifica ?? '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Acción Realizada</label>
                <input type="text" name="accion_realizada" class="form-control"
                       value="{{ old('accion_realizada', $tarea->accion_realizada ?? '') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control"
                       value="{{ old('fecha_inicio', $tarea->fecha_inicio ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha Término</label>
                <input type="date" name="fecha_termino" class="form-control"
                       value="{{ old('fecha_termino', $tarea->fecha_termino ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Próximo Seguimiento</label>
                <input type="text" name="proximo_seguimiento" class="form-control"
                       value="{{ old('proximo_seguimiento', $tarea->proximo_seguimiento ?? '') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $tarea->observaciones ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>
            {{ isset($tarea) ? 'Actualizar' : 'Guardar Registro' }}
        </button>
        <a href="{{ route('tareas-apiario.index', $apiario->id) }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
@endsection
