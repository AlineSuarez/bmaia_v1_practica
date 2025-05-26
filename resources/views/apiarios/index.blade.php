@extends('layouts.app')

<head>
    <link href="{{ asset('./css/components/home-user/apiarios.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Mono&display=swap"
        rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

@section('content')

<!-- TEST idioma: <p>Idioma activo: {{ app()->getLocale() }}</p>  -->    


    <div class="apiarios-container animated-element hexagon-bg">
        <div class="page-header">
            <h1 class="page-title"></h1>

            <div class="filters-panel">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Buscar apiarios..." id="searchInput">
                </div>
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
                                                    data-bs-toggle="modal" data-bs-target="#imageModal{{ $apiario->id }}">
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
                    <!-- Estado vacío (ahora dentro de la tabla) -->
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
@endsection

@section('optional-scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.select-checkbox');
            const multiDeleteButton = document.getElementById('multiDeleteButton');

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

            // Ejecutar una vez para establecer el estado inicial
            updateMultiDeleteButton();

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

            // Lógica para eliminación múltiple
            const confirmDeleteButton = document.getElementById('confirmDelete');

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
                const modalId = this.getAttribute('data-target');

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