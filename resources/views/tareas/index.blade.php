@extends('layouts.app')

@section('title', 'B-MaiA - Plan de Trabajo Anual')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('./css/components/home-user/tareas-home.css') }}">
    </head>

    <!-- Container principal con ancho completo -->
    <div class="apiario-container-full-width">
        <!-- Loader global para todas las vistas de tareas -->
        <div id="globalLoader" class="global-loader">
            <div class="global-loader-content">
                <div class="global-loader-spinner"></div>
                <p class="global-loader-message">Cargando tareas</p>
                <p class="global-loader-subtitle">Organizando tu colmena de productividad</p>
            </div>
        </div>

        <div class="apiario-wrapper">
            <!-- Encabezado principal -->
            <header class="apiario-header">
                <h1 class="apiario-title"><span>Plan de Trabajo Anual</span></h1>
            </header>

            <!-- Controles de vista y administración -->
            <section class="apiario-controls">
                <div class="view-controls">
                    <button class="btn-panal view-toggler" data-view="list" title="Ver en formato lista">Lista</button>
                    <button class="btn-panal view-toggler" data-view="kanban"
                        title="Ver en formato tablero">Tablero</button>
                    <button class="btn-panal view-toggler" data-view="timeline" title="Ver en línea temporal">Línea de
                        Tiempo</button>
                    <button class="btn-panal view-toggler" data-view="calendar"
                        title="Ver en calendario">Calendario</button>
                </div>

                <!-- Nuevos botones separados -->
                <div class="admin-controls">
                    <!-- <button id="btn-tareas-predefinidas" class="btn-miel" data-bs-toggle="modal"
                                        data-bs-target="#tareasPredefinidasModal" title="Agregar tareas predefinidas">
                                        <i class="fa fa-list-check"></i> Tareas Predefinidas
                                    </button> -->
                    <button id="btn-crear-tareas" class="btn-miel" data-bs-toggle="modal" data-bs-target="#crearTareasModal"
                        title="Crear tareas personalizadas">
                        <i class="fa fa-plus-circle"></i> Crear Tareas
                    </button>
                    <button id="btn-ver-archivadas" class="btn-miel" type="button" title="Ver tareas archivadas"
                        data-url="{{ route('tareas.archivadas') }}">
                        <i class="fa fa-archive"></i> Ver Archivadas
                    </button>
                </div>
            </section>

            <!-- Contenedor principal de vistas de tareas -->
            <main id="task-view-container" class="contenedor-vistas">
                <!-- Vista Lista -->
                <div class="view list active">
                    @include('tareas.list')
                </div>

                <!-- Vista Kanban -->
                <div class="view kanban">
                    @include('tareas.kanban')
                    <div class="kanban-column" data-estado="Pendiente">
                        <div class="kanban-tasks-container">
                            <!-- Aquí van las tareas .kanban-task -->
                        </div>
                    </div>
                </div>

                <!-- Vista Timeline -->
                <div class="view timeline">
                    @include('tareas.timeline')
                </div>

                <!-- Vista Calendario -->
                <div class="view calendar">
                    @include('tareas.calendario')
                </div>

                <!-- Consejo final -->
                <div class="consejo-final">
                    <p>Revisa regularmente tus tareas para mantener el progreso de tu proyecto</p>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal de Tareas Predefinidas -->
    <div class="modal fade" id="tareasPredefinidasModal" tabindex="-1" aria-labelledby="tareasPredefinidasModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tareasPredefinidasModalLabel">
                        <i class="fa fa-list-check"></i>
                        <span>Tareas Predefinidas del Sistema</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar modal">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="modal-description-section">
                        <p class="section-description">
                            <i class="fa fa-info-circle me-2"></i>
                            Selecciona de nuestra biblioteca de tareas predefinidas para acelerar la configuración de tu
                            proyecto.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('tareas.default') }}" id="form-tareas-predefinidas">
                        @csrf

                        <!-- Contenedor de etapas con tablas separadas -->
                        <div class="etapas-container">
                            @foreach ($listaEtapa as $tareaGeneral)
                                <div class="etapa-section">
                                    <!-- Título de la etapa -->
                                    <div class="etapa-header">
                                        <h4 class="etapa-titulo">
                                            <i class="fa fa-folder-open me-2"></i>
                                            {{ $tareaGeneral->nombre }}
                                        </h4>
                                        <div class="etapa-info">
                                            <span class="badge badge-info">
                                                {{ count($tareaGeneral->predefinidas) }} tareas disponibles
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Tabla específica para esta etapa -->
                                    @if(count($tareaGeneral->predefinidas) > 0)
                                        <div class="tabla-wrapper etapa-tabla">
                                            <table class="tabla-panal">
                                                <thead>
                                                    <tr>
                                                        <th class="celda-check">
                                                            <label for="select-all-etapa-{{ $tareaGeneral->id }}"
                                                                class="check-label">
                                                                <input type="checkbox" id="select-all-etapa-{{ $tareaGeneral->id }}"
                                                                    class="check-miel select-all-etapa"
                                                                    data-etapa="{{ $tareaGeneral->id }}" />
                                                                <span>Todo</span>
                                                            </label>
                                                        </th>
                                                        <th><i class="fa fa-tasks me-1"></i>Nombre de la Tarea</th>
                                                        <th><i class="fa fa-calendar-day me-1"></i>Fecha Inicio</th>
                                                        <th><i class="fa fa-calendar-check me-1"></i>Fecha Límite</th>
                                                        <th><i class="fa fa-flag me-1"></i>Prioridad</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($tareaGeneral->predefinidas as $subtarea)
                                                        <tr class="fila-tarea" data-etapa="{{ $tareaGeneral->id }}">
                                                            <td class="celda-check">
                                                                <input type="checkbox" name="subtareas[]" value="{{ $subtarea->id }}"
                                                                    class="check-miel subtask-checkbox-predefinidas etapa-{{ $tareaGeneral->id }}-checkbox">
                                                            </td>
                                                            <td>
                                                                <div class="task-name">
                                                                    <i class="fa fa-task"></i>
                                                                    {{ $subtarea->nombre }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-dark">
                                                                    {{ \Carbon\Carbon::parse($subtarea->fecha_inicio)->format('d/m/Y') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-dark">
                                                                    {{ \Carbon\Carbon::parse($subtarea->fecha_limite)->format('d/m/Y') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge priority-{{ strtolower($subtarea->prioridad) }}">
                                                                    {{ ucfirst($subtarea->prioridad) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="empty-etapa">
                                            <i class="fa fa-inbox"></i>
                                            <p>No hay tareas predefinidas para esta etapa</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Selector global -->
                        <div class="global-selector">
                            <label for="select-all-global" class="check-label-global">
                                <input type="checkbox" id="select-all-global" class="check-miel" />
                                <span>Seleccionar todas las tareas de todas las etapas</span>
                            </label>
                        </div>

                        <div class="nota-sutil">
                            <i class="fa fa-info-circle"></i>
                            <div>
                                <strong>Importante:</strong> Las tareas seleccionadas se añadirán a tu proyecto con sus
                                fechas y prioridades originales. Podrás editarlas individualmente después de agregarlas.
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div class="modal-footer-content">
                        <div id="tareas-seleccionadas-count">0 tareas seleccionadas</div>
                        <div class="modal-footer-buttons">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i>Cancelar
                            </button>
                            <button type="submit" form="form-tareas-predefinidas" id="btn-agregar-seleccionadas"
                                class="btn-miel-submit" disabled>
                                <i class="fa fa-plus me-1"></i>Agregar Tareas Seleccionadas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Crear Tareas -->
    <div class="modal fade" id="crearTareasModal" tabindex="-1" aria-labelledby="crearTareasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearTareasModalLabel">
                        <i class="fa fa-plus-circle"></i>Crear Tareas Personalizadas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar modal">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tareas.store') }}" method="POST" id="form-crear-tareas">
                        @csrf

                        <!-- Selector de etapa -->
                        <div class="grupo-form">
                            <label for="tarea_general_id_modal" class="etiqueta-miel">Etapa del proyecto</label>
                            <div class="etapa-selector-container">
                                <select name="tarea_general_id" id="tarea_general_id_modal" class="select-miel" required>
                                    <option value="" disabled selected>Seleccione una etapa</option>
                                    @foreach($listaEtapa as $tarea)
                                        <option value="{{ $tarea->id }}">{{ $tarea->nombre }}</option>
                                    @endforeach
                                </select>
                                <!-- Botón para abrir formulario modal -->
                                <button type="button" class="btn-nueva-etapa" id="btn-nueva-etapa-modal">
                                    + Nueva Etapa
                                </button>
                            </div>
                        </div>

                        <!-- Formulario oculto para nueva etapa -->
                        <div id="form-nueva-etapa-modal" class="form-nueva-etapa-container" style="display: none;">
                            <label for="nombre_nueva_etapa_modal" class="etiqueta-miel">Nombre de nueva etapa</label>
                            <input type="text" id="nombre_nueva_etapa_modal" class="input-miel"
                                placeholder="Ej: Inspección de apiarios">
                            <div class="nueva-etapa-buttons">
                                <button type="button" class="btn-miel-submit" id="guardar-nueva-etapa-modal">Guardar
                                    Etapa</button>
                                <button type="button" class="btn btn-secondary"
                                    id="cancelar-nueva-etapa-modal">Cancelar</button>
                            </div>
                        </div>

                        <!-- Contenedor de subtareas -->
                        <div id="subtareas-container-modal" class="contenedor-subtareas">
                            <!-- Header con botón - FIJO EN EL BLADE -->
                            <div class="subtareas-header">
                                <h4 class="subtitulo-panel">Definir Tareas</h4>
                                <button type="button" id="add-subtarea-modal" class="btn-agregar">
                                    <i class="fa fa-plus"></i>Agregar Tarea
                                </button>
                            </div>

                            <!-- Contenedor dinámico para las tareas -->
                            <div id="tareas-dinamicas-container">
                                <!-- Estado vacío inicial -->
                                <div class="empty-subtareas" id="empty-subtareas-state">
                                    <div class="empty-icon">
                                        <i class="fa fa-tasks"></i>
                                    </div>
                                    <p class="empty-title">No hay tareas definidas aún</p>
                                    <small class="empty-subtitle">Haz clic en "Agregar Tarea" para comenzar</small>
                                </div>
                            </div>

                            <!-- Plantilla de subtarea (oculta) -->
                            <div class="subtarea subtarea-template" id="subtarea-template-modal" style="display: none;">
                                <div class="subtarea-header">
                                    <h6 class="subtarea-title">
                                        <i class="fa fa-task"></i>
                                        Tarea <span class="numero-tarea">1</span>
                                    </h6>
                                    <button type="button" class="remove-subtarea-modal">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>

                                <div class="fila-form">
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Título de la tarea</label>
                                        <input type="text" data-field="nombre" class="input-miel"
                                            placeholder="Ej: Preparar documentación" required disabled>
                                    </div>
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Prioridad</label>
                                        <select data-field="prioridad" class="select-miel" disabled>
                                            <option value="no-prioritaria">No Prioritaria</option>
                                            <option value="baja">Baja</option>
                                            <option value="media" selected>Media</option>
                                            <option value="alta">Alta</option>
                                            <option value="urgente">Urgente</option>
                                        </select>
                                    </div>
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Fecha Inicio</label>
                                        <input type="date" data-field="fecha_inicio" class="input-miel" disabled>
                                    </div>
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Fecha Fin</label>
                                        <input type="date" data-field="fecha_fin" class="input-miel" disabled>
                                    </div>
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Estado</label>
                                        <select data-field="estado" class="select-miel" disabled>
                                            <option value="Pendiente" selected>Pendiente</option>
                                            <option value="En progreso">En Progreso</option>
                                            <option value="completada">Completada</option>
                                            <option value="Vencida">Vencida</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="modal-footer-content">
                        <div class="modal-footer-buttons">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" form="form-crear-tareas" id="btn-guardar-tareas" class="btn-miel-submit"
                                disabled>
                                <i class="fa fa-save"></i>Guardar Todas las Tareas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script específico para tareas -->
    <script src="{{ asset('js/components/home-user/tareas.js') }}"></script>
    <script src="{{ asset('js/components/home-user/tasks/calendario.js') }}"></script>
@endsection