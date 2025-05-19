@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/apiario-theme.css') }}">
@endsection

@section('content')
    <div class="apiario-container">
        <div class="container">
            <!-- Encabezado principal -->
            <div class="apiario-header">
                <h1 class="apiario-title"><span>Colmena de Tareas</span></h1>
                <p class="apiario-subtitle">Organiza y gestiona tus tareas con eficiencia</p>
                <small class="tip-sutil">Cada tarea completada es un paso hacia el éxito de tu proyecto</small>
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
                <!-- Pequeña guía de vistas -->
                <div class="vista-guia">
                    <p class="vista-tip">Cambia entre las diferentes vistas para gestionar tus tareas según tus necesidades
                    </p>
                    <div class="vista-info">
                        <span class="vista-info-item">Lista: Visión detallada</span>
                        <span class="vista-info-item">Tablero: Organización por estados</span>
                        <span class="vista-info-item">Línea de Tiempo: Secuencia cronológica</span>
                        <span class="vista-info-item">Calendario: Planificación temporal</span>
                    </div>
                </div>

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

<style>
    :root {
        --miel-claro: #fff8e1;
        --miel-suave: #ffecb3;
        --miel-medio: #ffe082;
        --miel-dorado: #ffd54f;
        --miel-ambar: #ffca28;
        --miel-oscuro: #ffb300;
        --panal-borde: #ffa000;
        --abeja-negro: #3e2723;
        --cera-suave: #bcaaa4;
        --fondo-claro: #ffffff;
        --texto-oscuro: #4e342e;
        --texto-suave: #6d4c41;
        --sombra-miel: rgba(255, 179, 0, 0.2);
        --sombra-miel-hover: rgba(255, 179, 0, 0.3);
    }

    .kanban-tasks-container {
        min-height: 60px;
        padding: 8px 0;
        position: relative;
        transition: background 0.2s;
    }

    .kanban-tasks-container:empty::after {
        content: "Arrastra aquí";
        color: #c2a13a;
        opacity: 0.5;
        font-size: 0.95em;
        position: absolute;
        left: 50%;
        top: 20%;
        transform: translate(-50%, 0);
        pointer-events: none;
    }

    /* Estilos generales */
    body {
        background-color: var(--fondo-claro);
        color: var(--texto-oscuro);
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }

    .apiario-container {
        position: relative;
        padding: 2rem 0;
        background-color: var(--fondo-claro);
        min-height: 100vh;
    }

    /* Encabezado */
    .apiario-header {
        text-align: center;
        margin-bottom: 2.5rem;
        position: relative;
    }

    .apiario-title {
        color: var(--miel-oscuro);
        font-size: 2.5rem;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        position: relative;
        display: inline-block;
        padding: 0 2rem 0.8rem;
        margin-bottom: 0.5rem;
    }

    .apiario-title::before {
        content: "❖";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        color: var(--miel-ambar);
        font-size: 1.5rem;
    }

    .apiario-title::after {
        content: "❖";
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        color: var(--miel-ambar);
        font-size: 1.5rem;
    }

    .apiario-title span {
        position: relative;
    }

    .apiario-title span::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, transparent, var(--miel-medio), var(--miel-dorado), var(--miel-medio), transparent);
        border-radius: 2px;
    }

    .apiario-subtitle {
        color: var(--texto-suave);
        font-size: 1.1rem;
        margin: 0 auto;
    }

    /* Controles de vista */
    .apiario-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
        position: relative;
    }

    .apiario-controls::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--miel-suave), var(--miel-medio), var(--miel-suave), transparent);
    }

    .view-controls {
        display: flex;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    /* Botones de panal */
    .btn-panal {
        background-color: var(--miel-medio);
        color: var(--texto-oscuro);
        border: none;
        padding: 0.7rem 1.3rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px var(--sombra-miel);
        z-index: 1;
    }

    .btn-panal::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--miel-dorado), var(--miel-ambar));
        opacity: 0;
        z-index: -1;
        transition: opacity 0.3s ease;
    }

    .btn-panal:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--sombra-miel-hover);
        color: var(--abeja-negro);
    }

    .btn-panal:hover::before {
        opacity: 1;
    }

    .btn-panal:active {
        transform: translateY(-1px);
        box-shadow: 0 3px 6px var(--sombra-miel);
    }

    /* Botón de miel */
    .btn-miel {
        background: linear-gradient(135deg, var(--miel-ambar), var(--miel-oscuro));
        color: var(--abeja-negro);
        border: none;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        border-radius: 25px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 3px 10px var(--sombra-miel);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .btn-miel:hover {
        color: white;
        box-shadow: 0 5px 15px var(--sombra-miel-hover);
        transform: translateY(-2px);
    }

    .btn-miel:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px var(--sombra-miel);
    }

    .btn-miel-submit {
        background: linear-gradient(135deg, var(--miel-oscuro), var(--panal-borde));
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 3px 10px var(--sombra-miel);
        margin-top: 1.5rem;
    }

    .btn-miel-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px var(--sombra-miel-hover);
    }

    .btn-miel-submit:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px var(--sombra-miel);
    }

    /* Panel de apiario */
    .apiario-panel {
        background-color: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--miel-suave);
        position: relative;
        overflow: hidden;
    }

    .apiario-panel::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(90deg,
                var(--miel-suave),
                var(--miel-medio),
                var(--miel-dorado),
                var(--miel-medio),
                var(--miel-suave));
    }

    /* Títulos de secciones */
    .panel-titulo {
        color: var(--miel-oscuro);
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        position: relative;
        display: inline-block;
    }

    .section-description {
        color: var(--texto-suave);
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .subtitulo-panel {
        color: var(--texto-oscuro);
        font-size: 1.4rem;
        margin: 1.5rem 0 1rem;
        position: relative;
        padding-left: 1.5rem;
    }

    .subtitulo-panel::before {
        content: "▶";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        color: var(--miel-ambar);
        font-size: 0.8rem;
    }

    /* Separador sutil */
    .separador-sutil {
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--miel-suave), var(--miel-medio), var(--miel-suave), transparent);
        margin: 2rem 0;
    }

    /* Tabla de panal */
    .tabla-panal {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 1.5rem;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .tabla-panal thead {
        background: linear-gradient(135deg, var(--miel-dorado), var(--miel-ambar));
        color: var(--abeja-negro);
    }

    .tabla-panal th {
        padding: 1rem;
        text-align: center;
        font-weight: bold;
        border-bottom: 2px solid var(--miel-oscuro);
        font-size: 0.9rem;
    }

    .tabla-panal td {
        padding: 0.8rem;
        text-align: center;
        border-bottom: 1px solid var(--miel-suave);
        transition: all 0.3s ease;
    }

    .fila-etapa {
        background-color: var(--miel-claro);
    }

    .celda-etapa {
        font-weight: bold;
        color: var(--abeja-negro);
        font-size: 1.05rem;
    }

    .fila-tarea {
        transition: all 0.3s ease;
        position: relative;
    }

    .fila-tarea:hover {
        background-color: var(--miel-suave);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        z-index: 1;
    }

    /* Checkboxes estilizados */
    .celda-check {
        width: 45px;
    }

    .check-miel {
        appearance: none;
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        background-color: white;
        border: 2px solid var(--miel-ambar);
        border-radius: 5px;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
    }

    .check-miel:checked {
        background-color: var(--miel-ambar);
        border-color: var(--miel-oscuro);
    }

    .check-miel:checked::after {
        content: "✓";
        position: absolute;
        color: white;
        font-size: 14px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .check-miel:hover {
        border-color: var(--miel-oscuro);
    }

    .check-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-weight: 500;
    }

    /* Formularios */
    .grupo-form {
        margin-bottom: 1.5rem;
    }

    .fila-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.2rem;
        padding: 1.2rem;
        background-color: var(--miel-claro);
        border-radius: 10px;
        position: relative;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        border-left: 3px solid var(--miel-medio);
    }

    .fila-form:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
        border-left-color: var(--miel-ambar);
    }

    .columna-form {
        flex: 1;
        min-width: 180px;
    }

    .etiqueta-miel {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--texto-oscuro);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .input-miel,
    .select-miel {
        width: 100%;
        padding: 0.8rem;
        border: 2px solid var(--miel-medio);
        border-radius: 6px;
        background-color: white;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .input-miel:focus,
    .select-miel:focus {
        border-color: var(--miel-ambar);
        box-shadow: 0 0 0 3px rgba(255, 202, 40, 0.2);
        outline: none;
    }

    .input-miel:hover,
    .select-miel:hover {
        border-color: var(--miel-dorado);
    }

    /* Contenedor de subtareas */
    .contenedor-subtareas {
        background-color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px dashed var(--miel-medio);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
    }

    /* Botones de acción */
    .acciones-form {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-agregar {
        background: linear-gradient(135deg, var(--cera-suave), #a1887f);
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        height: 50px;
        margin-top: 24px;
    }

    .btn-agregar:hover {
        background: linear-gradient(135deg, #a1887f, #8d6e63);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .btn-agregar:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-eliminar {
        background: linear-gradient(135deg, #ef5350, #e53935);
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        align-self: flex-end;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-eliminar:hover {
        background: linear-gradient(135deg, #e53935, #d32f2f);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .btn-eliminar:active {
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    /* Contenedor de vistas */
    .contenedor-vistas {
        background-color: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--miel-suave);
        min-height: 400px;
        position: relative;
        overflow: hidden;
    }

    .contenedor-vistas::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(90deg,
                var(--miel-suave),
                var(--miel-medio),
                var(--miel-dorado),
                var(--miel-medio),
                var(--miel-suave));
    }

    /* Secciones de tareas */
    .tareas-predefinidas,
    .tareas-personalizadas {
        margin-bottom: 2rem;
        position: relative;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .apiario-title {
            font-size: 2.2rem;
        }

        .panel-titulo {
            font-size: 1.6rem;
        }

        .apiario-panel,
        .contenedor-vistas {
            padding: 1.8rem;
        }
    }

    @media (max-width: 768px) {
        .apiario-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .view-controls {
            justify-content: center;
            margin-bottom: 1rem;
        }

        .btn-miel {
            width: 100%;
            text-align: center;
        }

        .apiario-title {
            font-size: 2rem;
            padding: 0 1.5rem 0.6rem;
        }

        .apiario-title::before,
        .apiario-title::after {
            font-size: 1.3rem;
        }

        .fila-form {
            flex-direction: column;
            padding: 1rem;
        }

        .columna-form {
            width: 100%;
            margin-bottom: 0.8rem;
        }

        .acciones-form {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-agregar,
        .btn-miel-submit {
            width: 100%;
        }
    }

    @media (max-width: 576px) {

        .apiario-panel,
        .contenedor-vistas {
            padding: 1.5rem;
            border-radius: 10px;
        }

        .apiario-title {
            font-size: 1.8rem;
        }

        .panel-titulo {
            font-size: 1.5rem;
        }

        .tabla-panal {
            font-size: 0.85rem;
        }

        .tabla-panal th,
        .tabla-panal td {
            padding: 0.7rem 0.4rem;
        }
    }

    /* Añadir estos estilos al final del archivo CSS existente */

    /* Estilos para consejos y notas sutiles */
    .tip-sutil {
        display: block;
        color: var(--texto-suave);
        font-size: 0.9rem;
        margin-top: 0.5rem;
        font-style: italic;
    }

    .consejo-sutil {
        display: block;
        color: var(--texto-suave);
        font-size: 0.85rem;
        margin: -0.5rem 0 1rem;
        font-style: italic;
    }

    .nota-sutil {
        color: var(--texto-suave);
        font-size: 0.85rem;
        margin: 0.8rem 0;
        font-style: italic;
    }

    /* Mini guía de tareas */
    .mini-guia {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.2rem;
        font-size: 0.85rem;
    }

    .guia-item {
        background-color: var(--miel-claro);
        padding: 0.4rem 0.8rem;
        border-radius: 4px;
        color: var(--texto-oscuro);
    }

    /* Guía de vistas */
    .vista-guia {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px dashed var(--miel-suave);
    }

    .vista-tip {
        color: var(--texto-oscuro);
        font-size: 0.95rem;
        margin-bottom: 0.8rem;
    }

    .vista-info {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.85rem;
    }

    .vista-info-item {
        color: var(--texto-suave);
        background-color: var(--miel-claro);
        padding: 0.3rem 0.7rem;
        border-radius: 4px;
    }

    /* Consejo final */
    .consejo-final {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px dashed var(--miel-suave);
        text-align: center;
        font-size: 0.95rem;
        color: var(--texto-suave);
        font-style: italic;
    }

    /* Responsive para los nuevos elementos */
    @media (max-width: 768px) {

        .mini-guia,
        .vista-info {
            flex-direction: column;
            gap: 0.5rem;
        }

        .guia-item,
        .vista-info-item {
            font-size: 0.8rem;
        }
    }
</style>