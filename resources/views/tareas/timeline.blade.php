<style>
    /* Estilos generales */
    .prioridad-badge {
        padding: 0.2rem 0.4rem;
        border-radius: 1.5rem;
        font-size: 0.75rem;
    }
    .prioridad-badge[data-prioridad="alta"] { background-color: #ffc107; color: #212529; }
    .prioridad-badge[data-prioridad="media"] { background-color: #0dcaf0; color: #fff; }
    .prioridad-badge[data-prioridad="baja"] { background-color: #20c997; color: #fff; }
    .prioridad-badge[data-prioridad="urgente"] { background-color: #dc3545; color: #fff; }

    .estado-indicador {
        padding: 0.2rem 0.5rem;
        border-radius: 1.5rem;
        font-size: 0.75rem;
    }
    .estado-pendiente { background-color: #ffc107; color: #000; }
    .estado-en { background-color: #0d6efd; color: #fff; }
    .estado-completada { background-color: #198754; color: #fff; }
    .estado-vencida { background-color: #dc3545; color: #fff; }

    /* Contenedor principal */
    .timeline-container {
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        overflow: hidden;
    }

    .timeline-item {
        border-left: 4px solid #0d6efd;
        padding-left: 15px;
        margin-bottom: 15px;
        position: relative;
        background: #fff;
        border-radius: 8px;
        padding: 12px;
    }

    .timeline-item .timeline-date {
        font-weight: bold;
    }

    .timeline-item .timeline-status {
        margin-top: 8px;
        font-size: 0.8rem;
    }

    .timeline-item button {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    /* Estilo responsive para pantallas pequeñas */
    @media (max-width: 768px) {
        .timeline-item {
            padding: 10px;
            margin-bottom: 10px;
        }

        .timeline-container {
            padding: 15px;
        }

        .timeline-item .timeline-date {
            font-size: 0.9rem;
        }

        .timeline-item .timeline-status {
            font-size: 0.75rem;
        }

        .timeline-item button {
            font-size: 0.8rem;
        }
    }

    /* Estilos para los items de la timeline */
    .timeline-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        border-left: 4px solid #0d6efd;
        padding-left: 10px;
        padding-right: 10px;
    }

    .timeline-item .fw-medium {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .timeline-item .estado-indicador {
        margin-left: 10px;
    }
</style>

<div class="timeline-container p-4">
    <div class="accordion" id="timelineAccordion">
        @foreach ($tareasGenerales as $tareaGeneral)
            @php
                $subtareas = $tareaGeneral->subtareas;
                $completadas = $subtareas->where('estado', 'Completada')->count();
                $total = $subtareas->count();
                $progreso = $total > 0 ? round(($completadas / $total) * 100) : 0;
            @endphp

            <div class="card mb-3 border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center rounded-top-4">
                    <div class="d-flex align-items-center gap-3" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#etapa{{ $tareaGeneral->id }}">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <!-- Espacio para imagen -->
                        </div>
                        <div>
                            <h5 class="mb-1 text-dark fw-semibold">{{ $tareaGeneral->nombre }}</h5>
                            <small class="text-muted">{{ $total }} tareas - Progreso: {{ $progreso }}%</small>
                        </div>
                    </div>
                    <div>
                        <div class="progress" style="width: 120px; height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progreso }}%"></div>
                        </div>
                    </div>
                </div>

                <div id="etapa{{ $tareaGeneral->id }}" class="collapse">
                    <ul class="list-group list-group-flush">
                        @foreach ($subtareas as $subtarea)
                            <li class="timeline-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="checkbox" class="form-check-input toggle-completada" data-id="{{ $subtarea->id }}" {{ $subtarea->estado === 'Completada' ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-medium">{{ $subtarea->nombre }}</div>
                                        <div class="text-muted small">
                                            <span class="badge prioridad-badge" data-prioridad="{{ $subtarea->prioridad }}">{{ ucfirst($subtarea->prioridad) }}</span>
                                            <span class="estado-indicador estado-{{ strtolower($subtarea->estado) }} ms-2">{{ $subtarea->estado }}</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-info estado-badge" data-id="{{ $subtarea->id }}" data-current-state="{{ $subtarea->estado }}">
                                        {{ $subtarea->estado }}
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-completada').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const subtareaId = this.dataset.id;
                const checked = this.checked;

                fetch(`/tareas/${subtareaId}/update-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ estado: checked ? 'Completada' : 'Pendiente' })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        recargarTimeline(); // Recargar el timeline al actualizar el estado
                    } else {
                        alert('Error al actualizar el estado');
                    }
                });
            });
        });
    });

    function recargarTimeline() {
        fetch('/datos-subtareas') // Obtener las subtareas desde el backend
            .then(response => response.json())
            .then(data => {
                const timelineContainer = document.querySelector('.timeline-container');
                timelineContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevas tareas
                data.forEach(task => {
                    const taskElement = document.createElement('div');
                    taskElement.className = 'timeline-item';
                    taskElement.innerHTML = `
                        <div class="timeline-date">
                            <strong>${task.nombre}</strong>
                            <p>Inicio: ${task.fecha_inicio}</p>
                            <p>Fin: ${task.fecha_fin}</p>
                        </div>
                        <div class="timeline-status">
                            <span class="badge badge-${task.estado === 'Completada' ? 'success' : task.estado === 'En progreso' ? 'info' : 'warning'}">
                                ${task.estado}
                            </span>
                        </div>
                    `;
                    timelineContainer.appendChild(taskElement);
                });
            })
            .catch(error => console.error('Error al cargar las subtareas:', error));
    }
</script>