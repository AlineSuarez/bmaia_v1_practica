{{-- Meta tags requeridos --}}

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('./css/components/home-user/tasks/kanban.css') }}" rel="stylesheet">
</head>

{{-- Contenedor principal del Kanban --}}
<div class="task-list-container">
    <div class="kanban-container" id="kanbanBoard">

        {{-- Header del Kanban (nuevo, igual al listado) --}}
        <div class="task-list-header">
            <div class="header-content">
                <div class="header-left">
                    <h1 class="header-title">
                        <i class="fa-solid fa-table-columns"></i>
                        Tablero Kanban
                    </h1>
                    <p class="header-subtitle">Visualiza y organiza tus tareas de forma dinámica por columnas
                    </p>
                </div>
            </div>
        </div>

        {{-- Tablero con las 4 columnas --}}
        <div class="kanban-board">
            @foreach (['Pendiente', 'En progreso', 'Completada', 'Vencida'] as $estado)
                <div class="kanban-column" data-status="{{ $estado }}">

                    {{-- Header de la columna --}}
                    <div class="column-header">
                        <h3 class="column-title">{{ $estado }}</h3>
                        <span class="task-count">{{ $subtareas->where('estado', $estado)->count() }}</span>
                    </div>

                    {{-- Lista de tareas de la columna --}}
                    <div class="task-list" data-status="{{ $estado }}">
                        @foreach ($subtareas->where('estado', $estado) as $task)
                            <div class="task-card" data-task-id="{{ $task->id }}" data-priority="{{ $task->prioridad }}">

                                {{-- Header de la tarjeta --}}
                                <div class="card-header">
                                    <span class="task-name">{{ $task->nombre }}</span>
                                    <span class="priority-indicator priority-{{ $task->prioridad }}"></span>
                                </div>

                                {{-- Contenido de la tarjeta --}}
                                <div class="card-content">
                                    <div class="task-stage">{{ $task->tareaGeneral->nombre }}</div>
                                    <div class="task-dates">
                                        <span
                                            class="date-start">{{ \Carbon\Carbon::parse($task->fecha_inicio)->format('d/m') }}</span>
                                        <span class="date-separator">-</span>
                                        <span
                                            class="date-end">{{ \Carbon\Carbon::parse($task->fecha_limite)->format('d/m') }}</span>
                                    </div>
                                </div>

                                {{-- Footer de la tarjeta --}}
                                <div class="card-footer">
                                    <div class="task-actions">
                                        <button class="action-btn edit-btn" data-task-id="{{ $task->id }}" title="Editar tarea">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete-btn" data-task-id="{{ $task->id }}"
                                            title="Eliminar tarea">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                        {{-- Mensaje cuando no hay tareas --}}
                        @if($subtareas->where('estado', $estado)->count() === 0)
                            <div class="empty-column">
                                <p class="empty-message">No hay tareas en {{ strtolower($estado) }}</p>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</div>

{{-- Modal para editar tareas --}}
<div class="modal-overlay" id="editTaskModal" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Editar Tarea</h3>
            <button class="modal-close" id="closeModal">&times;</button>
        </div>

        <form id="editTaskForm">
            <div class="modal-body">
                <input type="hidden" id="taskId">

                <div class="form-group">
                    <label for="taskName">Nombre de la tarea</label>
                    <input type="text" id="taskName" name="nombre" readonly>
                </div>

                <div class="form-group">
                    <label for="taskStage">Etapa</label>
                    <input type="text" id="taskStage" readonly>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="taskStartDate">Fecha de inicio</label>
                        <input type="date" id="taskStartDate" name="fecha_inicio">
                    </div>

                    <div class="form-group">
                        <label for="taskEndDate">Fecha límite</label>
                        <input type="date" id="taskEndDate" name="fecha_limite">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="taskPriority">Prioridad</label>
                        <select id="taskPriority" name="prioridad">
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="taskStatus">Estado</label>
                        <select id="taskStatus" name="estado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="En progreso">En progreso</option>
                            <option value="Completada">Completada</option>
                            <option value="Vencida">Vencida</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelEdit">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/components/home-user/tasks/kanban.js') }}"></script>