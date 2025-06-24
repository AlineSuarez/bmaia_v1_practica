@extends('layouts.app')

@section('title', 'Detalle de Colmena')

@section('content')
        <div class="container py-4">
            <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                <a href="{{ route('apiarios') }}">Apiarios</a>
                </li>
                <li class="breadcrumb-item">
                <a href="{{ route('colmenas.index', $apiario->id) }}">
                    {{ $apiario->nombre }}
                </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                Colmena #{{ $colmena->numero }}
                </li>
            </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">Colmena #{{ $colmena->numero }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 text-center">
                                @php
                                    $url = route('colmenas.show', [
                                        'apiario' => $apiario->id,
                                        'colmena'  => $colmena->id
                                    ]);
                                @endphp

                                <img
                                src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($url) }}&size=150x150"
                                alt="QR Colmena #{{ $colmena->numero }}"
                                />
                                <div class="mt-2">
                                    <a href="#" class="btn btn-sm btn-outline-secondary">Imprimir QR</a>
                                </div>
                            </div>
                            <!--
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
                            -->
                        </div>

                        @if($pccs->isEmpty())
                            <div class="alert alert-info mt-3">No hay evaluaciones registradas para esta colmena.</div>
                        @else
                            <div class="accordion mt-4" id="accordionPccHistorial">
                                @foreach($pccs as $index => $pcc)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $index }}"
                                                    aria-expanded="false"
                                                    aria-controls="collapse{{ $index }}">
                                                Evaluación PCC – {{ \Carbon\Carbon::parse($pcc->fecha)->format('d/m/Y') }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $index }}"
                                            data-bs-parent="#accordionPccHistorial">
                                            <div class="accordion-body">
                                                <h6 class="mb-2">PCC1 – Desarrollo Cámara de Cría</h6>
                                                <ul>
                                                    <li><strong>Vigor colmena:</strong> {{ $pcc->desarrolloCria->vigor_colmena ?? 'N/A' }}</li>
                                                    <li><strong>Actividad abejas:</strong> {{ $pcc->desarrolloCria->actividad_abejas ?? 'N/A' }}</li>
                                                    <li><strong>Ingreso polen:</strong> {{ $pcc->desarrolloCria->ingreso_polen ?? 'N/A' }}</li>
                                                    <li><strong>Presencia celdas reales:</strong> {{ $pcc->desarrolloCria->presencia_celdas_reales ?? 'N/A' }}</li>
                                                    <li><strong>Marcos con cría:</strong> {{ $pcc->desarrolloCria->cantidad_marcos_con_cria ?? 'N/A' }}</li>
                                                </ul>

                                                <h6 class="mt-3">PCC2 – Calidad de la Reina</h6>
                                                <ul>
                                                    <li><strong>Postura reina:</strong> {{ $pcc->calidadReina->postura_reina ?? 'N/A' }}</li>
                                                    <li><strong>Estado cría:</strong> {{ $pcc->calidadReina->estado_cria ?? 'N/A' }}</li>
                                                    <li><strong>Fecha introducción:</strong>
                                                        {{ optional(optional($pcc->calidadReina)->fecha_introduccion)->format('d/m/Y') ?? 'N/A' }}
                                                    </li>
                                                </ul>

                                                <h6 class="mt-3">PCC3 – Estado Nutricional</h6>
                                                <ul>
                                                    <li><strong>Objetivo:</strong> {{ $pcc->estadoNutricional->objetivo ?? 'N/A' }}</li>
                                                    <li><strong>Tipo alimentación:</strong> {{ $pcc->estadoNutricional->tipo_alimentacion ?? 'N/A' }}</li>
                                                    <li><strong>Insumo utilizado:</strong> {{ $pcc->estadoNutricional->insumo_utilizado ?? 'N/A' }}</li>
                                                </ul>

                                                <h6 class="mt-3">PCC4 – Varroa</h6>
                                                <ul>
                                                    <li><strong>Diagnóstico visual:</strong> {{ $pcc->presenciaVarroa->diagnostico_visual ?? 'N/A' }}</li>
                                                    <li><strong>Método diagnóstico:</strong> {{ $pcc->presenciaVarroa->metodo_diagnostico ?? 'N/A' }}</li>
                                                </ul>

                                                <h6 class="mt-3">PCC5 – Nosemosis</h6>
                                                <ul>
                                                    <li><strong>Signos clínicos:</strong> {{ $pcc->presenciaNosemosis->signos_clinicos ?? 'N/A' }}</li>
                                                    <li><strong>Tratamiento:</strong> {{ $pcc->presenciaNosemosis->tratamiento ?? 'N/A' }}</li>
                                                </ul>

                                                <h6 class="mt-3">PCC6 – Índice de Cosecha</h6>
                                                <ul>
                                                    <li><strong>Madurez miel:</strong> {{ $pcc->indiceCosecha->madurez_miel ?? 'N/A' }}</li>
                                                    <li><strong>Alzas promedio:</strong> {{ $pcc->indiceCosecha->num_alzadas ?? 'N/A' }}</li>
                                                </ul>

                                                <h6 class="mt-3">PCC7 – Preparación Invernada</h6>
                                                <ul>
                                                    <li><strong>Control sanitario:</strong> {{ $pcc->preparacionInvernada->control_sanitario ?? 'N/A' }}</li>
                                                    <li><strong>Reina presente:</strong>
                                                        @if(isset($pcc->preparacionInvernada) && $pcc->preparacionInvernada->presencia_reina !== null)
                                                            {{ $pcc->preparacionInvernada->presencia_reina ? 'Sí' : 'No' }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @endif


                        <div class="card-footer d-flex justify-content-between">
                            <form id="form-delete-colmena" method="POST" action="{{ route('colmenas.destroy', [$apiario->id, $colmena->id]) }}" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <!--<button type="button" class="btn btn-sm btn-outline-danger btn-delete-colmena">Eliminar</button>-->
                            </form> 
        <!--<a href="{{ route('colmenas.edit', [$apiario->id, $colmena->id]) }}" class="btn btn-outline-warning btn-sm">
                                Editar

                            <a href="{{ route('sistemaexperto.editpcc', $colmena) }}"  class="btn btn-outline-danger btn-sm" y>Editar PCC</a>
                            </a>-->
                            @php
    $sistemaexperto = \App\Models\SistemaExperto::where('colmena_id', $colmena->id)->latest('fecha')->first();
                            @endphp

                            @if($sistemaexperto)
                                <a href="{{ route('sistemaexperto.editpcc', $colmena) }}" class="btn btn-outline-primary btn-sm">
                                    Editar PCC
                                </a>
                            @else
                                <a href="{{ route('sistemaexperto.create', $apiario) }}" class="btn btn-outline-success btn-sm">
                                    Crear PCC
                                </a>
                            @endif


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
