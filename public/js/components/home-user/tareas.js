document.addEventListener("DOMContentLoaded", function () {
    // EventBus para comunicación entre componentes - implementación mejorada
    window.eventBus = window.eventBus || new EventTarget();

    // Inicializar variables con caché de selectores DOM
    const DOM = {
        selectAllCheckbox: document.getElementById("select-all-subtasks"),
        subtasksCheckboxes: document.querySelectorAll(".subtask-checkbox"),
        estadoModal: document.getElementById("estadoModal")
            ? new bootstrap.Modal(document.getElementById("estadoModal"))
            : null,
        addSubtareaBtn: document.getElementById("add-subtarea"),
        subtareasContainer: document.getElementById("subtareas-container"),
        toggleFormBtn: document.getElementById("toggle-form"),
        confirmarEstadoBtn: document.getElementById("confirmarEstado"),
        viewTogglers: document.querySelectorAll(".view-toggler"),
        views: document.querySelectorAll(".view"),
        form: document.querySelector("form"),
        calendar: document.getElementById("calendar"),
    };

    // Contador para indexar subtareas dinámicas
    let subtareaIndex = 0;
    let currentButton;

    // Función optimizada para actualizar el Kanban - esqueleto para futura implementación
    const actualizarKanban = () => {
        // Implementación pendiente
        console.debug("Actualizando Kanban...");
    };

    // Función optimizada para actualizar la línea de tiempo - esqueleto para futura implementación
    const actualizarTimeline = () => {
        // Implementación pendiente
        console.debug("Actualizando Timeline...");
    };

    // Función optimizada para recargar subtareas - esqueleto para futura implementación
    const recargarSubtareas = () => {
        // Implementación pendiente
        console.debug("Recargando subtareas...");
    };

    // API de EventBus mejorada con manejo de errores
    const emitirEvento = (nombre, detalle = {}) => {
        try {
            const evento = new CustomEvent(nombre, { detail: detalle });
            window.eventBus.dispatchEvent(evento);
            console.debug(`Evento "${nombre}" emitido`, detalle);
        } catch (error) {
            console.error(`Error al emitir evento "${nombre}":`, error);
        }
    };

    const escucharEvento = (nombre, callback) => {
        if (typeof callback !== "function") {
            console.warn(
                "El callback proporcionado para el evento no es una función válida"
            );
            return;
        }
        window.eventBus.addEventListener(nombre, callback);
    };

    // Gestión mejorada de checkboxes
    const updateSelectAllCheckbox = () => {
        if (!DOM.selectAllCheckbox) return;

        const checkboxes = Array.from(DOM.subtasksCheckboxes);
        if (checkboxes.length === 0) return;

        const allChecked = checkboxes.every((checkbox) => checkbox.checked);
        const someChecked = checkboxes.some((checkbox) => checkbox.checked);

        DOM.selectAllCheckbox.checked = allChecked;
        DOM.selectAllCheckbox.indeterminate = !allChecked && someChecked;
    };

    // Inicializar listeners para checkboxes
    if (DOM.selectAllCheckbox) {
        DOM.selectAllCheckbox.addEventListener("change", () => {
            const isChecked = DOM.selectAllCheckbox.checked;
            DOM.subtasksCheckboxes.forEach((checkbox) => {
                checkbox.checked = isChecked;
            });
        });
    }

    DOM.subtasksCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", updateSelectAllCheckbox);
    });

    // Inicializar estado de checkboxes
    updateSelectAllCheckbox();

    // Usar delegación de eventos para mejorar rendimiento
    document.body.addEventListener("click", (event) => {
        // Manejar clics en los badges de estado
        const estadoBadge = event.target.closest(".estado-badge");
        if (estadoBadge) {
            console.debug("Badge clickeado:", estadoBadge);
            currentButton = estadoBadge;

            const subtareaId = estadoBadge.dataset.id;
            const currentState = estadoBadge.dataset.currentState;

            document.getElementById("subtareaId").value = subtareaId;
            document.getElementById("nuevoEstado").value = currentState;

            DOM.estadoModal?.show();
        }

        // Manejar clics en botones de eliminación de subtareas
        if (event.target.classList.contains("remove-subtarea")) {
            const subtarea = event.target.closest(".subtarea");
            if (subtarea) subtarea.remove();
        }
    });

    // Confirmar cambio de estado con validación mejorada
    if (DOM.confirmarEstadoBtn) {
        DOM.confirmarEstadoBtn.addEventListener("click", () => {
            const subtareaId = document.getElementById("subtareaId").value;
            const nuevoEstado = document.getElementById("nuevoEstado").value;

            if (!subtareaId || !nuevoEstado) {
                console.error("ID de subtarea o nuevo estado no válidos");
                Swal.fire(
                    "Error",
                    "Datos incompletos para actualizar estado",
                    "error"
                );
                return;
            }

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            if (!csrfToken) {
                console.error("Token CSRF no encontrado");
                Swal.fire(
                    "Error",
                    "Error de seguridad. Refresca la página e intenta nuevamente.",
                    "error"
                );
                return;
            }

            fetch(`/tareas/${subtareaId}/update-status`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ estado: nuevoEstado }),
            })
                .then((response) => {
                    if (!response.ok)
                        throw new Error(`Error HTTP: ${response.status}`);
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        currentButton.textContent = nuevoEstado;
                        currentButton.dataset.currentState = nuevoEstado;

                        Swal.fire({
                            title: "¡Actualizado!",
                            text: "El estado de la subtarea ha sido actualizado.",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500,
                        });

                        // Emitir evento para actualizar vistas
                        emitirEvento("subtareaActualizada", {
                            id: subtareaId,
                            estado: nuevoEstado,
                        });
                    } else {
                        Swal.fire(
                            "Error",
                            data.message ||
                                "Hubo un problema al actualizar el estado.",
                            "error"
                        );
                    }
                    DOM.estadoModal?.hide();
                })
                .catch((error) => {
                    console.error("Error en la petición:", error);
                    Swal.fire(
                        "Error",
                        "Error al procesar la solicitud. Intenta de nuevo.",
                        "error"
                    );
                    DOM.estadoModal?.hide();
                });
        });
    }

    // Agregar subtareas dinámicamente con mejor manejo de errores
    if (DOM.addSubtareaBtn) {
        DOM.addSubtareaBtn.addEventListener("click", function () {
            const template = document.getElementById("subtarea-template");
            if (!template || !DOM.subtareasContainer) {
                console.error(
                    "No se encontró la plantilla o el contenedor de subtareas"
                );
                return;
            }

            const newSubtarea = template.cloneNode(true);
            newSubtarea.style.display = "block";
            newSubtarea.removeAttribute("id");

            // Asignar nombres e IDs únicos a los inputs
            const inputs = newSubtarea.querySelectorAll("input, select");
            inputs.forEach((input) => {
                const fieldName = input.getAttribute("data-field");
                if (fieldName) {
                    input.name = `subtareas[${subtareaIndex}][${fieldName}]`;
                    input.id = `subtarea_${subtareaIndex}_${fieldName}`;
                    input.required = true;
                }
            });

            DOM.subtareasContainer.appendChild(newSubtarea);
            subtareaIndex++;

            // Animar entrada de la nueva subtarea para mejor experiencia de usuario
            newSubtarea.style.opacity = "0";
            newSubtarea.style.transform = "translateY(20px)";
            newSubtarea.style.transition =
                "opacity 0.3s ease, transform 0.3s ease";

            // Forzar un reflow para asegurar que la transición funcione
            newSubtarea.offsetHeight;

            // Aplicar transición
            setTimeout(() => {
                newSubtarea.style.opacity = "1";
                newSubtarea.style.transform = "translateY(0)";
            }, 10);
        });
    }

    // Gestión de vistas mejorada con transiciones suaves
    const hideAllViews = () => {
        DOM.views.forEach((view) => {
            if (!view.classList.contains("active")) {
                view.style.display = "none";
            }
        });
    };

    // Inicializar vista por defecto
    DOM.views.forEach((view) => view.classList.remove("active"));
    const defaultView = document.querySelector(".view.list");
    if (defaultView) defaultView.classList.add("active");
    hideAllViews();

    // Cambiar entre vistas con manejo mejorado
    DOM.viewTogglers.forEach((button) => {
        button.addEventListener("click", function () {
            const viewName = this.getAttribute("data-view");
            if (!viewName) return;

            // Actualizar clases activas
            DOM.views.forEach((v) => v.classList.remove("active"));
            const targetView = document.querySelector(`.view.${viewName}`);
            if (!targetView) return;

            targetView.classList.add("active");
            hideAllViews();

            // Mostrar vista seleccionada con transición suave
            targetView.style.display = "";
            targetView.style.opacity = "0";
            targetView.style.transition = "opacity 0.3s ease";

            setTimeout(() => {
                targetView.style.opacity = "1";
            }, 10);

            // Inicializar calendario si se selecciona esa vista
            if (viewName === "calendar") {
                initializeCalendar();
            }
        });
    });

    // Función para inicializar FullCalendar con manejo de errores
    function initializeCalendar() {
        if (!DOM.calendar || DOM.calendar.classList.contains("initialized"))
            return;

        console.debug("Inicializando calendario...");
        try {
            DOM.calendar.classList.add("initialized");

            const calendarConfig = {
                initialView: "dayGridMonth",
                events: window.calendarEvents || [],
                locale: "es",
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay",
                },
                buttonText: {
                    today: "Hoy",
                    month: "Mes",
                    week: "Semana",
                    day: "Día",
                },
                eventClick: function (info) {
                    const evento = info.event;

                    // Actualizar contenido del modal
                    document.getElementById("task-title").textContent =
                        evento.title;
                    document.getElementById("task-general").textContent =
                        evento.extendedProps.tareaGeneral || "No especificado";
                    document.getElementById("task-description").textContent =
                        evento.extendedProps.descripcion || "Sin descripción";
                    document.getElementById("task-status").textContent =
                        evento.extendedProps.estado || "Pendiente";
                    document.getElementById("task-priority").textContent =
                        evento.extendedProps.prioridad || "Normal";

                    // Mostrar modal
                    const taskModal = new bootstrap.Modal(
                        document.getElementById("taskModal")
                    );
                    taskModal.show();
                },
                eventTimeFormat: {
                    hour: "2-digit",
                    minute: "2-digit",
                    meridiem: false,
                    hour12: false,
                },
            };

            new FullCalendar.Calendar(DOM.calendar, calendarConfig).render();
            console.debug("Calendario inicializado correctamente");
        } catch (error) {
            console.error("Error al inicializar el calendario:", error);
        }
    }

    // Toggle del formulario con animación mejorada
    if (DOM.toggleFormBtn) {
        DOM.toggleFormBtn.addEventListener("click", function () {
            const form = document.getElementById("new-task-form");
            if (!form) return;

            // Si está oculto, mostrarlo con animación
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
                form.style.maxHeight = "0px";
                form.style.overflow = "hidden";
                form.style.transition = "max-height 0.5s ease";

                // Forzar un reflow para asegurar que la transición funcione
                form.offsetHeight;

                // Obtener la altura natural del contenido
                const height = form.scrollHeight;
                form.style.maxHeight = height + "px";

                // Quitar restricciones después de la transición
                setTimeout(() => {
                    form.style.maxHeight = "";
                    form.style.overflow = "";
                }, 500);
            } else {
                // Si está visible, ocultarlo con animación
                form.style.maxHeight = form.scrollHeight + "px";
                form.style.overflow = "hidden";
                form.style.transition = "max-height 0.5s ease";

                // Forzar un reflow
                form.offsetHeight;

                // Colapsar
                form.style.maxHeight = "0px";

                // Ocultar después de la transición
                setTimeout(() => {
                    form.style.display = "none";
                    form.style.maxHeight = "";
                    form.style.overflow = "";
                }, 500);
            }
        });
    }

    // Sincronizar el editor Quill si existe
    let quill;
    if (DOM.form) {
        DOM.form.addEventListener("submit", function (e) {
            if (quill) {
                try {
                    const descriptionField =
                        document.querySelector("#description");
                    if (descriptionField) {
                        descriptionField.value = quill.root.innerHTML;
                    }
                } catch (error) {
                    console.error("Error al sincronizar editor Quill:", error);
                    // No prevenir envío del formulario para evitar bloqueo
                }
            }
        });
    }

    // Escuchar eventos para actualizar vistas
    escucharEvento("subtareaActualizada", (event) => {
        console.debug("Evento subtareaActualizada recibido:", event.detail);
        recargarSubtareas();
        actualizarKanban();
        actualizarTimeline();
        initializeCalendar();
    });

    // Iniciar con la primera vista
    const defaultViewToggler = document.querySelector(
        '.view-toggler[data-view="list"]'
    );
    if (defaultViewToggler) {
        defaultViewToggler.click();
    }
});
