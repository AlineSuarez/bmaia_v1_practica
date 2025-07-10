<head>
    <link href="{{ asset('./css/components/home-user/tasks/timeline.css') }}" rel="stylesheet">
</head>

<div class="task-list-container">
    <!-- Header con título y subtítulo -->
    <div class="task-list-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="header-title">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Línea de tiempo
                </h1>
                <p class="header-subtitle">Visualiza el avance y estado de tus tareas por etapas</p>
            </div>
        </div>
    </div>

    <!-- Contenido principal de la línea de tiempo -->
    <div class="tareas-timeline-wrapper">
        <div class="timeline-container">
            <div class="etapas-grid">
                @foreach ($tareasGenerales as $tareaGeneral)
                                                    @php
                    $subtareas = $tareaGeneral->subtareas;
                    $completadas = $subtareas->where('estado', 'Completada')->count();
                    $total = $subtareas->count();
                    $progreso = $total > 0 ? round(($completadas / $total) * 100) : 0;
                                                    @endphp

                    <div class="etapa-card fade-in">
                        <!-- Header de la etapa -->
                        <div class="etapa-header">
                            <div class="etapa-title">
                                <div class="etapa-info">
                                    <h3>{{ $tareaGeneral->nombre }}</h3>
                                </div>
                            </div>

                            <div class="etapa-stats">
                                <p class="etapa-subtitle">
                                <div class="progress-container">
                                    <span class="progress-text">{{ $progreso }}%</span>
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" style="width: {{ $progreso }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido de tareas -->
                        <div class="etapa-content">
                            @forelse ($subtareas as $subtarea)
                                <div class="task-mini-card {{ $subtarea->estado === 'Completada' ? 'completed' : '' }}"
                                    data-task-id="{{ $subtarea->id }}">

                                    <div class="task-header">
                                        <input type="checkbox" class="task-checkbox toggle-completada" data-id="{{ $subtarea->id }}"
                                            {{ $subtarea->estado === 'Completada' ? 'checked' : '' }}>

                                        <div class="task-name {{ $subtarea->estado === 'Completada' ? 'completed' : '' }}">
                                            {{ $subtarea->nombre }}
                                        </div>
                                    </div>

                                    <div class="task-meta">
                                        <div class="task-badges">
                                            <span class="prioridad-badge" data-prioridad="{{ strtolower($subtarea->prioridad) }}">
                                                {{ ucfirst($subtarea->prioridad) }}
                                            </span>

                                            <span class="estado-badge estado-{{ strtolower(str_replace(' ', '', $subtarea->estado)) }}"
                                                data-id="{{ $subtarea->id }}" data-current-state="{{ $subtarea->estado }}">
                                                {{ $subtarea->estado }}
                                            </span>
                                        </div>

                                        @if ($subtarea->fecha_inicio || $subtarea->fecha_fin)
                                            <div class="task-dates">
                                                @if ($subtarea->fecha_inicio)
                                                    <div class="date-item">
                                                        <span>{{ \Carbon\Carbon::parse($subtarea->fecha_inicio)->format('d/m/Y') }}</span>
                                                    </div>
                                                @endif
                                                @if ($subtarea->fecha_fin)
                                                    <div class="date-item">
                                                        <span>{{ \Carbon\Carbon::parse($subtarea->fecha_fin)->format('d/m/Y') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="task-mini-card">
                                    <div class="task-header">
                                        <div class="task-name" style="color: var(--text-muted); font-style: italic;">
                                            No hay tareas en esta etapa
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer de la etapa -->
                        <div class="etapa-footer">
                            <div class="etapa-summary">
                                @if ($total > 0)
                                    {{ $completadas }} de {{ $total }} completadas
                                @else
                                    Sin tareas asignadas
                                @endif
                            </div>

                            <div class="etapa-actions">
                                <button class="btn-etapa deshacer-seleccionadas-btn" data-etapa-id="{{ $tareaGeneral->id }}" disabled>
                                    ⎌ Deshacer (<span class="deshacer-count">0</span>)
                                </button>
                                <button class="btn-etapa completar-seleccionadas-btn" data-etapa-id="{{ $tareaGeneral->id }}" disabled>
                                    ✓ Completar (<span class="completar-count">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div> <!-- Cierre de .task-list-container -->

<div id="tareas-success-toast"
    style="display:none;position:fixed;top:30px;right:30px;z-index:9999;background:#4caf50;color:#fff;padding:16px 24px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.15);font-size:16px;">
    ¡Tareas completadas!
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Función para actualizar el estado de una tarea
        function actualizarEstadoTarea(subtareaId, nuevoEstado) {
            const taskCard = document.querySelector(`[data-task-id="${subtareaId}"]`);
            if (taskCard) {
                taskCard.classList.add('updating');
            }

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
                        // Actualizar la interfaz
                        actualizarInterfazTarea(subtareaId, nuevoEstado);
                        // Recalcular progreso de la etapa
                        recalcularProgreso();
                    } else {
                        console.error('Error al actualizar el estado:', data.message);
                        if (taskCard) {
                            taskCard.classList.add('error');
                            setTimeout(() => taskCard.classList.remove('error'), 2000);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error en la petición:', error);
                    if (taskCard) {
                        taskCard.classList.add('error');
                        setTimeout(() => taskCard.classList.remove('error'), 2000);
                    }
                })
                .finally(() => {
                    if (taskCard) {
                        taskCard.classList.remove('updating');
                    }
                });
        }

        // Función para actualizar la interfaz de una tarea
        function actualizarInterfazTarea(subtareaId, nuevoEstado) {
            const taskCard = document.querySelector(`[data-task-id="${subtareaId}"]`);
            if (!taskCard) return; // Si no existe, sal

            const checkbox = taskCard.querySelector('.toggle-completada');
            if (!checkbox) return; // Si no existe, sal

            const taskName = taskCard.querySelector('.task-name');
            const estadoBadge = taskCard.querySelector('.estado-badge');

            // Actualizar checkbox
            checkbox.checked = nuevoEstado === 'Completada';

            // Actualizar clases de completado
            if (nuevoEstado === 'Completada') {
                taskCard.classList.add('completed');
                if (taskName) taskName.classList.add('completed');
            } else {
                taskCard.classList.remove('completed');
                if (taskName) taskName.classList.remove('completed');
                checkbox.disabled = false;
            }

            // Actualizar badge de estado
            if (estadoBadge) {
                estadoBadge.textContent = nuevoEstado;
                estadoBadge.className = `estado-badge estado-${nuevoEstado.toLowerCase().replace(' ', '')}`;
                estadoBadge.setAttribute('data-current-state', nuevoEstado);
            }
        }

        // Función para recalcular el progreso de todas las etapas
        function recalcularProgreso() {
            document.querySelectorAll('.etapa-card').forEach(etapaCard => {
                const totalTasks = etapaCard.querySelectorAll('.task-mini-card').length;
                const completedTasks = etapaCard.querySelectorAll('.task-mini-card.completed').length;
                const progreso = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;

                // Actualizar barra de progreso
                const progressFill = etapaCard.querySelector('.progress-fill');
                const progressText = etapaCard.querySelector('.progress-text');
                const etapaSummary = etapaCard.querySelector('.etapa-summary');
                const etapaSubtitle = etapaCard.querySelector('.etapa-subtitle');

                if (progressFill) progressFill.style.width = `${progreso}%`;
                if (progressText) progressText.textContent = `${progreso}%`;
                if (etapaSummary) etapaSummary.textContent = `${completedTasks} de ${totalTasks} completadas`;
            });
        }

        // Acción para "Completar seleccionadas"
        document.querySelectorAll('.completar-seleccionadas-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const etapaCard = this.closest('.etapa-card');
                const checkboxes = etapaCard.querySelectorAll('.toggle-completada:checked:not(:disabled)');
                const tareasACompletar = Array.from(checkboxes).filter(checkbox => {
                    const taskCard = checkbox.closest('.task-mini-card');
                    return !taskCard.classList.contains('completed');
                });

                if (tareasACompletar.length === 0) {
                    alert('No hay tareas seleccionadas para completar.');
                    return;
                }

                // Animación: ocultar el botón original
                this.classList.add('fade-out');
                setTimeout(() => {
                    this.style.display = 'none';

                    // Crear botón de cancelar
                    const btnCancelar = document.createElement('button');
                    btnCancelar.className = 'btn-etapa cancelar fade-in';
                    btnCancelar.textContent = 'x Cancelar';

                    // Crear botón de confirmar
                    const btnConfirmar = document.createElement('button');
                    btnConfirmar.className = 'btn-etapa confirmar fade-in';
                    btnConfirmar.textContent = '¿Confirmar selección?';

                    // Insertar botones: primero cancelar, luego confirmar
                    const actions = this.parentElement;

                    // OCULTAR el botón de deshacer mientras se confirma
                    const btnDeshacer = actions.querySelector('.deshacer-seleccionadas-btn');
                    if (btnDeshacer) {
                        btnDeshacer.style.display = 'none';
                    }

                    actions.appendChild(btnCancelar);
                    actions.appendChild(btnConfirmar);

                    // Confirmar
                    btnConfirmar.addEventListener('click', () => {
                        tareasACompletar.forEach(checkbox => {
                            const subtareaId = checkbox.dataset.id;
                            actualizarEstadoTarea(subtareaId, 'Completada');
                        });

                        // Cambiar texto y animar el botón de confirmar
                        btnConfirmar.textContent = '¡Tarea(s) completada(s)!';
                        btnConfirmar.classList.remove('confirmar');
                        btnConfirmar.classList.add('completado');
                        btnConfirmar.disabled = true;

                        // OCULTAR o DESHABILITAR el botón de deshacer
                        const btnDeshacer = btn.parentElement.querySelector('.deshacer-seleccionadas-btn');
                        if (btnDeshacer) {
                            btnDeshacer.style.display = 'none'; // O usa: btnDeshacer.disabled = true;
                        }

                        // Opcional: animación de color para feedback
                        btnConfirmar.style.background = '#4caf50';
                        btnConfirmar.style.color = '#fff';

                        // Restaurar el botón original o recargar después de un tiempo
                        setTimeout(() => {
                            location.reload();
                        }, 1200);
                    });

                    // Cancelar
                    btnCancelar.addEventListener('click', () => {
                        btnConfirmar.classList.remove('fade-in');
                        btnCancelar.classList.remove('fade-in');
                        btnConfirmar.classList.add('fade-out');
                        btnCancelar.classList.add('fade-out');
                        setTimeout(() => {
                            btnConfirmar.remove();
                            btnCancelar.remove();
                            btn.style.display = '';
                            btn.classList.remove('fade-out');
                            btn.classList.add('fade-in');
                            setTimeout(() => btn.classList.remove('fade-in'), 250);

                            // MOSTRAR de nuevo el botón de deshacer
                            if (btnDeshacer) {
                                btnDeshacer.style.display = '';
                            }
                        }, 250);
                    });
                }, 250);
            });
        });

        document.querySelectorAll('.etapa-card').forEach(etapaCard => {
            const btnDeshacer = etapaCard.querySelector('.deshacer-seleccionadas-btn');
            const deshacerCount = btnDeshacer.querySelector('.deshacer-count');
            const btnCompletar = etapaCard.querySelector('.completar-seleccionadas-btn');
            const completarCount = btnCompletar.querySelector('.completar-count');
            const checkboxes = etapaCard.querySelectorAll('.toggle-completada');

            // Permitir seleccionar tareas completadas para deshacer
            checkboxes.forEach(checkbox => {
                const taskCard = checkbox.closest('.task-mini-card');
                if (taskCard.classList.contains('completed')) {
                    checkbox.disabled = false;
                }
            });

            function actualizarBotones() {
                // Deshacer: tareas completadas desmarcadas
                const desmarcadas = Array.from(checkboxes).filter(checkbox => {
                    const taskCard = checkbox.closest('.task-mini-card');
                    return taskCard.classList.contains('completed') && !checkbox.checked;
                });
                deshacerCount.textContent = desmarcadas.length;
                btnDeshacer.disabled = desmarcadas.length === 0;

                // Completar: tareas NO completadas marcadas
                const seleccionadas = Array.from(checkboxes).filter(checkbox => {
                    const taskCard = checkbox.closest('.task-mini-card');
                    return !taskCard.classList.contains('completed') && checkbox.checked;
                });
                completarCount.textContent = seleccionadas.length;
                btnCompletar.disabled = seleccionadas.length === 0;
            }

            // Actualiza los botones cada vez que cambia un checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', actualizarBotones);
            });

            // Inicializa el estado de los botones al cargar
            actualizarBotones();

            // Acción al hacer click en "Deshacer"
            btnDeshacer.addEventListener('click', function () {
                const desmarcadas = Array.from(checkboxes).filter(checkbox => {
                    const taskCard = checkbox.closest('.task-mini-card');
                    return taskCard.classList.contains('completed') && !checkbox.checked;
                });
                if (desmarcadas.length === 0) return;
                desmarcadas.forEach(checkbox => {
                    const subtareaId = checkbox.dataset.id;
                    actualizarEstadoTarea(subtareaId, 'Pendiente');
                });
                // Feedback visual
                btnDeshacer.textContent = 'Tarea quitada';
                btnDeshacer.disabled = true;
                setTimeout(() => location.reload(), 1200);
            });
        });
    });

    // Funciones para los botones de acción
    function verDetallesEtapa(etapaId) {
        console.log('Ver detalles de etapa:', etapaId);
    }

    function agregarTareaEtapa(etapaId) {
        console.log('Agregar tarea a etapa:', etapaId);
    }
</script>