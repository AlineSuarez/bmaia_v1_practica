@extends('layouts.app')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/apiarios.css') }}" rel="stylesheet">
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Mono&display=swap"
            rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>


    <!-- Botones de acciones -->
                <div class="action-buttons">
                    <a href="{{ route('apiarios.create') }}" class="action-button primary"
                        data-tooltip="Crear nuevo apiario">
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
                <button class="nav-link" id="archivados-tab" data-bs-toggle="tab" data-bs-target="#archivados"
                    type="button" role="tab" aria-controls="archivados" aria-selected="false">
                    <i class="fas fa-folder"></i> Apiarios Archivados
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="apiariosTabContent">
            <!-- Pestaña Apiarios Fijos -->
            <div class="tab-pane fade show active" id="fijos" role="tabpanel" aria-labelledby="fijos-tab">
                

                <!-- Tabla de Apiarios Fijos -->
                <div class="apiarios-table-wrapper">
                    <div class="table-responsive">
                        @if(count($apiariosFijos) > 0)
                            <table id="apiariosTable" class="apiarios-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <label class="custom-checkbox">
                                                <input type="checkbox" id="selectAll">
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
                                    @foreach ($apiariosFijos as $apiario)
                                        <tr>
                                            <td class="text-center">
                                                <label class="custom-checkbox">
                                                    <input type="checkbox" class="select-checkbox" value="{{ $apiario->id }}">
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
                                                    <span class="coordinates">{{ $apiario->latitud }},
                                                        {{ $apiario->longitud }}</span>
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
                                                    <a href="{{ route('apiarios.editar', $apiario->id) }}"
                                                        class="btn-table-action btn-edit" data-tooltip="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn-table-action btn-delete" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $apiario->id }}" data-tooltip="Eliminar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    <a href="{{ route('generate.document', $apiario->id) }}"
                                                        class="btn-table-action btn-info" data-tooltip="Descargar detalle PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('colmenas.index', $apiario->id) }}"
                                                            class="btn-table-action btn-info" data-tooltip="Ver Colmenas">
                                                            <i class="fas fa-cubes"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <!-- Estado vacío -->
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

            <!-- Pestaña Apiarios Trashumantes -->

            <div class="tab-pane fade" id="trashumantes" role="tabpanel" aria-labelledby="trashumantes-tab">
                
                <!-- Botones de acciones para trashumantes -->
                <div class="action-buttons">
                    <button id="trasladarColmenasButton" class="action-button warning" disabled
                            data-tooltip="Trasladar colmenas seleccionadas">
                        <i class="fas fa-arrow-right"></i> Trasladar Colmenas
                    </button>

                    <button id="retornarColmenasButton" class="action-button success" disabled
                            data-tooltip="Retornar colmenas a su apiario original">
                        <i class="fas fa-arrow-left"></i> Retornar Colmenas
                    </button>
                </div>

                <div class="action-button mb-2">
                    
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
                                                        <input type="checkbox" class="select-checkbox-trashumante"
                                                            value="{{ $apiario->id }}">
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
                                                        <span class="coordinates">{{ $apiario->latitud }},
                                                            {{ $apiario->longitud }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($apiario->foto)
                                                        <div class="apiario-image">
                                                            <img src="{{ asset('storage/' . $apiario->foto) }}"
                                                                alt="Fotografía del Apiario" data-bs-toggle="modal"
                                                                data-bs-target="#imageModal{{ $apiario->id }}">
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Sin imagen</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="table-actions">
                                                        <a href="{{ route('apiarios.editar', $apiario->id) }}"
                                                            class="btn-table-action btn-edit" data-tooltip="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="btn-table-action btn-delete" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $apiario->id }}"
                                                            data-tooltip="Eliminar">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        <a href="{{ route('generate.document', $apiario->id) }}"
                                                            class="btn-table-action btn-info" data-tooltip="Descargar detalle PDF">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <a href="{{ route('colmenas.index', $apiario->id) }}"
                                                            class="btn-table-action btn-info" data-tooltip="Ver Colmenas">
                                                            <i class="fas fa-cubes"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                                        <tr>
                                            <td class="text-center">
                                                <label class="custom-checkbox">
                                                    <input type="checkbox"
                                                        class="select-checkbox-temporales"
                                                        value="{{ $apiario->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">{{ $apiario->nombre }}</td>
                                            <td class="text-center">{{ $apiario->num_colmenas }}</td>
                                            <td class="text-center">
                                                {{ optional(optional($apiario->comuna)->region)->nombre ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                {{ optional($apiario->comuna)->nombre ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $apiario->created_at->format('Y-m-d') }}
                                            </td>
                                            
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                            No hay apiarios temporales
                                            </td>
                                        </tr>
                                        @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña Apiarios Archivados -->
            <div class="tab-pane fade" id="archivados" role="tabpanel" aria-labelledby="archivados-tab">
                <div class="apiarios-table-wrapper">
                    <div class="table-responsive">
                        @if(isset($apiariosArchivados) && $apiariosArchivados->count() > 0)
                            <table id="apiariosArchivadosTable" class="apiarios-table table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><span class="column-title">Apiario</span></th>
                                        <th class="text-center"><span class="column-title">Temporada</span></th>
                                        <th class="text-center"><span class="column-title">Región</span></th>
                                        <th class="text-center"><span class="column-title">Comuna</span></th>
                                        <th class="text-center"><span class="column-title">Fecha Archivado</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($apiariosArchivados as $apiario)
                                        <tr>
                                            <td class="text-center">{{ $apiario->nombre }}</td>
                                            <td class="text-center">{{ $apiario->temporada_produccion }}</td>
                                            <td class="text-center">{{ optional(optional($apiario->comuna)->region)->nombre ?? 'N/A' }}</td>
                                            <td class="text-center">{{ optional($apiario->comuna)->nombre ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $apiario->updated_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state text-center py-4">
                                <i class="fas fa-folder-open fa-2x mb-2"></i>
                                <p class="text-muted">No hay apiarios archivados.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear Apiario Temporal -->
    <div class="modal fade custom-modal" id="createTemporalModal" tabindex="-1" aria-labelledby="createTemporalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTemporalModalLabel">
                        <i class="fas fa-route"></i> Crear Apiario Temporal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
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

    <!-- Modales (mantienen la misma estructura) -->
    <div class="modal fade custom-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar los apiarios seleccionados? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="modal-btn modal-btn-danger" id="confirmDelete">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmacion para un apiario individual -->
    @foreach ($apiariosFijos as $apiario)
        <div class="modal fade custom-modal" id="deleteModal{{ $apiario->id }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $apiario->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $apiario->id }}">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar el apiario
                            <strong>{{ $apiario->id }}-{{ $apiario->nombre }}</strong>? Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn modal-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('apiarios.destroy', $apiario->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="modal-btn modal-btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para ver imagen ampliada -->
        @if($apiario->foto)
            <div class="modal fade custom-modal" id="imageModal{{ $apiario->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Fotografía del Apiario {{ $apiario->id }}-{{ $apiario->nombre }}</h5>
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
@endsection
@section('optional-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ==================================================
    // 1) APIARIOS FIJOS (eliminar múltiple)
    // ==================================================
    function setupFijosHandlers(selectAllId, checkboxSelector, buttonId) {
        const selectAll = document.getElementById(selectAllId);
        const checkboxes = document.querySelectorAll(checkboxSelector);
        const button = document.getElementById(buttonId);

        function updateButtonState() {
            const checkedCount = document.querySelectorAll(checkboxSelector + ':checked').length;
            if (!button) return;

            button.disabled = (checkedCount === 0);

            // Si existe multiDeleteButton y hay seleccionados, mostramos la cuenta
            if (checkedCount > 0) {
                button.innerHTML = `<i class="fas fa-trash-alt"></i> Eliminar (${checkedCount})`;
            } else {
                button.innerHTML = `<i class="fas fa-trash-alt"></i> Eliminar seleccionados`;
            }
        }

        // 1.1) “Select all” para apiarios fijos
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(chk => chk.checked = selectAll.checked);
                updateButtonState();
            });
        }

        // 1.2) Chequeo individual en cada fila
        checkboxes.forEach(chk => {
            chk.addEventListener('change', function () {
                // Si todos los checkboxes están chequeados, marcar “selectAll”
                const total = checkboxes.length;
                const totalChecked = document.querySelectorAll(checkboxSelector + ':checked').length;
                if (selectAll) {
                    selectAll.checked = (total === totalChecked);
                }
                updateButtonState();
            });
        });

        // 1.3) Estado inicial (por si ya hay “old()” marcado)
        updateButtonState();
    }

    // Invocamos para “Apiarios Fijos” 
    setupFijosHandlers('selectAll', '.select-checkbox', 'multiDeleteButton');

    // ==================================================
    // 2) APIARIOS TRASHUMANTES (BASE) – Trasladar/Retornar desde apiarios base
    // ==================================================
    function setupTrashumanteBaseHandlers(selectAllId, checkboxSelector, trasladarBtnId, retornarBtnId) {
        const selectAll = document.getElementById(selectAllId);
        const checkboxes = document.querySelectorAll(checkboxSelector);
        const trasladarBtn = document.getElementById(trasladarBtnId);
        const retornarBtn = document.getElementById(retornarBtnId);

        function updateTrashumanteButtons() {
            const selectedCount = document.querySelectorAll(checkboxSelector + ':checked').length;

            // 2.1) Trasladar
            if (trasladarBtn) {
                trasladarBtn.disabled = (selectedCount === 0);
                if (selectedCount > 0) {
                    trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas (${selectedCount})`;
                } else {
                    trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas`;
                }
            }

            // 2.2) Retornar
            if (retornarBtn) {
                retornarBtn.disabled = (selectedCount === 0);
                if (selectedCount > 0) {
                    retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas (${selectedCount})`;
                } else {
                    retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas`;
                }
            }
        }

        // 2.3) “Select All” para apiarios base (trashumantes)
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const marcado = selectAll.checked;
                checkboxes.forEach(chk => chk.checked = marcado);
                updateTrashumanteButtons();
            });
        }

        // 2.4) Chequeo individual en cada una de las filas de apiarios base
        checkboxes.forEach(chk => {
            chk.addEventListener('change', function () {
                const total = checkboxes.length;
                const totalChecked = document.querySelectorAll(checkboxSelector + ':checked').length;
                if (selectAll) {
                    selectAll.checked = (total === totalChecked);
                }
                updateTrashumanteButtons();
            });
        });

        // 2.5) Estado inicial para botones
        updateTrashumanteButtons();
    }

    // Invocamos para “Apiarios Base” (trashumantes) – habilita “Trasladar” y “Retornar”
    setupTrashumanteBaseHandlers(
        'selectAllTrashumante',
        '.select-checkbox-trashumante',
        'trasladarColmenasButton',
        'retornarColmenasButton'
    );

    // 2.6) Si clickean en “Trasladar Colmenas” → redirige al wizard con tipo=traslado
    const trasladarBtn = document.getElementById('trasladarColmenasButton');
    if (trasladarBtn) {
        trasladarBtn.addEventListener('click', function () {
            const seleccionados = Array.from(document.querySelectorAll('.select-checkbox-trashumante:checked'))
                                      .map(chk => chk.value);
            if (seleccionados.length === 0) return;

            const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
            url.searchParams.set('tipo', 'traslado');
            url.searchParams.set('apiarios', seleccionados.join(','));
            window.location.href = url.toString();
        });
    }

    // 2.7) Si clickean en “Retornar Colmenas” → redirige al wizard con tipo=retorno
    const retornarBtnBase = document.getElementById('retornarColmenasButton');
    if (retornarBtnBase) {
        retornarBtnBase.addEventListener('click', function () {
            const seleccionados = Array.from(document.querySelectorAll('.select-checkbox-trashumante:checked'))
                                      .map(chk => chk.value);
            if (seleccionados.length === 0) return;

            const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
            url.searchParams.set('tipo', 'retorno');
            url.searchParams.set('apiarios', seleccionados.join(','));
            window.location.href = url.toString();
        });
    }

    // ==================================================
    // 3) APIARIOS TEMPORALES – “Retornar Colmenas” desde la lista de temporales
    // ==================================================
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

        // 3.1) “Select All” para apiarios temporales
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const marcado = selectAll.checked;
                checkboxes.forEach(chk => chk.checked = marcado);
                updateRetornarButtonState();
            });
        }

        // 3.2) Chequeo individual en cada fila de temporales
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

        // 3.3) Estado inicial
        updateRetornarButtonState();
    }

    // Invocamos para “Apiarios Temporales”
    setupTemporalesHandlers('selectAllTemporales', '.select-checkbox-temporales', 'retornarColmenasButton');

    // 3.4) Redirección al hacer clic en “Retornar Colmenas” dentro de temporales
    const retornarBtnTemp = document.getElementById('retornarColmenasButton');
    if (retornarBtnTemp) {
        retornarBtnTemp.addEventListener('click', function () {
            const seleccionados = Array.from(document.querySelectorAll('.select-checkbox-temporales:checked'))
                                      .map(chk => chk.value);
            if (seleccionados.length === 0) return;

            const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
            url.searchParams.set('tipo', 'retorno');
            url.searchParams.set('apiarios', seleccionados.join(','));
            window.location.href = url.toString();
        });
    }

    // ==================================================
    // 4) CREAR / RETORNAR APIARIO TEMPORAL (MODAL)
    // ==================================================
    const createTemporalButton = document.getElementById('createTemporalButton');
    const createTemporalModal = document.getElementById('createTemporalModal');
    const selectedApiariosList = document.getElementById('selectedApiariosList');
    const createTrasladoButton = document.getElementById('createTrasladoButton');
    const createRetornoButton  = document.getElementById('createRetornoButton');

    // 4.1) Al hacer clic en “Crear Apiario Temporal” → mostrar modal con lista
    if (createTemporalButton) {
        createTemporalButton.addEventListener('click', function () {
            const selectedCheckboxes = document.querySelectorAll('.select-checkbox-trashumante:checked');
            if (selectedCheckboxes.length === 0) return;

            // Limpiar lista anterior
            selectedApiariosList.innerHTML = '';

            // Recorrer los seleccionados y mostrar nombre + colmenas en el modal
            selectedCheckboxes.forEach(chk => {
                const row = chk.closest('tr');
                const apiarioName = row.querySelector('.apiario-id').textContent;
                const numColmenas = row.querySelector('.counter').textContent;

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

            // Mostrar modal
            new bootstrap.Modal(createTemporalModal).show();
        });
    }

    // 4.2) Botón “Traslado” dentro del modal
    if (createTrasladoButton) {
        createTrasladoButton.addEventListener('click', function () {
            const seleccionados = Array.from(document.querySelectorAll('.select-checkbox-trashumante:checked'))
                                      .map(chk => chk.value);
            if (seleccionados.length === 0) return;

            const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
            url.searchParams.set('tipo', 'traslado');
            url.searchParams.set('apiarios', seleccionados.join(','));
            window.location.href = url.toString();
        });
    }

    // 4.3) Botón “Retorno” dentro del modal
    if (createRetornoButton) {
        createRetornoButton.addEventListener('click', function () {
            const seleccionados = Array.from(document.querySelectorAll('.select-checkbox-trashumante:checked'))
                                      .map(chk => chk.value);
            if (seleccionados.length === 0) return;

            const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
            url.searchParams.set('tipo', 'retorno');
            url.searchParams.set('apiarios', seleccionados.join(','));
            window.location.href = url.toString();
        });
    }

    // ==================================================
    // 5) FILTRADO DE TABLA, ANIMACIONES, ELIMINAR FIJOS, TOOLTIP, ETC.
    //    (este bloque permanece exactamente igual que antes)
    // ==================================================

    // 5.1) Filtrado de tabla de “Apiarios Fijos”
    const searchInput = document.getElementById('searchInput');
    const filterTipo  = document.getElementById('filterTipo');
    const tableRows   = document.querySelectorAll('#apiariosTable tbody tr');

    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const tipoFilter = filterTipo ? filterTipo.value.toLowerCase() : '';

        tableRows.forEach(row => {
            const apiarioText = row.textContent.toLowerCase();
            const tipoCell    = row.querySelector('td:nth-child(6)');
            const tipoText    = tipoCell ? tipoCell.textContent.toLowerCase() : '';

            const matchesSearch = (searchTerm === '') || apiarioText.includes(searchTerm);
            const matchesTipo   = (tipoFilter === '') || tipoText.includes(tipoFilter);
            row.style.display = (matchesSearch && matchesTipo) ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    if (filterTipo) {
        filterTipo.addEventListener('change', filterTable);
    }

    // 5.2) Animación de entrada para filas de “Apiarios Fijos”
    const rows = document.querySelectorAll('#apiariosTable tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        row.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s forwards`;
    });

    // 5.3) Eliminación múltiple de apiarios fijos (ajax)
    const confirmDeleteButton = document.getElementById('confirmDelete');
    const multiDeleteButton   = document.getElementById('multiDeleteButton');

    if (multiDeleteButton) {
        multiDeleteButton.addEventListener('click', function () {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    }

    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function () {
            const selectedIds = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                                     .map(chk => chk.value);
            if (selectedIds.length === 0) return;

            // Construir formulario oculto
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("apiarios.massDelete") }}';
            form.style.display = 'none';

            // CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // IDs seleccionados
            selectedIds.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                form.appendChild(inp);
            });

            document.body.appendChild(form);

            // Envío vía fetch (ajax)
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                if (response.ok) {
                    // Cerrar modal y recargar
                    const deleteModal = document.getElementById('deleteModal');
                    if (deleteModal) {
                        const bsModal = bootstrap.Modal.getInstance(deleteModal);
                        if (bsModal) bsModal.hide();
                    }
                    window.location.reload();
                } else {
                    console.error('Error al eliminar los apiarios');
                    alert('Ha ocurrido un error al eliminar los apiarios');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ha ocurrido un error al procesar la solicitud');
            });

            return false; // evitar envío normal
        });
    }

    // 5.4) Tooltip para imágenes y demás
    const apiarioImages = document.querySelectorAll('.apiario-image img');
    apiarioImages.forEach(img => {
        img.addEventListener('click', function () {
            const modalId = this.getAttribute('data-bs-target');
            const imageModal = new bootstrap.Modal(document.querySelector(modalId));
            imageModal.show();

            const modalImg = document.querySelector(`${modalId} .modal-body img`);
            if (modalImg) {
                modalImg.style.opacity = '0';
                setTimeout(() => {
                    modalImg.style.transition = 'opacity 0.3s ease';
                    modalImg.style.opacity = '1';
                }, 100);
            }
        });

        img.classList.add('clickable-image');
        img.title = "Clic para ampliar";
        img.style.cursor = "pointer";
    });

    // 5.5) Custom Tooltips (hover)
    (function setupTooltips() {
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        tooltip.style.display = 'none';
        document.body.appendChild(tooltip);

        function showTooltip(e) {
            const text = this.getAttribute('data-tooltip');
            if (!text) return;

            tooltip.textContent = text;
            tooltip.style.display = 'block';

            const rect = this.getBoundingClientRect();
            tooltip.style.top  = (rect.top - tooltip.offsetHeight - 10) + 'px';
            tooltip.style.left = (rect.left + rect.width/2 - tooltip.offsetWidth/2) + 'px';

            const tooltipRect = tooltip.getBoundingClientRect();
            if (tooltipRect.left < 10) {
                tooltip.style.left = '10px';
            } else if (tooltipRect.right > window.innerWidth - 10) {
                tooltip.style.left = (window.innerWidth - tooltipRect.width - 10) + 'px';
            }
            if (tooltipRect.top < 10) {
                tooltip.style.top = (rect.bottom + 10) + 'px';
            }
        }

        function hideTooltip() {
            tooltip.style.display = 'none';
        }

        document.querySelectorAll('[data-tooltip]').forEach(el => {
            el.addEventListener('mouseenter', showTooltip);
            el.addEventListener('mouseleave', hideTooltip);
        });

        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1) {
                            if (node.hasAttribute('data-tooltip')) {
                                node.addEventListener('mouseenter', showTooltip);
                                node.addEventListener('mouseleave', hideTooltip);
                            }
                            node.querySelectorAll('[data-tooltip]').forEach(el => {
                                el.addEventListener('mouseenter', showTooltip);
                                el.addEventListener('mouseleave', hideTooltip);
                            });
                        }
                    });
                }
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    })();

}); // Fin DOMContentLoaded
</script>
@endsection
