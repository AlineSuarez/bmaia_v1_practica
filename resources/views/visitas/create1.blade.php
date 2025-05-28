@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Alerta si ya existe una visita registrada -->
            @if ($visita)
                <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
                    <div>
                        <strong>¡Atención!</strong> Ya existe una visita registrada para este apiario (Fecha: {{ $visita->fecha_visita }}).
                        ¿Deseas modificar esta información o cancelar?
                    </div>
                    <div>
                        <a href="#" class="btn btn-sm btn-primary">Modificar</a>
                        <a href="{{ route('visita.index') }}" class="btn btn-sm btn-secondary">Cancelar</a>
                    </div>
                </div>
            @endif

            <!-- Nuevo formulario para el registro de visitas -->
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h5>Registro de Visitas</h5>
                </div>
            
                <div class="card-body">
                    <form action="{{ route('apiarios.visitas-general.store', $apiario) }}" method="POST">
                        @csrf
                        @php
                            $userFormat = config('app.date_format', 'DD/MM/YYYY');
                        @endphp
                        <!-- Input para fecha de la inspección -->
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <!--<input type="date" class="form-control" name="fecha" required> -->
                            <input
                                type="text"
                                id="fecha"
                                name="fecha"
                                class="form-control"
                                placeholder="{{ $userFormat }}"
                                value="{{ old('fecha') }}"
                                autocomplete="off"
                                required
                            >
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->last_name }}" readonly>
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">RUT</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->rut }}" readonly>
                        </div>
                
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <input type="text" class="form-control" name="motivo" required>
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->telefono }}" readonly>
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">Firma</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->firma }}" readonly>
                        </div>
                
                        <!-- Boton de guardado-->
                        <button type="submit" class="btn btn-primary btn-lg w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let fmt = "{{ $userFormat }}"
        .replace(/DD/g, 'd')
        .replace(/MM/g, 'm')
        .replace(/YYYY/g, 'Y');

    flatpickr("#fecha", {
        dateFormat: fmt,
        locale: "es", // puedes usar app()->getLocale() para hacerlo dinámico
    });
});
</script>
@endpush
