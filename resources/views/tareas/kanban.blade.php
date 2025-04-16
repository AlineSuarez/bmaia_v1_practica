<div class="KANBAN">
    <style>
    .kanban-card {
        background-color: #fff;
        border-left: 4px solid #ccc;
        border-radius: 6px;
        transition: box-shadow 0.2s ease;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        height: 120px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        }
    .kanban-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .kanban-card .title-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        max-width: 100%;
    }

    .flag-icon { font-size: 0.9rem; }

        .text-danger i { color: #dc3545; }
        .text-warning i { color: #ffc107; }
        .text-info i { color: #0dcaf0; }
        .text-success i { color: #198754; }

    .kanban-title {
        font-weight: 600;
        font-size: 1rem;
        text-transform: uppercase;
        color: #ff7a00;
    }
    .kanban-header {
        background-color: #f5f5f5;
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #ddd;
        border-radius: 6px 6px 0 0;
    }
    .ui-state-highlight {
        background-color: #ffeeba !important;
        height: 100px;
        border: 2px dashed #ffc107;
    }
    </style>

    <div class="row" id="kanban-board">
        @foreach (['Pendiente' => 'Pendientes', 'En progreso' => 'En Progreso', 'Completada' => 'Completadas', 'Vencida' => 'Vencidas'] as $status => $title)
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
            <div class="kanban-header">
                <h6 class="kanban-title mb-0"><i class="fa fa-layer-group me-2"></i> {{ $title }}</h6>
            </div>
            <div class="card-body p-2" style="min-height: 400px; background-color: #f9f9f9;">
                <ul class="list-unstyled task-list" data-status="{{ $status }}">
                @foreach ($subtareas->where('estado', $status) as $subtarea)
                @php
                    $priorityColor = match($subtarea->prioridad) {
                    'urgente' => 'text-danger',
                    'alta' => 'text-warning',
                    'media' => 'text-info',
                    'baja' => 'text-success',
                    default => 'text-secondary',
                    };
                    $iconoPrioridad = '<i class="fa fa-flag"></i>';
                @endphp
                <li class="kanban-card task-item shadow-sm border" data-id="{{ $subtarea->id }}"
                    data-nombre="{{ $subtarea->nombre }}"
                    data-inicio="{{ $subtarea->fecha_inicio }}"
                    data-fin="{{ $subtarea->fecha_limite }}"
                    data-prioridad="{{ $subtarea->prioridad }}"
                    data-estado="{{ $subtarea->estado }}"
                    data-etapa="{{ $subtarea->tareaGeneral->nombre }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold title-truncate" title="{{ $subtarea->nombre }}">{{ $subtarea->nombre }}</span>
                    <span class="flag-icon {{ $priorityColor }}" title="Prioridad">{!! $iconoPrioridad !!}</span>
                    </div>
                    <div class="d-flex justify-content-start align-items-center gap-2 small text-muted">
                    <span><i class="fa fa-cogs me-1"></i>{{ $subtarea->tareaGeneral->nombre }}</span>
                    <span><i class="fa fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($subtarea->fecha_inicio)->format('d/m') }} - {{ \Carbon\Carbon::parse($subtarea->fecha_limite)->format('d/m') }}</span>
                    </div>
                </li>
                @endforeach
                </ul>
            </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="modal fade" id="editarTareaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editarTareaForm">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Editar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <input type="hidden" id="modal-tarea-id">
                <div class="mb-2">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="modal-nombre" name="nombre" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">Etapa</label>
                    <input type="text" class="form-control" id="modal-etapa" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">Inicio</label>
                    <input type="date" class="form-control" id="modal-inicio" name="fecha_inicio">
                </div>
                <div class="mb-2">
                    <label class="form-label">Fin</label>
                    <input type="date" class="form-control" id="modal-fin" name="fecha_limite">
                </div>
                <div class="mb-2">
                    <label class="form-label">Prioridad</label>
                    <select class="form-select" id="modal-prioridad" name="prioridad">
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="modal-estado" name="estado">
                    <option value="Pendiente">Pendiente</option>
                    <option value="En progreso">En Progreso</option>
                    <option value="Completada">Completada</option>
                    <option value="Vencida">Vencida</option>
                    </select>
                </div>
                </div>
                <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
            </form>
        </div>
        </div>
    </div>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    function activarSortableKanban() {
        $(".task-list").sortable({
            connectWith: ".task-list", // Permite mover las tarjetas entre columnas
            placeholder: "ui-state-highlight", // Efecto visual al mover la tarjeta
            receive: function (event, ui) {
                const card = ui.item; // La tarjeta que se movió
                const id = card.data("id"); // ID de la subtarea
                const newEstado = $(this).data("status"); // Nuevo estado basado en la columna

                // Enviar nuevo estado al servidor cuando se mueve una tarjeta
                fetch(`/tareas/${id}/update-status`, { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: `_method=PATCH&estado=${encodeURIComponent(newEstado)}`
                })
                .then(res => {
                    if (res.ok) {
                        toastr.success("Estado actualizado.");
                        recargarSubtareas(); // Actualizar la lista y el kanban con los nuevos datos
                    } else {
                        toastr.error("No se pudo actualizar el estado.");
                    }
                });
            }
        }).disableSelection(); // Deshabilita la selección de los elementos al moverlos
    }

    $(document).ready(function () {
        activarSortableKanban();
    });


    // Actualizar la vista de la lista con los datos más recientes
    function actualizarLista(tareasGenerales) {
        const tbody = document.querySelector('#subtareasTable tbody');
        if (!tbody) return;
        tbody.innerHTML = ''; // Limpia la tabla actual

        tareasGenerales.forEach(tg => {
            tg.subtareas.forEach(task => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${task.nombre}</td>
                    <td><input type="date" class="form-control fecha-inicio" value="${task.fecha_inicio}" data-id="${task.id}" /></td>
                    <td><input type="date" class="form-control fecha-fin" value="${task.fecha_limite}" data-id="${task.id}" /></td>
                    <td>
                        <select class="form-select prioridad" data-id="${task.id}">
                            <option value="baja" ${task.prioridad === 'baja' ? 'selected' : ''}>Baja</option>
                            <option value="media" ${task.prioridad === 'media' ? 'selected' : ''}>Media</option>
                            <option value="alta" ${task.prioridad === 'alta' ? 'selected' : ''}>Alta</option>
                            <option value="urgente" ${task.prioridad === 'urgente' ? 'selected' : ''}>Urgente</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select estado" data-id="${task.id}">
                            <option value="Pendiente" ${task.estado === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                            <option value="En progreso" ${task.estado === 'En progreso' ? 'selected' : ''}>En progreso</option>
                            <option value="Completada" ${task.estado === 'Completada' ? 'selected' : ''}>Completada</option>
                            <option value="Vencida" ${task.estado === 'Vencida' ? 'selected' : ''}>Vencida</option>
                        </select>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-success btn-sm guardar-cambios" data-id="${task.id}">
                                <i class="fa-solid fa-save"></i>
                            </button>
                            <button class="btn btn-danger btn-sm eliminar-tarea" data-id="${task.id}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        });
        reasociarEventosLista();  // Reasociar los eventos de la lista
    }

    // Reasociar los eventos de la lista (como select2)
    function reasociarEventosLista() {
        $('.estado').select2({
            width: '100%',
            templateResult: function (data) {
                return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
            },
            templateSelection: function (data) {
                return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
            }
        });
    }

</script>