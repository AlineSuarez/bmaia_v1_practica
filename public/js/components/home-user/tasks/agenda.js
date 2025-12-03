// Configuración y variables globales
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedDate = null;
let tareasAgenda = window.tareasData || [];

// Mapeo de prioridades a colores
const prioridadColores = {
    'baja': 'blue',
    'media': 'green',
    'alta': 'yellow',
    'urgente': 'red'
};

// Mapeo de estados a iconos
const estadoIconos = {
    'Pendiente': 'fa-hourglass-start',
    'En progreso': 'fa-spinner',
    'Completada': 'fa-check-circle'
};

// Nombres de meses
const nombresMeses = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

// Inicializar al cargar el DOM
document.addEventListener('DOMContentLoaded', function() {
    inicializarAgenda();
    configurarEventos();
    renderCalendario();
    
    // Si acabamos de conectar Google Calendar, iniciar sincronización
    if (window.googleCalendarJustConnected) {
        iniciarSincronizacionConProgreso();
    }
});

function inicializarAgenda() {
    // Seleccionar el día actual por defecto
    const hoy = new Date();
    if (hoy.getMonth() === currentMonth && hoy.getFullYear() === currentYear) {
        // Guardar como Date completo (no solo el número) para comparar mes/año/día
        selectedDate = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
        mostrarTareasDelDia(selectedDate);
    }
    
    // Verificar estado de Google Calendar
    verificarEstadoGoogleCalendar();
}

// Verificar si el usuario ya tiene Google Calendar conectado
function verificarEstadoGoogleCalendar() {
    fetch('/google-calendar/status')
        .then(response => response.json())
        .then(data => {
            const btn = document.getElementById('GoogleCalendarConnect');
            if (btn) {
                if (data.connected) {
                    btn.innerHTML = '<i class="fa-brands fa-google" aria-hidden="true"></i> Conectado con Google Calendar';
                    btn.style.background = 'linear-gradient(135deg, #34a853 0%, #0f9d58 100%)';
                    btn.disabled = false;
                    btn.onclick = () => mostrarOpcionesGoogleCalendar();
                } else {
                    // Estado inicial: no conectado
                    btn.innerHTML = '<i class="fa-brands fa-google" aria-hidden="true"></i> Conectar con Google Calendar';
                    btn.style.background = '';
                    btn.disabled = false;
                    btn.onclick = () => mostrarPopupConfirmacion();
                }
            }
        })
        .catch(error => {
            console.error('Error al verificar estado de Google Calendar:', error);
        });
}

function configurarEventos() {
    // Botón de Google Calendar - Manejado dinámicamente por verificarEstadoGoogleCalendar()
    // No agregar addEventListener aquí para evitar duplicados

    // Navegación de meses
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        // no cambiar selectedDate aquí: el día seleccionado no debe "moverse" autom.
        renderCalendario();
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        // no cambiar selectedDate aquí
        renderCalendario();
    });

    // Navegador de meses
    const navegadorMeses = document.getElementById('navegador-meses');
    const opcionesMeses = document.getElementById('opciones-meses');
    
    navegadorMeses.addEventListener('click', (e) => {
        e.stopPropagation();
        opcionesMeses.classList.toggle('hidden');
    });

    // Seleccionar mes del dropdown
    document.querySelectorAll('.mes').forEach((mesElement, index) => {
        mesElement.addEventListener('click', () => {
            currentMonth = index;
            opcionesMeses.classList.add('hidden');
            renderCalendario();
        });
    });

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!opcionesMeses.contains(e.target) && e.target !== navegadorMeses) {
            opcionesMeses.classList.add('hidden');
        }
    });
}

function renderCalendario() {
    const calendarGrid = document.getElementById('calendarGrid');
    const monthYear = document.getElementById('monthYear');
    
    // Actualizar título
    monthYear.textContent = `${nombresMeses[currentMonth]} ${currentYear}`;
    
    // Limpiar calendario
    calendarGrid.innerHTML = '';
    
    // Obtener primer día del mes y cantidad de días
    const primerDia = new Date(currentYear, currentMonth, 1);
    const ultimoDia = new Date(currentYear, currentMonth + 1, 0);
    const diasEnMes = ultimoDia.getDate();
    
    // Ajustar el día de la semana (0 = Domingo, 1 = Lunes, etc.)
    let primerDiaSemana = primerDia.getDay();
    primerDiaSemana = primerDiaSemana === 0 ? 6 : primerDiaSemana - 1; // Lunes = 0
    
    // Días del mes anterior
    const ultimoDiaMesAnterior = new Date(currentYear, currentMonth, 0).getDate();
    for (let i = primerDiaSemana - 1; i >= 0; i--) {
        const dia = ultimoDiaMesAnterior - i;
        const dayElement = crearElementoDia(dia, true, currentMonth - 1);
        calendarGrid.appendChild(dayElement);
    }
    
    // Días del mes actual
    for (let dia = 1; dia <= diasEnMes; dia++) {
        const fecha = new Date(currentYear, currentMonth, dia);
        const dayElement = crearElementoDia(dia, false, currentMonth, fecha);
        calendarGrid.appendChild(dayElement);
    }
    
    // Días del siguiente mes
    const diasRestantes = 42 - (primerDiaSemana + diasEnMes); // 6 semanas x 7 días
    for (let dia = 1; dia <= diasRestantes; dia++) {
        const dayElement = crearElementoDia(dia, true, currentMonth + 1);
        calendarGrid.appendChild(dayElement);
    }

    // Actualizar el highlight del dropdown de meses para reflejar currentMonth
    actualizarMesSeleccionadoEnDropdown();
}

// Crear elemento de día
function crearElementoDia(dia, otroMes, mes, fecha = null) {
    const dayElement = document.createElement('div');
    dayElement.className = 'day'; // ✓ Cambiado de 'calendar-day' a 'day'
    
    if (otroMes) {
        dayElement.classList.add('empty'); // ✓ Cambiado de 'other-month' a 'empty'
    }
    
    // Marcar día actual
    const hoy = new Date();
    if (fecha && 
        fecha.getDate() === hoy.getDate() && 
        fecha.getMonth() === hoy.getMonth() && 
        fecha.getFullYear() === hoy.getFullYear()) {
        dayElement.classList.add('today');
    }
    
    // Marcar día seleccionado SOLO si selectedDate es Date y coincide año/mes/día
    if (!otroMes && selectedDate instanceof Date &&
        fecha &&
        selectedDate.getFullYear() === fecha.getFullYear() &&
        selectedDate.getMonth() === fecha.getMonth() &&
        selectedDate.getDate() === fecha.getDate()) {
        dayElement.classList.add('selected');
    }
    
    dayElement.innerHTML = `<span class="day-number">${dia}</span>`;
    
    // Verificar si hay tareas ese día
    if (fecha && !otroMes) {
        const tareasDia = obtenerTareasDelDia(fecha);
        if (tareasDia.length > 0) {
            dayElement.classList.add('has-tasks'); // ✓ Agregar esta clase
            dayElement.setAttribute('data-tasks', tareasDia.length); // ✓ Agregar atributo
            
            // Verificar si todas las tareas están completadas
            const todasCompletadas = tareasDia.every(tarea => 
                tarea.estado.toLowerCase() === 'completada' || 
                tarea.estado.toLowerCase() === 'completado'
            );
            
            if (todasCompletadas) {
                dayElement.classList.add('completed-tasks'); // ✓ Nueva clase opcional
            }
        }
    }
    
    // Evento click
    if (!otroMes) {
        dayElement.addEventListener('click', () => {
            // Guardar selección con año/mes/día actuales (evita que la selección "salte" a otro mes)
            selectedDate = new Date(currentYear, currentMonth, dia);
            // Re-renderizar calendario para actualizar clases (selected)
            renderCalendario();
            // Mostrar tareas del día seleccionado
            mostrarTareasDelDia(selectedDate);
        });
    }
    
    return dayElement;
}

function obtenerTareasDelDia(fecha) {
    // Usa los componentes en hora local del Date recibido (ya creado con currentYear/currentMonth/dia)
    const year = fecha.getFullYear();
    const month = fecha.getMonth();
    const day = fecha.getDate();

    return tareasAgenda.filter(tarea => {
        // Parsear la fecha de la tarea y comparar componentes en hora local
        const fechaInicio = new Date(tarea.fecha_inicio);
        return (
            fechaInicio.getFullYear() === year &&
            fechaInicio.getMonth() === month &&
            fechaInicio.getDate() === day
        );
    });
}

function mostrarTareasDelDia(fecha) {
    const tasksList = document.getElementById('tasksList');
    const dateSubtitle = document.getElementById('dateSubtitle');
    
    // Actualizar subtítulo
    const opciones = { month: 'long', weekday: 'long', day: 'numeric' };
    dateSubtitle.textContent = fecha.toLocaleDateString('es-ES', opciones);
    
    // Obtener tareas del día
    const tareas = obtenerTareasDelDia(fecha);
    
    // Limpiar lista
    tasksList.innerHTML = '';
    
    if (tareas.length === 0) {
        tasksList.innerHTML = `
            <div class="empty-tasks">
                <i class="fa fa-calendar-check" style="font-size: 3rem; color: #ccc;"></i>
                <p>No hay tareas programadas para este día</p>
            </div>
        `;
        return;
    }
    
    // Renderizar tareas
    tareas.forEach(tarea => {
        const tareaElement = crearElementoTarea(tarea);
        tasksList.appendChild(tareaElement);
    });
}

// Zona de creación de elementos de tarea
function crearElementoTarea(tarea) {
    const taskItem = document.createElement('div');
    taskItem.className = 'task-item';
    taskItem.setAttribute('data-task-id', tarea.id);
    // Color de prioridad e ícono de estado
    const colorPrioridad = prioridadColores[tarea.prioridad] || 'blue';
    const iconoEstado = estadoIconos[tarea.estado] || 'fa-hourglass-start';

    // Estado para clase (completed / in-progress / pending)
    const estadoLower = String(tarea.estado).toLowerCase();
    let estadoClase = 'pending';
    if (estadoLower.includes('complet')) estadoClase = 'completed';
    else if (estadoLower.includes('progreso') || estadoLower.includes('en progreso')) estadoClase = 'in-progress';

    taskItem.innerHTML = `
        <div class="priority-bar ${colorPrioridad}"></div>
        <div class="task-content">
            <div class="task-name">${escapeHtml(tarea.nombre)}</div>
            <div class="task-dates">
                <div class="task-date">Inicio: ${formatearFechaDisplay(tarea.fecha_inicio)}</div>
                <div class="task-date">Límite: ${formatearFechaDisplay(tarea.fecha_limite)}</div>
            </div>
        </div>
        <div class="task-actions">
            <div class="status-pill ${estadoClase}" title="${escapeHtml(tarea.estado)}" tabindex="0" data-estado="${escapeHtml(tarea.estado)}">
                <i class="fa ${iconoEstado}" title="${tarea.estado}"></i>
                <span class="status-text">${escapeHtml(tarea.estado)}</span>
                <i class="fa fa-caret-down" style="margin-left:auto; opacity:0.6"></i>
                <div class="status-popover hidden" role="menu" aria-hidden="true">
                    <button type="button" class="status-option" data-estado="Pendiente">Pendiente</button>
                    <button type="button" class="status-option" data-estado="En progreso">En progreso</button>
                    <button type="button" class="status-option" data-estado="Completada">Completada</button>
                </div>
            </div>
        </div>
    `;

    // --- Popover & estado change handling ---
    const pill = taskItem.querySelector('.status-pill');
    const popover = taskItem.querySelector('.status-popover');
    const statusText = pill.querySelector('.status-text');
    const statusIcon = pill.querySelector('i.fa');
    // store current estado text
    pill.dataset.estado = tarea.estado;

    function aplicarCambioVisual(pillEl, nuevoEstado) {
        // actualizar clase visual
        pillEl.classList.remove('pending', 'in-progress', 'completed');
        const nl = String(nuevoEstado).toLowerCase();
        if (nl.includes('complet')) pillEl.classList.add('completed');
        else if (nl.includes('progreso') || nl.includes('en progreso')) pillEl.classList.add('in-progress');
        else pillEl.classList.add('pending');

        // icono
        const nuevoIcono = estadoIconos[nuevoEstado] || estadoIconos[Object.keys(estadoIconos).find(k=> nuevoEstado.toLowerCase().includes(k.toLowerCase()))] || 'fa-hourglass-start';
        if (statusIcon) {
            // mantener la clase fa y reemplazar la otra
            statusIcon.className = 'fa ' + nuevoIcono;
            statusIcon.setAttribute('title', nuevoEstado);
        }
        // texto
        if (statusText) statusText.textContent = nuevoEstado;
        pillEl.setAttribute('title', nuevoEstado);
        pillEl.dataset.estado = nuevoEstado;
    }

    // abrir/cerrar popover al click en el pill
    pill.addEventListener('click', (e) => {
        e.stopPropagation();
        const opened = !popover.classList.contains('hidden');
        // cerrar todos los popovers abiertos primero
        document.querySelectorAll('.status-popover').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.status-pill.open').forEach(p => p.classList.remove('open'));
        if (!opened) {
            popover.classList.remove('hidden');
            pill.classList.add('open');
            popover.setAttribute('aria-hidden', 'false');
        } else {
            popover.classList.add('hidden');
            pill.classList.remove('open');
            popover.setAttribute('aria-hidden', 'true');
        }
    });

    // cerrar al click fuera (listener temporal específico a este taskItem)
    const closeOnClickOutside = (ev) => {
        if (!taskItem.contains(ev.target)) {
            popover.classList.add('hidden');
            pill.classList.remove('open');
            popover.setAttribute('aria-hidden', 'true');
        }
    };
    document.addEventListener('click', closeOnClickOutside);

    // manejar selección de estado
    taskItem.querySelectorAll('.status-option').forEach(btn => {
        btn.addEventListener('click', (ev) => {
            ev.stopPropagation();
            const nuevoEstado = btn.dataset.estado;
            const previo = pill.dataset.estado || tarea.estado;

            // aplicar cambio optimista
            aplicarCambioVisual(pill, nuevoEstado);
            popover.classList.add('hidden');
            pill.classList.remove('open');

            // enviar petición al backend (esqueleto)
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

            // usar la ruta definida en routes/web.php -> PATCH /tareas/{id}/update-status
            fetch(`/tareas/${tarea.id}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ estado: nuevoEstado })
            }).then(res => {
                if (!res.ok) return Promise.reject(res);
                // Intentar parsear JSON si existe, pero no fallar si no hay body
                return res.json().catch(() => null);
            }).then(data => {
                // Si realmente se guardó un nuevo estado (diferente al previo), recargar la página
                if (nuevoEstado !== previo) {
                    // recarga completa para reflejar cambios en lista/kanban/línea de tiempo
                    location.reload();
                }
                // Si no cambió, no hacemos nada (evita recargas innecesarias)
            }).catch(() => {
                // revertir en caso de error
                aplicarCambioVisual(pill, previo);
                alert('Error al actualizar el estado. Intenta nuevamente.');
            });
        });
    });

    // limpiar listeners cuando elemento se remueva (no estrictamente necesario aquí)
    
    return taskItem;
}

// Funciones auxiliares
function formatearFecha(fecha) {
    const year = fecha.getFullYear();
    const month = String(fecha.getMonth() + 1).padStart(2, '0');
    const day = String(fecha.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatearFechaDisplay(fechaStr) {
    const fecha = new Date(fechaStr);
    const day = String(fecha.getDate()).padStart(2, '0');
    const month = String(fecha.getMonth() + 1).padStart(2, '0');
    const year = fecha.getFullYear();
    return `${day}/${month}/${year}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Función para mostrar el popup de confirmación de Google Calendar
function mostrarPopupConfirmacion() {
    // Crear el overlay
    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    overlay.id = 'googleCalendarPopup';
    
    // Crear el contenido del popup
    overlay.innerHTML = `
        <div class="popup-container">
            <div class="popup-header">
                <i class="fa-brands fa-google"></i>
                <h3>Conectar con Google Calendar</h3>
            </div>
            <div class="popup-body">
                <p>¿Estás seguro de que deseas conectar tu cuenta de Google Calendar?</p>
                <p class="popup-note">Esto sincronizará todas tus tareas actuales con tu calendario de Google.</p>
            </div>
            <div class="popup-actions">
                <button class="popup-btn popup-btn-cancel" id="popupCancelar">
                    <i class="fa fa-times"></i>
                    Cancelar
                </button>
                <button class="popup-btn popup-btn-confirm" id="popupConfirmar">
                    <i class="fa fa-check"></i>
                    Confirmar
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Event listeners para los botones
    document.getElementById('popupCancelar').addEventListener('click', cerrarPopup);
    document.getElementById('popupConfirmar').addEventListener('click', () => {
        cerrarPopup();
        // Redirigir a la ruta de autorización de Google Calendar
        window.location.href = '/auth/google-calendar';
    });
    
    // Cerrar al hacer clic fuera del popup
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            cerrarPopup();
        }
    });
}

function cerrarPopup() {
    // Buscar y eliminar cualquier popup-overlay activo
    const popups = document.querySelectorAll('.popup-overlay');
    popups.forEach(popup => {
        popup.remove();
    });
}

//Sincronizar el dropdown de meses con currentMonth
function actualizarMesSeleccionadoEnDropdown() {
    const opciones = document.querySelectorAll('#opciones-meses .mes');
    if (!opciones || opciones.length === 0) return;
    opciones.forEach((el, idx) => {
        if (idx === currentMonth) {
            el.classList.add('selected');
            el.classList.add('current-month'); // opcional si usas esa clase en CSS
        } else {
            el.classList.remove('selected');
            el.classList.remove('current-month');
        }
    });
}

// ===== SINCRONIZACIÓN CON PROGRESO =====
async function iniciarSincronizacionConProgreso() {
    try {
        // Mostrar modal
        const modal = document.getElementById('syncProgressModal');
        modal.style.display = 'flex';
        
        // Asegurar que la barra de progreso esté visible y el loader oculto
        const progressBar = modal.querySelector('.progress-bar');
        const progressFill = modal.querySelector('.progress-fill');
        const loader = modal.querySelector('.loader');
        if (progressBar) progressBar.style.display = 'block';
        if (progressFill) progressFill.style.width = '0%';
        if (loader) loader.style.display = 'none';
        
        // Obtener el total de tareas
        const statusResponse = await fetch('/google-calendar/sync-status');
        const statusData = await statusResponse.json();
        
        if (!statusData.connected) {
            throw new Error('No conectado con Google Calendar');
        }
        
        const totalTareas = statusData.total;
        const batchSize = 10; // Sincronizar 10 tareas a la vez
        let tareasProcessadas = 0;
        let tareasExitosas = 0;
        let errores = 0;
        
        document.getElementById('syncMessage').textContent = 
            'Sincronizando tareas con Google Calendar...';
        document.getElementById('syncDetails').textContent = 
            'Por favor espera...';
        
        // Sincronizar por lotes
        while (tareasProcessadas < totalTareas) {
            const response = await fetch('/google-calendar/sync-batch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    offset: tareasProcessadas,
                    limit: batchSize
                })
            });
            
            if (!response.ok) {
                throw new Error('Error en la sincronización');
            }
            
            const data = await response.json();
            tareasProcessadas += data.procesadas;
            tareasExitosas += data.sincronizadas;
            errores += data.errores;
            
            // Actualizar progreso
            const porcentaje = Math.round((tareasProcessadas / totalTareas) * 100);
            document.getElementById('syncProgressBar').style.width = `${porcentaje}%`;
            document.getElementById('syncPercentage').textContent = `${porcentaje}%`;
            document.getElementById('syncDetails').textContent = 'Sincronizando...';
            
            // Si es el último lote, terminar
            if (data.complete) {
                break;
            }
            
            // Pequeña pausa entre lotes
            await new Promise(resolve => setTimeout(resolve, 200));
        }
        
        // Mostrar mensaje final
        document.getElementById('syncMessage').textContent = '¡Sincronización completada!';
        document.getElementById('syncDetails').textContent = 'Tareas sincronizadas exitosamente';
        
        // Cerrar modal después de 2 segundos
        setTimeout(() => {
            modal.style.display = 'none';
            
            // Mostrar notificación de éxito
            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion('success', 'Tareas sincronizadas con Google Calendar');
            }
        }, 2000);
        
    } catch (error) {
        console.error('Error en sincronización:', error);
        document.getElementById('syncMessage').textContent = 'Error en la sincronización';
        document.getElementById('syncDetails').textContent = error.message;
        
        // Cerrar modal después de 3 segundos
        setTimeout(() => {
            document.getElementById('syncProgressModal').style.display = 'none';
        }, 3000);
    }
}

// ===== OPCIONES DE GOOGLE CALENDAR =====
function mostrarOpcionesGoogleCalendar() {
    // Crear el overlay
    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    overlay.id = 'googleCalendarOptionsPopup';
    
    // Crear el contenido del popup
    overlay.innerHTML = `
        <div class="popup-container">
            <div class="popup-header">
                <i class="fa-brands fa-google"></i>
                <h3>Google Calendar</h3>
            </div>
            <div class="popup-body">
                <p>¿Qué deseas hacer?</p>
            </div>
            <div class="popup-actions" style="flex-direction: column; gap: 10px;">
                <button class="popup-btn popup-btn-confirm" id="optionResync" style="width: 100%;">
                    <i class="fa fa-sync"></i>
                    Volver a sincronizar
                </button>
                <button class="popup-btn popup-btn-cancel" id="optionDelete" style="width: 100%; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                    <i class="fa fa-trash"></i>
                    Eliminar tareas del calendario
                </button>
                <button class="popup-btn" id="optionCancel" style="width: 100%; background: #6b7280; color: white;">
                    <i class="fa fa-times"></i>
                    Cancelar
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Event listeners para los botones
    document.getElementById('optionResync').addEventListener('click', () => {
        cerrarPopup();
        window.location.href = '/auth/google-calendar';
    });
    
    document.getElementById('optionDelete').addEventListener('click', () => {
        cerrarPopup();
        confirmarEliminacionTareas();
    });
    
    document.getElementById('optionCancel').addEventListener('click', cerrarPopup);
    
    // Cerrar al hacer clic fuera del popup
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            cerrarPopup();
        }
    });
}

function confirmarEliminacionTareas() {
    // Primera confirmación
    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    overlay.id = 'googleCalendarDeletePopup';
    
    overlay.innerHTML = `
        <div class="popup-container">
            <div class="popup-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <i class="fa fa-exclamation-triangle"></i>
                <h3>Eliminar tareas de Google Calendar</h3>
            </div>
            <div class="popup-body">
                <p style="color: #dc2626; font-weight: 600;">
                <i class="fa fa-exclamation-triangle"></i>
                ¿Estás seguro?</p>
                <p>Esta acción eliminará TODAS las tareas que se subieron a Google Calendar.</p>
                <p style="font-size: 0.9rem; color: #6b7280;">Podrás volver a sincronizarlas después si lo deseas.</p>
            </div>
            <div class="popup-actions">
                <button class="popup-btn" id="deleteCancelBtn" style="background: #6b72806e;">
                    <i class="fa fa-times"></i>
                    Cancelar
                </button>
                <button class="popup-btn popup-btn-cancel" id="deleteConfirmBtn">
                    <i class="fa fa-trash"></i>
                    Sí, eliminar
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    document.getElementById('deleteCancelBtn').addEventListener('click', cerrarPopup);
    document.getElementById('deleteConfirmBtn').addEventListener('click', () => {
        cerrarPopup();
        confirmarEliminacionFinal();
    });
    
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            cerrarPopup();
        }
    });
}

function confirmarEliminacionFinal() {
    // Segunda confirmación
    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay';
    overlay.id = 'googleCalendarDeleteFinalPopup';
    
    overlay.innerHTML = `
        <div class="popup-container">
            <div class="popup-header" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                <i class="fa fa-exclamation-circle"></i>
                <h3>Confirmación Final</h3>
            </div>
            <div class="popup-body">
                <p style="color: #dc2626; font-weight: 700; font-size: 1.1rem;">
                <i class="fa fa-exclamation-triangle"></i>
                ÚLTIMA ADVERTENCIA</p>
                <p>¿Realmente deseas eliminar todas las tareas de Google Calendar?</p>
                <p style="font-size: 0.85rem; color: #991b1b;">Esta acción no se puede deshacer.</p>
            </div>
            <div class="popup-actions">
                <button class="popup-btn" id="finalCancelBtn" style="background: #6b72806e;">
                    <i class="fa fa-times"></i>
                    No, cancelar
                </button>
                <button class="popup-btn popup-btn-cancel" id="finalConfirmBtn">
                    <i class="fa fa-check"></i>
                    Sí, estoy seguro
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    document.getElementById('finalCancelBtn').addEventListener('click', cerrarPopup);
    document.getElementById('finalConfirmBtn').addEventListener('click', () => {
        cerrarPopup();
        eliminarTareasDeGoogleCalendar();
    });
    
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            cerrarPopup();
        }
    });
}

async function eliminarTareasDeGoogleCalendar() {
    // Mostrar modal de progreso
    const modal = document.getElementById('syncProgressModal');
    modal.style.display = 'flex';
    
    // Ocultar barra de progreso y mostrar loader circular
    const progressBar = modal.querySelector('.progress-bar');
    const loader = modal.querySelector('.loader');
    if (progressBar) progressBar.style.display = 'none';
    if (loader) loader.style.display = 'block';
    
    document.getElementById('syncMessage').textContent = 'Eliminando tareas de Google Calendar...';
    document.getElementById('syncPercentage').textContent = '';
    document.getElementById('syncDetails').textContent = 'Por favor espera...';
    
    try {
        const response = await fetch('/google-calendar/delete-all', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Error al eliminar tareas');
        }
        
        const data = await response.json();
        
        // Actualizar mensaje de éxito
        document.getElementById('syncPercentage').textContent = '✓';
        document.getElementById('syncMessage').textContent = '¡Tareas eliminadas exitosamente!';
        document.getElementById('syncDetails').textContent = 'Las tareas han sido eliminadas de Google Calendar';
        
        // Cerrar modal después de 2 segundos
        setTimeout(() => {
            modal.style.display = 'none';
            
            // Restablecer el botón al estado original (naranja, no conectado)
            const btn = document.getElementById('GoogleCalendarConnect');
            if (btn) {
                btn.innerHTML = '<i class="fa-brands fa-google" aria-hidden="true"></i> Conectar con Google Calendar';
                btn.style.background = ''; // Remover inline style para usar CSS original (naranja)
                btn.disabled = false;
                btn.onclick = () => mostrarPopupConfirmacion();
            }
            
            // Mostrar notificación
            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion('success', 'Tareas eliminadas de Google Calendar');
            }
        }, 2000);
        
    } catch (error) {
        console.error('Error eliminando tareas:', error);
        document.getElementById('syncMessage').textContent = 'Error al eliminar';
        document.getElementById('syncDetails').textContent = error.message;
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 3000);
    }
}