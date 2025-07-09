// === CONFIGURACIÓN GLOBAL ===
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
        tasksGrid: "#tasksGrid",
        loadingState: "#taskListLoading",
    },
};

// Variable para rastrear la vista actual
let currentView = "cards";
let filtroActivo = "all";

/**
 * ================================================================
 * FUNCIONES PRINCIPALES DE GESTIÓN
 * ================================================================ */

function recargarSubtareas() {
    mostrarGlobalLoader(true); // Mostrar loader global
    mostrarLoadingState(true);

    fetch(TaskConfig.endpoints.datosSubtareas)
        .then((response) => response.json())
        .then((data) => {
            actualizarLista(data);
            if (typeof actualizarKanban === "function") actualizarKanban(data);
            if (typeof actualizarTimeline === "function")
                actualizarTimeline(data);
        })
        .catch((error) => {
            console.error("❌ Error al recargar las subtareas:", error);
            mostrarNotificacion("error", "Error al cargar los datos");
        })
        .finally(() => {
            mostrarLoadingState(false);
            // mostrarGlobalLoader(false); // <-- ELIMINA O COMENTA ESTA LÍNEA
        });
}

function actualizarLista(tareasGenerales) {
    if (currentView === "cards") {
        actualizarVistaTargetas(tareasGenerales);
    } else {
        actualizarVistaTabla(tareasGenerales);
    }

    actualizarFooterContadores();
}

function actualizarVistaTargetas(tareasGenerales) {
    const tasksGrid = document.querySelector("#tasksGrid");
    if (!tasksGrid) return;

    // Limpiar grid
    tasksGrid.innerHTML = "";

    // Verificar si hay tareas
    const totalTareas = tareasGenerales.reduce(
        (acc, tg) => acc + tg.subtareas.length,
        0
    );

    if (totalTareas === 0) {
        mostrarEstadoVacio(tasksGrid);
        return;
    }

    // Generar tarjetas
    tareasGenerales.forEach((tg) => {
        tg.subtareas.forEach((task, index) => {
            const card = crearTarjetaTarea(task, index);
            tasksGrid.appendChild(card);
        });
    });

    // Reasociar eventos y plugins
    reasociarEventosLista();
    aplicarAnimacionesEntrada();
    paginarTarjetas(); // <-- Agrega esto al final
}

function crearTarjetaTarea(task, index = 0) {
    const card = document.createElement("div");
    card.className = "task-card";
    card.setAttribute("data-task-id", task.id);
    card.setAttribute("data-status", task.estado);
    card.setAttribute("data-priority", task.prioridad);

    // Añadir delay de animación
    card.style.animationDelay = `${index * 0.1}s`;

    card.innerHTML = `
        <!-- Header de la tarjeta -->
        <div class="task-card-header">
            <div class="task-title-section">
                <h3 class="task-title">${escapeHtml(task.nombre)}</h3>
                <div class="task-meta">
                    <span class="priority-badge priority-${task.prioridad}">
                        <i class="fa-solid fa-flag"></i>
                        ${capitalizeFirst(task.prioridad)}
                    </span>
                    <span class="status-badge status-${task.estado
                        .toLowerCase()
                        .replace(" ", "-")}">
                        ${getEstadoIconHTML(task.estado)}
                        ${task.estado}
                    </span>
                </div>
            </div>
        </div>

        <!-- Contenido de la tarjeta -->
        <div class="task-card-content">
            
            <!-- Fechas -->
            <div class="task-dates">
                <div class="date-group">
                    <label class="date-label">
                        <i class="fa-solid fa-calendar-day"></i>
                        Inicio
                    </label>
                    <input type="date" 
                           class="date-input fecha-inicio"
                           value="${formatDateForInput(task.fecha_inicio)}"
                           data-id="${task.id}"
                           aria-label="Fecha de inicio para ${escapeHtml(
                               task.nombre
                           )}" />
                </div>
                
                <div class="date-group">
                    <label class="date-label">
                        <i class="fa-solid fa-calendar-check"></i>
                        Fin
                    </label>
                    <input type="date" 
                           class="date-input fecha-fin"
                           value="${formatDateForInput(task.fecha_limite)}"
                           data-id="${task.id}"
                           aria-label="Fecha límite para ${escapeHtml(
                               task.nombre
                           )}" />
                </div>
            </div>

            <!-- Controles -->
            <div class="task-controls">
                <div class="control-group">
                    <label class="control-label">
                        <i class="fa-solid fa-bolt"></i>
                        Prioridad
                    </label>
                    <select class="priority-select prioridad-select prioridad" 
                            data-id="${task.id}"
                            aria-label="Prioridad para ${escapeHtml(
                                task.nombre
                            )}">
                        ${generarOpcionesPrioridad(task.prioridad)}
                    </select>
                </div>

                <div class="control-group">
                    <label class="control-label">
                        <i class="fa-solid fa-tasks"></i>
                        Estado
                    </label>
                    <select class="status-select estado-select estado" 
                            data-id="${task.id}"
                            aria-label="Estado para ${escapeHtml(task.nombre)}">
                        ${generarOpcionesEstado(task.estado)}
                    </select>
                </div>
            </div>
        </div>

        <!-- Footer de la tarjeta -->
        <div class="task-card-footer">
            <div class="task-actions">
                <button type="button"
                        class="action-button save-button guardar-cambios" 
                        data-id="${task.id}"
                        title="Guardar cambios">
                    <i class="fa-solid fa-save"></i>
                    <span>Guardar</span>
                </button>

                <button type="button"
                        class="action-button delete-button eliminar-tarea" 
                        data-id="${task.id}"
                        title="Eliminar tarea">
                    <i class="fa-solid fa-trash"></i>
                    <span>Eliminar</span>
                </button>
            </div>
        </div>
    `;

    return card;
}

/**
 * ================================================================
 * FUNCIONES DE UTILIDAD PARA GENERAR OPCIONES
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
                }>
            ${label}
        </option>`
        )
        .join("");
}

function generarOpcionesEstado(estadoSeleccionado) {
    const estados = ["Pendiente", "En progreso", "Completada", "Vencida"];

    return estados
        .map(
            (estado) =>
                `<option value="${estado}" ${
                    estadoSeleccionado === estado ? "selected" : ""
                }>
            ${estado}
        </option>`
        )
        .join("");
}

function getEstadoIconHTML(estado) {
    const iconMap = {
        Pendiente: '<i class="fa-solid fa-hourglass-start"></i>',
        "En progreso": '<i class="fa-solid fa-spinner"></i>',
        Completada: '<i class="fa-solid fa-check-circle"></i>',
        Vencida: '<i class="fa-solid fa-exclamation-triangle"></i>',
    };
    return iconMap[estado] || '<i class="fa-solid fa-question-circle"></i>';
}

function getPrioridadIcon(prioridad) {
    const iconMap = {
        alta: '<i class="fa fa-flag me-1 text-warning"></i>',
        media: '<i class="fa fa-flag me-1 text-info"></i>',
        baja: '<i class="fa fa-flag me-1 text-success"></i>',
        urgente: '<i class="fa fa-flag me-1 text-danger"></i>',
    };
    return iconMap[prioridad] || '<i class="fa fa-flag me-1 text-muted"></i>';
}

function getEstadoIcon(estado) {
    const iconMap = {
        Pendiente: '<i class="fa fa-hourglass-start me-1 text-secondary"></i>',
        "En progreso": '<i class="fa fa-spinner me-1 text-primary"></i>',
        Completada: '<i class="fa fa-check-circle me-1 text-success"></i>',
        Vencida: '<i class="fa fa-exclamation-circle me-1 text-danger"></i>',
    };
    return (
        iconMap[estado] ||
        '<i class="fa fa-question-circle me-1 text-muted"></i>'
    );
}

/**
 * ================================================================
 * GESTIÓN DE EVENTOS Y SELECT2
 * ================================================================ */

function reasociarEventosLista() {
    // Solo destruir si existen y están inicializados
    $(".prioridad, .estado, .prioridad-select, .estado-select").each(
        function () {
            if ($(this).data("select2")) {
                $(this).select2("destroy");
            }
        }
    );

    // Configurar Select2 para Prioridad
    $(".prioridad, .prioridad-select").select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        templateResult: function (data) {
            if (!data.id) return data.text;
            return $(`<span>${getPrioridadIcon(data.id)} ${data.text}</span>`);
        },
        templateSelection: function (data) {
            return $(`<span>${getPrioridadIcon(data.id)} ${data.text}</span>`);
        },
    });

    // Configurar Select2 para Estado
    $(".estado, .estado-select").select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        templateResult: function (data) {
            if (!data.id) return data.text;
            return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
        },
        templateSelection: function (data) {
            return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
        },
    });

    aplicarAtributosDatos();
}

function aplicarAtributosDatos() {
    // Para prioridad
    $(".prioridad, .prioridad-select")
        .on("change", function () {
            $(this)
                .next(".select2-container")
                .find(".select2-selection__rendered")
                .attr("data-priority", $(this).val());
        })
        .trigger("change");

    // Para estado
    $(".estado, .estado-select")
        .on("change", function () {
            $(this)
                .next(".select2-container")
                .find(".select2-selection__rendered")
                .attr("data-estado", $(this).val());
        })
        .trigger("change");
}

/**
 * ================================================================
 * EVENTOS DE INTERACCIÓN CON TAREAS
 * ================================================================ */

// Configurar todos los eventos cuando el DOM esté listo
$(document).ready(function () {
    // Configurar Select2 inicial
    configurarSelect2Inicial();

    // Configurar filtros
    configurarFiltros();

    // Configurar cambio de vista
    configurarSelectorVista();

    // Event Listeners para acciones
    configurarEventListeners();
    mostrarGlobalLoader(false); // Oculta el loader si ya está todo listo
});

function configurarSelect2Inicial() {
    // Select2 para elementos que ya existen
    $(".prioridad-select").select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        templateResult: function (data) {
            if (!data.id) return data.text;
            return $(`<span>${getPrioridadIcon(data.id)} ${data.text}</span>`);
        },
        templateSelection: function (data) {
            return $(`<span>${getPrioridadIcon(data.id)} ${data.text}</span>`);
        },
    });

    $(".estado-select").select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        templateResult: function (data) {
            if (!data.id) return data.text;
            return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
        },
        templateSelection: function (data) {
            return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
        },
    });

    aplicarAtributosDatos();
}

function configurarSelectorVista() {
    $(".view-btn").on("click", function () {
        const newView = $(this).data("view");

        // Actualizar botones activos
        $(".view-btn").removeClass("active");
        $(this).addClass("active");

        // Cambiar vista
        cambiarVista(newView);
    });
}

function cambiarVista(vista) {
    currentView = vista;

    // Ocultar todas las vistas
    $(".view-container").addClass("hidden");

    if (vista === "cards") {
        // Mostrar vista de tarjetas
        $("#cardsView").removeClass("hidden");
        $("#filtersContainer").removeClass("hidden");

        // Reconfigurar Select2 para tarjetas
        setTimeout(() => {
            reasociarEventosLista();
            paginarTarjetas();
        }, 100);
    } else if (vista === "table") {
        // Mostrar vista de tabla
        $("#tableView").removeClass("hidden");
        $("#filtersContainer").addClass("hidden");

        // Reconfigurar Select2 para tabla
        setTimeout(() => {
            configurarSelect2Tabla();
        }, 100);
    }

    // Actualizar contadores
    actualizarFooterContadores();
}

function aplicarVista(vista) {
    cambiarVista(vista);
}

function configurarSelect2Tabla() {
    // Destruir Select2 existentes en tabla
    $("#tableView .prioridad-select, #tableView .estado-select").each(
        function () {
            if ($(this).data("select2")) {
                $(this).select2("destroy");
            }
        }
    );

    // Configurar Select2 para tabla
    $("#tableView .prioridad-select").select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        templateResult: function (data) {
            if (!data.id) return data.text;
            return $(`<span>${getPrioridadIcon(data.id)} ${data.text}</span>`);
        },
        templateSelection: function (data) {
            return $(`<span>${getPrioridadIcon(data.id)} ${data.text}</span>`);
        },
    });

    $("#tableView .estado-select").select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        templateResult: function (data) {
            if (!data.id) return data.text;
            return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
        },
        templateSelection: function (data) {
            return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
        },
    });
}

function configurarEventListeners() {
    // Guardar cambios
    $(document).on("click", ".guardar-cambios", manejarGuardarCambios);

    // Eliminar tarea
    $(document).on("click", ".eliminar-tarea", manejarEliminarTarea);

    // Cambios en selects dinámicos
    $(document).on("change", ".prioridad", function () {
        const $select = $(this);
        const $card = $select.closest(".task-card");
        const nuevaPrioridad = $select.val();
        const $priorityBadge = $card.find(".priority-badge");
        $priorityBadge
            .removeClass(
                "priority-baja priority-media priority-alta priority-urgente"
            )
            .addClass(`priority-${nuevaPrioridad}`)
            .html(
                `<i class="fa-solid fa-flag"></i> ${capitalizeFirst(
                    nuevaPrioridad
                )}`
            );
    });

    $(document).on("change", ".estado", function () {
        $(this)
            .next(".select2-container")
            .find(".select2-selection__rendered")
            .attr("data-estado", $(this).val());
    });

    $(document).on("change", ".estado", function () {
        const $select = $(this);
        const $card = $select.closest(".task-card");
        const nuevoEstado = $select.val();
        const $statusBadge = $card.find(".status-badge");
        $statusBadge
            .removeClass(
                "status-pendiente status-en-progreso status-completada status-vencida"
            )
            .addClass(`status-${nuevoEstado.toLowerCase().replace(" ", "-")}`)
            .html(`${getEstadoIconHTML(nuevoEstado)} ${nuevoEstado}`);
    });
}

async function manejarGuardarCambios(event) {
    const $button = $(event.currentTarget);
    const taskId = $button.data("id");
    const $card = $button.closest(".task-card");

    // Extraer datos de la tarjeta
    const taskData = {
        fecha_inicio: $card.find(".fecha-inicio").val(),
        fecha_limite: $card.find(".fecha-fin").val(),
        prioridad: $card.find(".prioridad").val(),
        estado: $card.find(".estado").val(),
    };

    // UI Loading
    const originalHtml = $button.html();
    $button
        .prop("disabled", true)
        .html('<i class="fas fa-spinner fa-spin"></i>')
        .addClass("loading");

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

            // Actualizar atributos de la tarjeta
            $card.attr("data-status", taskData.estado);
            $card.attr("data-priority", taskData.prioridad);

            // Actualizar badges visuales
            actualizarBadgesTarjeta($card, taskData);

            // Recargar lista completa
            await recargarSubtareas();
        } else {
            throw new Error(`Error HTTP: ${response.status}`);
        }
    } catch (error) {
        console.error("❌ Error al guardar:", error);
        mostrarNotificacion("error", "Error al guardar los cambios");
    } finally {
        $button
            .prop("disabled", false)
            .html(originalHtml)
            .removeClass("loading");
    }
}

async function manejarEliminarTarea(event) {
    const $button = $(event.currentTarget);
    const taskId = $button.data("id");
    const $card = $button.closest(".task-card");

    const confirmed = await mostrarConfirmacion({
        title: "¿Está seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
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
            mostrarNotificacion("success", "La tarea ha sido eliminada");

            // Animación de salida antes de eliminar
            $card.addClass("removing");
            setTimeout(() => {
                $card.remove();
                actualizarFooterContadores(); // <--- Agrega esto

                // Verificar si queda alguna tarjeta
                const remainingCards =
                    document.querySelectorAll(".task-card").length;
                if (remainingCards === 0) {
                    mostrarEstadoVacio(
                        document.querySelector(TaskConfig.selectors.tasksGrid)
                    );
                }
            }, 300);
        } else {
            throw new Error(data.message || "Error desconocido");
        }
    } catch (error) {
        console.error("❌ Error al eliminar:", error);
        mostrarNotificacion("error", "Hubo un problema al eliminar la tarea");
    }
}

/**
 * ================================================================
 * SISTEMA DE FILTROS
 * ================================================================ */

function configurarFiltros() {
    $(document).on("click", ".filter-btn", function () {
        const filter = $(this).data("filter");

        // Actualizar estado visual de botones
        $(".filter-btn").removeClass("active").prop("disabled", false);
        $(this).addClass("active").prop("disabled", true);

        // Aplicar filtro
        aplicarFiltro(filter);
    });

    // Al cargar, deshabilita el filtro activo por defecto
    setTimeout(() => {
        $(".filter-btn.active").prop("disabled", true);
    }, 0);
}

function aplicarFiltro(filter) {
    filtroActivo = filter; // Guardar filtro activo
    PAGINACION_TAREAS.paginaActual = 1; // Reiniciar a la primera página
    paginarTarjetas(); // Repaginar según el filtro
}

/**
 * ================================================================
 * FUNCIONES DE UTILIDAD Y HELPERS
 * ================================================================ */

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDateForInput(dateString) {
    if (!dateString) return "";
    try {
        const date = new Date(dateString);
        return date.toISOString().split("T")[0];
    } catch (e) {
        return dateString;
    }
}

function mostrarEstadoVacio(container) {
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
    const $loading = $(TaskConfig.selectors.loadingState);
    if (show) {
        $loading.removeClass("hidden").addClass("loading-visible");
    } else {
        $loading.addClass("hidden").removeClass("loading-visible");
    }
}

function mostrarNotificacion(type, message) {
    if (typeof toastr !== "undefined") {
        toastr[type](message);
    } else {
        const emoji = type === "success" ? "✅" : "❌";
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
            customClass: {
                popup: "custom-swal-popup",
                confirmButton: "custom-swal-confirm",
                cancelButton: "custom-swal-cancel",
            },
        });
        return result.isConfirmed;
    } else {
        return confirm(`${title}\n${text}`);
    }
}

function actualizarBadgesTarjeta($card, taskData) {
    // Actualizar badge de prioridad
    const $priorityBadge = $card.find(".priority-badge");
    $priorityBadge
        .removeClass(
            "priority-baja priority-media priority-alta priority-urgente"
        )
        .addClass(`priority-${taskData.prioridad}`)
        .html(
            `<i class="fa-solid fa-flag"></i> ${capitalizeFirst(
                taskData.prioridad
            )}`
        );

    // Actualizar badge de estado
    const $statusBadge = $card.find(".status-badge");
    $statusBadge
        .removeClass(
            "status-pendiente status-en-progreso status-completada status-vencida"
        )
        .addClass(`status-${taskData.estado.toLowerCase().replace(" ", "-")}`)
        .html(`${getEstadoIconHTML(taskData.estado)} ${taskData.estado}`);
}

function aplicarAnimacionesEntrada() {
    const cards = document.querySelectorAll(".task-card");
    if (cards.length === 0) {
        mostrarGlobalLoader(false);
        return;
    }

    let animacionAplicada = false;

    cards.forEach((card) => card.classList.remove("fade-in-up"));

    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add("fade-in-up");
        animacionAplicada = true;
        if (index === cards.length - 1) {
            card.addEventListener("animationend", function handler() {
                mostrarGlobalLoader(false);
                card.removeEventListener("animationend", handler);
            });
        }
    });

    // Si no hay animaciones CSS activas, oculta el loader igual después de un pequeño delay
    if (!animacionAplicada) {
        setTimeout(() => mostrarGlobalLoader(false), 300);
    }
}

function actualizarFooterContadores() {
    // Contar tareas por estado en el DOM
    const cards = document.querySelectorAll(".task-card");
    let completadas = 0,
        enprogreso = 0,
        pendientes = 0;

    cards.forEach((card) => {
        const estado = card.getAttribute("data-status");
        if (estado === "Completada") completadas++;
        else if (estado === "En progreso") enprogreso++;
        else if (estado === "Pendiente") pendientes++;
    });

    // Actualizar los contadores en el footer
    const $completadas = document.getElementById("count-completadas");
    const $enprogreso = document.getElementById("count-enprogreso");
    const $pendientes = document.getElementById("count-pendientes");

    if ($completadas) $completadas.textContent = `${completadas} Completadas`;
    if ($enprogreso) $enprogreso.textContent = `${enprogreso} En Progreso`;
    if ($pendientes) $pendientes.textContent = `${pendientes} Pendientes`;
}

// Configuración de paginación
const PAGINACION_TAREAS = {
    porPagina: 8, // Cambia este número para mostrar más/menos tarjetas por página
    paginaActual: 1,
    totalPaginas: 1,
};

function paginarTarjetas() {
    const allCards = Array.from(document.querySelectorAll(".task-card"));
    // Filtrar según el filtro activo
    const cards = allCards.filter((card) => {
        const status = card.getAttribute("data-status");
        return filtroActivo === "all" || status === filtroActivo;
    });

    const pagContainer = document.getElementById("tasksPagination");
    const porPagina = PAGINACION_TAREAS.porPagina;
    const total = cards.length;
    const totalPaginas = Math.ceil(total / porPagina) || 1;
    let paginaActual = PAGINACION_TAREAS.paginaActual;

    // Ajustar página si está fuera de rango
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;
    if (paginaActual < 1) paginaActual = 1;
    PAGINACION_TAREAS.paginaActual = paginaActual;
    PAGINACION_TAREAS.totalPaginas = totalPaginas;

    // Ocultar todas las tarjetas primero
    allCards.forEach((card) => (card.style.display = "none"));

    // Mostrar solo las tarjetas de la página actual y filtro
    cards.forEach((card, idx) => {
        if (
            idx >= (paginaActual - 1) * porPagina &&
            idx < paginaActual * porPagina
        ) {
            card.style.display = "";
        }
    });

    // Renderizar controles de paginación
    if (totalPaginas <= 1) {
        pagContainer.innerHTML = "";
        return;
    }

    let html = "";
    html += `<button class="pagination-btn" ${
        paginaActual === 1 ? "disabled" : ""
    } data-page="${paginaActual - 1}">&laquo;</button>`;

    html += `<button class="pagination-btn${
        paginaActual === 1 ? " active" : ""
    }" data-page="1">1</button>`;

    if (paginaActual > 4) {
        html += `<span style="padding:0 0.5rem;">...</span>`;
    }

    let start = Math.max(2, paginaActual - 1);
    let end = Math.min(totalPaginas - 1, paginaActual + 1);

    for (let i = start; i <= end; i++) {
        if (i !== 1 && i !== totalPaginas) {
            html += `<button class="pagination-btn${
                i === paginaActual ? " active" : ""
            }" data-page="${i}">${i}</button>`;
        }
    }

    if (paginaActual < totalPaginas - 3) {
        html += `<span style="padding:0 0.5rem;">...</span>`;
    }

    if (totalPaginas > 1) {
        html += `<button class="pagination-btn${
            paginaActual === totalPaginas ? " active" : ""
        }" data-page="${totalPaginas}">${totalPaginas}</button>`;
    }

    html += `<button class="pagination-btn" ${
        paginaActual === totalPaginas ? "disabled" : ""
    } data-page="${paginaActual + 1}">&raquo;</button>`;

    pagContainer.innerHTML = html;
}

// Evento para cambiar de página
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("pagination-btn") && !e.target.disabled) {
        const page = parseInt(e.target.getAttribute("data-page"));
        if (!isNaN(page)) {
            PAGINACION_TAREAS.paginaActual = page;
            paginarTarjetas();
        }
    }
});

// Llama a la paginación después de renderizar o actualizar la lista
function actualizarLista(tareasGenerales) {
    aplicarAnimacionesEntrada();
    paginarTarjetas();

    // Calcula el total de tareas antes del log
    const totalTareas = tareasGenerales.reduce(
        (acc, tg) => acc + (tg.subtareas ? tg.subtareas.length : 0),
        0
    );
}

$(document).ready(function () {
    mostrarGlobalLoader(true); // Mostrar loader al cargar la página
    paginarTarjetas();
    aplicarAnimacionesEntrada(); // <-- Agrega esto aquí
    // ...otros inits...
});

// Exponer funciones globales
window.recargarSubtareas = recargarSubtareas;
window.actualizarLista = actualizarLista;
window.aplicarFiltro = aplicarFiltro;

function mostrarGlobalLoader(show = true) {
    const loader = document.getElementById("globalLoader");
    if (!loader) return;
    if (show) {
        loader.classList.remove("hidden");
    } else {
        loader.classList.add("hidden");
    }
}
