@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Registro de Inspección del Apiario</h2>

    <form action="{{ route('apiarios.inspeccion-apiario.store', $apiario) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="fecha_inspeccion" class="form-label">Fecha de Inspección</label>
            <input type="date" class="form-control" name="fecha_inspeccion" required>
        </div>
        
        <div class="row">
            <h4>Colmenas</h4>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="num_colmenas_totales" class="form-label">N° Colmenas Totales</label>
                    <input type="number" class="form-control" name="num_colmenas_totales" required>
                </div>
                <div class="mb-3">
                    <label for="num_colmenas_activas" class="form-label">N° Colmenas Activas</label>
                    <input type="number" class="form-control" name="num_colmenas_activas" required>
                </div>
                <div class="mb-3">
                    <label for="num_colmenas_enfermas" class="form-label">N° Colmenas Enfermas</label>
                    <input type="number" class="form-control" name="num_colmenas_enfermas" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="num_colmenas_muertas" class="form-label">N° Colmenas Muertas</label>
                    <input type="number" class="form-control" name="num_colmenas_muertas" required>
                </div>
                <div class="mb-3">
                    <label for="num_colmenas_inspeccionadas" class="form-label">N° Colmenas Inspeccionadas</label>
                    <input type="number" class="form-control" name="num_colmenas_inspeccionadas" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            
            <label><h4>Flujo Nectar / Polen</h4></label>
            <select class="form-select form-select-sm" name="flujo_nectar_polen" required>
                <option value="">Seleccionar</option>
                <option value="abundante">Abundante</option>
                <option value="regular">Regular</option>
                <option value="deficiente">Deficiente</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="nombre_revisor_apiario" class="form-label">Nombre Revisor del Apiario</label>
            <input type="text" class="form-control" name="nombre_revisor_apiario" required>
        </div>

        <div class="mb-3">
            <label for="sospecha_enfermedad" class="form-label">Sospecha de Enfermedad</label>
            <input type="text" class="form-control" name="sospecha_enfermedad">
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" name="observaciones" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

@endsection


