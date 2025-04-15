<div class="LIST" id="printableArea">
    <div class="table-responsive">
        <!-- Botón Imprimir
        <button  onclick="imprimirSoloContenido()" class="btn btn-outline-dark">
            <i class="fa fa-print"></i> Imprimir Tabla
        </button>
        -->
        <a href="{{ route('tareas.imprimirTodas') }}" target="_blank" class="btn btn-dark mb-2">
            <i class="fa fa-print"></i> Imprimir todas las tareas
        </a>
        
        <table id="subtareasTable" class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th><i class="fa-solid fa-thumbtack"></i> Nombre</th>
                    <th><i class="fa-solid fa-calendar-day"></i> Fecha Inicio</th>
                    <th><i class="fa-solid fa-calendar-check"></i> Fecha Fin</th>
                    <th><i class="fa-solid fa-bolt"></i> Prioridad</th>
                    <th><i class="fa-solid fa-check-circle"></i> Estado</th>
                    <th><i class="fa-solid fa-gears"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subtareas as $task)
                    <tr>
                        <td>{{ $task->nombre }}</td>
                        <!-- Editable Fecha Inicio -->
                        <td>
                            <input type="date" 
                                class="form-control fecha-inicio" 
                                value="{{ \Carbon\Carbon::parse($task->fecha_inicio)->format('Y-m-d') }}" 
                                data-id="{{ $task->id }}" />
                        </td>
                        <!-- Editable Fecha Fin -->
                        <td>
                            <input type="date" 
                                class="form-control fecha-fin" 
                                value="{{ \Carbon\Carbon::parse($task->fecha_limite)->format('Y-m-d') }}" 
                                data-id="{{ $task->id }}" />
                        </td>
                        <!-- Editable Prioridad -->
                        <td>
                            <select class="form-select prioridad-select prioridad" data-id="{{ $task->id }}">
                                <option value="alta" {{ $task->prioridad === 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="media" {{ $task->prioridad === 'media' ? 'selected' : '' }}>Media</option>
                                <option value="baja" {{ $task->prioridad === 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="urgente" {{ $task->prioridad === 'urgente' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </td>
                        <!-- Editable Estado -->
                        <td>
                            <select class="form-select estado-select estado" data-id="{{ $task->id }}">
                                <option value="Pendiente" {{ $task->estado === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="En progreso" {{ $task->estado === 'En progreso' ? 'selected' : '' }}>En Progreso</option>
                                <option value="Completada" {{ $task->estado === 'Completada' ? 'selected' : '' }}>Completada</option>
                                <option value="Vencida" {{ $task->estado === 'Vencida' ? 'selected' : '' }}>Vencida</option>
                            </select>
                        </td>
                    <!-- Botón Acciones -->
                        <!-- Botón Eliminar -->
                        <td>
                            <div class="btn-group" role="group" aria-label="Acciones de Tarea">
                                <!-- Botón Guardar Cambios -->
                                <button class="btn btn-success btn-sm guardar-cambios" data-id="{{ $task->id }}" 
                                    title="Guardar los cambios sobre ésta tarea.">
                                    <i class="fa-solid fa-save"></i>
                                </button>
                                <!-- Botón Eliminar -->
                                <button class="btn btn-danger btn-sm eliminar-tarea" data-id="{{ $task->id }}" 
                                title="Eliminar esta tarea">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@section('optional-scripts')
<script>
    let guardadoAutomatico = false; // Estado inicial: bloqueado

    $(document).ready(function () {
        // Personalización de Prioridad
        $(".prioridad-select").select2({
            width: '100%',
            templateResult: function (data) {
                if (!data.id) return data.text; // Retorna texto si no hay ID
                return $(`<span>${getPrioridadIcon(data.id)} ${data.text}</span>`);
            },
            templateSelection: function (data) {
                return $(`<span>${getPrioridadIcon(data.id)} ${data.text}</span>`);
            }
        });

        // Personalización de Estado
        $(".estado-select").select2({
            width: '100%',
            templateResult: function (data) {
                if (!data.id) return data.text;
                return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
            },
            templateSelection: function (data) {
                return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
            }
        });

        function getEstadoIcon(estado) {
            switch (estado) {
                case "Pendiente": return '<i class="fa fa-hourglass-start me-1 text-secondary"></i>';
                case "En progreso": return '<i class="fa fa-spinner me-1 text-primary"></i>';
                case "Completada": return '<i class="fa fa-check-circle me-1 text-success"></i>';
                case "Vencida": return '<i class="fa fa-exclamation-circle me-1 text-danger"></i>';
                default: return '<i class="fa fa-question-circle me-1 text-muted"></i>';
            }
        }
        function getPrioridadIcon(prioridad) {
            switch (prioridad) {
                case "alta": return '<i class="fa fa-flag me-1 text-warning"></i>';
                case "media": return '<i class="fa fa-flag me-1 text-info"></i>';
                case "baja": return '<i class="fa fa-flag me-1 text-success"></i>';
                case "urgente": return '<i class="fa fa-flag me-1 text-danger"></i>';
                default: return '<i class="fa fa-flag me-1 text-muted"></i>';
            }
        }

        // Guardar cambios manualmente
        $(document).on('click', '.guardar-cambios', function () {
            const taskId = $(this).data('id');
            const row = $(this).closest('tr');
            const fechaInicio = row.find('.fecha-inicio').val();
            const fechaFin = row.find('.fecha-fin').val();
            const prioridad = row.find('.prioridad').val();
            const estado = row.find('.estado').val();
            console.log("Datos a enviar:", {
                fecha_inicio: fechaInicio,
                fecha_limite: fechaFin,
                prioridad: prioridad,
                estado: estado
            });
            fetch(`/tareas/update/${taskId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    fecha_inicio: fechaInicio,
                    fecha_limite: fechaFin,
                    prioridad: prioridad,
                    estado: estado
                })
            }).then(response => {
                if (response.ok) {
                    toastr.success("Cambios guardados exitosamente.");
                } else {
                    toastr.error("Error al guardar los cambios.");
                }
            });
        });
    });
    
    // Eliminar tarea con confirmación
    $(document).on('click', '.eliminar-tarea', function () {
        const taskId = $(this).data('id');
        const buttonElement = this;

        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/tareas/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => {
                    console.log("Raw response:", res);
                    return res.json();
                })
                .then(data => {
                    console.log("Parsed JSON:", data);
                    if (data.message === 'Subtarea eliminada exitosamente.') {
                        Swal.fire('Eliminado!', 'La tarea ha sido eliminada.', 'success');
                        $(buttonElement).closest('tr').remove();
                    } else {
                        Swal.fire('Error', 'No se pudo eliminar.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar o al parsear JSON:', error);
                    Swal.fire('Error', 'Hubo un problema al eliminar la tarea.', 'error');
                });
            }
        });
    });
    // Función para imprimir solo el contenido de la tabla
    function imprimirSoloContenido() {
    // Desactivar paginación y mostrar todos los datos
    const tabla = $('#subtareasTable').DataTable();
    tabla.page.len(-1).draw(); // Mostrar todos los registros (sin paginación)
    setTimeout(() => {
        const contenido = document.getElementById("printableArea").innerHTML;
        const ventana = window.open('', '', 'height=800,width=1000');
        ventana.document.write('<html><head><title>Imprimir</title>');
        ventana.document.write('<style>button, select, .btn, .acciones-columna { display: none; }</style>');
        ventana.document.write('</head><body>');
        ventana.document.write('<h2 style="text-align:center;">Resumen de Subtareas</h2>');
        ventana.document.write(contenido);
        ventana.document.write('</body></html>');
        ventana.document.close();
        ventana.focus();
        ventana.print();
        ventana.close();
        // Restaurar paginación
        tabla.page.len(10).draw();
    }, 500); // Esperamos un poco para que se rendericen todos los registros
    }
</script>


@endsection