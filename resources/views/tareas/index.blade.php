@extends('layouts.app')

@section('title', 'B-MaiA - Colmena de Tareas')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('./css/components/home-user/tareas-home.css') }}">
    </head>
    <div class="apiario-container">
        <div class="container">
            <!-- Encabezado principal -->
            <div class="apiario-header">
                <h1 class="apiario-title"><span>Colmena de Tareas</span></h1>
                <p class="apiario-subtitle">Organiza y gestiona tus tareas con eficiencia</p>
            </div>

            <!-- Controles de vista -->
            <div class="apiario-controls">
                <div class="view-controls">
                    <button class="btn-panal view-toggler" data-view="list" title="Ver en formato lista">Lista</button>
                    <button class="btn-panal view-toggler" data-view="kanban"
                        title="Ver en formato tablero">Tablero</button>
                    <button class="btn-panal view-toggler" data-view="timeline" title="Ver en línea temporal">Línea de
                        Tiempo</button>
                    <button class="btn-panal view-toggler" data-view="calendar"
                        title="Ver en calendario">Calendario</button>
                </div>

                <button id="toggle-form" class="btn-miel" title="Mostrar/ocultar panel de gestión">
                    <i class="fa fa-tasks"></i> Administrar tareas
                </button>
            </div>

            <!-- Formulario de gestión de tareas (colapsable) -->
            <div id="new-task-form" style="display:none;" class="apiario-panel">
                <!-- Sección de tareas predefinidas -->
                <div class="tareas-predefinidas">
                    <form method="POST" action="{{ route('tareas.default') }}">
                        @csrf
                        <h3 class="panel-titulo">Agregar Tareas Predefinidas</h3>
                        <p class="section-description">Selecciona tareas ya establecidas para añadirlas a tu proyecto.</p>
                        <small class="consejo-sutil">Consejo: Utiliza tareas predefinidas para ahorrar tiempo en la
                            planificación</small>

                        <table class="tabla-panal">
                            <thead>
                                <tr>
                                    <th class="celda-check">
                                        <label for="select-all-subtasks" class="check-label">
                                            <input type="checkbox" id="select-all-subtasks" class="check-miel" />
                                            <span>Seleccionar</span>
                                        </label>
                                    </th>
                                    <th>Etapa</th>
                                    <th><i class="fa-solid fa-thumbtack"></i> Nombre</th>
                                    <th><i class="fa-solid fa-calendar-day"></i> Inicio</th>
                                    <th><i class="fa-solid fa-calendar-check"></i> Fin</th>
                                    <th><i class="fa-solid fa-bolt"></i> Prioridad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listaEtapa as $tareaGeneral)
                                    <!-- Fila de etapa -->
                                    <tr class="fila-etapa">
                                        <td></td>
                                        <td class="celda-etapa">
                                            <strong>{{ $tareaGeneral->nombre }}</strong>
                                        </td>
                                        <td colspan="4"></td>
                                    </tr>

                                    <!-- Filas de subtareas -->
                                    @foreach ($tareaGeneral->predefinidas as $subtarea)
                                        <tr class="fila-tarea">
                                            <td class="celda-check">
                                                <input type="checkbox" name="subtareas[]" value="{{ $subtarea->id }}"
                                                    class="check-miel subtask-checkbox">
                                            </td>
                                            <td></td>
                                            <td>{{ $subtarea->nombre }}</td>
                                            <td>{{ $subtarea->fecha_inicio }}</td>
                                            <td>{{ $subtarea->fecha_limite }}</td>
                                            <td>{{ $subtarea->prioridad }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                        <div class="nota-sutil">Nota: Las tareas seleccionadas se añadirán con sus fechas y prioridades
                            originales</div>

                        <button type="submit" class="btn-miel-submit">Agregar Tareas Seleccionadas</button>
                    </form>
                </div>

                <!-- Separador sutil -->
                <div class="separador-sutil"></div>

                <!-- Sección de tareas personalizadas -->
                <div class="tareas-personalizadas">
                    <form action="{{ route('tareas.store') }}" method="POST">
                        @csrf
                        <h3 class="panel-titulo">Crear Tareas Personalizadas</h3>
                        <p class="section-description">Diseña tus propias tareas adaptadas a las necesidades de tu proyecto.
                        </p>
                        <small class="consejo-sutil">Consejo: Divide los grandes objetivos en tareas más pequeñas y
                            manejables</small>

                        <!-- Selector de etapa -->
                        <div class="grupo-form">
                            <label for="tarea_general_id" class="etiqueta-miel">Etapa del proyecto</label>
                            <select name="tarea_general_id" id="tarea_general_id" class="select-miel" required>
                                <option value="" disabled selected>Seleccione una etapa</option>
                                @foreach($listaEtapa as $tarea)
                                    <option value="{{ $tarea->id }}">{{ $tarea->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Contenedor de subtareas -->
                        <div id="subtareas-container" class="contenedor-subtareas">
                            <h4 class="subtitulo-panel">Definir Tareas</h4>
                            <div class="mini-guia">
                                <span class="guia-item"><strong>Prioridad alta:</strong> Tareas críticas para el
                                    avance</span>
                                <span class="guia-item"><strong>Fechas realistas:</strong> Considera imprevistos</span>
                                <span class="guia-item"><strong>Títulos claros:</strong> Específicos y concisos</span>
                            </div>

                            <!-- Plantilla de subtarea (oculta) -->
                            <div class="subtarea" id="subtarea-template" style="display: none;">
                                <div class="fila-form">
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Prioridad</label>
                                        <select data-field="prioridad" class="select-miel">
                                            <option value="no-prioritaria">No Prioritaria</option>
                                            <option value="baja">Baja</option>
                                            <option value="media">Media</option>
                                            <option value="alta">Alta</option>
                                            <option value="urgente">Urgente</option>
                                        </select>
                                    </div>
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Título de la tarea</label>
                                        <input type="text" data-field="nombre" class="input-miel"
                                            placeholder="Ej: Preparar documentación">
                                    </div>
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Fecha Inicio</label>
                                        <input type="date" data-field="fecha_inicio" class="input-miel">
                                    </div>
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Fecha Fin</label>
                                        <input type="date" data-field="fecha_fin" class="input-miel">
                                    </div>
                                    <div class="columna-form">
                                        <label class="etiqueta-miel">Estado</label>
                                        <select data-field="estado" class="select-miel">
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="En progreso">En Progreso</option>
                                            <option value="completada">Completada</option>
                                            <option value="Vencida">Vencida</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn-eliminar remove-subtarea">Eliminar</button>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="acciones-form">
                            <button type="button" id="add-subtarea" class="btn-agregar">
                                Agregar Nueva Tarea
                            </button>
                            <button type="submit" class="btn-miel-submit">Guardar Todas las Tareas</button>
                        </div>
                        <div class="nota-sutil">Nota: Puedes editar las tareas más tarde si necesitas hacer cambios</div>
                    </form>
                </div>
            </div>

            <!-- Contenedor de vistas de tareas -->
            <div id="task-view-container" class="contenedor-vistas">

                <!-- Vistas -->
                <div class="view list active">
                    @include('tareas.list')
                </div>
                <div class="view kanban">
                    @include('tareas.kanban')
                    <div class="kanban-column" data-estado="Pendiente">
                        <div class="kanban-tasks-container">
                            <!-- Aquí van las tareas .kanban-task -->
                        </div>
                    </div>
                </div>
                <div class="view timeline">
                    @include('tareas.timeline')
                </div>
                <div class="view calendar">
                    <div id="calendar"></div>
                </div>

                <!-- Consejo final -->
                <div class="consejo-final">
                    <p>Revisa regularmente tus tareas para mantener el progreso de tu proyecto</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script específico para tareas (mantenemos el original) -->
    <script src="{{ asset('js/components/home-user/tareas.js') }}"></script>
@endsection