@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalles del Registro de Calidad de Reina</h2>
        <div>
            {{-- Botón para editar el registro --}}
            @if(isset($calidadReina))
                {{-- Asumiendo una ruta para editar el registro de CalidadReina --}}
                <a href="{{ route('visitas.edit4', ['apiario' => $apiario->id, 'calidadReina' => $calidadReina->id]) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-2"></i>Editar Registro
                </a>
            @endif

            {{-- Botón para descargar el documento --}}
            {{-- Asegúrate de que el ID de la calidadReina sea pasado a la ruta --}}
            <a href="{{ route('document.generateReinaDocument', ['calidadReina' => $calidadReina->id]) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-download me-2"></i>Descargar Documento
            </a>
        </div>
    </div>

    @if(isset($calidadReina))
        <div class="card mb-4">
            <div class="card-header">
                Información General
            </div>
            <div class="card-body">
                <p><strong>Postura de la Reina:</strong> {{ $calidadReina->postura_reina ?? 'N/A' }}</p>
                <p><strong>Estado de la Cría:</strong> {{ $calidadReina->estado_cria ?? 'N/A' }}</p>
                <p><strong>Postura de Zánganos:</strong> {{ $calidadReina->postura_zanganos ?? 'N/A' }}</p>
                <p><strong>Origen de la Reina:</strong> {{ $calidadReina->origen_reina ?? 'N/A' }}</p>
                <p><strong>Raza:</strong> {{ $calidadReina->raza ?? 'N/A' }}</p>
                <p><strong>Línea Genética:</strong> {{ $calidadReina->linea_genetica ?? 'N/A' }}</p>
                <p><strong>Fecha de Introducción:</strong> {{ $calidadReina->fecha_introduccion ? \Carbon\Carbon::parse($calidadReina->fecha_introduccion)->format('d-m-Y') : 'N/A' }}</p>
                <p><strong>Estado Actual:</strong> {{ $calidadReina->estado_actual ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Reemplazos Realizados
            </div>
            <div class="card-body">
                @php
                    $reemplazos = $calidadReina->reemplazos_realizados;

                    // Si está guardado como string JSON, decodificarlo
                    if (is_string($reemplazos)) {
                        $decodedReemplazos = json_decode($reemplazos, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedReemplazos)) {
                            $reemplazos = $decodedReemplazos;
                        } else {
                            $reemplazos = [];
                        }
                    } elseif (!is_array($reemplazos)) {
                        $reemplazos = [];
                    }
                @endphp

                @if(!empty($reemplazos))
                    <ul class="list-group">
                        @foreach($reemplazos as $reemplazo)
                            <li class="list-group-item">
                                <strong>Fecha:</strong> {{ $reemplazo['fecha'] ? \Carbon\Carbon::parse($reemplazo['fecha'])->format('d-m-Y') : 'N/A' }}<br>
                                <strong>Motivo:</strong> {{ $reemplazo['motivo'] ?? 'N/A' }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No se han registrado reemplazos.</p>
                @endif
            </div>
        </div>
    @else
        <p>No se encontró el registro de calidad de reina.</p>
    @endif

    <a href="{{ route('visitas.index', $apiario->id) }}" class="btn btn-secondary mt-3">Volver al Apiario</a>
</div>
@endsection