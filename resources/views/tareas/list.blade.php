
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
                </h1>
                <p class="header-subtitle">Gestiona y organiza tus tareas de manera eficiente</p>
            </div>
            <div class="header-actions">
                <div class="task-stats">
                    <span class="stat-item">
                        <i class="fa-solid fa-tasks"></i>
                        <span>{{ $subtareas->count() }} tareas</span>
                    </span>
                </div>
                <a href="{{ route('tareas.imprimirTodas') }}" 
                   target="_blank" 
                   class="print-button">
                    <i class="fa fa-print"></i>
                    <span>Imprimir</span>
                </a>
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

    {{-- Contenedor de tarjetas --}}
    <div class="tasks-grid" id="tasksGrid">
        @forelse ($subtareas as $task)
            <div class="task-card" data-task-id="{{ $task->id }}" data-status="{{ $task->estado }}" data-priority="{{ $task->prioridad }}">
                
                {{-- Header de la tarjeta --}}
                <div class="task-card-header">
                    <div class="task-title-section">
                        <h3 class="task-title">{{ $task->nombre }}</h3>
                        <div class="task-meta">
                            <span class="priority-badge priority-{{ $task->prioridad }}">
                                <i class="fa-solid fa-flag"></i>
                                {{ ucfirst($task->prioridad) }}
                            </span>
                            <span class="status-badge status-{{ str_replace(' ', '-', strtolower($task->estado)) }}">
                                @switch($task->estado)
                                    @case('Pendiente')
                                        <i class="fa-solid fa-hourglass-start"></i>
                                        @break
                                    @case('En progreso')
                                        <i class="fa-solid fa-spinner"></i>
                                        @break
                                    @case('Completada')
                                        <i class="fa-solid fa-check-circle"></i>
                                        @break
                                    @case('Vencida')
                                        <i class="fa-solid fa-exclamation-triangle"></i>
                                        @break
                                @endswitch
                                {{ $task->estado }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Contenido de la tarjeta --}}
                <div class="task-card-content">
                    
                    {{-- Fechas --}}
                    <div class="task-dates">
                        <div class="date-group">
                            <label class="date-label">
                                <i class="fa-solid fa-calendar-day"></i>
                                Inicio
                            </label>
                            <input type="date" 
                                   class="date-input fecha-inicio"
                                   value="{{ \Carbon\Carbon::parse($task->fecha_inicio)->format('Y-m-d') }}"
                                   data-id="{{ $task->id }}"
                                   aria-label="Fecha de inicio para {{ $task->nombre }}" />
                        </div>
                        
                        <div class="date-group">
                            <label class="date-label">
                                <i class="fa-solid fa-calendar-check"></i>
                                Fin
                            </label>
                            <input type="date" 
                                   class="date-input fecha-fin"
                                   value="{{ \Carbon\Carbon::parse($task->fecha_limite)->format('Y-m-d') }}"
                                   data-id="{{ $task->id }}"
                                   aria-label="Fecha límite para {{ $task->nombre }}" />
                        </div>
                    </div>

                    {{-- Controles --}}
                    <div class="task-controls">
                        <div class="control-group">
                            <label class="control-label">
                                <i class="fa-solid fa-bolt"></i>
                                Prioridad
                            </label>
                            <select class="priority-select prioridad-select prioridad" 
                                    data-id="{{ $task->id }}"
                                    aria-label="Prioridad para {{ $task->nombre }}">
                                @foreach(['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'urgente' => 'Urgente'] as $value => $label)
                                    <option value="{{ $value }}" 
                                            @selected($task->prioridad === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="control-group">
                            <label class="control-label">
                                <i class="fa-solid fa-tasks"></i>
                                Estado
                            </label>
                            <select class="status-select estado-select estado" 
                                    data-id="{{ $task->id }}"
                                    aria-label="Estado para {{ $task->nombre }}">
                                @foreach(['Pendiente', 'En progreso', 'Completada', 'Vencida'] as $estado)
                                    <option value="{{ $estado }}" 
                                            @selected($task->estado === $estado)>
                                        {{ $estado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Footer de la tarjeta --}}
                <div class="task-card-footer">
                    <div class="task-actions">
                        <button type="button"
                                class="action-button save-button guardar-cambios" 
                                data-id="{{ $task->id }}"
                                title="Guardar cambios">
                            <i class="fa-solid fa-save"></i>
                            <span>Guardar</span>
                        </button>

                        <button type="button"
                                class="action-button delete-button eliminar-tarea" 
                                data-id="{{ $task->id }}"
                                title="Eliminar tarea">
                            <i class="fa-solid fa-trash"></i>
                            <span>Eliminar</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
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
        @endforelse
    </div>

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
                        <span id="count-completadas">{{ $subtareas->where('estado', 'Completada')->count() }} Completadas</span>
                    </div>
                    <div class="stat-group">
                        <i class="fa-solid fa-spinner text-primary"></i>
                        <span id="count-enprogreso">{{ $subtareas->where('estado', 'En progreso')->count() }} En Progreso</span>
                    </div>
                    <div class="stat-group">
                        <i class="fa-solid fa-hourglass-start text-warning"></i>
                        <span id="count-pendientes">{{ $subtareas->where('estado', 'Pendiente')->count() }} Pendientes</span>
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