@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="mb-4">Tareas</h1>
    <!-- Botones para cambiar la vista -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="btn-group" role="group" aria-label="Vista de Tareas">
            <button class="btn btn-success view-toggler" data-view="list">Lista</button>
            <button class="btn btn-warning view-toggler" data-view="kanban">Tablero</button>
            <button class="btn btn-primary view-toggler" data-view="timeline">Línea de Tiempo</button>
            <button class="btn btn-secondary view-toggler" data-view="calendar">Calendario</button>
        </div>
        <!-- Botón para agregar nueva tarea -->
        <button id="toggle-form" class="btn btn-warning"><i class="fa fa-tasks"></i>Administrar tareas</button>
    </div>
    <!-- Formulario para agregar nueva tarea (colapsable) -->
    <div id="new-task-form" style="display:none;" class="mb-4">
    <div id="tareas-pre-definidas" class="contenedor-gestor-tareas">
    <form method="POST" action="{{ route('tareas.default') }}">
        @csrf
        <h3>Agregar Tareas Predefinidas</h3>
        <!-- Tabla para mostrar las tareas generales y subtareas -->
        <table class="table table-bordered table-striped mt-3 text-center">
            <thead class="table-dark">
                <tr>
                    <th class="checkbox-header" style="width: 10px">
                        <label for="select-all-subtasks" class="checkbox-label">
                            <input type="checkbox" id="select-all-subtasks" />
                            <span>Seleccionar Todo</span>
                        </label>
                    </th>
                    <th>Etapa</th>
                    <th><i class="fa-solid fa-thumbtack"></i> Nombre</th>
                    <th style="width: 130px;"><i class="fa-solid fa-calendar-day"></i> Fecha Inicio</th>
                    <th style="width: 130px;"><i class="fa-solid fa-calendar-check"></i> Fecha Fin</th>
                    <th style="width: 130px;"><i class="fa-solid fa-bolt"></i> Prioridad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaEtapa as $tareaGeneral)
                    <!-- Mostrar la tarea general solo una vez -->
                    <tr>
                        <td colspan="1"></td>
                        <td class="font-weight-bold" colspan="1" style="width: 14px"> <strong>{{ $tareaGeneral->nombre }}</strong> </td> <!-- Solo una vez -->
                        <td colspan="4"></td> <!-- Vaciar las otras columnas para que se vea la tarea general -->
                    </tr>
                    @foreach ($tareaGeneral->predefinidas as $subtarea)
                        <tr>
                            <td class="checkbox-cell" style="width: 10px">
                                <input 
                                    type="checkbox" 
                                    name="subtareas[]" 
                                    value="{{ $subtarea->id }}" 
                                    class="subtask-checkbox">
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
        <button type="submit" class="btn btn-primary">Agregar Subtareas Seleccionadas</button>
    </form>
</div>

<form action="{{ route('tareas.store') }}" method="POST">
    @csrf
    <h3>Crear Tareas Personalizadas</h3>
    <!-- Sección para la Tarea General -->
    <div class="form-group">
        <label for="tarea_general_id">Etapa</label>
        <select name="tarea_general_id" id="tarea_general_id" class="form-control" required>
            <option value="" disabled selected>Seleccione una Etapa</option>
            @foreach($listaEtapa as $tarea)
                <option value="{{ $tarea->id }}">{{ $tarea->nombre }}</option>
            @endforeach
        </select>
    </div>
        <!-- Sección para Subtareas -->
            <div id="subtareas-container">
                <h4>Tareas</h4>
                <div class="subtarea" id="subtarea-template" style="display: none;">
                    <div class="form-row align-items-center">
                        <div class="col-md-2">
                            <label>Prioridad</label>
                            <select data-field="prioridad" class="form-control">
                                <option value="no-prioritaria">No Prioritaria</option>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Título de la tarea</label>
                            <input type="text" data-field="nombre" class="form-control" placeholder="Ejemplo: Limpiar la zona">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Inicio</label>
                            <input type="date" data-field="fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Fin</label>
                            <input type="date" data-field="fecha_fin" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Estado</label>
                            <select data-field="estado" class="form-control">
                                <option value="Pendiente">Pendiente</option>
                                <option value="En progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                                <option value="Vencida">Vencida</option>
                            </select>
                        </div>
                        <button type="button" class="remove-subtarea">Eliminar</button>
                    </div>
                    <hr>
                </div>
            </div>
            <button type="button" id="add-subtarea" class="btn btn-secondary">Agregar Tarea</button>
            <!-- Botón de Envío -->
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">Guardar Tareas</button>
            </div>
        </div>
</form>
    

    <!-- Contenedor donde se cargan las vistas dinámicas -->
    <div id="task-view-container">
        <div class="view list active">
            @include('tareas.list')
        </div>
        <div class="view kanban ">
            @include('tareas.kanban')
        </div>
        <div class="view timeline">
            @include('tareas.timeline')
        </div>
        <div class="view calendar">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<script>

    function actualizarKanban() {

    }

    


document.addEventListener('DOMContentLoaded', function() {
    // Obtén los elementos
    const selectAllCheckbox = document.getElementById('select-all-subtasks');
    const subtasksCheckboxes = document.querySelectorAll('.subtask-checkbox');
    // Función para verificar si todos los checkboxes están seleccionados
    function updateSelectAllCheckbox() {
        const allChecked = Array.from(subtasksCheckboxes).every(checkbox => checkbox.checked);
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = !allChecked && Array.from(subtasksCheckboxes).some(checkbox => checkbox.checked);
    }
    // Maneja el cambio en el checkbox "Seleccionar todo"
    selectAllCheckbox.addEventListener('change', function () {
        const isChecked = selectAllCheckbox.checked;
        subtasksCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    });
    // Escuchar cambios en las subtareas
    subtasksCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllCheckbox);
    });
    // Inicializar el estado del "Seleccionar todo"
    updateSelectAllCheckbox();

document.body.addEventListener('click', (event) => {
    console.log("clicando algún event");
        const target = event.target.closest('.estado-badge');
        if (target) {
            console.log("Badge clickeado");
            currentButton = target;
            const subtareaId = currentButton.dataset.id;
            const currentState = currentButton.dataset.currentState;

            document.getElementById('subtareaId').value = subtareaId;
            document.getElementById('nuevoEstado').value = currentState;

            estadoModal.show();
        }
    });
        // Confirmar el cambio de estado
        document.getElementById('confirmarEstado').addEventListener('click', () => {
            const subtareaId = document.getElementById('subtareaId').value;
            const nuevoEstado = document.getElementById('nuevoEstado').value;

            fetch(`/tareas/${subtareaId}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ estado: nuevoEstado })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentButton.textContent = nuevoEstado;
                    currentButton.dataset.currentState = nuevoEstado;
                    Swal.fire('¡Actualizado!', 'El estado de la subtarea ha sido actualizado.', 'success');
                } else {
                    Swal.fire('Error', 'Hubo un problema al actualizar el estado.', 'error');
                }
                estadoModal.hide();
            });
        });
    });

let subtareaIndex = 0; // Contador para los índices de subtareas

document.getElementById('add-subtarea').addEventListener('click', function () {
    const template = document.getElementById('subtarea-template');
    const container = document.getElementById('subtareas-container');
    const newSubtarea = template.cloneNode(true);

    newSubtarea.style.display = 'block';
    newSubtarea.removeAttribute('id');

    // Actualizar los nombres de los inputs para que incluyan el índice
    const inputs = newSubtarea.querySelectorAll('input, select');
    inputs.forEach(input => {
        const fieldName = input.getAttribute('data-field'); // Usa un atributo para identificar el campo
        if (fieldName) {
            input.name = `subtareas[${subtareaIndex}][${fieldName}]`;// Genera el nombre dinámico
        }
        input.required = true; // Agrega required donde sea necesario
    });

    container.appendChild(newSubtarea);
    subtareaIndex++; // Incrementa el índice para la siguiente subtarea
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-subtarea')) {
            const subtarea = e.target.closest('.subtarea');
            subtarea.remove();
        }
    });
    // Mostrar la vista por defecto (Kanban)
    const views = document.querySelectorAll('.view');
    views.forEach(view => view.classList.remove('active'));
    document.querySelector('.view.list').classList.add('active');

    // Ocultar todas las vistas
    const hideAllViews = () => {
        views.forEach(view => {
            if (!view.classList.contains('active')) {
            view.style.display = 'none';
        }
        });
    };
    // Inicialmente, ocultamos todas las vistas
    hideAllViews();

    // Cambiar entre vistas
    document.querySelectorAll('.view-toggler').forEach(button => {
        button.addEventListener('click', function () {
            const view = this.getAttribute('data-view');
            views.forEach(v => v.classList.remove('active'));
            document.querySelector(`.view.${view}`).classList.add('active');
            hideAllViews();
            const viewToShow= document.querySelector(`.view.${view}`);
            if (viewToShow) {
                viewToShow.style.display = '';
            }
            // Si se selecciona la vista de calendario, inicializar FullCalendar
            if (view === 'calendar') {
                initializeCalendar();
            }
        });
    });

    // Inicializar FullCalendar
    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        console.log("Initializing calendar", calendarEl); // Verifica si el contenedor se está encontrando
        if (calendarEl && !calendarEl.classList.contains('initialized')) {
            calendarEl.classList.add('initialized');
            new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    @foreach($subtareas as $task)
                    {
                        id: '{{ $task->id }}',
                        title: '{{ $task->nombre }}',
                        start: '{{ $task->fecha_inicio }}',
                        end: '{{ $task->fecha_fin }}',
                        extendedProps: {
                            descripcion: '{{ $task->descripcion }}',
                            tareaGeneral: '{{ $task->tareaGeneral->nombre }}',
                            estado: '{{ $task->estado }}',
                            prioridad: '{{ ucfirst($task->prioridad) }}'
                        }
                    },
                    @endforeach
                ],
                eventClick: function (info) {
                    const evento = info.event;
                    document.getElementById('task-title').textContent = evento.title;
                    document.getElementById('task-general').textContent = evento.extendedProps.tareaGeneral;
                    document.getElementById('task-description').textContent = evento.extendedProps.descripcion;
                    document.getElementById('task-status').textContent = evento.extendedProps.estado;
                    document.getElementById('task-priority').textContent = evento.extendedProps.prioridad;
                    const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
                    taskModal.show();
                }
            }).render();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var quill;
        // Mostrar/ocultar el formulario y asegurar que el editor esté inicializado
        document.getElementById('toggle-form').addEventListener('click', function () {
            const form = document.getElementById('new-task-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
        // Asegurar que el contenido del editor esté sincronizado al enviar el formulario
        document.querySelector('form').addEventListener('submit', function () {
            if (quill) {
                document.querySelector('#description').value = quill.root.innerHTML;
            }
        });
        // Escuchar el evento 'subtareaActualizada' y recargar las subtareas
        escucharEvento('subtareaActualizada', () => {
            recargarSubtareas(); // Esto actualizará la lista de subtareas
            actualizarKanban(); // Actualizar el tablero Kanban
            actualizarTimeline(); // Actualizar la línea de tiempo
            initializeCalendar(); // Actualizar el calendario
        });
        
    });

    function emitirEvento(nombre, detalle = {}) {
        const evento = new CustomEvent(nombre, { detail: detalle });
        window.eventBus.dispatchEvent(evento);
    }

    function escucharEvento(nombre, callback) {
        window.eventBus.addEventListener(nombre, callback);
    }
</script>
@endsection
@section('optional-scripts')
<script src="{{ asset('vendor/chatbot/js/chatbot.js') }}"></script>
@endsection