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
});

function inicializarAgenda() {
    // Seleccionar el día actual por defecto
    const hoy = new Date();
    if (hoy.getMonth() === currentMonth && hoy.getFullYear() === currentYear) {
        // Guardar como Date completo (no solo el número) para comparar mes/año/día
        selectedDate = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
        mostrarTareasDelDia(selectedDate);
    }
}

function configurarEventos() {
    // Botón de Google Calendar
    const googleCalendarBtn = document.getElementById('GoogleCalendarConnect');
    if (googleCalendarBtn) {
        googleCalendarBtn.addEventListener('click', () => {
            mostrarPopupConfirmacion();
        });
    }

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
                <p class="popup-note">Esto permitirá sincronizar tus tareas con tu calendario de Google.</p>
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
        window.location.href = '/auth/google/connect';
    });
    
    // Cerrar al hacer clic fuera del popup
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            cerrarPopup();
        }
    });
}

function cerrarPopup() {
    const popup = document.getElementById('googleCalendarPopup');
    if (popup) {
        popup.remove();
    }
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