@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Historial de Visitas del Apiario: {{ $apiario->nombre }}</h2>
    <p><strong>Ubicación:</strong> {{ $apiario->ubicacion }}</p>

    @if($apiario->visitas->isEmpty())
        <p class="alert alert-warning">No hay visitas registradas para este apiario.</p>
    @else
        <ul class="nav nav-tabs" id="visitTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">Visitas Generales</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inspeccion-tab" data-bs-toggle="tab" data-bs-target="#inspeccion" type="button" role="tab" aria-controls="inspeccion" aria-selected="false">Inspecciones</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="medicamentos-tab" data-bs-toggle="tab" data-bs-target="#medicamentos" type="button" role="tab" aria-controls="medicamentos" aria-selected="false">Uso de Medicamentos</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="visitTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                @if($apiario->visitas->where('tipo_visita', 'Visita General')->isEmpty())
                    <p>No hay visitas generales registradas.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>RUT</th>
                                <th>Motivo</th>
                                <th>Teléfono</th>
                                <th>Firma</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apiario->visitas->where('tipo_visita', 'Visita General') as $visita)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($visita->fecha_visita)->format('d/m/Y') }}</td>
                                    <td>{{ $visita->nombres }}</td>
                                    <td>{{ $visita->apellidos }}</td>
                                    <td>{{ $visita->rut }}</td>
                                    <td>{{ $visita->motivo }}</td>
                                    <td>{{ $visita->telefono }}</td>
                                    <td>{{ $visita->firma }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="tab-pane fade" id="inspeccion" role="tabpanel" aria-labelledby="inspeccion-tab">
                @if($apiario->visitas->where('tipo_visita', 'Inspección de Visita')->isEmpty())
                    <p>No hay inspecciones registradas.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>N° Colmenas Totales</th>
                                <th>N° Colmenas Activas</th>
                                <th>N° Colmenas Enfermas</th>
                                <th>N° Colmenas Muertas</th>
                                <th>Colmenas Inspeccionadas</th>
                                <th>Flujo Néctar/Polen</th>
                                <th>Revisor</th>
                                <th>Sospecha Enfermedad</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apiario->visitas->where('tipo_visita', 'Inspección de Visita') as $visita)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($visita->fecha_visita)->format('d/m/Y') }}</td>
                                    <td>{{ $visita->num_colmenas_totales ?? 'N/A' }}</td>
                                    <td>{{ $visita->num_colmenas_activas ?? 'N/A' }}</td>
                                    <td>{{ $visita->num_colmenas_enfermas ?? 'N/A' }}</td>
                                    <td>{{ $visita->num_colmenas_muertas ?? 'N/A' }}</td>
                                    <td>{{ $visita->num_colmenas_inspeccionadas ?? 'N/A' }}</td>
                                    <td>
                                        {{ $visita->flujo_nectar_polen ?? 'N/A' }} <!-- manera 1 de mostrar flujo po -->

                                    </td>
                                    <td>{{ $visita->nombre_revisor_apiario ?? 'N/A' }}</td>
                                    <td>{{ $visita->sospecha_enfermedad ?? 'N/A' }}</td>
                                    <td>{{ $visita->observaciones ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="tab-pane fade" id="medicamentos" role="tabpanel" aria-labelledby="medicamentos-tab">
                @if($apiario->visitas->where('tipo_visita', 'Uso de Medicamentos')->isEmpty())
                    <p>No hay registros de uso de medicamentos.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>N° Colmenas Tratadas</th>
                                <th>Motivo del Tratamiento</th>
                                <th>Nombre Comercial</th>
                                <th>Principio Activo</th>
                                <th>Período de Resguardo</th>
                                <th>Responsable</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apiario->visitas->where('tipo_visita', 'Uso de Medicamentos') as $visita)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($visita->fecha_visita)->format('d/m/Y') }}</td>
                                    <td>{{ $visita->num_colmenas_tratadas ?? 'N/A' }}</td>
                                    <td>{{ $visita->motivo_tratamiento ?? 'N/A' }}</td>
                                    <td>{{ $visita->nombre_comercial_medicamento ?? 'N/A' }}</td>
                                    <td>{{ $visita->principio_activo_medicamento ?? 'N/A' }}</td>
                                    <td>{{ $visita->periodo_resguardo ?? 'N/A' }}</td>
                                    <td>{{ $visita->responsable ?? 'N/A' }}</td>
                                    <td>{{ $visita->observaciones ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endif

    <a href="{{ route('visitas') }}" class="btn btn-secondary mt-3">Volver a Mis Apiarios</a>
</div>
@endsection

@section('scripts')
    <script>
        // Activar la pestaña por defecto (Visitas Generales)
        var firstTabEl = document.querySelector('#general-tab')
        if (firstTabEl) {
            var tab = new bootstrap.Tab(firstTabEl);
            tab.show();
        }
    </script>
@endsection