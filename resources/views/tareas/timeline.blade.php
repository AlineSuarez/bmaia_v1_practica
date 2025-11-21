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

                    <div class="etapa-card fade-in collapsed">
                        <!-- Header de la etapa -->
                        <div class="etapa-header" style="cursor:pointer;">
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
                        <div class="etapa-content" style="display:none;">
                            @forelse ($subtareas as $subtarea)
                                <div class="task-mini-card {{ $subtarea->estado === 'Completada' ? 'completed' : '' }}"
                                    data-task-id="{{ $subtarea->id }}">

                                    <div class="task-header">
                                        <div class="task-checkbox-placeholder" aria-hidden="true"></div>

                                        <div class="task-name">
                                            {{ $subtarea->nombre }}
                                        </div>
                                        <div class="task-meta">
                                            <div class="task-badges">
                                                <span class="prioridad-badge" data-prioridad="{{ strtolower($subtarea->prioridad) }}">
                                                    <span class="prio"><span class="prio-dot" aria-hidden="true"></span>{{ ucfirst($subtarea->prioridad) }}</span>
                                                </span>

                                                <select class="estado-badge estado-{{ strtolower(str_replace(' ', '', $subtarea->estado)) }}"
                                                    data-id="{{ $subtarea->id }}" data-current-state="{{ $subtarea->estado }}" aria-label="Cambiar estado">
                                                    <option value="Pendiente" {{ $subtarea->estado === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                    <option value="En progreso" {{ $subtarea->estado === 'En progreso' ? 'selected' : '' }}>En progreso</option>
                                                    <option value="Completada" {{ $subtarea->estado === 'Completada' ? 'selected' : '' }}>Completada</option>
                                                </select>
                                            </div>

                                            @if ($subtarea->fecha_inicio || $subtarea->fecha_limite)
                                                <div class="task-dates">
                                                    @if ($subtarea->fecha_inicio)
                                                        <div class="date-item">
                                                            <span> Fecha de Inicio: {{ \Carbon\Carbon::parse($subtarea->fecha_inicio)->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                    @if ($subtarea->fecha_limite)
                                                    <!-- │ -->
                                                        <div class="date-item">
                                                            <span> Fecha Límite: {{ \Carbon\Carbon::parse($subtarea->fecha_limite)->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>  
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
                        <div class="etapa-footer" style="display:none;">
                            <div class="etapa-summary">
                                @if ($total > 0)
                                    {{ $completadas }} de {{ $total }} completadas
                                @else
                                    Sin tareas asignadas
                                @endif
                            </div>

                            <div class="etapa-actions">
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
    style="display:none;position:fixed;top:30px;right:30px;z-index:9999;background:#4caf50;color:#fff;padding: 16px;px 24px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.15);font-size:16px;">
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

            return fetch(`/tareas/${subtareaId}/update-status`, {
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
                    return data;
                })
                .catch(error => {
                    console.error('Error en la petición:', error);
                    if (taskCard) {
                        taskCard.classList.add('error');
                        setTimeout(() => taskCard.classList.remove('error'), 2000);
                    }
                    throw error;
                })
                .finally(() => {
                    if (taskCard) {
                        taskCard.classList.remove('updating');
                        // Re-enable the estado select if present
                        const sel = taskCard.querySelector('.estado-badge');
                        if (sel && sel.tagName === 'SELECT') sel.disabled = false;
                    }
                });
        }

        // Función para actualizar la interfaz de una tarea
        function actualizarInterfazTarea(subtareaId, nuevoEstado) {
            const taskCard = document.querySelector(`[data-task-id="${subtareaId}"]`);
            if (!taskCard) return; // Si no existe, sal

            const checkbox = taskCard.querySelector('.toggle-completada');

            const taskName = taskCard.querySelector('.task-name');
            const estadoBadge = taskCard.querySelector('.estado-badge');

            // Actualizar checkbox (si existe)
            if (checkbox) checkbox.checked = nuevoEstado === 'Completada';

            // Actualizar clases de completado
            if (nuevoEstado === 'Completada') {
                taskCard.classList.add('completed');
            } else {
                taskCard.classList.remove('completed');
                if (checkbox) checkbox.disabled = false;
            }

            // Actualizar badge/select de estado
            if (estadoBadge) {
                if (estadoBadge.tagName === 'SELECT') {
                    estadoBadge.value = nuevoEstado;
                } else {
                    estadoBadge.textContent = nuevoEstado;
                }
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

        

        document.querySelectorAll('.etapa-card').forEach(etapaCard => {
            const btnCompletar = etapaCard.querySelector('.completar-seleccionadas-btn');
            const selects = etapaCard.querySelectorAll('.estado-badge');

            // Inicializar: permitir foco/uso en selects
            selects.forEach(sel => sel.disabled = false);

            function actualizarBotones() {
                // Completar / Aplicar cambios: selects cuyo valor difiere del original
                const cambios = Array.from(selects).filter(sel => {
                    const original = sel.getAttribute('data-current-state');
                    return sel.value !== original;
                });

                // Construir el contenido del botón sin perder el span de conteo
                if (cambios.length > 0) {
                    const allToComplete = cambios.every(s => s.value === 'Completada');
                    const label = allToComplete ? '✓ Completar' : 'Aplicar cambios';
                    btnCompletar.disabled = false;
                    btnCompletar.innerHTML = `${label} (<span class="completar-count">${cambios.length}</span>)`;
                } else {
                    btnCompletar.disabled = true;
                    btnCompletar.innerHTML = '✓ Completar (<span class="completar-count">0</span>)';
                }
            }

            // Actualiza los botones cada vez que cambia un select
            selects.forEach(sel => {
                sel.addEventListener('change', actualizarBotones);
            });

            // Inicializa el estado de los botones al cargar
            actualizarBotones();

            

            // Acción al hacer click en "Completar / Aplicar cambios"
            btnCompletar.addEventListener('click', function (e) {
                e.preventDefault();
                // Guardar referencia al botón original para poder restaurarlo al cancelar
                const originalBtn = this;
                // Recolectar selects con cambios
                const cambios = Array.from(selects).filter(sel => sel.value !== sel.getAttribute('data-current-state'));
                if (cambios.length === 0) {
                    alert('No hay tareas seleccionadas para completar.');
                    return;
                }

                // Animación: ocultar el botón original
                originalBtn.classList.add('fade-out');
                setTimeout(() => {
                    originalBtn.style.display = 'none';

                    // Crear botón de cancelar
                    const btnCancelar = document.createElement('button');
                    btnCancelar.className = 'btn-etapa cancelar fade-in';
                    btnCancelar.textContent = 'x Cancelar';

                    // Crear botón de confirmar
                    const btnConfirmar = document.createElement('button');
                    btnConfirmar.className = 'btn-etapa confirmar fade-in';
                    btnConfirmar.textContent = `¿Confirmar cambios (${cambios.length})?`;

                    const actions = this.parentElement;

                    actions.appendChild(btnCancelar);
                    actions.appendChild(btnConfirmar);

                    // Confirmar
                    btnConfirmar.addEventListener('click', () => {
                        // Ejecutar todas las peticiones y esperar a que terminen
                        const promises = cambios.map(sel => {
                            const subtareaId = sel.getAttribute('data-id');
                            const nuevoEstado = sel.value;
                            // marcar tarjeta como updating para feedback
                            const card = document.querySelector(`[data-task-id="${subtareaId}"]`);
                            if (card) card.classList.add('updating');
                            return actualizarEstadoTarea(subtareaId, nuevoEstado).catch(err => ({ success: false, error: err }));
                        });

                        // Cambiar texto y bloquear el botón mientras se ejecutan
                        btnConfirmar.textContent = 'Procesando...';
                        btnConfirmar.classList.remove('confirmar');
                        btnConfirmar.disabled = true;

                        Promise.all(promises).then(results => {
                            // Después de aplicar todos los cambios recargar para mostrar el estado real
                            location.reload();
                        }).catch(() => {
                            // En caso de error, recargar también para intentar mostrar el estado real y limpiar UI
                            location.reload();
                        });
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
                            // Restaurar el botón original
                            originalBtn.style.display = '';
                            originalBtn.classList.remove('fade-out');
                            originalBtn.classList.add('fade-in');
                            setTimeout(() => originalBtn.classList.remove('fade-in'), 250);

                            // no hay botón 'Deshacer' — nada que mostrar
                            // Recalcular botones (en caso de que selects hayan cambiado)
                            actualizarBotones();
                        }, 250);
                    });
                }, 250);
            });
        });

        document.querySelectorAll('.etapa-card').forEach(card => {
            const header = card.querySelector('.etapa-header');
            const content = card.querySelector('.etapa-content');
            const footer = card.querySelector('.etapa-footer');

            header.addEventListener('click', function () {
                card.classList.toggle('collapsed');
                const isCollapsed = card.classList.contains('collapsed');
                content.style.display = isCollapsed ? 'none' : '';
                footer.style.display = isCollapsed ? 'none' : '';
            });

        });

        // Manejar cambios en los selects de estado dentro de cada etapa (delegación)
        document.querySelectorAll('.etapa-card').forEach(card => {
            card.addEventListener('change', function (e) {
                const target = e.target;
                if (!target) return;
                if (target.classList && target.classList.contains('estado-badge') && target.tagName === 'SELECT') {
                    const subtareaId = target.getAttribute('data-id');
                    const nuevoEstado = target.value;
                    // No deshabilitamos el select aquí para permitir re-apertura; se gestiona en la respuesta.
                    actualizarEstadoTarea(subtareaId, nuevoEstado);
                }
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