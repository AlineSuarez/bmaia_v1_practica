{{-- Encabezado del movimiento --}}
<h5 class="mt-2">
    {{ $apiario->nombre }} – Movimiento del {{ \Carbon\Carbon::parse($mov->fecha_movimiento)->format('d/m/Y') }}
</h5>

{{-- Datos del Movimiento --}}
<div class="card p-3 mb-3">
    <p><strong>Tipo:</strong> {{ ucfirst($mov->tipo_movimiento) }}</p>
    <p><strong>Motivo:</strong> {{ $mov->motivo_movimiento }}</p>
    <p><strong>Destino:</strong> {{ optional($apiario->comuna->region)->nombre }}, {{ optional($apiario->comuna)->nombre }}</p>
    <p><strong>Coordenadas:</strong> {{ $apiario->latitud }}, {{ $apiario->longitud }}</p>
    <p><strong>Transportista:</strong> {{ $mov->transportista }}</p>
    <p><strong>Vehículo:</strong> {{ $mov->vehiculo }}</p>
</div>

{{-- Colmenas trasladadas --}}
<h5 class="mb-2">Colmenas trasladadas</h5>
@forelse($colmenasPorOrigen as $origenNombre => $colmenas)
    <div class="mb-3">
        <strong><i class="fas fa-home"></i> {{ $origenNombre }} ({{ $colmenas->count() }} colmenas)</strong>
        <div class="d-flex flex-wrap mt-2">
            @foreach($colmenas as $colmena)
                <div class="card text-center shadow-sm m-1" style="min-width: 90px; background-color: {{ $colmena->color_etiqueta ?? '#f0f0f0' }}">
                    <div class="card-body p-2">
                        <div style="font-size: 18px; font-weight: bold;">
                            #{{ $colmena->numero }}
                        </div>
                        <div style="font-size: 12px;">Colmena</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <p>No hay colmenas registradas para este movimiento.</p>
@endforelse
