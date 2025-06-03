@extends('layouts.app')

@section('title', 'Detalle de Colmena')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Colmena #{{ $colmena->numero }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($colmena->codigo_qr) }}&size=150x150" alt="QR">
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Color etiqueta:</span>
                            <span class="badge" style="background-color: {{ $colmena->color_etiqueta }};">{{ $colmena->color_etiqueta }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Número de marcos:</span>
                            <span>{{ $colmena->numero_marcos ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Estado inicial:</span>
                            <span>{{ $colmena->estado_inicial ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Observaciones:</strong>
                            <p class="mt-2">{{ $colmena->observaciones ?? 'Sin observaciones' }}</p>
                        </li>
                    </ul>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <form id="form-delete-colmena" method="POST" action="{{ route('colmenas.destroy', [$apiario->id, $colmena->id]) }}" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-colmena">Eliminar</button>
                    </form> 
                    <a href="{{ route('colmenas.edit', [$apiario->id, $colmena->id]) }}" class="btn btn-outline-warning btn-sm">
                        Editar
                    </a>
                    <a href="{{ route('colmenas.historial', [$apiario->id, $colmena->id]) }}" class="btn btn-outline-primary btn-sm">
                        Ver Historial de Movimientos
                    </a>
                    <a href="{{ route('colmenas.index', $apiario->id) }}" class="btn btn-secondary btn-sm">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('.btn-delete-colmena').addEventListener('click', function (e) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-colmena').submit();
            }
        });
    });
</script>
@endpush
