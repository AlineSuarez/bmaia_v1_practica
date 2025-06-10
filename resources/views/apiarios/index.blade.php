@extends('layouts.app')

@section('content')
    <head>
        <link href="{{ asset('./css/components/home-user/apiarios.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Mono&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <!-- Botones de acciones principales -->
    <div class="action-buttons">
        <a href="{{ route('apiarios.create') }}" class="action-button primary">
            <i class="fas fa-plus-circle"></i> Nuevo Apiario
        </a>
    </div>

    <div class="apiarios-container animated-element hexagon-bg">
        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="apiariosTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="fijos-tab" data-bs-toggle="tab" data-bs-target="#fijos" type="button"
                    role="tab" aria-controls="fijos" aria-selected="true">
                    <i class="fas fa-warehouse"></i> Apiarios Fijos/Permanentes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="trashumantes-tab" data-bs-toggle="tab" data-bs-target="#trashumantes"
                    type="button" role="tab" aria-controls="trashumantes" aria-selected="false">
                    <i class="fas fa-truck"></i> Apiarios Temporales
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="archivados-tab" data-bs-toggle="tab" data-bs-target="#archivados" type="button"
                    role="tab" aria-controls="archivados" aria-selected="false">
                    <i class="fas fa-folder"></i> Apiarios Archivados
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="apiariosTabContent">

            <!-- ========== PESTAÑA APIARIOS FIJOS ========== -->
            <div class="tab-pane fade show active" id="fijos" role="tabpanel" aria-labelledby="fijos-tab">
            <h3 class="table-title">
                <i class="fas fa-warehouse"></i> Apiarios Fijos/Permanentes
            </h3>
                <div class="apiarios-table-wrapper">
                    <div class="table-responsive">
                        @if(count($apiariosFijos) > 0)
                            <table id="apiariosTable" class="apiarios-table">
                                <thead>
                                    <tr>
                                        <th class="text-center"><span class="column-title">Apiario</span></th>
                                        <th class="text-center"><span class="column-title">Temp. prod.</span></th>
                                        <th class="text-center"><span class="column-title">Reg. SAG</span></th>
                                        <th class="text-center"><span class="column-title">Colmenas</span></th>
                                        <th class="text-center"><span class="column-title">Tipo apiario</span></th>
                                        <th class="text-center"><span class="column-title">Manejo</span></th>
                                        <th class="text-center"><span class="column-title">Obj. prod.</span></th>
                                        <th class="text-center"><span class="column-title">Comuna</span></th>
                                        <th class="text-center"><span class="column-title">Ubicación</span></th>
                                        <th class="text-center"><span class="column-title">Foto</span></th>
                                        <th class="text-center"><span class="column-title">Acción</span></th>
                                    </tr>
                                </thead>
                                <tbody id="apiariosTableBody">
                                    @foreach ($apiariosFijos as $apiario)
                                        <tr data-table="fijos">
                                            <td class="text-center">
                                                <span class="apiario-id">{{ $apiario->nombre }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="tag tag-primary">{{ $apiario->temporada_produccion }}</span>
                                            </td>
                                            <td class="text-center">{{ $apiario->registro_sag }}</td>
                                            <td class="text-center">
                                                <div class="counter">{{ $apiario->num_colmenas }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="tag tag-secondary">{{ $apiario->tipo_apiario }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="tag tag-info">{{ $apiario->tipo_manejo }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="tag tag-warning">{{ $apiario->objetivo_produccion }}</span>
                                            </td>
                                            <td class="text-center">
                                                {{ $apiario->comuna && $apiario->comuna->nombre ? $apiario->comuna->nombre : 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                <div class="location-info">
                                                    <span class="coordinates">{{ $apiario->latitud }}, {{ $apiario->longitud }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($apiario->foto)
                                                    <div class="apiario-image">
                                                        <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Fotografía del Apiario"
                                                            data-bs-toggle="modal" data-bs-target="#imageModal{{ $apiario->id }}">
                                                    </div>
                                                @else
                                                    <span class="text-muted">Sin imagen</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="table-actions">
                                                    <a href="{{ route('apiarios.editar', $apiario->id) }}" class="btn-table-action btn-edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('colmenas.index', $apiario->id) }}" class="btn-table-action btn-info">
                                                        <i class="fas fa-cubes"></i>
                                                    </a>
                                                    <a href="{{ route('generate.document', $apiario->id) }}" class="btn-table-action btn-info">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="btn-table-action btn-delete" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $apiario->id }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Paginación para Apiarios Fijos -->
                            <div class="pagination-wrapper">
                                <div class="pagination-info">
                                    <span id="fijos-pagination-info">Mostrando 1-{{ count($apiariosFijos) }} de {{ count($apiariosFijos) }} apiarios</span>
                                </div>
                                <nav aria-label="Paginación de apiarios fijos">
                                    <ul class="pagination" id="fijos-pagination">
                                        <!-- Se genera dinámicamente con JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <h3 class="empty-state-text">No hay apiarios registrados</h3>
                                <a href="{{ route('apiarios.create') }}" class="action-button primary">
                                    <i class="fas fa-plus-circle"></i> Crear primer apiario
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ========== PESTAÑA APIARIOS TRASHUMANTES ========== -->
            <div class="tab-pane fade" id="trashumantes" role="tabpanel" aria-labelledby="trashumantes-tab">

                <!-- Botones de acciones para trashumantes -->
                <div class="action-buttons">
                    <button id="trasladarColmenasButton" class="action-button warning" disabled>
                        <i class="fas fa-arrow-right"></i> Trasladar Colmenas
                    </button>
                    <button id="retornarColmenasButton" class="action-button success" disabled>
                        <i class="fas fa-arrow-left"></i> Retornar Colmenas
                    </button>
                </div>

                <!-- Tabla de Apiarios Base -->
                <div class="table-section">
                    <h3 class="table-title">
                        <i class="fas fa-truck"></i> Apiarios Base
                    </h3>
                    <div class="apiarios-table-wrapper">
                        <div class="table-responsive">
                            @if(count($apiariosBase) > 0)
                                <table id="apiariosTableTrashumante" class="apiarios-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">
                                                <label class="custom-checkbox">
                                                    <input type="checkbox" id="selectAllTrashumante">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </th>
                                            <th class="text-center"><span class="column-title">Apiario</span></th>
                                            <th class="text-center"><span class="column-title">Temp. prod.</span></th>
                                            <th class="text-center"><span class="column-title">Reg. SAG</span></th>
                                            <th class="text-center"><span class="column-title">Colmenas</span></th>
                                            <th class="text-center"><span class="column-title">Tipo apiario</span></th>
                                            <th class="text-center"><span class="column-title">Manejo</span></th>
                                            <th class="text-center"><span class="column-title">Obj. prod.</span></th>
                                            <th class="text-center"><span class="column-title">Comuna</span></th>
                                            <th class="text-center"><span class="column-title">Ubicación</span></th>
                                            <th class="text-center"><span class="column-title">Foto</span></th>
                                            <th class="text-center"><span class="column-title">Acción</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($apiariosBase as $apiario)
                                            <tr>
                                                <td class="text-center">
                                                    <label class="custom-checkbox">
                                                        <input type="checkbox" class="select-checkbox-trashumante" value="{{ $apiario->id }}">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td class="text-center">
                                                    <span class="apiario-id">{{ $apiario->nombre }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="tag tag-primary">{{ $apiario->temporada_produccion }}</span>
                                                </td>
                                                <td class="text-center">{{ $apiario->registro_sag }}</td>
                                                <td class="text-center">
                                                    <div class="counter">{{ $apiario->num_colmenas }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="tag tag-secondary">{{ $apiario->tipo_apiario }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="tag tag-info">{{ $apiario->tipo_manejo }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="tag tag-warning">{{ $apiario->objetivo_produccion }}</span>
                                                </td>
                                                <td class="text-center">
                                                    {{ $apiario->comuna && $apiario->comuna->nombre ? $apiario->comuna->nombre : 'N/A' }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="location-info">
                                                        <span class="coordinates">{{ $apiario->latitud }}, {{ $apiario->longitud }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($apiario->foto)
                                                        <div class="apiario-image">
                                                            <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Fotografía del Apiario" 
                                                                 data-bs-toggle="modal" data-bs-target="#imageModal{{ $apiario->id }}">
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Sin imagen</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="table-actions">
                                                        <a href="{{ route('apiarios.editar', $apiario->id) }}" class="btn-table-action btn-edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('colmenas.index', $apiario->id) }}" class="btn-table-action btn-info">
                                                            <i class="fas fa-cubes"></i>
                                                        </a>
                                                        <a href="{{ route('generate.document', $apiario->id) }}" class="btn-table-action btn-info">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Paginación para Apiarios Base -->
                                <div class="pagination-wrapper">
                                    <div class="pagination-info">
                                        <span id="base-pagination-info">Mostrando 1-{{ count($apiariosBase) }} de {{ count($apiariosBase) }} apiarios</span>
                                    </div>
                                    <nav aria-label="Paginación de apiarios base">
                                        <ul class="pagination" id="base-pagination">
                                            <!-- Se genera dinámicamente con JavaScript -->
                                        </ul>
                                    </nav>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <h3 class="empty-state-text">No hay apiarios trashumantes registrados</h3>
                                    <a href="{{ route('apiarios.create') }}" class="action-button primary">
                                        <i class="fas fa-plus-circle"></i> Crear primer apiario trashumante
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tabla de Apiarios Temporales -->
                <div class="table-section">
                    <h3 class="table-title">
                        <i class="fas fa-calendar-alt"></i> Apiarios Temporales
                    </h3>
                    <div class="apiarios-table-wrapper">
                        <div class="table-responsive">
                            <table id="apiariosTemporalesTable" class="apiarios-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <label class="custom-checkbox">
                                                <input type="checkbox" id="selectAllTemporales">
                                                <span class="checkmark"></span>
                                            </label>
                                        </th>
                                        <th class="text-center"><span class="column-title">Apicultor</span></th>
                                        <th class="text-center"><span class="column-title">Nº Colmenas</span></th>
                                        <th class="text-center"><span class="column-title">Región Origen</span></th>
                                        <th class="text-center"><span class="column-title">Comuna Origen</span></th>
                                        <th class="text-center"><span class="column-title">Región Destino</span></th>
                                        <th class="text-center"><span class="column-title">Comuna Destino</span></th>
                                        <th class="text-center"><span class="column-title">Fecha Movimiento</span></th>
                                        <th class="text-center"><span class="column-title">Motivo (Prod./Pol.)</span></th>
                                        <th class="text-center"><span class="column-title">Cultivo</span></th>
                                        <th class="text-center"><span class="column-title">Acción</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($apiariosTemporales as $apiario)
                                                                    @php
    $mov = $apiario->ultimoMovimientoDestino;
    $apiarioOrigen = $mov ? $mov->apiarioOrigen : null;
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="text-center">
                                                                            <label class="custom-checkbox">
                                                                                <input type="checkbox" class="select-checkbox-temporales" value="{{ $apiario->id }}">
                                                                                <span class="checkmark"></span>
                                                                            </label>
                                                                        </td>
                                                                        <td class="text-center">{{ $apiario->nombre }}</td>
                                                                        <td class="text-center">
                                                                            <div class="counter">{{ $apiario->num_colmenas }}</div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ $apiarioOrigen && $apiarioOrigen->comuna && $apiarioOrigen->comuna->region
        ? $apiarioOrigen->comuna->region->nombre : 'N/A' }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ $apiarioOrigen && $apiarioOrigen->comuna
        ? $apiarioOrigen->comuna->nombre : 'N/A' }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ optional(optional($apiario->comuna)->region)->nombre ?? 'N/A' }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ optional($apiario->comuna)->nombre ?? 'N/A' }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ $mov ? $mov->fecha_movimiento->format('Y-m-d') : '—' }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="tag tag-{{ $mov && $mov->motivo_movimiento === 'Polinización' ? 'success' : 'warning' }}">
                                                                                {{ $mov ? $mov->motivo_movimiento : '—' }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ $mov && $mov->motivo_movimiento === 'Polinización' ? ($mov->cultivo ?? '—') : '—' }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <div class="table-actions">
                                                                                <a href="{{ route('apiarios.editar', $apiario->id) }}" class="btn-table-action btn-edit">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                                <a href="{{ route('colmenas.index', $apiario->id) }}" class="btn-table-action btn-info">
                                                                                    <i class="fas fa-cubes"></i>
                                                                                </a>
                                                                                <a href="{{ route('generate.document', $apiario->id) }}" class="btn-table-action btn-info">
                                                                                    <i class="fas fa-download"></i>
                                                                                </a>
                                                                                <button class="btn-table-action btn-delete" type="button" data-bs-toggle="modal"
                                                                                    data-bs-target="#deleteModalTemporal{{ $apiario->id }}">
                                                                                    <i class="fas fa-archive"></i>
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="fas fa-calendar-times"></i>
                                                    </div>
                                                    <h3 class="empty-state-text">No hay apiarios temporales</h3>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Paginación para Apiarios Temporales -->
                            <div class="pagination-wrapper">
                                <div class="pagination-info">
                                    <span id="temporales-pagination-info">Mostrando 1-{{ count($apiariosTemporales) }} de {{ count($apiariosTemporales) }} apiarios</span>
                                </div>
                                <nav aria-label="Paginación de apiarios temporales">
                                    <ul class="pagination" id="temporales-pagination">
                                        <!-- Se genera dinámicamente con JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== PESTAÑA APIARIOS ARCHIVADOS ========== -->
            <div class="tab-pane fade" id="archivados" role="tabpanel" aria-labelledby="archivados-tab">
                <div class="apiarios-table-wrapper">
                    <div class="table-responsive">
                        @if($apiariosArchivados->count() > 0)
                            <table id="apiariosArchivadosTable" class="apiarios-table">
                                <thead>
                                    <tr>
                                        <th class="text-center"><span class="column-title">Apiario</span></th>
                                        <th class="text-center"><span class="column-title">N° Colmenas</span></th>
                                        <th class="text-center"><span class="column-title">Tipo</span></th>
                                        <th class="text-center"><span class="column-title">Objetivo</span></th>
                                        <th class="text-center"><span class="column-title">Región</span></th>
                                        <th class="text-center"><span class="column-title">Comuna</span></th>
                                        <th class="text-center"><span class="column-title">Fecha Archivado</span></th>
                                        <th class="text-center"><span class="column-title">Acción</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($apiariosArchivados as $apiario)
                                        <tr>
                                            <td class="text-center">
                                                <span class="apiario-id">{{ $apiario->nombre }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="counter">{{ $apiario->colmenas_historicas ?? 0 }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="tag tag-secondary">{{ ucfirst($apiario->tipo_apiario) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="tag tag-warning">{{ $apiario->objetivo_produccion }}</span>
                                            </td>
                                            <td class="text-center">
                                                {{ optional(optional($apiario->comuna)->region)->nombre ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                {{ optional($apiario->comuna)->nombre ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $apiario->updated_at->format('Y-m-d') }}
                                            </td>
                                            <td class="text-center">
                                                <div class="table-actions">
                                                    <form action="{{ route('apiarios.destroy', $apiario->id) }}" method="POST" 
                                                          style="display: inline-block;" 
                                                          onsubmit="return confirm('¿Está seguro de eliminar permanentemente este apiario?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-table-action btn-delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h3 class="empty-state-text">No hay apiarios archivados</h3>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MODALES ========== -->

    <!-- Modales para apiarios fijos -->
    @foreach ($apiariosFijos as $apiario)
        <!-- Modal de eliminación -->
        <div class="modal fade custom-modal" id="deleteModal{{ $apiario->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $apiario->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $apiario->id }}">
                            <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar el apiario <strong>{{ $apiario->nombre }}</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn modal-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('apiarios.destroy', $apiario->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="modal-btn modal-btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de imagen -->
        @if($apiario->foto)
            <div class="modal fade custom-modal" id="imageModal{{ $apiario->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Fotografía del Apiario {{ $apiario->nombre }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Fotografía del Apiario" 
                                 style="max-width: 100%; border-radius: 8px;">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Modales para apiarios base -->
    @foreach ($apiariosBase as $apiario)
        @if($apiario->foto)
            <div class="modal fade custom-modal" id="imageModal{{ $apiario->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Fotografía del Apiario {{ $apiario->nombre }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Fotografía del Apiario" 
                                 style="max-width: 100%; border-radius: 8px;">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Modales para apiarios temporales -->
    @foreach($apiariosTemporales as $apiario)
        <div class="modal fade custom-modal" id="deleteModalTemporal{{ $apiario->id }}" tabindex="-1" 
             aria-labelledby="deleteModalLabelTemporal{{ $apiario->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabelTemporal{{ $apiario->id }}">
                            <i class="fas fa-archive" style="margin-right: 10px;"></i> Archivar Apiario Temporal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea archivar el apiario temporal <strong>{{ $apiario->nombre }}</strong>?</p><br>
                        <p><small class="text-muted">El apiario será movido a la sección de archivados.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn modal-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('apiarios.destroy', $apiario->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="modal-btn modal-btn-warning">Archivar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal para crear apiario temporal -->
    <div class="modal fade custom-modal" id="createTemporalModal" tabindex="-1" aria-labelledby="createTemporalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTemporalModalLabel">
                        <i class="fas fa-route"></i> Crear Apiario Temporal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">¿Desea crear un apiario temporal con los apiarios seleccionados?</p>
                    <div class="selected-apiarios-info">
                        <h6>Apiarios seleccionados:</h6>
                        <ul id="selectedApiariosList" class="list-unstyled">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </ul>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Traslado:</strong> Mover apiarios a una ubicación temporal.<br>
                        <hr>
                        <i class="fas fa-info-circle"></i>
                        <strong>Retorno:</strong> Devolver apiarios a su ubicación original.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="modal-btn modal-btn-warning" id="createTrasladoButton">
                        <i class="fas fa-arrow-right"></i> Traslado
                    </button>
                    <button type="button" class="modal-btn modal-btn-success" id="createRetornoButton">
                        <i class="fas fa-arrow-left"></i> Retorno
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para retorno -->
    <div class="modal fade custom-modal" id="returnConfirmationModal" tabindex="-1" aria-labelledby="returnConfirmationLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="returnConfirmationLabel" class="modal-title">
                        <i class="fas fa-exclamation-circle"></i> Confirmar Retorno
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>Las colmenas de los apiarios temporales seleccionados serán devueltas a sus apiarios de origen correspondientes, y estos apiarios temporales serán finalmente archivados.</p>
                    <div class="selected-apiarios-info">
                        <h6>Apiarios temporales seleccionados:</h6>
                        <ul id="returnSelectedList" class="list-unstyled">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </ul>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¿Está seguro de que desea continuar?</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="modal-btn modal-btn-success" id="confirmReturnButton">
                        <i class="fas fa-check"></i> Confirmar Retorno
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('optional-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ============================================================
            // SISTEMA DE PAGINACIÓN PERSONALIZADO
            // ============================================================

            class TablePagination {
                constructor(tableId, paginationId, infoId, itemsPerPage = 4) {
                    this.table = document.getElementById(tableId);
                    this.pagination = document.getElementById(paginationId);
                    this.info = document.getElementById(infoId);
                    this.itemsPerPage = itemsPerPage;
                    this.currentPage = 1;
                    this.totalItems = 0;
                    this.totalPages = 0;
                    this.rows = [];

                    if (this.table && this.pagination && this.info) {
                        this.init();
                    }
                }

                init() {
                    this.rows = Array.from(this.table.querySelectorAll('tbody tr')).filter(row =>
                        !row.id || (!row.id.includes('empty') && row.style.display !== 'none')
                    );
                    this.totalItems = this.rows.length;
                    this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

                    if (this.totalItems > 0) {
                        this.showPage(1);
                        this.createPagination();
                        this.updateInfo();
                    } else {
                        this.pagination.style.display = 'none';
                        this.info.style.display = 'none';
                    }
                }

                showPage(page) {
                    this.currentPage = page;
                    const start = (page - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;

                    this.rows.forEach((row, index) => {
                        if (index >= start && index < end) {
                            row.style.display = '';
                            // Animación de entrada
                            row.style.opacity = '0';
                            row.style.transform = 'translateY(10px)';
                            setTimeout(() => {
                                row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                row.style.opacity = '1';
                                row.style.transform = 'translateY(0)';
                            }, (index - start) * 50);
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    this.updatePagination();
                    this.updateInfo();
                }

                createPagination() {
                    if (this.totalPages <= 1) {
                        this.pagination.style.display = 'none';
                        return;
                    }

                    this.pagination.innerHTML = '';
                    this.pagination.style.display = 'flex';

                    // Botón Anterior
                    const prevLi = document.createElement('li');
                    prevLi.className = `page-item ${this.currentPage === 1 ? 'disabled' : ''}`;
                    prevLi.innerHTML = `
                                    <a class="page-link" href="#" data-page="${this.currentPage - 1}">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                `;
                    this.pagination.appendChild(prevLi);

                    // Botones de páginas
                    for (let i = 1; i <= this.totalPages; i++) {
                        if (this.shouldShowPage(i)) {
                            const li = document.createElement('li');
                            li.className = `page-item ${i === this.currentPage ? 'active' : ''}`;
                            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                            this.pagination.appendChild(li);
                        } else if (this.shouldShowEllipsis(i)) {
                            const li = document.createElement('li');
                            li.className = 'page-item disabled';
                            li.innerHTML = '<span class="page-link">...</span>';
                            this.pagination.appendChild(li);
                        }
                    }

                    // Botón Siguiente
                    const nextLi = document.createElement('li');
                    nextLi.className = `page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}`;
                    nextLi.innerHTML = `
                                    <a class="page-link" href="#" data-page="${this.currentPage + 1}">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                `;
                    this.pagination.appendChild(nextLi);

                    // Event listeners
                    this.pagination.addEventListener('click', (e) => {
                        e.preventDefault();
                        const link = e.target.closest('.page-link');
                        if (link && !link.closest('.page-item').classList.contains('disabled')) {
                            const page = parseInt(link.dataset.page);
                            if (page && page !== this.currentPage && page >= 1 && page <= this.totalPages) {
                                this.showPage(page);
                            }
                        }
                    });
                }

                shouldShowPage(page) {
                    if (this.totalPages <= 7) return true;
                    if (page === 1 || page === this.totalPages) return true;
                    if (Math.abs(page - this.currentPage) <= 2) return true;
                    return false;
                }

                shouldShowEllipsis(page) {
                    if (this.totalPages <= 7) return false;
                    return (page === 2 && this.currentPage > 4) ||
                        (page === this.totalPages - 1 && this.currentPage < this.totalPages - 3);
                }

                updatePagination() {
                    if (!this.pagination) return;

                    const items = this.pagination.querySelectorAll('.page-item');
                    items.forEach(item => {
                        const link = item.querySelector('.page-link');
                        if (link && link.dataset.page) {
                            const page = parseInt(link.dataset.page);
                            if (page === this.currentPage) {
                                item.classList.add('active');
                            } else {
                                item.classList.remove('active');
                            }
                        }
                    });

                    // Actualizar estados de prev/next
                    const firstItem = this.pagination.querySelector('.page-item:first-child');
                    const lastItem = this.pagination.querySelector('.page-item:last-child');

                    if (firstItem) {
                        firstItem.classList.toggle('disabled', this.currentPage === 1);
                        const firstLink = firstItem.querySelector('.page-link');
                        if (firstLink) {
                            firstLink.dataset.page = this.currentPage - 1;
                        }
                    }
                    if (lastItem) {
                        lastItem.classList.toggle('disabled', this.currentPage === this.totalPages);
                        const lastLink = lastItem.querySelector('.page-link');
                        if (lastLink) {
                            lastLink.dataset.page = this.currentPage + 1;
                        }
                    }
                }

                updateInfo() {
                    if (!this.info) return;

                    const start = Math.min((this.currentPage - 1) * this.itemsPerPage + 1, this.totalItems);
                    const end = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);
                    this.info.textContent = `Mostrando ${start}-${end} de ${this.totalItems} apiarios`;
                }

                refresh() {
                    this.init();
                }

                // Método para obtener elementos visibles (para checkboxes)
                getVisibleRows() {
                    return this.rows.filter((row, index) => {
                        const start = (this.currentPage - 1) * this.itemsPerPage;
                        const end = start + this.itemsPerPage;
                        return index >= start && index < end;
                    });
                }
            }

            // Inicializar paginación para todas las tablas
            const fijosPagination = new TablePagination('apiariosTable', 'fijos-pagination', 'fijos-pagination-info');
            const basePagination = new TablePagination('apiariosTableTrashumante', 'base-pagination', 'base-pagination-info');
            const temporalesPagination = new TablePagination('apiariosTemporalesTable', 'temporales-pagination', 'temporales-pagination-info');

            // ============================================================
            // FUNCIONES AUXILIARES PARA GESTIÓN DE MODALES
            // ============================================================

            function cleanupModalBackdrops() {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                const activeModals = document.querySelectorAll('.modal.show');

                if (activeModals.length === 0) {
                    backdrops.forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                } else if (backdrops.length > 1) {
                    for (let i = 1; i < backdrops.length; i++) {
                        backdrops[i].remove();
                    }
                }
            }

            function setupModalEvents() {
                const modals = document.querySelectorAll('.modal');

                modals.forEach(modal => {
                    modal.addEventListener('shown.bs.modal', function () {
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        if (backdrops.length > 1) {
                            for (let i = 1; i < backdrops.length; i++) {
                                backdrops[i].remove();
                            }
                        }
                    });

                    modal.addEventListener('hidden.bs.modal', function () {
                        setTimeout(() => {
                            const activeModals = document.querySelectorAll('.modal.show');
                            if (activeModals.length === 0) {
                                cleanupModalBackdrops();
                            }
                        }, 150);
                    });
                });
            }

            setupModalEvents();
            cleanupModalBackdrops();

            // ============================================================
            // ACTUALIZAR HANDLERS DE CHECKBOXES PARA USAR PAGINACIÓN
            // ============================================================
            function setupFijosHandlers(selectAllId, checkboxSelector, buttonId, pagination) {
                const selectAll = document.getElementById(selectAllId);
                const button = document.getElementById(buttonId);

                function updateButtonState() {
                    // Solo contar checkboxes visibles en la página actual
                    const visibleRows = pagination ? pagination.getVisibleRows() :
                        Array.from(document.querySelectorAll('#apiariosTable tbody tr')).filter(row =>
                            row.style.display !== 'none'
                        );

                    const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                    const checkedCount = visibleCheckboxes.filter(chk => chk.checked).length;

                    if (!button) return;

                    button.disabled = (checkedCount === 0);

                    if (checkedCount > 0) {
                        button.innerHTML = `<i class="fas fa-trash-alt"></i> Eliminar (${checkedCount})`;
                    } else {
                        button.innerHTML = `<i class="fas fa-trash-alt"></i> Eliminar seleccionados`;
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        // Solo afectar checkboxes visibles en la página actual
                        const visibleRows = pagination ? pagination.getVisibleRows() :
                            Array.from(document.querySelectorAll('#apiariosTable tbody tr')).filter(row =>
                                row.style.display !== 'none'
                            );

                        const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                        visibleCheckboxes.forEach(chk => chk.checked = selectAll.checked);
                        updateButtonState();
                    });
                }

                // Usar delegación de eventos para manejar checkboxes dinámicamente
                document.addEventListener('change', function (e) {
                    if (e.target.matches(checkboxSelector)) {
                        const visibleRows = pagination ? pagination.getVisibleRows() :
                            Array.from(document.querySelectorAll('#apiariosTable tbody tr')).filter(row =>
                                row.style.display !== 'none'
                            );

                        const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                        const total = visibleCheckboxes.length;
                        const totalChecked = visibleCheckboxes.filter(chk => chk.checked).length;

                        if (selectAll) {
                            selectAll.checked = (total === totalChecked && total > 0);
                        }
                        updateButtonState();
                    }
                });

                updateButtonState();
            }

            function setupTrashumanteBaseHandlers(selectAllId, checkboxSelector, trasladarBtnId, pagination) {
                const selectAll = document.getElementById(selectAllId);
                const trasladarBtn = document.getElementById(trasladarBtnId);

                function updateTrasladarButton() {
                    const visibleRows = pagination ? pagination.getVisibleRows() :
                        Array.from(document.querySelectorAll('#apiariosTableTrashumante tbody tr')).filter(row =>
                            row.style.display !== 'none'
                        );

                    const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                    const selectedCount = visibleCheckboxes.filter(chk => chk.checked).length;

                    if (trasladarBtn) {
                        trasladarBtn.disabled = (selectedCount === 0);
                        if (selectedCount > 0) {
                            trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas (${selectedCount})`;
                        } else {
                            trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas`;
                        }
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        const visibleRows = pagination ? pagination.getVisibleRows() :
                            Array.from(document.querySelectorAll('#apiariosTableTrashumante tbody tr')).filter(row =>
                                row.style.display !== 'none'
                            );

                        const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                        visibleCheckboxes.forEach(chk => chk.checked = selectAll.checked);
                        updateTrasladarButton();
                    });
                }

                // Usar delegación de eventos para manejar checkboxes dinámicamente
                document.addEventListener('change', function (e) {
                    if (e.target.matches(checkboxSelector) && e.target.closest('#apiariosTableTrashumante')) {
                        const visibleRows = pagination ? pagination.getVisibleRows() :
                            Array.from(document.querySelectorAll('#apiariosTableTrashumante tbody tr')).filter(row =>
                                row.style.display !== 'none'
                            );

                        const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                        const total = visibleCheckboxes.length;
                        const totalChecked = visibleCheckboxes.filter(chk => chk.checked).length;

                        if (selectAll) {
                            selectAll.checked = (total === totalChecked && total > 0);
                        }
                        updateTrasladarButton();
                    }
                });

                updateTrasladarButton();
            }

            function setupTemporalesHandlers(selectAllId, checkboxSelector, buttonId, pagination) {
                const selectAll = document.getElementById(selectAllId);
                const retornarBtn = document.getElementById(buttonId);

                function updateRetornarButtonState() {
                    const visibleRows = pagination ? pagination.getVisibleRows() :
                        Array.from(document.querySelectorAll('#apiariosTemporalesTable tbody tr')).filter(row =>
                            row.style.display !== 'none'
                        );

                    const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                    const checkedCount = visibleCheckboxes.filter(chk => chk.checked).length;

                    if (!retornarBtn) return;

                    retornarBtn.disabled = (checkedCount === 0);
                    if (checkedCount > 0) {
                        retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas (${checkedCount})`;
                    } else {
                        retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas`;
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        const visibleRows = pagination ? pagination.getVisibleRows() :
                            Array.from(document.querySelectorAll('#apiariosTemporalesTable tbody tr')).filter(row =>
                                row.style.display !== 'none'
                            );

                        const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                        visibleCheckboxes.forEach(chk => chk.checked = selectAll.checked);
                        updateRetornarButtonState();
                    });
                }

                // Usar delegación de eventos para manejar checkboxes dinámicamente
                document.addEventListener('change', function (e) {
                    if (e.target.matches(checkboxSelector) && e.target.closest('#apiariosTemporalesTable')) {
                        const visibleRows = pagination ? pagination.getVisibleRows() :
                            Array.from(document.querySelectorAll('#apiariosTemporalesTable tbody tr')).filter(row =>
                                row.style.display !== 'none'
                            );

                        const visibleCheckboxes = visibleRows.map(row => row.querySelector(checkboxSelector)).filter(chk => chk);
                        const total = visibleCheckboxes.length;
                        const totalChecked = visibleCheckboxes.filter(chk => chk.checked).length;

                        if (selectAll) {
                            selectAll.checked = (total === totalChecked && total > 0);
                        }
                        updateRetornarButtonState();
                    }
                });

                updateRetornarButtonState();
            }

            // Configurar handlers para todas las tablas con sus respectivas paginaciones
            setupFijosHandlers('selectAll', '.select-checkbox', 'multiDeleteButton', fijosPagination);
            setupTrashumanteBaseHandlers('selectAllTrashumante', '.select-checkbox-trashumante', 'trasladarColmenasButton', basePagination);
            setupTemporalesHandlers('selectAllTemporales', '.select-checkbox-temporales', 'retornarColmenasButton', temporalesPagination);

            // ============================================================
            // RESTO DE FUNCIONES (mantenidas igual)
            // ============================================================

            function setupTrashumanteBaseHandlers(selectAllId, checkboxSelector, trasladarBtnId) {
                const selectAll = document.getElementById(selectAllId);
                const checkboxes = document.querySelectorAll(checkboxSelector);
                const trasladarBtn = document.getElementById(trasladarBtnId);

                function updateTrasladarButton() {
                    const selectedCount = document.querySelectorAll(checkboxSelector + ':checked').length;

                    if (trasladarBtn) {
                        trasladarBtn.disabled = (selectedCount === 0);
                        if (selectedCount > 0) {
                            trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas (${selectedCount})`;
                        } else {
                            trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas`;
                        }
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        const marcado = selectAll.checked;
                        checkboxes.forEach(chk => chk.checked = marcado);
                        updateTrasladarButton();
                    });
                }

                checkboxes.forEach(chk => {
                    chk.addEventListener('change', function () {
                        const total = checkboxes.length;
                        const totalChecked = document.querySelectorAll(checkboxSelector + ':checked').length;
                        if (selectAll) {
                            selectAll.checked = (total === totalChecked);
                        }
                        updateTrasladarButton();
                    });
                });

                updateTrasladarButton();
            }

            setupTrashumanteBaseHandlers(
                'selectAllTrashumante',
                '.select-checkbox-trashumante',
                'trasladarColmenasButton'
            );

            function setupTemporalesHandlers(selectAllId, checkboxSelector, buttonId) {
                const selectAll = document.getElementById(selectAllId);
                const checkboxes = document.querySelectorAll(checkboxSelector);
                const retornarBtn = document.getElementById(buttonId);

                function updateRetornarButtonState() {
                    const checkedCount = document.querySelectorAll(checkboxSelector + ':checked').length;
                    if (!retornarBtn) return;

                    retornarBtn.disabled = (checkedCount === 0);
                    if (checkedCount > 0) {
                        retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas (${checkedCount})`;
                    } else {
                        retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas`;
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        const marcado = selectAll.checked;
                        checkboxes.forEach(chk => chk.checked = marcado);
                        updateRetornarButtonState();
                    });
                }

                checkboxes.forEach(chk => {
                    chk.addEventListener('change', function () {
                        const total = checkboxes.length;
                        const totalChecked = document.querySelectorAll(checkboxSelector + ':checked').length;
                        if (selectAll) {
                            selectAll.checked = (total === totalChecked);
                        }
                        updateRetornarButtonState();
                    });
                });

                updateRetornarButtonState();
            }

            setupTemporalesHandlers('selectAllTemporales', '.select-checkbox-temporales', 'retornarColmenasButton');

            // Handler para botón trasladar
            const trasladarBtn = document.getElementById('trasladarColmenasButton');
            if (trasladarBtn) {
                trasladarBtn.addEventListener('click', function () {
                    const seleccionados = Array.from(
                        document.querySelectorAll('.select-checkbox-trashumante:checked')
                    ).map(chk => chk.value);

                    if (seleccionados.length === 0) return;

                    const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
                    url.searchParams.set('tipo', 'traslado');
                    url.searchParams.set('apiarios', seleccionados.join(','));
                    window.location.href = url.toString();
                });
            }

            // Handler para retornar colmenas con modal de confirmación
            const retornarBtnTemp = document.getElementById('retornarColmenasButton');
            const returnConfirmationModal = document.getElementById('returnConfirmationModal');
            const returnSelectedList = document.getElementById('returnSelectedList');
            const confirmReturnButton = document.getElementById('confirmReturnButton');

            if (retornarBtnTemp && returnConfirmationModal) {
                retornarBtnTemp.addEventListener('click', function () {
                    const seleccionados = Array.from(
                        document.querySelectorAll('.select-checkbox-temporales:checked')
                    ).map(chk => chk.value);

                    if (seleccionados.length === 0) return;

                    // Limpiar lista previa
                    if (returnSelectedList) {
                        returnSelectedList.innerHTML = '';

                        seleccionados.forEach(id => {
                            const chk = document.querySelector(`.select-checkbox-temporales[value="${id}"]`);
                            if (!chk) return;

                            const fila = chk.closest('tr');
                            const apicultorNombre = fila.querySelector('td:nth-child(2)')?.textContent?.trim() || 'N/A';

                            const li = document.createElement('li');
                            li.className = 'mb-2';
                            li.innerHTML = `<i class="fas fa-warehouse"></i> ${apicultorNombre}`;
                            returnSelectedList.appendChild(li);
                        });
                    }

                    // Abrir modal con gestión segura
                    const bsModal = new bootstrap.Modal(returnConfirmationModal);
                    bsModal.show();
                });
            }

            if (confirmReturnButton) {
                confirmReturnButton.addEventListener('click', function () {
                    const seleccionados = Array.from(
                        document.querySelectorAll('.select-checkbox-temporales:checked')
                    ).map(chk => chk.value);

                    if (seleccionados.length === 0) {
                        const bsModal = bootstrap.Modal.getInstance(returnConfirmationModal);
                        if (bsModal) bsModal.hide();
                        return;
                    }

                    // Deshabilitar botón para evitar clics múltiples
                    confirmReturnButton.disabled = true;
                    confirmReturnButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("apiarios.archivarMultiples") }}';
                    form.style.display = 'none';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    seleccionados.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);

                    // Cerrar modal antes de enviar
                    const bsModal = bootstrap.Modal.getInstance(returnConfirmationModal);
                    if (bsModal) {
                        bsModal.hide();
                    }

                    setTimeout(() => {
                        cleanupModalBackdrops();
                        form.submit();
                    }, 300);
                });
            }

            // ============================================================
            // 4) MODAL CREAR APIARIO TEMPORAL
            // ============================================================
            const createTemporalButton = document.getElementById('createTemporalButton');
            const createTemporalModal = document.getElementById('createTemporalModal');
            const selectedApiariosList = document.getElementById('selectedApiariosList');
            const createTrasladoButton = document.getElementById('createTrasladoButton');
            const createRetornoButton = document.getElementById('createRetornoButton');

            if (createTemporalButton && createTemporalModal) {
                createTemporalButton.addEventListener('click', function () {
                    const selectedCheckboxes = document.querySelectorAll('.select-checkbox-trashumante:checked');
                    if (selectedCheckboxes.length === 0) return;

                    if (selectedApiariosList) {
                        selectedApiariosList.innerHTML = '';

                        selectedCheckboxes.forEach(chk => {
                            const row = chk.closest('tr');
                            const apiarioName = row.querySelector('.apiario-id')?.textContent || 'N/A';
                            const numColmenas = row.querySelector('.counter')?.textContent || '0';

                            const listItem = document.createElement('li');
                            listItem.className = 'mb-2';
                            listItem.innerHTML = `
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <span><i class="fas fa-warehouse"></i> ${apiarioName}</span>
                                                                            <span class="badge bg-primary">${numColmenas} colmenas</span>
                                                                        </div>
                                                                    `;
                            selectedApiariosList.appendChild(listItem);
                        });
                    }

                    const bsModal = new bootstrap.Modal(createTemporalModal);
                    bsModal.show();
                });
            }

            if (createTrasladoButton) {
                createTrasladoButton.addEventListener('click', function () {
                    const seleccionados = Array.from(
                        document.querySelectorAll('.select-checkbox-trashumante:checked')
                    ).map(chk => chk.value);

                    if (seleccionados.length === 0) return;

                    const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
                    url.searchParams.set('tipo', 'traslado');
                    url.searchParams.set('apiarios', seleccionados.join(','));
                    window.location.href = url.toString();
                });
            }

            if (createRetornoButton) {
                createRetornoButton.addEventListener('click', function () {
                    const seleccionados = Array.from(
                        document.querySelectorAll('.select-checkbox-trashumante:checked')
                    ).map(chk => chk.value);

                    if (seleccionados.length === 0) return;

                    const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
                    url.searchParams.set('tipo', 'retorno');
                    url.searchParams.set('apiarios', seleccionados.join(','));
                    window.location.href = url.toString();
                });
            }

            // ============================================================
            // 5) ELIMINACIÓN MÚLTIPLE Y MODALES DE CONFIRMACIÓN
            // ============================================================
            const confirmDeleteButton = document.getElementById('confirmDelete');
            const multiDeleteButton = document.getElementById('multiDeleteButton');

            if (multiDeleteButton) {
                multiDeleteButton.addEventListener('click', function () {
                    const deleteModal = document.getElementById('deleteModal');
                    if (deleteModal) {
                        const bsModal = new bootstrap.Modal(deleteModal);
                        bsModal.show();
                    }
                });
            }

            if (confirmDeleteButton) {
                confirmDeleteButton.addEventListener('click', function () {
                    const selectedIds = Array.from(
                        document.querySelectorAll('.select-checkbox:checked')
                    ).map(chk => chk.value);

                    if (selectedIds.length === 0) return;

                    // Deshabilitar botón para evitar clics múltiples
                    confirmDeleteButton.disabled = true;
                    confirmDeleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("apiarios.massDelete") }}';
                    form.style.display = 'none';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    selectedIds.forEach(id => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'ids[]';
                        inp.value = id;
                        form.appendChild(inp);
                    });

                    document.body.appendChild(form);

                    // Cerrar modal y enviar formulario
                    const deleteModal = document.getElementById('deleteModal');
                    if (deleteModal) {
                        const bsModal = bootstrap.Modal.getInstance(deleteModal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }

                    setTimeout(() => {
                        cleanupModalBackdrops();

                        fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(response => {
                                if (response.ok) {
                                    window.location.reload();
                                } else {
                                    console.error('Error al eliminar los apiarios');
                                    alert('Ha ocurrido un error al eliminar los apiarios');
                                    confirmDeleteButton.disabled = false;
                                    confirmDeleteButton.innerHTML = '<i class="fas fa-trash-alt"></i> Eliminar';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Ha ocurrido un error al procesar la solicitud');
                                confirmDeleteButton.disabled = false;
                                confirmDeleteButton.innerHTML = '<i class="fas fa-trash-alt"></i> Eliminar';
                            });
                    }, 300);

                    return false;
                });
            }

            // ============================================================
            // 6) MANEJO ESPECÍFICO DE MODALES DE ELIMINACIÓN INDIVIDUAL
            // ============================================================
            function setupIndividualDeleteModals() {
                document.querySelectorAll('[id^="deleteModal"]').forEach(modal => {
                    const form = modal.querySelector('form');
                    const submitButton = modal.querySelector('.modal-btn-danger');

                    if (submitButton && form) {
                        submitButton.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            // Deshabilitar el botón temporalmente
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

                            // Cerrar el modal primero
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }

                            // Limpiar backdrops y enviar formulario después de un delay
                            setTimeout(() => {
                                cleanupModalBackdrops();
                                form.submit();
                            }, 300);
                        });
                    }
                });

                // Manejar modales de temporales específicamente
                document.querySelectorAll('[id^="deleteModalTemporal"]').forEach(modal => {
                    const form = modal.querySelector('form');
                    const submitButton = modal.querySelector('.modal-btn-danger');

                    if (submitButton && form) {
                        submitButton.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            // Deshabilitar el botón temporalmente
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

                            // Cerrar el modal primero
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }

                            // Limpiar backdrops y enviar formulario después de un delay
                            setTimeout(() => {
                                cleanupModalBackdrops();
                                form.submit();
                            }, 300);
                        });
                    }
                });
            }

            // Configurar modales de eliminación individual
            setupIndividualDeleteModals();

            // ============================================================
            // 7) FILTRADO DE TABLA Y ANIMACIONES
            // ============================================================
            const searchInput = document.getElementById('searchInput');
            const filterTipo = document.getElementById('filterTipo');
            const tableRows = document.querySelectorAll('#apiariosTable tbody tr');

            function filterTable() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const tipoFilter = filterTipo ? filterTipo.value.toLowerCase() : '';

                tableRows.forEach(row => {
                    const apiarioText = row.textContent.toLowerCase();
                    const tipoCell = row.querySelector('td:nth-child(6)');
                    const tipoText = tipoCell ? tipoCell.textContent.toLowerCase() : '';

                    const matchesSearch = (searchTerm === '') || apiarioText.includes(searchTerm);
                    const matchesTipo = (tipoFilter === '') || tipoText.includes(tipoFilter);
                    row.style.display = (matchesSearch && matchesTipo) ? '' : 'none';
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }
            if (filterTipo) {
                filterTipo.addEventListener('change', filterTable);
            }

            // Animación de entrada para filas
            const rows = document.querySelectorAll('#apiariosTable tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                row.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s forwards`;
            });

            // ============================================================
            // 8) TOOLTIPS DINÁMICOS - SOLUCIÓN COMPLETA
            // ============================================================

            document.addEventListener('DOMContentLoaded', function() {
    // Configurar tooltips dinámicos
    function setupDynamicTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', function(e) {
                const rect = e.target.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Verificar si hay headers de tabla sticky arriba
                const tableHeaders = document.querySelectorAll('.apiarios-table thead');
                let headerBottom = 0;
                
                tableHeaders.forEach(header => {
                    const headerRect = header.getBoundingClientRect();
                    if (headerRect.bottom > headerBottom) {
                        headerBottom = headerRect.bottom;
                    }
                });
                
                // Calcular espacio disponible arriba y abajo
                const spaceAbove = rect.top - headerBottom;
                const spaceBelow = viewportHeight - rect.bottom;
                
                // Determinar la mejor posición
                let position = 'top'; // Por defecto arriba
                
                // Si no hay suficiente espacio arriba (menos de 60px), usar abajo
                if (spaceAbove < 60 && spaceBelow > 60) {
                    position = 'bottom';
                }
                
                // Si está muy cerca de los bordes laterales, usar left/right
                if (rect.left < 100 && rect.right < viewportWidth - 100) {
                    position = 'right';
                } else if (rect.right > viewportWidth - 100 && rect.left > 100) {
                    position = 'left';
                }
                
                // Aplicar la posición calculada
                e.target.setAttribute('data-tooltip-position', position);
                
                // Si el tooltip se queda debajo del header, forzar posición bottom
                if (position === 'top' && rect.top - 45 < headerBottom) {
                    e.target.setAttribute('data-tooltip-position', 'bottom');
                }
            });
            
            element.addEventListener('mouseleave', function(e) {
                // Opcional: limpiar el atributo cuando se quita el hover
                // e.target.removeAttribute('data-tooltip-position');
            });
        });
    }
    
    // Inicializar tooltips
    setupDynamicTooltips();
    
    // Reinicializar tooltips cuando cambie el tamaño de ventana
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            setupDynamicTooltips();
        }, 250);
    });
    
    // Reinicializar tooltips cuando se cambie de pestaña
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function() {
            setTimeout(() => {
                setupDynamicTooltips();
            }, 100);
        });
    });
    
    // Observer para detectar nuevos elementos con tooltips
    const tooltipObserver = new MutationObserver(function(mutations) {
        let hasNewTooltips = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.hasAttribute && node.hasAttribute('data-tooltip') || 
                            node.querySelectorAll && node.querySelectorAll('[data-tooltip]').length > 0) {
                            hasNewTooltips = true;
                        }
                    }
                });
            }
        });
        
        if (hasNewTooltips) {
            setupDynamicTooltips();
        }
    });
    
    tooltipObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
});

            // ============================================================
            // 9) LIMPIEZA GLOBAL Y EVENTOS ADICIONALES
            // ============================================================

            // Interceptar clics en backdrops para cerrar modales correctamente
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('modal-backdrop')) {
                    const activeModal = document.querySelector('.modal.show');
                    if (activeModal) {
                        const modalInstance = bootstrap.Modal.getInstance(activeModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                    setTimeout(cleanupModalBackdrops, 200);
                }
            });

            // Limpiar backdrops cuando se cambia de pestaña
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function () {
                    cleanupModalBackdrops();
                });
            });

            // Observer para detectar cambios en el DOM y limpiar backdrops órfanos
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(function (node) {
                            if (node.nodeType === 1 && node.classList && node.classList.contains('modal-backdrop')) {
                                const existingBackdrops = document.querySelectorAll('.modal-backdrop');
                                if (existingBackdrops.length > 1) {
                                    node.remove();
                                }
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: false
            });

            // Limpieza final al cargar
            setTimeout(cleanupModalBackdrops, 500);
        });
    </script>
@endsection