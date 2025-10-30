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
};

/**
 * ================================================================
 * INICIALIZACIÓN
 * ================================================================ */

$(document).ready(function () {
    inicializarApp();
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
    cargarTareasIniciales();
}

/**
 * ================================================================
 * GESTIÓN DE DATOS Y ESTADO
 * ================================================================ */

async function cargarTareasIniciales() {
    // Solo cargar si no hay tareas ya renderizadas
    const existingRows = document.querySelectorAll(".task-row");
    if (existingRows.length > 0) {
        AppState.tareas = Array.from(existingRows).map((row) => ({
            id: row.getAttribute("data-task-id"),
            estado: row.getAttribute("data-status"),
            prioridad: row.getAttribute("data-priority"),
        }));
        paginarTabla();
        configurarSelect2();
    } else {
        await recargarTareas();
    }
}

async function recargarTareas() {
    if (AppState.isLoading) return;

    AppState.isLoading = true;
    mostrarLoadingState(true);

    try {
        const response = await fetch(TaskConfig.endpoints.datosSubtareas);
        const data = await response.json();

        if (response.ok) {
            AppState.tareas = data;
            renderizarTabla(data);
            actualizarContadores();
        } else {
            throw new Error("Error al cargar los datos");
        }
    } catch (error) {
        console.error("❌ Error al recargar tareas:", error);
        mostrarNotificacion("error", "Error al cargar los datos");
    } finally {
        AppState.isLoading = false;
        mostrarLoadingState(false);
    }
}

/**
 * ================================================================
 * RENDERIZADO DE LA TABLA
 * ================================================================ */

function renderizarTabla(tareasData) {
    const tbody = document.querySelector(TaskConfig.selectors.tasksTableBody);
    if (!tbody) return;

    // Extraer todas las subtareas
    const todasLasTareas = [];
    tareasData.forEach((tg) => {
        if (tg.subtareas && Array.isArray(tg.subtareas)) {
            todasLasTareas.push(...tg.subtareas);
        }
    });

    // Limpiar tabla
    tbody.innerHTML = "";

    if (todasLasTareas.length === 0) {
        mostrarEstadoVacio();
        return;
    }

    // Renderizar filas
    todasLasTareas.forEach((tarea) => {
        const row = crearFilaTarea(tarea);
        tbody.appendChild(row);
    });

    // Configurar funcionalidades
    configurarSelect2();
    paginarTabla();
    aplicarFiltros();
}

function crearFilaTarea(task) {
    const row = document.createElement("tr");
    row.className = "task-row";
    row.setAttribute("data-task-id", task.id);
    row.setAttribute("data-status", task.estado);
    row.setAttribute("data-priority", task.prioridad);
    row.setAttribute("data-fecha-inicio", formatDateForInput(task.fecha_inicio));
    row.setAttribute("data-fecha-limite", formatDateForInput(task.fecha_limite));


    row.innerHTML = `
        <td class="task-name-cell">
            <div class="task-name-content">
                <span class="task-name" title="${escapeHtml(
                    task.nombre
                )}">${escapeHtml(task.nombre)}</span>
            </div>
        </td>

        <td class="priority-cell">
            <select class="priority-select prioridad" data-id="${task.id}">
                ${generarOpcionesPrioridad(task.prioridad)}
            </select>
        </td>

        <td class="status-cell">
            <select class="status-select estado" data-id="${task.id}">
                ${generarOpcionesEstado(task.estado)}
            </select>
        </td>

        <td class="date-cell">
            <input type="date" 
                   class="date-input fecha-inicio"
                   value="${formatDateForInput(task.fecha_inicio)}"
                   data-id="${task.id}" />
        </td>

        <td class="date-cell">
            <input type="date" 
                   class="date-input fecha-fin"
                   value="${formatDateForInput(task.fecha_limite)}"
                   data-id="${task.id}" />
        </td>

        <td class="actions-cell">
            <div class="table-actions">
                <button type="button" class="action-button save-button guardar-cambios" 
                        data-id="${task.id}" title="Guardar cambios">
                    <i class="fa-solid fa-save"></i>
                </button>
                <button type="button" class="action-button delete-button eliminar-tarea" 
                        data-id="${task.id}" title="Eliminar tarea">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </td>
    `;

    return row;
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
    const totalPaginas = Math.ceil(rowsFiltradas.length / porPagina) || 1;

    // Actualizar estado
    AppState.paginacion.totalPaginas = totalPaginas;
    if (AppState.paginacion.paginaActual > totalPaginas) {
        AppState.paginacion.paginaActual = totalPaginas;
    }

    // Ocultar todas las filas
    allRows.forEach((row) => (row.style.display = "none"));

    // Mostrar solo las filas de la página actual
    const inicio = (AppState.paginacion.paginaActual - 1) * porPagina;
    const fin = inicio + porPagina;

    rowsFiltradas.slice(inicio, fin).forEach((row) => {
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

function ordenarFilas(filas) {
    // Definir orden de prioridad de los estados
    const ordenEstados = {
        Pendiente: 1,
        "En progreso": 2,
        Completada: 3,
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
        const fechaB = b.getAttribute('data-fecha-limite') || b.querySelector('.fecha-inicio')?.value || "";

        //Aqui se convierte el Date para comparar correctamente
        const dateA = new Date(fechaA);
        const dateB = new Date(fechaB);

        return dateA - dateB; //Orden ascendente (las más antiguas primero)
    });


}

/**
 * ================================================================
 * SISTEMA DE FILTROS
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

/**
 * ================================================================
 * ACCIONES DE TAREAS
 * ================================================================ */

async function manejarGuardarCambios(event) {
    const $button = $(event.currentTarget);
    const taskId = $button.data("id");
    const $row = $button.closest(".task-row");

    // Extraer datos
    const taskData = {
        fecha_inicio: $row.find(".fecha-inicio").val(),
        fecha_limite: $row.find(".fecha-fin").val(),
        prioridad: $row.find(".prioridad").val(),
        estado: $row.find(".estado").val(),
    };

    // UI Loading
    const originalHtml = $button.html();
    $button
        .prop("disabled", true)
        .html('<i class="fas fa-spinner fa-spin"></i>');

    try {
        const response = await fetch(
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

        if (response.ok) {
            mostrarNotificacion("success", "Cambios guardados exitosamente");

            // Actualizar atributos de la fila
            $row.attr("data-status", taskData.estado);
            $row.attr("data-priority", taskData.prioridad); // Actualizar atributo de prioridad
            $row.attr("data-fecha-inicio", taskData.fecha_inicio); // Actualizar atributo de fecha inicio
            $row.attr("data-fecha-limite", taskData.fecha_limite); // Actualizar atributo de fecha límite

            // Actualizar contadores
            actualizarContadores(); // Actualizar contadores después de guardar
            paginarTabla(); // Reaplicar paginación para reflejar cambios

        } else {
            throw new Error(`Error HTTP: ${response.status}`);
        }
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

function generarOpcionesPrioridad(prioridadSeleccionada) {
    const prioridades = {
        baja: "Baja",
        media: "Media",
        alta: "Alta",
        urgente: "Urgente",
    };
    return Object.entries(prioridades)
        .map(
            ([value, label]) =>
                `<option value="${value}" ${
                    prioridadSeleccionada === value ? "selected" : ""
                }>${label}</option>`
        )
        .join("");
}

function generarOpcionesEstado(estadoSeleccionado) {
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
    const contadores = { Completada: 0, "En progreso": 0, Pendiente: 0 }; // Reiniciar contadores

    // Contar estados en las filas filtradas
    rowsFiltradas.forEach((row) => {
        const estado = row.getAttribute("data-status");
        if (contadores.hasOwnProperty(estado)) {
            contadores[estado]++;
        }
    });

    // Actualizar UI
    const elementos = {
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
    const loading = document.querySelector(TaskConfig.selectors.loadingState);
    if (loading) {
        loading.classList.toggle("hidden", !show);
    }
}

function mostrarNotificacion(type, message) {
    if (typeof toastr !== "undefined") {
        toastr[type](message);
    } else {
        const emoji = type === "success" ? "✅" : "❌";
        console.log(`${emoji} ${message}`);
    }
}

async function mostrarConfirmacion({ title, text, confirmText, cancelText }) {
    if (typeof Swal !== "undefined") {
        const result = await Swal.fire({
            title,
            text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#6b7280",
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
        });
        return result.isConfirmed;
    } else {
        return confirm(`${title}\n${text}`);
    }
}
