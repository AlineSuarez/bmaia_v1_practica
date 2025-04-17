@extends('layouts.app')

<head>
    <link href="{{ asset('./css/components/home-user/apiarios.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Mono&display=swap"
        rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

@section('content')
    <div class="apiarios-container animated-element hexagon-bg">
        <div class="page-header">
            <h1 class="page-title">Mis Apiarios</h1>
            <div class="filters-panel">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Buscar apiarios..." id="searchInput">
                </div>
                <select class="filter-select" id="filterTipo">
                    <option value="">Todos los tipos</option>
                    <option value="Producción">Producción</option>
                    <option value="Polinización">Polinización</option>
                    <option value="Crianza">Crianza</option>
                </select>
            </div>
        </div>

        <!-- Botones de acciones -->
        <div class="action-buttons">
            <a href="{{ route('apiarios.create') }}" class="action-button primary" data-tooltip="Crear nuevo apiario">
                <i class="fas fa-plus-circle"></i> Nuevo Apiario
            </a>
            <button id="multiDeleteButton" class="action-button danger" disabled data-tooltip="Eliminar seleccionados">
                <i class="fas fa-trash-alt"></i> Eliminar seleccionados
            </button>
        </div>

        <!-- Tabla de Apiarios -->
        <div class="apiarios-table-wrapper">
            <div class="table-responsive">
                <table id="apiariosTable" class="apiarios-table">
                    <thead>
                        <tr>
                            <th class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" id="selectAll">
                                    <span class="checkmark"></span>
                                </label>
                            </th>
                            <th class="text-center"><span class="column-title">ID Apiario</span></th>
                            <th class="text-center"><span class="column-title">Temporada de producción</span></th>
                            <th class="text-center"><span class="column-title">N° Registro SAG</span></th>
                            <th class="text-center"><span class="column-title">N° de colmenas</span></th>
                            <th class="text-center"><span class="column-title">Tipo de apiario</span></th>
                            <th class="text-center"><span class="column-title">Tipo de manejo</span></th>
                            <th class="text-center"><span class="column-title">Objetivo de producción</span></th>
                            <th class="text-center"><span class="column-title">Comuna</span></th>
                            <th class="text-center"><span class="column-title">Localización</span></th>
                            <th class="text-center"><span class="column-title">Fotografía</span></th>
                            <th class="text-center"><span class="column-title">Acciones</span></th>
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
                                    <span class="apiario-id">{{ $apiario->id }}-{{ $apiario->nombre }}</span>
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
                                <td class="text-center">{{ $apiario->nombre_comuna ? $apiario->comuna->nombre : 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="location-info">
                                        <span class="coordinates">{{ $apiario->latitud }}, {{ $apiario->longitud }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($apiario->foto)
                                        <div class="apiario-image">
                                            <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Fotografía del Apiario"
                                                data-toggle="modal" data-target="#imageModal{{ $apiario->id }}">
                                        </div>
                                    @else
                                        <span class="text-muted">Sin imagen</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="table-actions">
                                        <a href="{{ route('apiarios.editar', $apiario->id) }}" class="btn-table-action btn-edit"
                                            data-tooltip="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn-table-action btn-delete" type="button" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $apiario->id }}" data-tooltip="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <a href="{{ route('generate.document', $apiario->id) }}"
                                            class="btn-table-action btn-info" data-tooltip="Descargar reporte">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <nav>
            <ul class="pagination">
                <li class="page-item disabled">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Modal de Confirmación para eliminación múltiple -->
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

    <!-- Estado vacío (se muestra cuando no hay apiarios) -->
    @if(count($apiarios) == 0)
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
@endsection

@section('optional-scripts')
    <script src="{{ asset('vendor/chatbot/js/chatbot.js') }}"></script>
    <script src="/js/VoiceCommands.js"></script>
    <script src="/js/apiarios.js"></script>

    <!-- Script adicional para mejorar interacciones de UI -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Manejar selección de filas
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.select-checkbox');
            const multiDeleteButton = document.getElementById('multiDeleteButton');

            // Actualizar estado del botón de eliminación múltiple
            function updateMultiDeleteButton() {
                const checkedBoxes = document.querySelectorAll('.select-checkbox:checked');
                multiDeleteButton.disabled = checkedBoxes.length === 0;

                if (checkedBoxes.length > 0) {
                    multiDeleteButton.innerHTML = `<i class="fas fa-trash-alt"></i> Eliminar (${checkedBoxes.length})`;
                } else {
                    multiDeleteButton.innerHTML = `<i class="fas fa-trash-alt"></i> Eliminar seleccionados`;
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
                    const allChecked = document.querySelectorAll('.select-checkbox').length ===
                        document.querySelectorAll('.select-checkbox:checked').length;
                    if (selectAll) {
                        selectAll.checked = allChecked;
                    }
                    updateMultiDeleteButton();
                });
            });

            // Filtrado de tabla
            const searchInput = document.getElementById('searchInput');
            const filterTipo = document.getElementById('filterTipo');
            const tableRows = document.querySelectorAll('#apiariosTable tbody tr');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const tipoFilter = filterTipo.value.toLowerCase();

                tableRows.forEach(row => {
                    const apiarioText = row.textContent.toLowerCase();
                    const tipoCell = row.querySelector('td:nth-child(6)').textContent.toLowerCase();

                    const matchesSearch = searchTerm === '' || apiarioText.includes(searchTerm);
                    const matchesTipo = tipoFilter === '' || tipoCell.includes(tipoFilter);

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
        });
    </script>
@endsection