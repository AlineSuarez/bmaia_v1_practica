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
        selectedDate = hoy.getDate();
        mostrarTareasDelDia(hoy);
    }
}

function configurarEventos() {
    // Navegación de meses
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendario();
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
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
}

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
    
    // Marcar día seleccionado
    if (!otroMes && selectedDate === dia) {
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
            selectedDate = dia;
            renderCalendario();
            mostrarTareasDelDia(new Date(currentYear, currentMonth, dia));
        });
    }
    
    return dayElement;
}

function obtenerTareasDelDia(fecha) {
    const fechaStr = formatearFecha(fecha);
    
    return tareasAgenda.filter(tarea => {
        const fechaInicio = new Date(tarea.fecha_inicio);
        const fechaActual = new Date(fechaStr);
        
        // Normalizar a medianoche para comparación correcta
        fechaInicio.setHours(0, 0, 0, 0);
        fechaActual.setHours(0, 0, 0, 0);
        
        // Solo comparar con la fecha de inicio
        return fechaActual.getTime() === fechaInicio.getTime();
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

function crearElementoTarea(tarea) {
    const taskItem = document.createElement('div');
    taskItem.className = 'task-item';
    taskItem.setAttribute('data-task-id', tarea.id);
    
    const colorPrioridad = prioridadColores[tarea.prioridad] || 'blue';
    const iconoEstado = estadoIconos[tarea.estado] || 'fa-hourglass-start';
    
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
            <i class="fa ${iconoEstado}" title="${tarea.estado}"></i>
            <i class="fa fa-edit edit-task-btn" data-task-id="${tarea.id}" title="Editar tarea"></i>
        </div>
    `;
    
    // Evento para editar tarea
    const editBtn = taskItem.querySelector('.edit-task-btn');
    editBtn.addEventListener('click', () => {
        window.location.href = `/tareas/${tarea.id}/editar`;
    });
    
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