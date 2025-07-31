let kanbanAutoScrollInterval = null;
let lastDragEvent = null;

function activarSortableKanban() {
    $(".kanban-container .task-list")
        .sortable({
            connectWith: ".task-list",
            placeholder: "ui-state-highlight",
            items: ".task-card",
            tolerance: "pointer",
            revert: 0,
            scroll: false,
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
            },
            drag: function (event, ui) {
                // Scroll vertical (opcional)
                if (window.innerWidth <= 768) {
                    var scrollContainer =
                        document.scrollingElement || document.documentElement;
                    var scrollTop = scrollContainer.scrollTop;
                    var windowHeight = window.innerHeight;
                    var mouseY = event.originalEvent.touches
                        ? event.originalEvent.touches[0].clientY
                        : event.pageY;

                    if (mouseY < 80) {
                        scrollContainer.scrollTop = scrollTop - 20;
                    } else if (mouseY > windowHeight - 80) {
                        scrollContainer.scrollTop = scrollTop + 20;
                    }
                }

                // --- Scroll horizontal anticipado ---
                const kanbanBoard = document.querySelector(".kanban-board");
                if (!kanbanBoard) return;
                const rect = kanbanBoard.getBoundingClientRect();
                const mouseX = event.originalEvent.touches
                    ? event.originalEvent.touches[0].clientX
                    : event.pageX;

                const scrollZone = 120;
                const scrollSpeed = 36;

                if (mouseX - rect.left < scrollZone) {
                    // Cerca del borde izquierdo
                    kanbanBoard.scrollLeft = Math.max(
                        kanbanBoard.scrollLeft - scrollSpeed,
                        0
                    );
                } else if (rect.right - mouseX < scrollZone) {
                    // Cerca del borde derecho
                    const maxScroll =
                        kanbanBoard.scrollWidth - kanbanBoard.clientWidth;
                    kanbanBoard.scrollLeft = Math.min(
                        kanbanBoard.scrollLeft + scrollSpeed,
                        maxScroll
                    );
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
            over: function (event, ui) {
                // Scroll horizontal SOLO cuando la tarjeta pasa sobre una columna
                const column = $(this).closest(".kanban-column")[0];
                const kanbanBoard = document.querySelector(".kanban-board");
                if (!column || !kanbanBoard) return;

                const scrollLeft = kanbanBoard.scrollLeft;
                const columnLeft = column.offsetLeft;
                const columnRight = columnLeft + column.offsetWidth;

                if (columnLeft < scrollLeft) {
                    kanbanBoard.scrollTo({
                        left: columnLeft,
                        behavior: "smooth",
                    });
                } else if (columnRight > scrollLeft + kanbanBoard.clientWidth) {
                    kanbanBoard.scrollTo({
                        left: columnRight - kanbanBoard.clientWidth,
                        behavior: "smooth",
                    });
                }
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
    reasociarEventosLista();
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
