@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Registro Uso de Medicamentos</h2>

    <form action="{{ route('apiarios.medicamentos-registro.store', $apiario) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" name="fecha" required>
        </div>

        <div class="mb-3">
            <label for="num_colmenas_tratadas" class="form-label">N° de Colmenas Tratadas</label>
            <input type="number" class="form-control" name="num_colmenas_tratadas" required>
        </div>

        <div class="mb-3">
            <label for="motivo_tratamiento" class="form-label">Motivo del Tratamiento</label>
            <input type="text" class="form-control" name="motivo_tratamiento" required>
        </div>

        <div class="mb-3">
            <label for="nombre_comercial_medicamento" class="form-label">Nombre Comercial del Medicamento</label>
            <input type="text" class="form-control" name="nombre_comercial_medicamento" required>
        </div>

        <div class="mb-3">
            <label for="principio_activo_medicamento" class="form-label">Principio Activo del Medicamento</label>
            <input type="text" class="form-control" name="principio_activo_medicamento" required>
        </div>

        <div class="mb-3">
            <label for="periodo_resguardo" class="form-label">Período de Resguardo</label>
            <input type="text" class="form-control" name="periodo_resguardo" required>
        </div>

        <div class="mb-3">
            <label for="responsable" class="form-label">Responsable</label>
            <input type="text" class="form-control" name="responsable" required>
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" name="observaciones" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection