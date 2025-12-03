/** 
 * ================================================================
 * CONFIGURACIÓN DE INTRO.JS
 * ================================================================ 
 */

document.addEventListener('DOMContentLoaded', function() {
    // Configuración del tour
    const intro = introJs();
    intro.setOptions({
        nextLabel: 'Siguiente →',
        prevLabel: '← Anterior',
        skipLabel: 'Salir',
        doneLabel: '¡Entendido!',
        tooltipPosition: 'auto',
        showProgress: true,
        showBullets: false,
        exitOnOverlayClick: false,
        disableInteraction: true,
        overlayOpacity: 0.7
    });

    // Botón para iniciar el tour
    document.getElementById('startTour').addEventListener('click', function() {
        intro.start();
    });
});

/**
 * ================================================================
 * CONFIGURACIÓN Y VARIABLES GLOBALES
 * ================================================================ */

const TaskConfig = {
    csrfToken: document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content"),
    endpoints: {
        datosSubtareas: "/datos-subtareas",
        updateTarea: "/tareas/update/",
        deleteTarea: "/tareas/",
    },
    selectors: {
        tasksTable: "#tasksTable",
        tasksTableBody: "#tasksTableBody",
        tasksContainer: "#tasksTableContainer",
        paginationContainer: "#tasksPagination",
        loadingState: "#taskListLoading",
    },
};

// Estado global de la aplicación
const AppState = {
    filtroActivo: "all",
    filtroActivoPrioridad: "all", // Nuevo estado para filtrar por prioridad
    paginacion: {
        porPagina: 15,
        paginaActual: 1,
        totalPaginas: 1,
    },
    tareas: [],
    isLoading: false,
    silencing: false, // bandera para evitar marcar cambios cuando se actualiza en lote
};

/**
 * ================================================================
 * INICIALIZACIÓN
 * ================================================================ */

$(document).ready(function () {
    inicializarApp();
    configurarEditarNombresGlobal(); // <-- Agregar esta línea aquí
});

function guardarPaginaActual(pagina) {
    localStorage.setItem("tareas_pagina_actual", pagina);
}

function obtenerPaginaActual() {
    const pagina = localStorage.getItem("tareas_pagina_actual");
    return pagina ? parseInt(pagina) : 1;
}

// Al inicializar la app, recupera la página guardada
function inicializarApp() {
    AppState.paginacion.paginaActual = obtenerPaginaActual();

    // Recuperar filtro guardado
    const filtroGuardado = localStorage.getItem("tareas_filtro_activo");
    if (filtroGuardado) {
        AppState.filtroActivo = filtroGuardado;
        // Marcar el botón activo en la UI si aplica
        $(".filter-btn").removeClass("active");
        $(`.filter-btn[data-filter="${filtroGuardado}"]`).addClass("active");
    }

    // Recuperar filtro de prioridad guardado
    const filtroPrioridadGuardado = localStorage.getItem("tareas_filtro_prioridad");
    if (filtroPrioridadGuardado && filtroPrioridadGuardado !== "all") {
        AppState.filtroActivoPrioridad = filtroPrioridadGuardado;
        $(`.priority-filter[data-priority="${filtroPrioridadGuardado}"]`).addClass("active");
    }

    configurarEventListeners();
    configurarFiltros();
    configurarFiltrosPrioridad(); // Nueva función para filtros de prioridad
    configurarSelect2();
    configurarActualizarPlanTrabajo(); // Añadido: configurar flujo de actualización anual
    cargarTareasIniciales();
}

// Configurar flujo para el botón "Actualizar Plan de Trabajo"
function configurarActualizarPlanTrabajo() {
    const btn = document.getElementById('actualizarPlanTrabajoBtn');
    if (!btn) return;

    btn.addEventListener('click', async function () {
        const confirmado = await askConfirm({
            title: '¿Deseas actualizar el Plan de Trabajo al siguiente año?',
            text: 'Se actualizarán las fechas al siguiente año y las tareas pasarán a estado Pendiente.',
            confirmText: 'Sí, actualizar',
            cancelText: 'Cancelar',
        });

        if (!confirmado) return;

        // Recolectar todas las filas de tareas
        const allRows = Array.from(document.querySelectorAll('.task-row'));

        // Detectar tareas no completadas
        const notCompleted = allRows.filter((row) => {
            const status = (row.getAttribute('data-status') || '').trim();
            return status !== 'Completada';
        });

        // Si existen tareas no completadas, preguntar si deben marcarse como Completada antes
        if (notCompleted.length > 0) {
            const marcar = await askConfirm({
                title: 'Hay tareas no completadas',
                text: `Se encontraron ${notCompleted.length} tareas que no están completadas. ¿Deseas marcarlas como Completadas antes de actualizar el año?`,
                confirmText: 'Sí, marcarlas',
                cancelText: 'No, cancelar',
            });

            if (!marcar) {
                // El usuario no desea marcar las tareas como completadas: cancelar toda la operación
                mostrarNotificacion('info', 'Acción cancelada. No se realizaron cambios.');
                return;
            }
            // Silenciar handlers de cambio para evitar marcar botones como "pendiente"
            AppState.silencing = true;

            // Marcar en DOM como Completada (temporal)
            notCompleted.forEach((row) => {
                const select = row.querySelector('.estado, .status-select');
                if (select) {
                    try {
                        select.value = 'Completada';
                        // Si usa Select2, disparar evento (sin efecto por silencing)
                        if (typeof $(select).trigger === 'function') $(select).trigger('change');
                    } catch (e) {}
                }
                row.setAttribute('data-status', 'Completada');
            });
        }

        // A estas alturas consideramos que todas las tareas están Completadas (reales o forzadas)
        // Preparar payload con nuevas fechas y estados
        const tasksPayload = [];
        allRows.forEach((row) => {
            const id = row.getAttribute('data-task-id');

            // Obtener fechas desde los inputs si existen, si no desde data-attributes
            const inputInicio = row.querySelector('.fecha-inicio');
            const inputFin = row.querySelector('.fecha-fin');

            const rawInicio = inputInicio ? inputInicio.value || row.getAttribute('data-fecha-inicio') : row.getAttribute('data-fecha-inicio');
            const rawFin = inputFin ? inputFin.value || row.getAttribute('data-fecha-limite') : row.getAttribute('data-fecha-limite');

            const nuevoInicio = incrementYearForDateString(rawInicio);
            const nuevoFin = incrementYearForDateString(rawFin);

            // Actualizar DOM: inputs (YYYY-MM-DD) y atributos data (DD-MM-YYYY)
            if (inputInicio) inputInicio.value = nuevoInicio.iso;
            if (inputFin) inputFin.value = nuevoFin.iso;
            row.setAttribute('data-fecha-inicio', nuevoInicio.dmy);
            row.setAttribute('data-fecha-limite', nuevoFin.dmy);

            // Cambiar estado a Pendiente (silenciando los handlers para evitar marcar como pendiente)
            const selectEstado = row.querySelector('.estado, .status-select');
            if (selectEstado) {
                try {
                    selectEstado.value = 'Pendiente';
                    if (typeof $(selectEstado).trigger === 'function') $(selectEstado).trigger('change');
                } catch (e) {}
            }
            row.setAttribute('data-status', 'Pendiente');

            tasksPayload.push({
                id: id,
                fecha_inicio: nuevoInicio.iso,
                fecha_limite: nuevoFin.iso,
                estado: 'Pendiente',
            });
        });

        // Enviar al servidor
        mostrarLoadingState(true);
        try {
            const url = '/tareas/actualizar-plan-trabajo';
            const resp = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': TaskConfig.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ tasks: tasksPayload }),
            });

            if (!resp.ok) {
                const text = await resp.text();
                throw new Error(text || 'Error en la actualización');
            }

            // Actualización exitosa
            mostrarNotificacion('success', 'Plan de Trabajo actualizado correctamente.');
            // Refrescar vistas/contadores
            actualizarContadores();
            paginarTabla();
        } catch (error) {
            console.error('Error actualizando plan de trabajo:', error);
            mostrarNotificacion('error', 'Ocurrió un error al actualizar el Plan de Trabajo.');
        } finally {
            // Quitar estado de loading
            mostrarLoadingState(false);

            // Levantar silencing para que los handlers vuelvan a funcionar
            AppState.silencing = false;

            // Limpiar marcas visuales de "pendiente" para evitar que el usuario tenga que guardar cada fila
            document.querySelectorAll('.guardar-cambios.pendiente-guardar').forEach((b) => b.classList.remove('pendiente-guardar'));
            // Ocultar recordatorios y contador
            try {
                const $contador = document.getElementById('contadorCambiosPendientes');
                if ($contador) $contador.style.display = 'none';
                const $record = document.getElementById('cambiosRecordatorio');
                if ($record) $record.style.display = 'none';
            } catch (e) {}
        }
    });
}

// Helper: preguntar confirmación con wrappers de mostrarConfirmacion/Swal/fallback
async function askConfirm({ title, text, confirmText = 'Sí', cancelText = 'No' }) {
    // Usar mostrarConfirmacion si existe
    if (typeof mostrarConfirmacion === 'function') {
        try {
            const res = await mostrarConfirmacion({ title, text, confirmText, cancelText });
            return !!res;
        } catch (e) {}
    }

    // Si Swal está disponible
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        const result = await Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
        });
        return !!result.isConfirmed;
    }

    // Fallback simple
    return confirm(`${title}\n\n${text}`);
}

// Helper: incrementar año de una fecha dada (acepta yyyy-mm-dd o dd-mm-yyyy)
function incrementYearForDateString(dateStr) {
    if (!dateStr) {
        // devolver hoy +1 año por defecto
        const d = new Date();
        d.setFullYear(d.getFullYear() + 1);
        return { iso: formatDateForInput(d.toISOString()), dmy: formatDateToDMY(d) };
    }

    // Usar parsearFecha si existe
    let dateObj;
    try {
        if (typeof parsearFecha === 'function') {
            dateObj = parsearFecha(dateStr);
            if (!(dateObj instanceof Date) || isNaN(dateObj)) dateObj = new Date(dateStr);
        } else {
            dateObj = new Date(dateStr);
        }
    } catch (e) {
        dateObj = new Date(dateStr);
    }

    if (!(dateObj instanceof Date) || isNaN(dateObj)) dateObj = new Date();
    dateObj.setFullYear(dateObj.getFullYear() + 1);

    // ISO para input yyyy-mm-dd
    const iso = dateObj.toISOString().slice(0, 10);
    const dmy = formatDateToDMY(dateObj);
    return { iso, dmy };
}

function formatDateToDMY(dateObj) {
    const d = dateObj.getDate().toString().padStart(2, '0');
    const m = (dateObj.getMonth() + 1).toString().padStart(2, '0');
    const y = dateObj.getFullYear();
    return `${d}-${m}-${y}`;
}

/**
 * ================================================================
 * GESTIÓN DE DATOS Y ESTADO
 * ================================================================ */

async function cargarTareasIniciales() {
    // Las tareas vienen renderizadas desde el servidor (Blade)
    // Solo necesitamos configurar la funcionalidad
    paginarTabla();
    configurarSelect2();
    actualizarContadores();
}

/**
 * ================================================================
 * PAGINACIÓN OPTIMIZADA
 * ================================================================ */

function paginarTabla() {
    const allRows = Array.from(document.querySelectorAll(".task-row"));

    // Aplicar filtros
    const rowsFiltradas = filtrarFilas(allRows);

    // Ordenar filas filtradas
    const rowsOrdenadas = ordenarFilas(rowsFiltradas);

    const { porPagina, paginaActual } = AppState.paginacion;
    const totalPaginas = Math.ceil(rowsOrdenadas.length / porPagina) || 1;

    // Actualizar estado
    AppState.paginacion.totalPaginas = totalPaginas;
    if (AppState.paginacion.paginaActual > totalPaginas) {
        AppState.paginacion.paginaActual = totalPaginas;
    }

    // ✨ REORDENAR FÍSICAMENTE LAS FILAS EN EL DOM
    const tbody = document.querySelector(TaskConfig.selectors.tasksTableBody);
    if (tbody && rowsOrdenadas.length > 0) {
        // Reorganizar todas las filas ordenadas en el DOM
        rowsOrdenadas.forEach(row => tbody.appendChild(row));
    }

    // Ocultar todas las filas
    allRows.forEach((row) => (row.style.display = "none"));

    // Mostrar solo las filas de la página actual
    const inicio = (AppState.paginacion.paginaActual - 1) * porPagina;
    const fin = inicio + porPagina;

    rowsOrdenadas.slice(inicio, fin).forEach((row) => {
        row.style.display = "";
    });

    // Renderizar controles de paginación
    renderizarPaginacion();
}

function filtrarFilas(filas) {
    return filas.filter((row) => {
        const estado = row.getAttribute("data-status");
        const prioridad = row.getAttribute("data-priority");
        
        // Verificar filtro de estado
        const cumpleEstado = AppState.filtroActivo === "all" || estado === AppState.filtroActivo;
        
        // Verificar filtro de prioridad
        const cumplePrioridad = AppState.filtroActivoPrioridad === "all" || prioridad === AppState.filtroActivoPrioridad;
        
        // La fila debe cumplir AMBOS filtros
        return cumpleEstado && cumplePrioridad;
    });
}

function renderizarPaginacion() {
    const container = document.querySelector(
        TaskConfig.selectors.paginationContainer
    );
    if (!container) return;

    const { totalPaginas, paginaActual } = AppState.paginacion;

    if (totalPaginas <= 1) {
        container.innerHTML = "";
        return;
    }

    let html = `
        <button class="pagination-btn ${paginaActual === 1 ? "disabled" : ""}" 
                data-page="${paginaActual - 1}" ${
        paginaActual === 1 ? "disabled" : ""
    }>
            &laquo;
        </button>
    `;

    // Generar botones de páginas
    for (let i = 1; i <= totalPaginas; i++) {
        if (
            totalPaginas <= 7 ||
            i === 1 ||
            i === totalPaginas ||
            (i >= paginaActual - 1 && i <= paginaActual + 1)
        ) {
            html += `
                <button class="pagination-btn ${
                    i === paginaActual ? "active" : ""
                }" 
                        data-page="${i}">
                    ${i}
                </button>
            `;
        } else if (i === paginaActual - 2 || i === paginaActual + 2) {
            html += `<span class="pagination-dots">...</span>`;
        }
    }

    html += `
        <button class="pagination-btn ${
            paginaActual === totalPaginas ? "disabled" : ""
        }" 
                data-page="${paginaActual + 1}" ${
        paginaActual === totalPaginas ? "disabled" : ""
    }>
            &raquo;
        </button>
    `;

    container.innerHTML = html;
}

/**
 * ================================================================
 * ORDENAMIENTO DE FILAS
 * ================================================================ */

// Función auxiliar para parsear fechas en formato dd-mm-yyyy
function parsearFecha(fechaStr) {
    if (!fechaStr) return new Date(0); // Fecha mínima si está vacía
    
    // Si ya está en formato yyyy-mm-dd (del input date)
    if (fechaStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
        return new Date(fechaStr);
    }
    
    // Si está en formato dd-mm-yyyy (del atributo data-fecha-inicio)
    if (fechaStr.match(/^\d{2}-\d{2}-\d{4}$/)) {
        const [dia, mes, anio] = fechaStr.split('-');
        return new Date(`${anio}-${mes}-${dia}`);
    }
    
    // Fallback
    return new Date(fechaStr);
}

function ordenarFilas(filas) {
    // Definir orden de prioridad de los estados
    const ordenEstados = {
        Vencida: 1,        // Las vencidas tienen máxima prioridad
        Pendiente: 2,
        "En progreso": 3,
        Completada: 4,
    };

    return filas.sort((a, b) => {
        const estadoA = a.getAttribute("data-status");
        const estadoB = b.getAttribute("data-status");

        //Primer criterio: ordenar por estado
        const prioridadEstadoA = ordenEstados[estadoA] || 999; 
        const prioridadEstadoB = ordenEstados[estadoB] || 999; 

        if (prioridadEstadoA !== prioridadEstadoB) { 
            return prioridadEstadoA - prioridadEstadoB; 
        }

        //Segundo criterio: ordenar por Fecha de inicio (ascendente)
        const fechaA = a.getAttribute('data-fecha-inicio') || a.querySelector('.fecha-inicio')?.value || "";
        const fechaB = b.getAttribute('data-fecha-inicio') || b.querySelector('.fecha-inicio')?.value || "";

        // Convertir fechas de formato dd-mm-yyyy a yyyy-mm-dd
        const dateA = parsearFecha(fechaA);
        const dateB = parsearFecha(fechaB);

        return dateA - dateB; //Orden ascendente (las más antiguas primero)
    });
}

/**
 * ================================================================
 * SISTEMA DE FILTROS DE ESTADO
 * ================================================================ */

function configurarFiltros() {
    $(document).on("click", ".filter-btn", function () {
        const filtro = $(this).data("filter");
        aplicarFiltro(filtro);

        // Actualizar UI
        $(".filter-btn").removeClass("active");
        $(this).addClass("active");
    });
}

function aplicarFiltro(filtro) {
    AppState.filtroActivo = filtro;
    AppState.paginacion.paginaActual = 1; // Reiniciar a primera página
    paginarTabla();
    // Guardar filtro en localStorage
    localStorage.setItem("tareas_filtro_activo", filtro);
}

function aplicarFiltros() {
    paginarTabla();
}


/**
 * ================================================================
 * CONFIGURACIÓN DE FILTROS DE PRIORIDAD
 * ================================================================ */

// Configurar eventos para filtros de prioridad
function configurarFiltrosPrioridad() {
    $(document).on("click", ".priority-filter", function () {
        const prioridad = $(this).data("priority");
        aplicarFiltroPrioridad(prioridad);
        
        // Actualizar UI
        $(".priority-filter").removeClass("active");
        if (AppState.filtroActivoPrioridad !== "all") {
            $(this).addClass("active");
        }
    });
}

// Función para aplicar el filtro de prioridad
function aplicarFiltroPrioridad(prioridad) {
    // Si se hace clic en el mismo filtro, se desactiva
    if (AppState.filtroActivoPrioridad === prioridad) {
        AppState.filtroActivoPrioridad = "all";
    } else {
        AppState.filtroActivoPrioridad = prioridad;
    }
    
    AppState.paginacion.paginaActual = 1; // Reiniciar a primera página
    paginarTabla();
    actualizarContadores();
    
    // Guardar filtro en localStorage
    localStorage.setItem("tareas_filtro_prioridad", AppState.filtroActivoPrioridad);
}


/**
 * ================================================================
 * CONFIGURACIÓN DE SELECT2
 * ================================================================ */

function configurarSelect2() {
    // Destruir Select2 existentes
    $(".prioridad, .estado").each(function () {
        if ($(this).data("select2")) {
            $(this).select2("destroy");
        }
    });

    // Configurar Select2 para prioridad
    $(".prioridad").select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        templateResult: formatPriorityOption,
        templateSelection: formatPriorityOption,
    });

    // Configurar Select2 para estado
    $(".estado").select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        templateResult: formatStatusOption,
        templateSelection: formatStatusOption,
    });
}

function formatPriorityOption(option) {
    if (!option.id) return option.text;

    const icons = {
        baja: '<i class="fa fa-circle "style="color: #ADD8E6;"></i>',
        media: '<i class="fa fa-circle text-success"></i>',
        alta: '<i class="fa fa-circle "style="color: #FFFF00;"></i>',
        urgente: '<i class="fa fa-circle text-danger"></i>',
    };

    return $(`<span>${icons[option.id] || ""} ${option.text}</span>`);
}

function formatStatusOption(option) {
    if (!option.id) return option.text;

    const icons = {
        Vencida: '<i class="fa fa-exclamation-triangle text-danger"></i>',
        Pendiente: '<i class="fa fa-hourglass-start text-secondary"></i>',
        "En progreso": '<i class="fa fa-spinner text-secondary"></i>',
        Completada: '<i class="fa fa-check-circle text-secondary"></i>',
    };

    return $(`<span>${icons[option.id] || ""} ${option.text}</span>`);
}

/**
 * ================================================================
 * EVENTOS DE INTERACCIÓN
 * ================================================================ */

function configurarEventListeners() {
    // Paginación
    $(document).on("click", ".pagination-btn:not(.disabled)", function () {
        const page = parseInt($(this).data("page"));
        if (!isNaN(page) && page !== AppState.paginacion.paginaActual) {
            AppState.paginacion.paginaActual = page;
            guardarPaginaActual(page);
            paginarTabla();
        }
    });

    // Eliminar tarea
    $(document).on("click", ".eliminar-tarea", manejarEliminarTarea);

    // Cambios en selects
    $(document).on("change", ".prioridad, .estado", function () {
        const $row = $(this).closest(".task-row");
        
        // Guardar el estado original antes del primer cambio
        if (!$row.attr("data-status-original")) {
            $row.attr("data-status-original", $row.attr("data-status"));
        }
        
        $row.attr("data-priority", $row.find(".prioridad").val());
        $row.attr("data-status", $row.find(".estado").val());
    });

    // Mostrar recordatorio cuando se cambie prioridad o estado
    $(document).on("change", ".priority-select, .status-select", function () {
        $("#cambiosRecordatorio").fadeIn(200);
    });

    // Contador global de cambios pendientes
    let cambiosPendientes = new Set();

    function actualizarContadorCambios() {
        const num = cambiosPendientes.size;
        const $contador = $("#contadorCambiosPendientes");
        $("#numCambiosPendientes").text(num);
        if (num > 0) {
            $contador.show();
        } else {
            $contador.hide();
        }
    }

    // Detectar cambios en prioridad o estado
    $(document).on("change", ".priority-select, .status-select", function () {
        // Si estamos silenciando cambios (actualización en lote), no marcar como pendiente
        if (AppState.silencing) return;

        const $row = $(this).closest(".task-row");
        const taskId = $row.data("task-id");
        cambiosPendientes.add(taskId);

        // Cambia el color del botón de guardar de esa fila
        $row.find(".guardar-cambios").addClass("pendiente-guardar");
        actualizarContadorCambios();
    });

    // Guardar cambios de una fila
    $(document).on("click", ".guardar-cambios", async function (e) {
        const $button = $(this);
        const $row = $button.closest(".task-row");
        const taskId = $row.data("task-id");

        await manejarGuardarCambios(e);

        // Quita el color de pendiente SOLO de esta fila
        $button.removeClass("pendiente-guardar");

        cambiosPendientes.delete(taskId);
        actualizarContadorCambios();

        if (cambiosPendientes.size === 0) {
            setTimeout(() => location.reload(), 1000);
        }
    });
}

// ===== FUNCIONALIDAD: EDITAR / GUARDAR NOMBRES EN BLOQUE =====
function configurarEditarNombresGlobal() {
    const btn = document.getElementById("btn-editar-nombre");
    if (!btn) return;

    const icon = btn.querySelector("i");
    const spanText = btn.querySelector("span");
    let editMode = false;

    btn.addEventListener("click", async (e) => {
        e.preventDefault();

        const rows = Array.from(document.querySelectorAll(".task-row"));
        if (rows.length === 0) return;

        // Entrar en modo edición: convertir spans a inputs
        if (!editMode) {
            editMode = true;
            btn.classList.add("activo");
            if (icon && icon.classList.contains("fa-edit")) icon.classList.replace("fa-edit", "fa-save");
            if (spanText) spanText.textContent = " Guardar";

            rows.forEach((row) => {
                const nameSpan = row.querySelector(".task-name");
                if (!nameSpan) return;

                const original = nameSpan.textContent.trim();
                row.dataset.originalName = original;

                const input = document.createElement("input");
                input.type = "text";
                input.className = "task-name-input input-miel";
                input.value = original;
                input.setAttribute("data-task-id", row.getAttribute("data-task-id"));
                input.style.minWidth = "180px";
                input.style.boxSizing = "border-box";
                input.autocomplete = "off";

                nameSpan.parentNode.replaceChild(input, nameSpan);
            });

            const firstInput = document.querySelector(".task-name-input");
            if (firstInput) firstInput.focus();
            return;
        }

        // Guardar: validar y enviar cambios
        btn.disabled = true;

        const csrfToken =
            TaskConfig.csrfToken ||
            document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ||
            "";

        const inputs = Array.from(document.querySelectorAll(".task-name-input"));
        
        // Validar vacíos
        for (const input of inputs) {
            const val = input.value.trim();
            if (val === "") {
                mostrarNotificacion("warning", "El nombre no puede quedar vacío");
                input.focus();
                input.style.borderColor = "#dc2626";
                btn.disabled = false;
                return;
            } else {
                input.style.borderColor = "";
            }
        }

        const cambios = [];
        inputs.forEach((input) => {
            const row = input.closest(".task-row");
            const original = (row && row.dataset.originalName) || "";
            const nuevo = input.value.trim();
            if (nuevo !== original) {
                cambios.push({ id: input.getAttribute("data-task-id"), nombre: nuevo, row, input });
            }
        });

        // Si no hay cambios, solo revertir inputs a spans
        if (cambios.length === 0) {
            revertirTodos();
            finishUI();
            btn.disabled = false;
            return;
        }

        // Enviar actualizaciones
        const resultados = await Promise.all(
            cambios.map(async (c) => {
                try {
                    const url = `${TaskConfig.endpoints.updateTarea}${c.id}`;
                    const res = await fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ nombre: c.nombre }),
                    });

                    const payloadText = await res.text();
                    let payload;
                    try {
                        payload = payloadText ? JSON.parse(payloadText) : {};
                    } catch {
                        payload = payloadText;
                    }

                    if (!res.ok) {
                        return {
                            ok: false,
                            status: res.status,
                            body: payload,
                            item: c,
                        };
                    }

                    return { ok: true, data: payload, item: c };
                } catch (error) {
                    return { ok: false, error, item: c };
                }
            })
        );

        const fallidos = resultados.filter((r) => !r.ok);
        const exitosos = resultados.filter((r) => r.ok);

        // Procesar fallidos
        if (fallidos.length > 0) {
            fallidos.forEach((f) => {
                const input = f.item.input;
                if (input) {
                    input.style.borderColor = "#dc2626";
                    input.focus();
                }
                console.error("Error guardando tarea", {
                    id: f.item.id,
                    status: f.status,
                    body: f.body,
                    error: f.error,
                });
            });
            mostrarNotificacion("error", `Error al guardar ${fallidos.length} tarea(s).`);
            btn.disabled = false;
            return;
        }

        // Si todo fue exitoso, mostrar mensaje y RECARGAR
        mostrarNotificacion("success", "Se han actualizado los nombres correctamente");
        
        // RECARGAR LA PÁGINA
        setTimeout(() => {
            window.location.reload();
        }, 1000);

        // Helpers
        function revertirTodos() {
            const allInputs = Array.from(document.querySelectorAll(".task-name-input"));
            allInputs.forEach((input) => {
                const row = input.closest(".task-row");
                const fallback = (row && row.dataset.originalName) || input.value.trim();
                const span = document.createElement("span");
                span.className = "task-name";
                span.textContent = fallback;
                input.parentNode.replaceChild(span, input);
                if (row) {
                    row.dataset.originalName = fallback;
                    row.setAttribute("data-nombre", fallback);
                }
            });
        }

        function finishUI() {
            editMode = false;
            btn.classList.remove("activo");
            if (icon && icon.classList.contains("fa-save")) icon.classList.replace("fa-save", "fa-edit");
            if (spanText) spanText.textContent = " Editar";
            rows.forEach((r) => delete r.dataset.originalName);
            if (typeof actualizarContadores === "function") actualizarContadores();
            if (typeof paginarTabla === "function") paginarTabla();
        }
    });
}

/**
 * ================================================================
 * ACCIONES DE TAREAS
 * ================================================================ */

async function manejarGuardarCambios(event) {
    const $button = $(event.currentTarget);
    const taskId = $button.data("id");
    const $row = $button.closest(".task-row");

    // Función auxiliar para convertir fecha YYYY-MM-DD a DD-MM-YYYY
    const convertirFecha = (fecha) => {
        if (!fecha) return fecha;
        const partes = fecha.split('-');
        if (partes.length === 3) {
            return `${partes[2]}-${partes[1]}-${partes[0]}`; // DD-MM-YYYY
        }
        return fecha;
    };

    // Extraer datos actuales
    const taskData = {
        fecha_inicio: $row.find(".fecha-inicio").val(),
        fecha_limite: $row.find(".fecha-fin").val(),
        prioridad: $row.attr("data-priority"), // La prioridad no es editable, se toma del data-attribute
        estado: $row.find(".estado").val(),
    };

    // Obtener datos anteriores (usar el estado original si existe, sino el actual)
    const datosAnteriores = {
        estado: $row.attr("data-status-original") || $row.attr("data-status"),
        prioridad: $row.attr("data-priority"),
        fecha_inicio: $row.attr("data-fecha-inicio"),
        fecha_limite: $row.attr("data-fecha-limite"),
    };

    // Convertir fechas actuales al formato DD-MM-YYYY para comparar
    const fechaInicioComparar = convertirFecha(taskData.fecha_inicio);
    const fechaLimiteComparar = convertirFecha(taskData.fecha_limite);

    // Verificar si solo cambió el estado (la prioridad no cambia porque no es editable en esta vista)
    const soloCambioEstado = 
        taskData.estado !== datosAnteriores.estado &&
        fechaInicioComparar === datosAnteriores.fecha_inicio &&
        fechaLimiteComparar === datosAnteriores.fecha_limite;

    // UI Loading
    const originalHtml = $button.html();
    $button
        .prop("disabled", true)
        .html('<i class="fas fa-spinner fa-spin"></i>');

    try {
        let response;
        
        // Si solo cambió el estado, usar el endpoint específico que maneja Google Calendar
        if (soloCambioEstado) {
            response = await fetch(`/tareas/${taskId}/update-status`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": TaskConfig.csrfToken,
                },
                body: JSON.stringify({ estado: taskData.estado }),
            });
        } else {
            // Si cambiaron otros campos, usar el endpoint completo
            response = await fetch(
                `${TaskConfig.endpoints.updateTarea}${taskId}`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": TaskConfig.csrfToken,
                    },
                    body: JSON.stringify(taskData),
                }
            );
        }

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        // Intentar parsear JSON solo si hay contenido
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            await response.json();
        }
        
        mostrarNotificacion("success", "Cambios guardados exitosamente");

        // Actualizar atributos de la fila (usando formato DD-MM-YYYY para las fechas)
        $row.attr("data-status", taskData.estado);
        // La prioridad no se actualiza porque no es editable en esta vista
        $row.attr("data-fecha-inicio", fechaInicioComparar);
        $row.attr("data-fecha-limite", fechaLimiteComparar);
        
        // Limpiar el estado original después de guardar exitosamente
        $row.removeAttr("data-status-original");

        // Actualizar contadores
        actualizarContadores();
        paginarTabla();

    } catch (error) {
        console.error("❌ Error al guardar:", error);
        mostrarNotificacion("error", "Error al guardar los cambios");
    } finally {
        $button.prop("disabled", false).html(originalHtml);
    }
}

async function manejarEliminarTarea(event) {
    const $button = $(event.currentTarget);
    const taskId = $button.data("id");
    const $row = $button.closest(".task-row");

    const confirmed = await mostrarConfirmacion({
        title: "¿Está seguro?",
        text: "Esta acción no se puede deshacer.",
        confirmText: "Sí, eliminar",
        cancelText: "Cancelar",
    });

    if (!confirmed) return;

    try {
        const response = await fetch(
            `${TaskConfig.endpoints.deleteTarea}${taskId}`,
            {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": TaskConfig.csrfToken,
                },
            }
        );

        const data = await response.json();

        if (data.message === "Subtarea eliminada exitosamente.") {
            mostrarNotificacion("success", "Tarea eliminada exitosamente");

            // Remover fila con animación
            $row.fadeOut(300, function () {
                $(this).remove();

                // Verificar si quedan tareas
                const remainingRows =
                    document.querySelectorAll(".task-row").length;
                if (remainingRows === 0) {
                    mostrarEstadoVacio();
                } else {
                    paginarTabla();
                    actualizarContadores();
                }
            });
        } else {
            throw new Error(data.message || "Error desconocido");
        }
    } catch (error) {
        console.error("❌ Error al eliminar:", error);
        mostrarNotificacion("error", "Error al eliminar la tarea");
    }
}

/**
 * ================================================================
 * UTILIDADES Y HELPERS
 * ================================================================ */

function generarOpcionesEstado(estadoSeleccionado) {
    // Si el estado actual es Vencida, solo permitir cambiar a Completada
    if (estadoSeleccionado === "Vencida") {
        return `
            <option value="Vencida" selected disabled>Vencida</option>
            <option value="Completada">Completada</option>
        `;
    }
    
    // Para otros estados, mostrar todas las opciones excepto Vencida
    const estados = ["Pendiente", "En progreso", "Completada"];
    return estados
        .map(
            (estado) =>
                `<option value="${estado}" ${
                    estadoSeleccionado === estado ? "selected" : ""
                }>${estado}</option>`
        )
        .join("");
}

function formatDateForInput(dateString) {
    if (!dateString) return "";
    try {
        return new Date(dateString).toISOString().split("T")[0];
    } catch (e) {
        return dateString;
    }
}

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}


// Actualizar contadores de tareas según filtros activos
function actualizarContadores() {
    const allRows = Array.from(document.querySelectorAll(".task-row")); // Todas las filas
    const rowsFiltradas = filtrarFilas(allRows); // Filas que cumplen los filtros activos
    const contadores = { Vencida: 0, Completada: 0, "En progreso": 0, Pendiente: 0 }; // Reiniciar contadores

    // Contar estados en las filas filtradas
    rowsFiltradas.forEach((row) => {
        const estado = row.getAttribute("data-status");
        if (contadores.hasOwnProperty(estado)) {
            contadores[estado]++;
        }
    });

    // Actualizar UI
    const elementos = {
        "count-vencidas": `${contadores.Vencida} Vencidas`,
        "count-completadas": `${contadores.Completada} Completadas`,
        "count-enprogreso": `${contadores["En progreso"]} En Progreso`,
        "count-pendientes": `${contadores.Pendiente} Pendientes`,
    };

    Object.entries(elementos).forEach(([id, texto]) => {
        const elemento = document.getElementById(id);
        if (elemento) elemento.textContent = texto;
    });
}

function mostrarEstadoVacio() {
    const container = document.querySelector(
        TaskConfig.selectors.tasksContainer
    );
    if (!container) return;

    container.innerHTML = `
        <div class="empty-state">
            <div class="empty-state-content">
                <div class="empty-icon">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <h3 class="empty-title">No hay tareas disponibles</h3>
                <p class="empty-message">Las tareas aparecerán aquí cuando sean creadas</p>
            </div>
        </div>
    `;
}

function mostrarLoadingState(show) {
    let loading = document.querySelector(TaskConfig.selectors.loadingState);
    // Si no existe, intentar buscar por id
    if (!loading) loading = document.getElementById('taskListLoading');
    if (!loading) return;

    // Asegurarnos de que el overlay está en el <body> para evitar problemas de stacking/contexto
    if (loading.parentElement !== document.body) {
        try {
            document.body.appendChild(loading);
        } catch (e) {
            // fallback: ignore
        }
    }

    loading.classList.toggle('hidden', !show);
    // Si mostramos, forzar focus para que el usuario lo vea inmediatamente
    if (show) {
        loading.setAttribute('tabindex', '-1');
        try { loading.focus(); } catch (e) {}
    }
}

function mostrarNotificacion(type, message) {
    // Usar SweetAlert como primer opción (estilo toast)
    if (typeof Swal !== "undefined") {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: type === "error" ? "error" : "success",
            title: message,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
        return;
    }

    // Si existe toastr, usarlo
    if (typeof toastr !== "undefined") {
        // mapear tipos comunes
        const t = type === "error" ? "error" : "success";
        toastr[t](message);
        return;
    }

    // Fallback: toast DOM simple y auto-removible
    (function showCustomToast(t, msg) {
        const id = "custom-toast-" + Date.now();
        const el = document.createElement("div");
        el.id = id;
        el.setAttribute("role", "status");
        el.style.position = "fixed";
        el.style.zIndex = 99999;
        el.style.right = "20px";
        el.style.top = "20px";
        el.style.minWidth = "220px";
        el.style.padding = "10px 14px";
        el.style.borderRadius = "8px";
        el.style.boxShadow = "0 6px 18px rgba(0,0,0,0.12)";
        el.style.color = "#fff";
        el.style.fontWeight = "600";
        el.style.fontSize = "0.94rem";
        el.style.opacity = "0";
        el.style.transition = "opacity 240ms ease, transform 240ms ease";
        el.style.transform = "translateY(-8px)";
        if (t === "error") {
            el.style.background = "linear-gradient(90deg,#ef4444,#dc2626)";
        } else {
            el.style.background = "linear-gradient(90deg,#10b981,#059669)";
        }
        el.textContent = msg;
        document.body.appendChild(el);
        // show
        requestAnimationFrame(() => {
            el.style.opacity = "1";
            el.style.transform = "translateY(0)";
        });
        // remove after timeout
        setTimeout(() => {
            el.style.opacity = "0";
            el.style.transform = "translateY(-8px)";
            setTimeout(() => {
                if (el && el.parentNode) el.parentNode.removeChild(el);
            }, 240);
        }, 2600);
    })(type, message);
}

/**
 * ================================================================
 * CONFIGURACIÓN DE MODALES
 * ================================================================ */

function mostrarModal({ title, contenido, onClose }) {
    // Crear estructura básica del modal
    const modalHtml = `
        <div class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h3 class="modal-title">${title}</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    ${contenido}
                </div>
            </div>
        </div>
    `;

    // Agregar el modal al body
    document.body.insertAdjacentHTML("beforeend", modalHtml);

    // Abrir el modal (con animación)
    const overlay = document.querySelector(".modal-overlay");
    overlay.classList.add("open");

    // Cerrar el modal al hacer clic en la X o fuera del modal
    document.querySelector(".modal-close").addEventListener("click", cerrarModal);
    overlay.addEventListener("click", function (e) {
        if (e.target === overlay) {
            cerrarModal();
        }
    });

    // Cerrar modal con animación
    function cerrarModal() {
        overlay.classList.remove("open");
        setTimeout(() => {
            overlay.remove();
            if (typeof onClose === "function") {
                onClose();
            }
        }, 300);
    }
}