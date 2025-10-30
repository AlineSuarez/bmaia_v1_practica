{{-- Meta tags requeridos --}}

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('./css/components/home-user/tasks/list.css') }}" rel="stylesheet">
</head>

{{-- Contenedor principal de la lista de tareas --}}
<div class="task-list-container" id="printableArea">

    {{-- Header con acciones globales --}}
    <div class="task-list-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="header-title">
                    <i class="fa-solid fa-list-check"></i>
                    Lista de Tareas 
                    <i class="fa-solid fa-circle-question"></i>
                </h1>
                
                <p class="header-subtitle"> Gestiona y organiza de manera eficiente que tareas son relevantes para su Plan de Trabajo Anual</p>
                
            </div>
                <div class="header-semaphore">
                    @php
                        $prioridades = [
                            ['color' => 'red', 'label' => 'Urgentes', 'value' => 'urgente'],
                            ['color' => 'yellow', 'label' => 'Alta', 'value' => 'alta'],
                            ['color' => 'green', 'label' => 'Media', 'value' => 'media'],
                            ['color' => 'lightblue', 'label' => 'Baja', 'value' => 'baja'],
                        ];
                    @endphp

                    @foreach($prioridades as $prioridad)
                        <div class="task-semaphore priority-filter" data-priority="{{ $prioridad['value'] }}" style="cursor: pointer;">
                            <i class="fa-solid fa-circle priority-light" style="color: {{ $prioridad['color'] }}"></i>
                            <span class="stat-semaphore">{{ $prioridad['label'] }}</span>
                            <span class="task-text">{{ $subtareas->where('prioridad', $prioridad['value'])->count() }}</span>
                        </div>
                    @endforeach
                </div>
        </div>
    </div>

    {{-- Contenedor de filtros rápidos --}}
    <div class="filters-container">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">
                <i class="fa-solid fa-list"></i>
                Todas
            </button>
            <button class="filter-btn" data-filter="Pendiente">
                <i class="fa-solid fa-hourglass-start"></i>
                Pendientes
            </button>
            <button class="filter-btn" data-filter="En progreso">
                <i class="fa-solid fa-spinner"></i>
                En Progreso
            </button>
            <button class="filter-btn" data-filter="Completada">
                <i class="fa-solid fa-check-circle"></i>
                Completadas
            </button>
        </div>
    </div>

    {{-- Contenedor de tabla --}}
    <div class="tasks-table-container" id="tasksTableContainer">
        @if($subtareas->count() > 0)
            <table class="tasks-table" id="tasksTable">
                <thead>
                    <tr>
                        <th class="sortable" data-column="nombre">
                            <i class="fa-solid fa-sort"></i>
                            Nombre de Tarea
                        </th>
                        <th class="sortable" data-column="prioridad">
                            <i class="fa-solid fa-sort"></i>
                            Prioridad
                        </th>
                        <th class="sortable" data-column="estado">
                            <i class="fa-solid fa-sort"></i>
                            Estado
                        </th>
                        <th class="sortable" data-column="fecha_inicio">
                            <i class="fa-solid fa-sort"></i>
                            Fecha Inicio
                        </th>
                        <th class="sortable" data-column="fecha_limite">
                            <i class="fa-solid fa-sort"></i>
                            Fecha Límite
                        </th>
                        <th class="actions-column">
                            <i class="fa-solid fa-cog"></i>
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody id="tasksTableBody">
                    @foreach ($subtareas->filter() as $task)
                        <tr class="task-row" data-task-id="{{ $task->id }}" data-status="{{ $task->estado }}"
                        data-priority="{{ $task->prioridad }}"
                        data-fecha-inicio ="{{ \Carbon\Carbon::parse($task->fecha_inicio)->format('d-m-Y') }}"
                        data-fecha-limite ="{{ \Carbon\Carbon::parse($task->fecha_limite)->format('d-m-Y') }}"
                        >
                            {{-- Nombre de la tarea --}}
                            <td class="task-name-cell">
                                <div class="task-name-content">
                                    <span class="task-name">{{ $task->nombre }}</span>
                                </div>
                            </td>

                            {{-- Prioridad --}}
                            <td class="priority-cell">
                                <select class="priority-select prioridad" data-id="{{ $task->id }}"
                                    aria-label="Prioridad para {{ $task->nombre }}">
                                    @foreach(['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'urgente' => 'Urgente'] as $value => $label)
                                        <option value="{{ $value }}" @selected($task->prioridad === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Estado --}}
                            <td class="status-cell">
                                <select class="status-select estado" data-id="{{ $task->id }}"
                                    aria-label="Estado para {{ $task->nombre }}">
                                    @foreach(['Pendiente', 'En progreso', 'Completada'] as $estado)
                                        <option value="{{ $estado }}" @selected($task->estado === $estado)>
                                            {{ $estado }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Fecha de inicio --}}
                            <td class="date-cell">
                                <input type="date" class="date-input fecha-inicio"
                                    value="{{ \Carbon\Carbon::parse($task->fecha_inicio)->format('Y-m-d') }}"
                                    data-id="{{ $task->id }}" aria-label="Fecha de inicio para {{ $task->nombre }}" />
                            </td>

                            {{-- Fecha límite --}}
                            <td class="date-cell">
                                <input type="date" class="date-input fecha-fin"
                                    value="{{ \Carbon\Carbon::parse($task->fecha_limite)->format('Y-m-d') }}"
                                    data-id="{{ $task->id }}" aria-label="Fecha límite para {{ $task->nombre }}" />
                            </td>

                            {{-- Acciones --}}
                            <td class="actions-cell">
                                <div class="table-actions">
                                    <button type="button" class="action-button save-button guardar-cambios"
                                        data-id="{{ $task->id }}" title="Guardar cambios">
                                        <i class="fa-solid fa-save"></i>
                                    </button>

                                    <form action="{{ route('tareas.archivar', $task->id) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro que deseas descartar esta tarea?');"
                                        style="display:inline;">
                                        @csrf
                                        <button type="submit" class="action-button archive-button" title="Descartar tarea">
                                            <i class="fa fa-x"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @else
            {{-- Estado vacío --}}
            <div class="empty-state">
                <div class="empty-state-content">
                    <div class="empty-icon">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                    <h3 class="empty-title">No hay tareas disponibles</h3>
                    <p class="empty-message">Las tareas aparecerán aquí cuando sean creadas</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Recordatorio de cambios pendientes -->
    <div id="cambiosRecordatorio" style="display:none; margin:1rem 0; text-align:center;">
        <span
            style="background:var(--amber-100); color:var(--amber-700); padding:0.5rem 1rem; border-radius:0.5rem; font-weight:600;">
            Tienes cambios sin guardar. Recuerda presionar el botón de guardar para aplicar los cambios.
        </span>
    </div>

    <div id="contadorCambiosPendientes" style="display:none; margin:1rem 0; text-align:center;">
        <span
            style="background:var(--amber-100); color:var(--amber-700); padding:0.5rem 1rem; border-radius:0.5rem; font-weight:600;">
            Cambios pendientes: <span id="numCambiosPendientes">0</span>
        </span>
    </div>

    {{-- Paginación --}}
    <div class="pagination-container" id="tasksPagination"></div>

    {{-- Loading state --}}
    <div class="loading-state hidden" id="taskListLoading">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p class="loading-message">Actualizando lista de tareas...</p>
        </div>
    </div>

    {{-- Footer informativo --}}
    @if($subtareas->count() > 0)
        <div class="task-list-footer">
            <div class="footer-content">
                <div class="footer-stats">
                    <div class="stat-group">
                        <i class="fa-solid fa-check-circle text-success"></i>
                        <span id="count-completadas">{{ $subtareas->where('estado', 'Completada')->count() }}
                            Completadas</span>
                    </div>
                    <div class="stat-group">
                        <i class="fa-solid fa-spinner text-primary"></i>
                        <span id="count-enprogreso">{{ $subtareas->where('estado', 'En progreso')->count() }} En
                            Progreso</span>
                    </div>
                    <div class="stat-group">
                        <i class="fa-solid fa-hourglass-start text-warning"></i>
                        <span id="count-pendientes">{{ $subtareas->where('estado', 'Pendiente')->count() }}
                            Pendientes</span>
                    </div>
                </div>
                <div class="footer-info">
                    <i class="fa-solid fa-clock"></i>
                    <span>Actualizado {{ now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>

@section('optional-scripts')
    <script src="{{ asset('js/components/home-user/tasks/list.js') }}"></script>
@endsection