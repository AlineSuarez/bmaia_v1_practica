function activarSortableKanban() {
    // Solo inicializa sortable en el kanban activo
    $(".kanban-container .task-list")
        .sortable({
            connectWith: ".task-list",
            placeholder: "ui-state-highlight",
            items: ".task-card",
            tolerance: "pointer",
            revert: 0,
            scroll: true, // Ya lo tienes
            scrollSensitivity: 100, // Distancia del borde para activar scroll
            scrollSpeed: 20, // Velocidad del scroll automático
            handle: ".drag-handle",
            helper: function (e, item) {
                var originalWidth = item.outerWidth();
                var clone = item.clone();
                clone.css({
                    width: originalWidth,
                    "box-sizing": "border-box",
                    transition: "none",
                });
                clone.addClass("dragging-card");
                return clone;
            },
            start: function (event, ui) {
                ui.placeholder.height(ui.item.outerHeight());
                ui.placeholder.width(ui.item.outerWidth());
                ui.placeholder.css("box-sizing", "border-box");
                ui.item.css("transition", "none");

                // Mejorar scroll en móvil
                if (window.innerWidth <= 768) {
                    // Habilitar scroll automático más agresivo en móvil
                    ui.helper.data("scrollSpeed", 40);
                    ui.helper.data("scrollSensitivity", 50);
                }
            },
            drag: function (event, ui) {
                // Auto-scroll vertical de la página (no de la columna)
                if (window.innerWidth <= 768) {
                    var scrollContainer =
                        document.scrollingElement || document.documentElement;
                    var scrollTop = scrollContainer.scrollTop;
                    var windowHeight = window.innerHeight;
                    var mouseY = event.originalEvent.touches
                        ? event.originalEvent.touches[0].clientY
                        : event.pageY;

                    // Si el dedo/mouse está cerca del borde superior, sube la página
                    if (mouseY < 80) {
                        scrollContainer.scrollTop = scrollTop - 20;
                    }
                    // Si está cerca del borde inferior, baja la página
                    else if (mouseY > windowHeight - 80) {
                        scrollContainer.scrollTop = scrollTop + 20;
                    }
                }
            },
            stop: function (event, ui) {
                ui.item.css("transition", "");
            },
            receive: function (event, ui) {
                const card = ui.item;
                const id = card.data("task-id");
                const newEstado = $(this).data("status");
                fetch(`/tareas/${id}/update-status`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: `_method=PATCH&estado=${encodeURIComponent(
                        newEstado
                    )}`,
                }).then((res) => {
                    if (res.ok) {
                        toastr.success("Estado actualizado.");
                        actualizarContadoresKanban();
                    } else {
                        toastr.error("No se pudo actualizar el estado.");
                    }
                });
            },
        })
        .disableSelection();
}

// Actualiza los contadores de tareas en cada columna
function actualizarContadoresKanban() {
    $(".kanban-column").each(function () {
        const estado = $(this).data("status");
        const count = $(this).find(".task-card").length;
        $(this).find(".task-count").text(count);

        // Muestra/oculta mensaje vacío
        const emptyMsg = $(this).find(".empty-column");
        if (count === 0) {
            emptyMsg.show();
        } else {
            emptyMsg.hide();
        }
    });
}

$(document).ready(function () {
    activarSortableKanban();
    actualizarContadoresKanban();
});

// Actualizar la vista de la lista con los datos más recientes
function actualizarLista(tareasGenerales) {
    const tbody = document.querySelector("#subtareasTable tbody");
    if (!tbody) return;
    tbody.innerHTML = ""; // Limpia la tabla actual

    tareasGenerales.forEach((tg) => {
        tg.subtareas.forEach((task) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                    <td>${task.nombre}</td>
                    <td><input type="date" class="form-control fecha-inicio" value="${
                        task.fecha_inicio
                    }" data-id="${task.id}" /></td>
                    <td><input type="date" class="form-control fecha-fin" value="${
                        task.fecha_limite
                    }" data-id="${task.id}" /></td>
                    <td>
                        <select class="form-select prioridad" data-id="${
                            task.id
                        }">
                            <option value="baja" ${
                                task.prioridad === "baja" ? "selected" : ""
                            }>Baja</option>
                            <option value="media" ${
                                task.prioridad === "media" ? "selected" : ""
                            }>Media</option>
                            <option value="alta" ${
                                task.prioridad === "alta" ? "selected" : ""
                            }>Alta</option>
                            <option value="urgente" ${
                                task.prioridad === "urgente" ? "selected" : ""
                            }>Urgente</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select estado" data-id="${task.id}">
                            <option value="Pendiente" ${
                                task.estado === "Pendiente" ? "selected" : ""
                            }>Pendiente</option>
                            <option value="En progreso" ${
                                task.estado === "En progreso" ? "selected" : ""
                            }>En progreso</option>
                            <option value="Completada" ${
                                task.estado === "Completada" ? "selected" : ""
                            }>Completada</option>
                            <option value="Vencida" ${
                                task.estado === "Vencida" ? "selected" : ""
                            }>Vencida</option>
                        </select>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-success btn-sm guardar-cambios" data-id="${
                                task.id
                            }">
                                <i class="fa-solid fa-save"></i>
                            </button>
                            <button class="btn btn-danger btn-sm eliminar-tarea" data-id="${
                                task.id
                            }">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
            tbody.appendChild(row);
        });
    });
    reasociarEventosLista(); // Reasociar los eventos de la lista
}

// Reasociar los eventos de la lista (como select2)
function reasociarEventosLista() {
    $(".estado").select2({
        width: "100%",
        templateResult: function (data) {
            return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
        },
        templateSelection: function (data) {
            return $(`<span>${getEstadoIcon(data.id)} ${data.text}</span>`);
        },
    });
}

// Prueba mínima de drag and drop
$(function () {
    $(".task-list")
        .sortable({
            connectWith: ".task-list",
            placeholder: "ui-state-highlight",
            items: ".task-card",
            tolerance: "pointer",
            revert: 0, // Sin animación al soltar
            scroll: true,
            handle: ".drag-handle", // <-- SOLO se puede arrastrar desde el handle
            helper: function (e, item) {
                // Clona la tarjeta y le fuerza el ancho exacto del original
                var originalWidth = item.outerWidth();
                var clone = item.clone();
                clone.css({
                    width: originalWidth,
                    "box-sizing": "border-box",
                    transition: "none",
                });
                clone.addClass("dragging-card");
                return clone;
            },
            start: function (event, ui) {
                // Placeholder igual al tamaño de la tarjeta
                ui.placeholder.height(ui.item.outerHeight());
                ui.placeholder.width(ui.item.outerWidth());
                ui.placeholder.css("box-sizing", "border-box");
                // Elimina transiciones del original mientras se arrastra
                ui.item.css("transition", "none");
            },
            stop: function (event, ui) {
                // Restaura la transición al soltar
                ui.item.css("transition", "");
            },
            receive: function (event, ui) {
                const card = ui.item;
                const id = card.data("task-id");
                const newEstado = $(this).data("status");
                fetch(`/tareas/${id}/update-status`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: `_method=PATCH&estado=${encodeURIComponent(
                        newEstado
                    )}`,
                }).then((res) => {
                    if (res.ok) {
                        toastr.success("Estado actualizado.");
                        actualizarContadoresKanban();
                    } else {
                        toastr.error("No se pudo actualizar el estado.");
                    }
                });
            },
        })
        .disableSelection();
});
