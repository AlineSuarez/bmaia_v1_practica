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
    $(function () {
        $(".task-list").sortable({
            connectWith: ".task-list",
            placeholder: "ui-state-highlight",
            receive: function (event, ui) {
            const card = ui.item;
            const id = card.data("id");
            const newEstado = $(this).data("status");
    
            $.ajax({
                url: `/tareas/${id}/update`,
                method: 'POST',
                data: {
                _method: 'PATCH',
                _token: '{{ csrf_token() }}',
                estado: newEstado
                },
                success: function () {
                console.log("Estado actualizado");
                },
                error: function (err) {
                console.error("Error al actualizar estado", err);
                }
            });
            }
        }).disableSelection();
    // Al hacer clic en la tarjeta, abrir modal
    $(document).on('click', '.kanban-card', function () {
        const tareaId = $(this).data("id");
        const nombre = $(this).data("nombre");
        const inicio = $(this).data("inicio");
        const fin = $(this).data("fin");
        const prioridad = $(this).data("prioridad");
        const estado = $(this).data("estado");
        const etapa = $(this).data("etapa");
        $('#modal-tarea-id').val(tareaId);
        $('#modal-nombre').val(nombre);
        $('#modal-inicio').val(inicio);
        $('#modal-fin').val(fin);
        $('#modal-prioridad').val(prioridad);
        $('#modal-estado').val(estado);
        $('#modal-etapa').val(etapa);
        $('#editarTareaForm').attr('action', `/tareas/${tareaId}/update`);
        const modal = new bootstrap.Modal(document.getElementById('editarTareaModal'));
        modal.show();
    });
    });
</script>