@extends('layouts.app')

@section('title', 'Historial de la Colmena')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Historial de la Colmena #{{ $colmena->numero }} del Apiario "{{ $apiario->nombre }}"</h2>

    @if($movimientos->isEmpty())
        <div class="alert alert-info text-center">No hay movimientos registrados para esta colmena.</div>
    @else
        <div class="timeline">
            @foreach($movimientos as $mov)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-exchange-alt"></i> {{ ucfirst($mov->tipo_movimiento) }}
                            </h5>
                            <span class="badge bg-secondary">{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</span>
                        </div>
                        <hr>
                        <p class="mb-1"><strong>Desde:</strong> {{ optional($mov->apiarioOrigen)->nombre ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Hacia:</strong> {{ optional($mov->apiarioDestino)->nombre ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Motivo:</strong> {{ $mov->motivo_movimiento }}</p>
                        @if($mov->observaciones)
                            <p class="mb-1"><strong>Observaciones:</strong> {{ $mov->observaciones }}</p>
                        @endif
                        <p class="mb-1"><strong>Transportista:</strong> {{ $mov->transportista ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Vehículo:</strong> {{ $mov->vehiculo ?? 'N/A' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    width: 2px;
    height: 100%;
    background: #dee2e6;
}
.timeline .card {
    margin-left: 50px;
    position: relative;
}
.timeline .card::before {
    content: '';
    position: absolute;
    left: -32px;
    top: 20px;
    width: 12px;
    height: 12px;
    background: #0d6efd;
    border-radius: 50%;
    border: 2px solid #fff;
}
</style>
@endpush
