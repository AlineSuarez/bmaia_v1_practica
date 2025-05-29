@extends('layouts.app')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/apiarios.css') }}" rel="stylesheet">
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Mono&display=swap"
            rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <div class="apiarios-container animated-element hexagon-bg">
        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="apiariosTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="fijos-tab" data-bs-toggle="tab" data-bs-target="#fijos" type="button"
                    role="tab" aria-controls="fijos" aria-selected="true">
                    <i class="fas fa-warehouse"></i> Apiarios Fijos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="trashumantes-tab" data-bs-toggle="tab" data-bs-target="#trashumantes"
                    type="button" role="tab" aria-controls="trashumantes" aria-selected="false">
                    <i class="fas fa-truck"></i> Apiarios Trashumantes
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="apiariosTabContent">
            <!-- Pestaña Apiarios Fijos -->
            <div class="tab-pane fade show active" id="fijos" role="tabpanel" aria-labelledby="fijos-tab">
                <!-- Botones de acciones -->
                <div class="action-buttons">
                    <a href="{{ route('apiarios.create') }}" class="action-button primary"
                        data-tooltip="Crear nuevo apiario">
                        <i class="fas fa-plus-circle"></i> Nuevo Apiario
                    </a>
                    <button id="multiDeleteButton" class="action-button danger" disabled
                        data-tooltip="Eliminar seleccionados">
                        <i class="fas fa-trash-alt"></i> Eliminar seleccionados
                    </button>
                </div>

                <!-- Tabla de Apiarios Fijos -->
                <div class="apiarios-table-wrapper">
                    <div class="table-responsive">
                        @if(count($apiarios) > 0)
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
                                    @foreach ($apiarios as $apiario)
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
                                            <td class="text-center">{{ $apiario->nombre_comuna ? $apiario->comuna->nombre : 'N/A' }}
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
                    <button id="createTemporalButton" class="action-button success" disabled
                        data-tooltip="Crear apiario temporal con seleccionados">
                        <i class="fas fa-route"></i> Crear Apiario Temporal
                    </button>
                </div>

                <!-- Tabla de Apiarios Trashumantes -->
                <div class="table-section">
                    <h3 class="table-title">
                        <i class="fas fa-truck"></i> Apiarios Trashumantes
                    </h3>
                    <div class="apiarios-table-wrapper">
                        <div class="table-responsive">
                            @if(count($apiarios) > 0)
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
                                        @foreach ($apiarios as $apiario)
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
                                                    {{ $apiario->nombre_comuna ? $apiario->comuna->nombre : 'N/A' }}
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
                                    {{-- Aquí irían los datos de los apiarios temporales cuando los tengas --}}
                                    {{-- Por ahora muestro un ejemplo de fila vacía --}}
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class="fas fa-calendar-times"></i>
                                                </div>
                                                <h3 class="empty-state-text">No hay movimientos temporales registrados</h3>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
    @foreach ($apiarios as $apiario)
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
            // Funciones para manejar checkboxes de apiarios fijos
            setupCheckboxHandlers('selectAll', '.select-checkbox', 'multiDeleteButton');

            // Funciones para manejar checkboxes de apiarios trashumantes - MODIFICADO
            setupTemporalCheckboxHandlers('selectAllTrashumante', '.select-checkbox-trashumante', 'createTemporalButton');

            function setupCheckboxHandlers(selectAllId, checkboxSelector, buttonId) {
                const selectAll = document.getElementById(selectAllId);
                const checkboxes = document.querySelectorAll(checkboxSelector);
                const multiDeleteButton = document.getElementById(buttonId);

                function updateMultiDeleteButton() {
                    const checkedBoxes = document.querySelectorAll(checkboxSelector + ':checked');
                    if (multiDeleteButton) {
                        multiDeleteButton.disabled = checkedBoxes.length === 0;

                        if (checkedBoxes.length > 0) {
                            multiDeleteButton.innerHTML = `<i class="fas fa-trash-alt"></i> Eliminar (${checkedBoxes.length})`;
                        } else {
                            multiDeleteButton.innerHTML = `<i class="fas fa-trash-alt"></i> Eliminar seleccionados`;
                        }
                    }
                }

                // Manejar "Seleccionar todos"
                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = selectAll.checked;
                        });
                        updateMultiDeleteButton();
                    });
                }

                // Manejar selecciones individuales
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const allChecked = document.querySelectorAll(checkboxSelector).length ===
                            document.querySelectorAll(checkboxSelector + ':checked').length;
                        if (selectAll) {
                            selectAll.checked = allChecked;
                        }
                        updateMultiDeleteButton();
                    });
                });

                // Ejecutar una vez para establecer el estado inicial
                updateMultiDeleteButton();
            }

            // NUEVA FUNCIÓN para manejar checkboxes de apiarios trashumantes
            function setupTemporalCheckboxHandlers(selectAllId, checkboxSelector, buttonId) {
                const selectAll = document.getElementById(selectAllId);
                const checkboxes = document.querySelectorAll(checkboxSelector);
                const createTemporalButton = document.getElementById(buttonId);

                function updateCreateTemporalButton() {
                    const checkedBoxes = document.querySelectorAll(checkboxSelector + ':checked');
                    if (createTemporalButton) {
                        createTemporalButton.disabled = checkedBoxes.length === 0;

                        if (checkedBoxes.length > 0) {
                            createTemporalButton.innerHTML = `<i class="fas fa-route"></i> Crear Apiario Temporal (${checkedBoxes.length})`;
                        } else {
                            createTemporalButton.innerHTML = `<i class="fas fa-route"></i> Crear Apiario Temporal`;
                        }
                    }
                }

                // Manejar "Seleccionar todos"
                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = selectAll.checked;
                        });
                        updateCreateTemporalButton();
                    });
                }

                // Manejar selecciones individuales
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const allChecked = document.querySelectorAll(checkboxSelector).length ===
                            document.querySelectorAll(checkboxSelector + ':checked').length;
                        if (selectAll) {
                            selectAll.checked = allChecked;
                        }
                        updateCreateTemporalButton();
                    });
                });

                // Ejecutar una vez para establecer el estado inicial
                updateCreateTemporalButton();
            }

            // Lógica para crear apiario temporal
            const createTemporalButton = document.getElementById('createTemporalButton');
            const createTemporalModal = document.getElementById('createTemporalModal');
            const selectedApiariosList = document.getElementById('selectedApiariosList');
            const createTrasladoButton = document.getElementById('createTrasladoButton');
            const createRetornoButton = document.getElementById('createRetornoButton');

            // Abrir modal al hacer clic en el botón de crear temporal
            if (createTemporalButton) {
                createTemporalButton.addEventListener('click', function () {
                    const selectedCheckboxes = document.querySelectorAll('.select-checkbox-trashumante:checked');

                    if (selectedCheckboxes.length > 0) {
                        // Limpiar lista anterior
                        selectedApiariosList.innerHTML = '';

                        // Agregar apiarios seleccionados a la lista
                        selectedCheckboxes.forEach(checkbox => {
                            const row = checkbox.closest('tr');
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
                        const modal = new bootstrap.Modal(createTemporalModal);
                        modal.show();
                    }
                });
            }

            // Manejar botón de Traslado
            if (createTrasladoButton) {
                createTrasladoButton.addEventListener('click', function () {
                    const selectedApiarios = Array.from(document.querySelectorAll('.select-checkbox-trashumante:checked'))
                        .map(checkbox => checkbox.value);

                    if (selectedApiarios.length > 0) {
                        // Crear URL con parámetros para navegar en la misma página
                        const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
                        url.searchParams.set('tipo', 'traslado');
                        url.searchParams.set('apiarios', selectedApiarios.join(','));

                        // Navegar en la misma página
                        window.location.href = url.toString();
                    }
                });
            }

            // Manejar botón de Retorno
            if (createRetornoButton) {
                createRetornoButton.addEventListener('click', function () {
                    const selectedApiarios = Array.from(document.querySelectorAll('.select-checkbox-trashumante:checked'))
                        .map(checkbox => checkbox.value);

                    if (selectedApiarios.length > 0) {
                        // Crear URL con parámetros para navegar en la misma página
                        const url = new URL('{{ route("apiarios.createTemporal") }}', window.location.origin);
                        url.searchParams.set('tipo', 'retorno');
                        url.searchParams.set('apiarios', selectedApiarios.join(','));

                        // Navegar en la misma página
                        window.location.href = url.toString();
                    }
                });
            }

            // Filtrado de tabla
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

                    const matchesSearch = searchTerm === '' || apiarioText.includes(searchTerm);
                    const matchesTipo = tipoFilter === '' || tipoText.includes(tipoFilter);

                    row.style.display = (matchesSearch && matchesTipo) ? '' : 'none';
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }

            if (filterTipo) {
                filterTipo.addEventListener('change', filterTable);
            }

            // Animación de entrada para elementos de la tabla
            const rows = document.querySelectorAll('#apiariosTable tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                row.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s forwards`;
            });

            // Lógica para eliminación múltiple (solo para apiarios fijos)
            const confirmDeleteButton = document.getElementById('confirmDelete');
            const multiDeleteButton = document.getElementById('multiDeleteButton');

            // Abrir modal al hacer clic en el botón de eliminación múltiple
            if (multiDeleteButton) {
                multiDeleteButton.addEventListener('click', function () {
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    deleteModal.show();
                });
            }

            // Procesar la eliminación múltiple cuando se confirma
            if (confirmDeleteButton) {
                confirmDeleteButton.addEventListener('click', function () {
                    const selectedApiarios = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                        .map(checkbox => checkbox.value);

                    if (selectedApiarios.length > 0) {
                        // Crear un formulario oculto para enviar la solicitud
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("apiarios.massDelete") }}';
                        form.style.display = 'none';

                        // Agregar token CSRF
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        // Agregar IDs seleccionados
                        selectedApiarios.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });

                        // Agregar el formulario al documento
                        document.body.appendChild(form);

                        // Enviar el formulario mediante AJAX para evitar redirección del servidor
                        fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => {
                                if (response.ok) {
                                    // Cerrar el modal
                                    const deleteModal = document.getElementById('deleteModal');
                                    if (deleteModal) {
                                        const bsModal = bootstrap.Modal.getInstance(deleteModal);
                                        if (bsModal) bsModal.hide();
                                    }

                                    // Recargar la página actual
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

                        // Prevenir que el formulario se envíe normalmente
                        return false;
                    }
                });
            }
        });

        // Script para manejar la expansión de imágenes
        const apiarioImages = document.querySelectorAll('.apiario-image img');
        apiarioImages.forEach(img => {
            img.addEventListener('click', function () {
                // Obtener el ID del modal desde el atributo data-target
                const modalId = this.getAttribute('data-bs-target');

                // Crear una instancia del modal de Bootstrap 5 y mostrarlo
                const imageModal = new bootstrap.Modal(document.querySelector(modalId));
                imageModal.show();

                // Mejorar la experiencia visual
                const modalImg = document.querySelector(`${modalId} .modal-body img`);
                if (modalImg) {
                    // Añadir una transición suave para la carga de la imagen
                    modalImg.style.opacity = '0';
                    setTimeout(() => {
                        modalImg.style.transition = 'opacity 0.3s ease';
                        modalImg.style.opacity = '1';
                    }, 100);
                }
            });

            // Añadir clase para indicar que es clicable y mejorar la experiencia
            img.classList.add('clickable-image');
            img.title = "Clic para ampliar";
            img.style.cursor = "pointer";
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Crear un elemento para el tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.style.display = 'none';
            document.body.appendChild(tooltip);

            // Función para mostrar el tooltip
            function showTooltip(e) {
                const text = this.getAttribute('data-tooltip');
                if (!text) return;

                tooltip.textContent = text;
                tooltip.style.display = 'block';

                // Posicionar el tooltip encima del elemento
                const rect = this.getBoundingClientRect();
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
                tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';

                // Asegurar que el tooltip no se salga de la pantalla
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

            // Función para ocultar el tooltip
            function hideTooltip() {
                tooltip.style.display = 'none';
            }

            // Agregar eventos a todos los elementos con data-tooltip
            document.querySelectorAll('[data-tooltip]').forEach(el => {
                el.addEventListener('mouseenter', showTooltip);
                el.addEventListener('mouseleave', hideTooltip);
            });

            // Actualizar los tooltips cuando se agregan nuevos elementos
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
        });
    </script>
@endsection